<?php
namespace PLejeune\StreamBundle\Requester;

use PLejeune\ApiBundle\Service\ApiService;
use PLejeune\StreamBundle\Entity\Stream;
use PLejeune\StreamBundle\Entity\StreamCategory;
use PLejeune\StreamBundle\Extension\StreamExtension;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractRequester
{
    /**
     * @var RegistryInterface
     */
    protected $registry;
    /**
     * @var ApiService
     */
    protected $apiService;
    /**
     * @var StreamExtension
     */
    protected $streamExtension;

    public function __construct(RegistryInterface $registry, ApiService $apiService, StreamExtension $streamExtension){
        $this->registry = $registry;
        $this->apiService = $apiService;
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