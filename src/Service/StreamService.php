<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Service;

use Darkanakin41\StreamBundle\Helper\StreamHelper;
use Darkanakin41\StreamBundle\Model\Stream;
use Darkanakin41\StreamBundle\Model\StreamCategory;
use Darkanakin41\StreamBundle\Nomenclature\StatusNomenclature;
use Darkanakin41\StreamBundle\Requester\AbstractRequester;
use Darkanakin41\StreamBundle\Twig\StreamExtension;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StreamService
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @var StreamExtension
     */
    private $streamExtension;

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container, ManagerRegistry $registryManager, StreamExtension $streamExtension)
    {
        $this->registry = $registryManager;
        $this->streamExtension = $streamExtension;
        $this->container = $container;
    }

    /**
     * Retrieve streams from the category.
     *
     * @param string $provider
     *
     * @return int
     *
     * @throws Exception
     */
    public function getFromGame(StreamCategory $streamCategory, $provider)
    {
        $requester = $this->getRequester($provider);

        return $requester->updateFromCategory($streamCategory);
    }

    /**
     * Create a stream based on his name and URL.
     *
     * @param string $url
     * @param string $name
     * @param bool   $highlighted
     *
     * @return bool true if created, false if not
     *
     * @throws Exception
     */
    public function create($url, $name, $highlighted = false)
    {
        if (null === $url) {
            return false;
        }

        $requester = $this->getRequester(StreamHelper::getProvider($url));

        $stream = $requester->createStreamObject();
        $stream->setIdentifier(StreamHelper::getIdentifiant($url));
        $stream->setName($name);
        $stream->setHighlighted($highlighted);
        $stream->setPlatform(StreamHelper::getProvider($url));
        $stream->setStatus(StatusNomenclature::OFFLINE);
        $stream->setUpdated(new DateTime());
        $stream->setTags(array());

        $exist = $this->registry->getRepository(Stream::class)->findOneBy(array(
            'identifier' => $stream->getIdentifier(),
            'platform' => $stream->getPlatform(),
        ));

        if (null !== $exist) {
            return false;
        }

        $this->registry->getManager()->persist($stream);
        $this->registry->getManager()->flush();

        $this->refresh(array($stream), $stream->getPlatform());

        return true;
    }

    /**
     * Update streams.
     *
     * @param Stream[] $streams
     * @param string   $platform
     *
     * @throws Exception
     */
    public function refresh(array $streams, $platform)
    {
        $requester = $this->getRequester($platform);

        $requester->refresh($streams);
    }

    /**
     * Retrieve the requester from the providers.
     *
     * @param string $provider
     *
     * @return AbstractRequester
     *
     * @throws Exception
     */
    public function getRequester($provider)
    {
        $classname = sprintf('Darkanakin41\\StreamBundle\\Requester\\%sRequester', ucfirst(strtolower($provider)));
        if (!class_exists($classname)) {
            throw new Exception('unhandled_provider');
        }

        return $this->container->get($classname);
    }
}
