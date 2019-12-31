<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Requester;

use Darkanakin41\StreamBundle\Endpoint\TwitchEndpoint;
use Darkanakin41\StreamBundle\Model\Stream;
use Darkanakin41\StreamBundle\Model\StreamCategory;
use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\StreamBundle\Nomenclature\StatusNomenclature;
use Darkanakin41\StreamBundle\Twig\StreamExtension;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TwitchRequester extends AbstractRequester
{
    const MAX_PAGE = 10;

    /** @var StreamCategory[] */
    private $categoriesCreated = array();

    /**
     * @var TwitchEndpoint
     */
    private $twitchEndpoint;

    public function __construct(ManagerRegistry $registry, StreamExtension $streamExtension, ParameterBagInterface $parameterBag, TwitchEndpoint $twitchEndpoint)
    {
        parent::__construct($registry, $streamExtension, $parameterBag);
        $this->twitchEndpoint = $twitchEndpoint;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     * @throws GuzzleException
     */
    public function updateFromCategory(StreamCategory $category)
    {
        $streams = array();

        foreach ($category->getPlatformKeys() as $key => $value) {
            if (0 !== stripos($key, 'twitch_')) {
                continue;
            }

            $cursor = null;
            for ($i = 0; $i < self::MAX_PAGE; ++$i) {
                $data = $this->twitchEndpoint->getGameStreams($value, $cursor);
                if (isset($data['data'])) {
                    foreach ($data['data'] as $streamData) {
                        if (isset($streams[strtolower($streamData['user_id'])])) {
                            continue; // @codeCoverageIgnore
                        }
                        $stream = $this->createStream($streamData, $category);
                        $streams[$stream->getUserId()] = $stream;
                    }
                }

                if ($this->isNextPage($cursor, $data)) {
                    $cursor = $data['pagination']['cursor'];
                } else {
                    break;
                }
            }
        }

        $this->updateAvatars($streams);

        return $streams;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     * @throws GuzzleException
     */
    public function refresh(array $toProcess)
    {
        $streams = array();
        $streamsId = array();
        foreach ($toProcess as $stream) {
            $this->resetStream($stream);
            if (null === $stream->getUserId()) {
                continue;
            }
            $streamsId[] = $stream->getUserId();
            $streams[$stream->getUserId()] = $stream;
        }

        $cursor = null;
        $empty = false;
        while (!$empty) {
            $data = $this->twitchEndpoint->getStreams($streamsId, $cursor);
            if (isset($data['data'])) {
                $empty = (0 === count($data['data']));
                foreach ($data['data'] as $streamData) {
                    $stream = $streams[strtolower($streamData['user_id'])];
                    $this->updateStream($stream, $streamData);
                }
            }

            $this->registry->getManager()->flush();
            if ($this->isNextPage($cursor, $data)) {
                $cursor = $data['pagination']['cursor']; // @codeCoverageIgnore
            } else {
                break;
            }
        }

        $this->updateAvatars($streams);
    }

    /**
     * Retrieve user data from username.
     *
     * @param $username
     *
     * @return array|null
     *
     * @throws GuzzleException
     */
    public function getUserData($username)
    {
        return $this->twitchEndpoint->getUserDataFromUsername($username);
    }

    /**
     * Create stream.
     *
     * @param array          $streamData the data to process
     * @param StreamCategory $category
     *
     * @return Stream the stream created
     *
     * @throws GuzzleException
     */
    private function createStream(array $streamData, StreamCategory $category = null)
    {
        $stream = $this->registry->getRepository($this->getStreamClass())->findOneBy(array('identifier' => strtolower($streamData['user_name'])));
        if (null === $stream) {
            $stream = $this->createStreamObject();
            $stream->setName($streamData['user_name']);
            $stream->setPlatform(PlatformNomenclature::TWITCH);
            $stream->setHighlighted(false);
        }

        $this->updateStream($stream, $streamData, $category);

        return $stream;
    }

    /**
     * Update stream with given data.
     *
     * @param Stream         $stream         the stream to update
     * @param array          $streamData     the data do process
     * @param StreamCategory $streamCategory the category to associate it to (if null, will look for it using the API)
     *
     * @return void
     *
     * @throws GuzzleException
     */
    private function updateStream(Stream $stream, array $streamData = array(), StreamCategory $streamCategory = null)
    {
        $stream->setUserId($streamData['user_id']);
        $stream->setTitle($streamData['title']);
        $stream->setStatus(StatusNomenclature::ONLINE);
        if ('other' !== $streamData['language']) {
            $languages = explode('-', $streamData['language']);
            $stream->setLanguage(array_shift($languages));
            $stream->setLanguage($this->streamExtension->language($stream));
        }
        $stream->setViewers($streamData['viewer_count']);
        $stream->setPreview($streamData['thumbnail_url']);
        $stream->setTags(array());

        $categoryUpdated = false;
        if (null !== $streamCategory) {
            foreach ($streamCategory->getPlatformKeys() as $key => $value) {
                if (0 !== stripos($key, 'twitch_')) {
                    continue;
                }
                $stream->setCategory($streamCategory);
                $categoryUpdated = true;
            }
        }

        if (!$categoryUpdated) {
            $category = $this->registry->getRepository($this->getStreamCategoryClass())->findByKey($stream->getPlatform(), $streamData['game_id']);
            if (null === $category && 0 != $streamData['game_id']) {
                if (isset($this->categoriesCreated[$streamData['game_id']])) {
                    $category = $this->categoriesCreated[$streamData['game_id']];
                }
            }

            if (null === $category && 0 != $streamData['game_id']) {
                $data = $this->twitchEndpoint->getGame($streamData['game_id']);
                $category = $this->createStreamCategoryObject();
                $category->setRefresh(false);
                $category->setDisplayed(false);
                $category->setTitle($data['name']);
                $category->setPlatformKeys(array('twitch_0' => $streamData['game_id']));
                $this->categoriesCreated[$streamData['game_id']] = $category;
            }

            $stream->setCategory($category);
        }
    }

    /**
     * Check whether of not there is a next page.
     *
     * @param string $currentCursor the current cursor
     * @param array  $data          the data from the API
     *
     * @return bool
     */
    private function isNextPage($currentCursor, $data)
    {
        return isset($data['pagination']) && isset($data['pagination']['cursor']) && 'IA' !== $data['pagination']['cursor'] && $data['pagination']['cursor'] !== $currentCursor;
    }

    /**
     * Update streams avatar.
     *
     * @param Stream[] $streams
     *
     * @throws Exception
     * @throws GuzzleException
     */
    private function updateAvatars(array $streams)
    {
        $streamsId = array_keys($streams);

        for ($i = 0; $i <= ceil(count($streamsId) / self::MAX_PAGE); ++$i) {
            $toUpdate = array_slice($streamsId, self::MAX_PAGE * $i, self::MAX_PAGE);
            if (empty($toUpdate)) {
                break;
            }
            $data = $this->twitchEndpoint->getUsers($toUpdate);
            if (isset($data['data'])) {
                foreach ($data['data'] as $streamData) {
                    $stream = $streams[$streamData['id']];
                    $stream->setLogo($streamData['profile_image_url']);
                    $stream->setUserId($streamData['id']);
                    $stream->setIdentifier($streamData['login']);
                }
            }
        }
    }

    /**
     * Reset data for the given stream.
     *
     * @throws Exception
     */
    private function resetStream(Stream $stream)
    {
        $stream->setUpdated(new DateTime());
        $stream->setStatus(StatusNomenclature::OFFLINE);
        $stream->setViewers(null);
        $stream->setCategory(null);
    }
}
