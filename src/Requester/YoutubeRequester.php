<?php

namespace Darkanakin41\StreamBundle\Requester;


use Darkanakin41\StreamBundle\Endpoint\YoutubeEndpoint;
use Darkanakin41\StreamBundle\Entity\Stream;
use Darkanakin41\StreamBundle\Entity\StreamCategory;
use Darkanakin41\StreamBundle\Extension\StreamExtension;
use DateTime;
use Exception;
use Google_Service_YouTube_Video;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class YoutubeRequester extends AbstractRequester
{
    const MAX_PAGE = 10;
    /**
     * @var YoutubeEndpoint
     */
    private $youtubeEndpoint;

    public function __construct(ManagerRegistry $registry, StreamExtension $streamExtension, YoutubeEndpoint $youtubeEndpoint)
    {
        parent::__construct($registry, $streamExtension);
        $this->youtubeEndpoint = $youtubeEndpoint;
    }


    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function updateFromCategory(StreamCategory $category)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function refresh(array $streams)
    {

        $ids = array();
        foreach ($streams as $stream) {
            $ids[] = $stream->getIdentifier();
        }

        $data = $this->youtubeEndpoint->getVideosData($ids);
        /** @var Google_Service_YouTube_Video[] $items */
        $items = $data->getItems();

        foreach ($streams as $stream) {
            /** @var array $data->getItems() */
            $items = array_filter($items, function (Google_Service_YouTube_Video $item) use ($stream) {
                return $item->getId() === $stream->getIdentifier();
            });

            $item = reset($items);

            if ($item === false || $item->getSnippet() === null || $item->getSnippet()->getLiveBroadcastContent() !== 'live') {
                $this->registry->getManager()->remove($stream);
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
     * @throws Exception
     */
    private function updateStreamData(Stream $stream, Google_Service_YouTube_Video $data)
    {
        $stream->setUpdated(new DateTime());

        $stream->setTitle($data->getSnippet()->getTitle());

        if ($data->getSnippet()->getThumbnails()->getHigh() !== '') {
            $stream->setPreview($data->getSnippet()->getThumbnails()->getHigh());
        } elseif ($data->getSnippet()->getThumbnails()->getMedium() !== '') {
            $stream->setPreview($data->getSnippet()->getThumbnails()->getMedium());
        } else {
            $stream->setPreview($data->getSnippet()->getThumbnails()->getDefault());
        }

        $stream->setViewers($data->getLiveStreamingDetails()->getConcurrentViewers());

        $categoryRepository = $this->registry->getRepository(StreamCategory::class);
        if(is_array($data->getSnippet()->getTags())){
            foreach ($data->getSnippet()->getTags() as $tag) {
                /** @var StreamCategory $category */
                $category = $categoryRepository->findOneBy(['title' => $tag]);
                if ($category !== null) {
                    $stream->setCategory($category);
                    break;
                }
            }
        }
    }
}
