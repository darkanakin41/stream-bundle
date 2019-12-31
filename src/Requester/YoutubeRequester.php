<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Requester;

use Darkanakin41\StreamBundle\Endpoint\YoutubeEndpoint;
use Darkanakin41\StreamBundle\Model\Stream;
use Darkanakin41\StreamBundle\Model\StreamCategory;
use Darkanakin41\StreamBundle\Twig\StreamExtension;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Google_Service_YouTube_Video;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class YoutubeRequester extends AbstractRequester
{
    const MAX_PAGE = 10;
    /**
     * @var YoutubeEndpoint
     */
    private $youtubeEndpoint;

    public function __construct(ManagerRegistry $registry, StreamExtension $streamExtension, ParameterBagInterface $parameterBag, YoutubeEndpoint $youtubeEndpoint)
    {
        parent::__construct($registry, $streamExtension, $parameterBag);
        $this->youtubeEndpoint = $youtubeEndpoint;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function updateFromCategory(StreamCategory $category)
    {
        return array();
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function refresh(array $toProcess)
    {
        $toRemove = array();
        $toUpdate = array();

        $ids = array();
        foreach ($toProcess as $video) {
            $ids[] = $video->getIdentifier();
        }

        $data = $this->youtubeEndpoint->getVideosData($ids);

        if (0 === count($data->getItems())) {
            return array('toUpdate' => $toUpdate, 'toRemove' => $toRemove);
        }

        foreach ($toProcess as $video) {
            $videoData = null;
            /** @var \Google_Service_YouTube_Video $item */
            foreach ($data->getItems() as $item) {
                if ($item->getId() === $video->getIdentifier()) {
                    $videoData = $item;
                }
            }

            if (null === $videoData || empty($videoData->getSnippet())) {
                $toRemove[] = $video;
                continue;
            }

            $this->updateStreamData($video, $videoData);
            $toUpdate[] = $video;
        }

        return array('toUpdate' => $toUpdate, 'toRemove' => $toRemove);
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

        $stream->setPreview($data->getSnippet()->getThumbnails()->getDefault());

        if ('' !== $data->getSnippet()->getThumbnails()->getMedium()) {
            $stream->setPreview($data->getSnippet()->getThumbnails()->getMedium());
        }

        if ('' !== $data->getSnippet()->getThumbnails()->getHigh()) {
            $stream->setPreview($data->getSnippet()->getThumbnails()->getHigh());
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
