<?php

namespace PLejeune\StreamBundle\Requester;


use PLejeune\ApiBundle\Nomenclature\ClientNomenclature;
use PLejeune\ApiBundle\Nomenclature\EndPointNomenclature;
use PLejeune\StreamBundle\Entity\Stream;
use PLejeune\StreamBundle\Entity\StreamCategory;
use PLejeune\StreamBundle\Nomenclature\ProviderNomenclature;
use PLejeune\StreamBundle\Nomenclature\StatusNomenclature;

class TwitchRequester extends AbstractRequester
{
    const MAX_PAGE = 10;

    /**
     * {@inheritdoc}
     */
    public function updateFromCategory(StreamCategory $category)
    {
        $endpoint = $this->apiService->getEndPoint(ClientNomenclature::TWITCH, EndPointNomenclature::STREAM);

        $streams = 0;

        foreach($category->getPlatformKeys() as $key => $value){
            if(stripos($key, "twitch_") !== 0) continue;

            $cursor = null;
            for($i = 0; $i < self::MAX_PAGE; $i++){
                $data = $endpoint->getGameStreams($value, $cursor);
                if(isset($data['data'])){
                    foreach($data['data'] as $streamData){
                        $streams += $this->createStream($category, $streamData);
                    }
                }
                $this->registry->getManager()->flush();
                if(isset($data['pagination']) && isset($data['pagination']['cursor'])) $cursor = $data['pagination']['cursor'];
                else break;
            }
        }


        return $streams;
    }

    /**
     * Create stream
     *
     * @param StreamCategory $category
     * @param array $streamData
     * @return int 1 if created, 0 if not
     * @throws \Exception
     */
    private function createStream(StreamCategory $category, array $streamData){
        $stream = $this->registry->getRepository(Stream::class)->findOneBy(['identifier' => strtolower($streamData['user_name'])]);
        $created = 0;
        if($stream === null){
            $stream = new Stream();
            $stream->setName($streamData['user_name']);
            $stream->setPlatform(ProviderNomenclature::TWITCH);
            $stream->setIdentifier(strtolower($streamData['user_name']));

            $created = 1;
        }

        $this->updateStream($stream, $category, $streamData);

        $this->registry->getManager()->persist($stream);
        return $created;
    }

    private function updateStream(Stream $stream, StreamCategory $streamCategory, array $streamData = []){

        if(count($streamData) === 0){
            $stream->setStatus(StatusNomenclature::OFFLINE);
            $stream->setViewers(null);
            $stream->setCategory(null);
            return;
        }

        $stream->setTitle($streamData['title']);
        $stream->setStatus(StatusNomenclature::ONLINE);
        $stream->setLanguage($streamData['language']);
        $stream->setViewers($streamData['viewer_count']);
        $stream->setPreview($streamData['thumbnail_url']);
        $stream->setTags([]);
        $stream->setUpdated(new \DateTime());

        $categoryUpdated = false;
        foreach($streamCategory->getPlatformKeys() as $key => $value){
            if(stripos($key, "twitch_") !== 0) continue;
            if($value !== $streamData['game_id']) continue;
            $stream->setCategory($streamCategory);
            $categoryUpdated = true;
            break;
        }

        if(!$categoryUpdated){
            $category = $this->registry->getRepository(StreamCategory::class)->findByKey($stream->getPlatform(), $streamData['game_id']);
            if($category === null){
                $endpoint = $this->apiService->getEndPoint(ClientNomenclature::TWITCH, EndPointNomenclature::GAMES);
                $data = $endpoint->getData($streamData['game_id']);
                $category = new StreamCategory();
                $category->setRefresh(false);
                $category->setDisplayed(false);
                $category->setTitle($data["data"][0]['name']);

                $this->registry->getManager()->persist($category);
            }
            $stream->setCategory($category);
        }
    }
}