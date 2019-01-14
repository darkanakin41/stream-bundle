<?php

namespace PLejeune\StreamBundle\Service;


use PLejeune\StreamBundle\Entity\Stream;
use PLejeune\StreamBundle\Entity\StreamCategory;
use PLejeune\StreamBundle\Requester\AbstractRequester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StreamService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Retrieve streams from the category
     *
     * @param StreamCategory $streamCategory
     * @param $provider
     *
     * @return integer
     * @throws \Exception
     */
    public function getFromGame(StreamCategory $streamCategory, $provider){
        $requester = $this->getRequester($provider);

        $streams = $requester->updateFromCategory($streamCategory);

        return $streams;
    }

    /**
     * Update streams
     *
     * @param Stream[] $streams
     * @param $provider
     *
     * @throws \Exception
     */
    public function refresh(array $streams, $provider){
        $requester = $this->getRequester($provider);

        $requester->refresh($streams);
    }

    /**
     * Retrieve the requester from the providers
     *
     * @param string $provider
     *
     * @return AbstractRequester
     * @throws \Exception
     */
    private function getRequester($provider){
        $classname = sprintf('PLejeune\\StreamBundle\\Requester\\%sRequester',ucfirst($provider));
        if(!class_exists($classname)) throw new \Exception('unhandled_provider');
        $object = new $classname($this->container->get('doctrine'), $this->container->get('plejeune.api'));
        return $object;
    }
}