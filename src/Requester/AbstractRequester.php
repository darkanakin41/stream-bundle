<?php
namespace Darkanakin41\StreamBundle\Requester;

use Darkanakin41\ApiBundle\Service\ApiService;
use Darkanakin41\StreamBundle\Entity\Stream;
use Darkanakin41\StreamBundle\Entity\StreamCategory;
use Darkanakin41\StreamBundle\Extension\StreamExtension;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;

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

    public function __construct(ManagerRegistry $registry, StreamExtension $streamExtension){
        $this->registry = $registry;
        $this->streamExtension = $streamExtension;
    }

    /**
     * Retrieve streams for the given $category
     * @param StreamCategory $category
     * @return integer Number of stream created
     */
    abstract public function updateFromCategory(StreamCategory $category);

    /**
     * Update the given stream
     * @param Stream[] $streams
     */
    abstract public function refresh(array $streams);
}
