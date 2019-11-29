<?php

namespace Darkanakin41\StreamBundle\Requester;


use Darkanakin41\ApiBundle\EndPoint\Twitch\GamesEndPoint;
use Darkanakin41\ApiBundle\EndPoint\Twitch\StreamEndPoint;
use Darkanakin41\ApiBundle\EndPoint\Twitch\UserEndPoint;
use Darkanakin41\ApiBundle\Nomenclature\ClientNomenclature;
use Darkanakin41\ApiBundle\Nomenclature\EndPointNomenclature;
use Darkanakin41\StreamBundle\Entity\Stream;
use Darkanakin41\StreamBundle\Entity\StreamCategory;
use Darkanakin41\StreamBundle\Nomenclature\ProviderNomenclature;
use Darkanakin41\StreamBundle\Nomenclature\StatusNomenclature;

class TwitchRequester extends AbstractRequester
{
    const MAX_PAGE = 10;

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function updateFromCategory(StreamCategory $category)
    {
        /** @var StreamEndPoint $endpoint */
        $endpoint = $this->apiService->getEndPoint(ClientNomenclature::TWITCH, EndPointNomenclature::STREAM);

        $streams = 0;
        $streamsId = [];

        foreach ($category->getPlatformKeys() as $key => $value) {
            if (stripos($key, "twitch_") !== 0) continue;

            $cursor = null;
            for ($i = 0; $i < self::MAX_PAGE; $i++) {
                $data = $endpoint->getGameStreams($value, $cursor);
                if (isset($data['data'])) {
                    foreach ($data['data'] as $streamData) {
                        $streams += $this->createStream($streamData, $category);
                        $streamsId[] = strtolower($streamData['user_name']);
                    }
                } else {
                    // TODO Create exception
                }
                $this->registry->getManager()->flush();
                if (isset($data['pagination']) && isset($data['pagination']['cursor'])) $cursor = $data['pagination']['cursor'];
                else break;
            }
        }

        $this->updateAvatars($streamsId);

        return $streams;
    }

    /**
     * Create stream
     *
     * @param StreamCategory $category
     * @param array          $streamData
     *
     * @return int 1 if created, 0 if not
     * @throws \Exception
     */
    private function createStream(array $streamData, StreamCategory $category = null)
    {
        $stream = $this->registry->getRepository(Stream::class)->findOneBy(['identifier' => strtolower($streamData['user_name'])]);
        $created = 0;
        if ($stream === null) {
            $stream = new Stream();
            $stream->setName($streamData['user_name']);
            $stream->setPlatform(ProviderNomenclature::TWITCH);
            $stream->setIdentifier(strtolower($streamData['user_name']));
            $stream->setHighlighted(false);

            $created = 1;
        }

        $this->updateStream($stream, $streamData, $category);

        $this->registry->getManager()->persist($stream);
        return $created;
    }

    /**
     * Update stream with given data
     *
     * @param Stream         $stream
     * @param StreamCategory $streamCategory
     * @param array          $streamData
     *
     * @return void 1 if created, 0 if not
     *
     * @throws \Exception
     */
    private function updateStream(Stream $stream, array $streamData = [], StreamCategory $streamCategory = null)
    {
        $stream->setUpdated(new \DateTime());

        if (count($streamData) === 0) {
            $stream->setStatus(StatusNomenclature::OFFLINE);
            $stream->setViewers(null);
            $stream->setCategory(null);
            return;
        }

        $stream->setTitle($streamData['title']);
        $stream->setStatus(StatusNomenclature::ONLINE);
        if ($streamData['language'] !== 'other') {
            $languages = explode('-', $streamData['language']);
            $stream->setLanguage(array_shift($languages));
            $stream->setLanguage($this->streamExtension->language($stream));
        }
        $stream->setViewers($streamData['viewer_count']);
        $stream->setPreview($streamData['thumbnail_url']);
        $stream->setTags([]);

        $categoryUpdated = false;
        if ($streamCategory !== null) {
            foreach ($streamCategory->getPlatformKeys() as $key => $value) {
                if (stripos($key, "twitch_") !== 0) continue;
                if ($value !== $streamData['game_id']) continue;
                $stream->setCategory($streamCategory);
                $categoryUpdated = true;
                break;
            }
        }

        try {
            if (!$categoryUpdated) {
                $category = $this->registry->getRepository(StreamCategory::class)->findByKey($stream->getPlatform(), $streamData['game_id']);
                if ($category === null && $streamData['game_id'] != 0) {
                    /** @var GamesEndPoint $endpoint */
                    $endpoint = $this->apiService->getEndPoint(ClientNomenclature::TWITCH, EndPointNomenclature::GAMES);
                    $data = $endpoint->getData($streamData['game_id']);
                    $category = new StreamCategory();
                    $category->setRefresh(false);
                    $category->setDisplayed(false);
                    $category->setTitle($data["data"][0]['name']);
                    $category->setPlatformKeys(['twitch_0' => $streamData['game_id']]);

                    $this->registry->getManager()->persist($category);
                }
                $stream->setCategory($category);
            }
        } catch (\Exception $e) {
            dump($data);
        }
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function refresh(array $streams)
    {
        /** @var StreamEndPoint $endpoint */
        $endpoint = $this->apiService->getEndPoint(ClientNomenclature::TWITCH, EndPointNomenclature::STREAM);

        $streamsId = [];
        foreach ($streams as $stream) {
            $streamsId[] = $stream->getIdentifier();
            $this->updateStream($stream);
        }

        $cursor = null;
        $empty = false;
        while (!$empty) {
            $data = $endpoint->getStreams($streamsId, $cursor);
            if (isset($data['data'])) {
                $empty = count($data['data']) === 0;
                foreach ($data['data'] as $streamData) {
                    $this->createStream($streamData, null);
                }
            } else {
                // TODO Create exception
            }
            $this->registry->getManager()->flush();
            if (isset($data['pagination']) && isset($data['pagination']['cursor'])) $cursor = $data['pagination']['cursor'];
            else break;
        }

        $this->updateAvatars($streamsId);
    }

    /**
     * @param string[] $streamsId
     *
     * @throws \Exception
     */
    private function updateAvatars(array $streamsId)
    {
        /** @var UserEndPoint $endpoint */
        $endpoint = $this->apiService->getEndPoint(ClientNomenclature::TWITCH, EndPointNomenclature::USER);

        $i = 0;

        while (!empty(array_slice($streamsId, self::MAX_PAGE * $i, self::MAX_PAGE))) {
            $data = $endpoint->getUsers(array_slice($streamsId, self::MAX_PAGE * $i, self::MAX_PAGE));
            if (isset($data['data'])) {
                foreach ($data['data'] as $streamData) {
                    $stream = $this->registry->getRepository(Stream::class)->findOneBy(['identifier' => $streamData['login']]);
                    $stream->setLogo($streamData['profile_image_url']);
                    $this->registry->getManager()->persist($stream);
                }
            } else {
                // TODO Create exception
            }
            $i++;
            $this->registry->getManager()->flush();
        }
    }
}
