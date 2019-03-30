<?php

namespace PLejeune\StreamBundle\Requester;


use PLejeune\ApiBundle\EndPoint\Google\YoutubeEndPoint;
use PLejeune\ApiBundle\Nomenclature\ClientNomenclature;
use PLejeune\ApiBundle\Nomenclature\EndPointNomenclature;
use PLejeune\StreamBundle\Entity\Stream;
use PLejeune\StreamBundle\Entity\StreamCategory;

class YoutubeRequester extends AbstractRequester
{
    const MAX_PAGE = 10;

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function updateFromCategory(StreamCategory $category)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function refresh(array $streams)
    {
        /** @var YoutubeEndPoint $endpoint */
        $endpoint = $this->apiService->getEndPoint(ClientNomenclature::GOOGLE, EndPointNomenclature::YOUTUBE);

        $ids = array();
        foreach ($streams as $stream) {
            $ids[] = $stream->getIdentifier();
        }

        $data = $endpoint->getVideosData($ids);

        foreach ($streams as $stream) {
            /** @var array $items */
            $items = array_filter($data['items'], function ($item) use ($stream) {
                return $item['id'] === $stream->getIdentifier();
            });

            $item = reset($items);

            var_dump($item['snippet']['liveBroadcastContent']);
            if ($item === false || !isset($item['snippet']) || $item['snippet']['liveBroadcastContent'] !== 'live') {
                $this->registry->getManager()->remove($stream);
                var_dump("OFFLINE");
                continue;
            }

            $this->updateStreamData($stream, $item);

            $this->registry->getManager()->persist($stream);
        }

        $this->registry->getManager()->flush();
    }

    /**
     * Update data of the stream based on retrieved informations
     *
     * @param Stream $stream
     * @param array  $data
     *
     * @throws \Exception
     */
    private function updateStreamData(Stream $stream, array $data)
    {
        $stream->setUpdated(new \DateTime());

        $stream->setTitle($data['snippet']['title']);

        if (isset($data['snippet']['thumbnails']['high'])) {
            $stream->setPreview($data['snippet']['thumbnails']['high']['url']);
        } elseif (isset($data['snippet']['thumbnails']['medium'])) {
            $stream->setPreview($data['snippet']['thumbnails']['medium']['url']);
        } else {
            $stream->setPreview($data['snippet']['thumbnails']['default']['url']);
        }

        $stream->setViewers($data['liveStreamingDetails']['concurrentViewers']);

        $categoryRepository = $this->registry->getRepository(StreamCategory::class);
        foreach($data['snippet']['tags'] as $tag){
            /** @var StreamCategory $category */
            $category = $categoryRepository->findOneBy(['title' => $tag]);
            if($category !== null){
                $stream->setCategory($category);
                break;
            }
        }

    }
}
