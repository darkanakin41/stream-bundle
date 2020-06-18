<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Service;

use Darkanakin41\StreamBundle\Exception\UnknownPlatformException;
use Darkanakin41\StreamBundle\Helper\StreamHelper;
use Darkanakin41\StreamBundle\Model\Stream;
use Darkanakin41\StreamBundle\Model\StreamCategory;
use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\StreamBundle\Nomenclature\StatusNomenclature;
use Darkanakin41\StreamBundle\Requester\AbstractRequester;
use Darkanakin41\StreamBundle\Requester\TwitchRequester;
use Darkanakin41\StreamBundle\Twig\StreamExtension;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
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
     * @return Stream[]
     *
     * @throws Exception
     */
    public function getFromGame(StreamCategory $streamCategory, $provider)
    {
        $requester = $this->getRequester($provider);

        return $requester->updateFromCategory($streamCategory);
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
            throw new UnknownPlatformException();
        }

        /** @var AbstractRequester $requester */
        $requester = $this->container->get($classname);

        return $requester;
    }

    /**
     * Create a stream based on his name and URL.
     *
     * @param string $url
     * @param string $name
     * @param bool   $highlighted
     *
     * @return Stream|null
     *
     * @throws Exception
     * @throws GuzzleException
     */
    public function create($url, $name, $highlighted = false)
    {
        if (null === $url) {
            return null;
        }

        $identifier = StreamHelper::getIdentifiant($url);
        $platform = StreamHelper::getProvider($url);

        if (null == $identifier || PlatformNomenclature::OTHER === $platform) {
            return null;
        }

        $requester = $this->getRequester($platform);

        $userId = null;

        if (PlatformNomenclature::TWITCH === $platform) {
            /** @var TwitchRequester $requester */
            $data = $requester->getUserData($identifier);
            $userId = $data['id'];
            $identifier = $data['login'];
        }

        if (null == $userId) {
            return null;
        }

        /** @var Stream $exist */
        $exist = $this->registry->getRepository($requester->getStreamClass())->findOneBy(array(
            'identifier' => $identifier,
            'platform' => $platform,
        ));

        if (null !== $exist) {
            return $exist;
        }

        $stream = $requester->createStreamObject();
        $stream->setIdentifier($identifier);
        $stream->setUserId($userId);
        $stream->setName($name);
        $stream->setHighlighted($highlighted);
        $stream->setPlatform($platform);
        $stream->setStatus(StatusNomenclature::OFFLINE);
        $stream->setUpdated(new DateTime());
        $stream->setTags(array());

        $this->registry->getManager()->persist($stream);

        $this->refresh(array($stream), $stream->getPlatform());

        $this->registry->getManager()->flush();

        return $stream;
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
}
