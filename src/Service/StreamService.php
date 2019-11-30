<?php

namespace Darkanakin41\StreamBundle\Service;


use Darkanakin41\StreamBundle\Entity\Stream;
use Darkanakin41\StreamBundle\Entity\StreamCategory;
use Darkanakin41\StreamBundle\Extension\StreamExtension;
use Darkanakin41\StreamBundle\Nomenclature\StatusNomenclature;
use Darkanakin41\StreamBundle\Requester\AbstractRequester;
use Darkanakin41\StreamBundle\Tool\StreamTool;
use DateTime;
use Exception;
use Symfony\Bridge\Doctrine\ManagerRegistry;

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

    public function __construct(ManagerRegistry $registryManager, StreamExtension $streamExtension)
    {
        $this->registry = $registryManager;
        $this->streamExtension = $streamExtension;
    }

    /**
     * Retrieve streams from the category
     *
     * @param StreamCategory $streamCategory
     * @param string         $provider
     *
     * @return integer
     * @throws Exception
     */
    public function getFromGame(StreamCategory $streamCategory, $provider)
    {
        $requester = $this->getRequester($provider);

        return $requester->updateFromCategory($streamCategory);
    }

    /**
     * Retrieve the requester from the providers
     *
     * @param string $provider
     *
     * @return AbstractRequester
     * @throws Exception
     */
    private function getRequester($provider)
    {
        $classname = sprintf('Darkanakin41\\StreamBundle\\Requester\\%sRequester', ucfirst(strtolower($provider)));
        if (!class_exists($classname)) throw new Exception('unhandled_provider');
        return new $classname($this->registry, $this->streamExtension);
    }

    /**
     * Create a stream based on his name and URL
     *
     * @param string $url
     * @param string $name
     * @param bool   $highlighted
     *
     * @return bool true if created, false if not
     * @throws Exception
     */
    public function create($url, $name, $highlighted = false)
    {
        if ($url === null) {
            return false;
        }
        $stream = new Stream();
        $stream->setIdentifier(StreamTool::getIdentifiant($url));
        $stream->setName($name);
        $stream->setHighlighted($highlighted);
        $stream->setPlatform(StreamTool::getProvider($url));
        $stream->setStatus(StatusNomenclature::OFFLINE);
        $stream->setUpdated(new DateTime());
        $stream->setTags([]);

        $exist = $this->registry->getRepository(Stream::class)->findOneBy(array(
            'identifier' => $stream->getIdentifier(),
            'platform' => $stream->getPlatform(),
        ));

        if ($exist !== null) {
            return false;
        }

        $this->registry->getManager()->persist($stream);
        $this->registry->getManager()->flush();

        $this->refresh([$stream], $stream->getPlatform());

        return true;
    }

    /**
     * Update streams
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
