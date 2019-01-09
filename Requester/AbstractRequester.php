<?php
namespace PLejeune\StreamBundle\Requester;

use PLejeune\ApiBundle\Service\ApiService;
use PLejeune\StreamBundle\Entity\StreamCategory;
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

    public function __construct(RegistryInterface $registry, ApiService $apiService){
        $this->registry = $registry;
        $this->apiService = $apiService;
    }

    /**
     * Retrieve streams for the given $category
     * @param StreamCategory $category
     * @return integer Number of stream created
     */
    abstract public function updateFromCategory(StreamCategory $category);
}