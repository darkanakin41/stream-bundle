<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Requester;

use Darkanakin41\StreamBundle\Endpoint\YoutubeEndpoint;
use Darkanakin41\StreamBundle\Extension\StreamExtension;
use Darkanakin41\StreamBundle\Model\Stream;
use Darkanakin41\StreamBundle\Model\StreamCategory;
use DateTime;
use Exception;
use Google_Service_YouTube_Video;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class YoutubeRequester extends AbstractRequester
{
    const MAX_PAGE = 10;
    /**
     * @var YoutubeEndpoint
     */
    private $youtubeEndpoint;

    public function __construct(ManagerRegistry $registry, StreamExtension $streamExtension, ContainerBuilder $containerBuilder, YoutubeEndpoint $youtubeEndpoint)
    {
        parent::__construct($registry, $streamExtension, $containerBuilder);
        $this->youtubeEndpoint = $youtubeEndpoint;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function updateFromCategory(StreamCategory $category)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     *
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
            /** @var array $data ->getItems() */
            $items = array_filter($items, function (Google_Service_YouTube_Video $item) use ($stream) {
                return $item->getId() === $stream->getIdentifier();
            });

            $item = reset($items);

            if (false === $item || null === $item->getSnippet() || 'live' !== $item->getSnippet()->getLiveBroadcastContent()) {
                $this->registry->getManager()->remove($stream);
                continue;
            }

            $this->updateStreamData($stream, $item);

            $this->registry->getManager()->persist($stream);
        }

        $this->registry->getManager()->flush();
    }

    /**
     * Update data of the stream based on retrieved informations.
     *
     * @param array $data
     *
     * @throws Exception
     */
    private function updateStreamData(Stream $stream, Google_Service_YouTube_Video $data)
    {
        $stream->setUpdated(new DateTime());

        $stream->setTitle($data->getSnippet()->getTitle());

        if ('' !== $data->getSnippet()->getThumbnails()->getHigh()) {
            $stream->setPreview($data->getSnippet()->getThumbnails()->getHigh());
        } elseif ('' !== $data->getSnippet()->getThumbnails()->getMedium()) {
            $stream->setPreview($data->getSnippet()->getThumbnails()->getMedium());
        } else {
            $stream->setPreview($data->getSnippet()->getThumbnails()->getDefault());
        }

        $stream->setViewers($data->getLiveStreamingDetails()->getConcurrentViewers());

        $categoryRepository = $this->registry->getRepository(StreamCategory::class);
        if (is_array($data->getSnippet()->getTags())) {
            foreach ($data->getSnippet()->getTags() as $tag) {
                /** @var StreamCategory $category */
                $category = $categoryRepository->findOneBy(array('title' => $tag));
                if (null !== $category) {
                    $stream->setCategory($category);
                    break;
                }
            }
        }
    }
}
