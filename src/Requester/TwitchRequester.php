<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Requester;

use Darkanakin41\StreamBundle\Endpoint\TwitchEndpoint;
use Darkanakin41\StreamBundle\Model\Stream;
use Darkanakin41\StreamBundle\Model\StreamCategory;
use Darkanakin41\StreamBundle\Extension\StreamExtension;
use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\StreamBundle\Nomenclature\StatusNomenclature;
use Exception;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwitchRequester extends AbstractRequester
{
    const MAX_PAGE = 10;

    /**
     * @var TwitchEndpoint
     */
    private $twitchEndpoint;

    public function __construct(ManagerRegistry $registry, StreamExtension $streamExtension, ContainerBuilder $containerBuilder, TwitchEndpoint $twitchEndpoint)
    {
        parent::__construct($registry, $streamExtension, $containerBuilder);
        $this->twitchEndpoint = $twitchEndpoint;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function updateFromCategory(StreamCategory $category)
    {
        $streams = 0;
        $streamsId = array();

        foreach ($category->getPlatformKeys() as $key => $value) {
            if (0 !== stripos($key, 'twitch_')) {
                continue;
            }

            $cursor = null;
            for ($i = 0; $i < self::MAX_PAGE; ++$i) {
                $data = $this->twitchEndpoint->getGameStreams($value, $cursor);
                if (isset($data['data'])) {
                    foreach ($data['data'] as $streamData) {
                        $streams += $this->createStream($streamData, $category);
                        $streamsId[] = strtolower($streamData['user_name']);
                    }
                }
                // TODO Create exception

                $this->registry->getManager()->flush();
                if (isset($data['pagination']) && isset($data['pagination']['cursor'])) {
                    $cursor = $data['pagination']['cursor'];
                } else {
                    break;
                }
            }
        }

        $this->updateAvatars($streamsId);

        return $streams;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function refresh(array $streams)
    {
        $streamsId = array();
        foreach ($streams as $stream) {
            $streamsId[] = $stream->getIdentifier();
            $this->updateStream($stream);
        }

        $cursor = null;
        $empty = false;
        while (!$empty) {
            $data = $this->twitchEndpoint->getStreams($streamsId, $cursor);
            if (isset($data['data'])) {
                $empty = 0 === count($data['data']);
                foreach ($data['data'] as $streamData) {
                    $this->createStream($streamData, null);
                }
            }
            // TODO Create exception

            $this->registry->getManager()->flush();
            if (isset($data['pagination']) && isset($data['pagination']['cursor'])) {
                $cursor = $data['pagination']['cursor'];
            } else {
                break;
            }
        }

        $this->updateAvatars($streamsId);
    }

    /**
     * Create stream.
     *
     * @param StreamCategory $category
     * @param array          $streamData
     *
     * @return int 1 if created, 0 if not
     *
     * @throws Exception
     */
    private function createStream(array $streamData, StreamCategory $category = null)
    {
        $stream = $this->registry->getRepository(Stream::class)->findOneBy(array('identifier' => strtolower($streamData['user_name'])));
        $created = 0;
        if (null === $stream) {
            $stream = $this->createStreamObject();
            $stream->setName($streamData['user_name']);
            $stream->setPlatform(PlatformNomenclature::TWITCH);
            $stream->setIdentifier(strtolower($streamData['user_name']));
            $stream->setHighlighted(false);

            $created = 1;
        }

        $this->updateStream($stream, $streamData, $category);

        $this->registry->getManager()->persist($stream);

        return $created;
    }

    /**
     * Update stream with given data.
     *
     * @param Stream         $stream
     * @param StreamCategory $streamCategory
     * @param array          $streamData
     *
     * @return void 1 if created, 0 if not
     *
     * @throws Exception
     */
    private function updateStream(Stream $stream, array $streamData = array(), StreamCategory $streamCategory = null)
    {
        $stream->setUpdated(new \DateTime());

        if (0 === count($streamData)) {
            $stream->setStatus(StatusNomenclature::OFFLINE);
            $stream->setViewers(null);
            $stream->setCategory(null);

            return;
        }

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
                if ($value !== $streamData['game_id']) {
                    continue;
                }
                $stream->setCategory($streamCategory);
                $categoryUpdated = true;
                break;
            }
        }

        try {
            if (!$categoryUpdated) {
                $category = $this->registry->getRepository(StreamCategory::class)->findByKey($stream->getPlatform(), $streamData['game_id']);
                if (null === $category && 0 != $streamData['game_id']) {
                    $data = $this->twitchEndpoint->getGame($streamData['game_id']);
                    $category = $this->createStreamCategoryObject();
                    $category->setRefresh(false);
                    $category->setDisplayed(false);
                    $category->setTitle($data['data'][0]['name']);
                    $category->setPlatformKeys(array('twitch_0' => $streamData['game_id']));

                    $this->registry->getManager()->persist($category);
                }
                $stream->setCategory($category);
            }
        } catch (Exception $e) {
            dump($data);
        }
    }

    /**
     * @param string[] $streamsId
     *
     * @throws Exception
     */
    private function updateAvatars(array $streamsId)
    {
        $i = 0;

        while (!empty(array_slice($streamsId, self::MAX_PAGE * $i, self::MAX_PAGE))) {
            $data = $this->twitchEndpoint->getUsers(array_slice($streamsId, self::MAX_PAGE * $i, self::MAX_PAGE));
            if (isset($data['data'])) {
                foreach ($data['data'] as $streamData) {
                    $stream = $this->registry->getRepository(Stream::class)->findOneBy(array('identifier' => $streamData['login']));
                    $stream->setLogo($streamData['profile_image_url']);
                    $this->registry->getManager()->persist($stream);
                }
            }
            // TODO Create exception

            ++$i;
            $this->registry->getManager()->flush();
        }
    }
}
