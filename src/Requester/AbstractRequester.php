<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Requester;

use Darkanakin41\StreamBundle\Extension\StreamExtension;
use Darkanakin41\StreamBundle\Model\Stream;
use Darkanakin41\StreamBundle\Model\StreamCategory;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class AbstractRequester
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;
    /**
     * @var StreamExtension
     */
    protected $streamExtension;
    /** @var string */
    private $streamCategoryClass;
    /** @var string */
    private $streamClass;

    public function __construct(ManagerRegistry $registry, StreamExtension $streamExtension, ContainerBuilder $containerBuilder)
    {
        $this->registry = $registry;
        $this->streamExtension = $streamExtension;

        $configuration = $containerBuilder->get('darkanakin41.stream.config');
        $this->streamClass = $configuration['stream_class'];
        $this->streamCategoryClass = $configuration['category_class'];
    }

    /**
     * @return Stream
     */
    public function createStreamObject()
    {
        $class = $this->streamClass;
        return new $class();
    }

    /**
     * @return StreamCategory
     */
    public function createStreamCategoryObject()
    {
        $class = $this->streamCategoryClass;
        return new $class();
    }

    /**
     * Retrieve streams for the given $category.
     *
     * @param StreamCategory $category
     *
     * @return int Number of stream created
     */
    abstract public function updateFromCategory(StreamCategory $category);

    /**
     * Update the given stream.
     *
     * @param Stream[] $streams
     */
    abstract public function refresh(array $streams);
}
