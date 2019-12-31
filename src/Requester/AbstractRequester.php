<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Requester;

use Darkanakin41\StreamBundle\DependencyInjection\Darkanakin41StreamExtension;
use Darkanakin41\StreamBundle\Model\Stream;
use Darkanakin41\StreamBundle\Model\StreamCategory;
use Darkanakin41\StreamBundle\Twig\StreamExtension;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

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

    public function __construct(ManagerRegistry $registry, StreamExtension $streamExtension, ParameterBagInterface $parameterBag)
    {
        $this->registry = $registry;
        $this->streamExtension = $streamExtension;

        $configuration = $parameterBag->get(Darkanakin41StreamExtension::CONFIG_KEY);
        $this->streamClass = $configuration['stream_class'];
        $this->streamCategoryClass = $configuration['category_class'];
    }

    /**
     * @return Stream
     */
    public function createStreamObject()
    {
        $class = $this->getStreamClass();

        return new $class();
    }

    /**
     * @return StreamCategory
     */
    public function createStreamCategoryObject()
    {
        $class = $this->getStreamCategoryClass();

        return new $class();
    }

    public function getStreamCategoryClass(): string
    {
        return $this->streamCategoryClass;
    }

    public function getStreamClass(): string
    {
        return $this->streamClass;
    }

    /**
     * Retrieve streams for the given $category.
     *
     * @return Stream[]
     */
    abstract public function updateFromCategory(StreamCategory $category);

    /**
     * Update the given stream.
     *
     * @param Stream[] $toProcess
     *
     * @return array [toUpdate, toRemove]
     */
    abstract public function refresh(array $toProcess);
}
