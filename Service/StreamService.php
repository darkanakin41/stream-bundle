<?php

namespace PLejeune\StreamBundle\Service;


use PLejeune\StreamBundle\Entity\Stream;
use PLejeune\StreamBundle\Entity\StreamCategory;
use PLejeune\StreamBundle\Nomenclature\StatusNomenclature;
use PLejeune\StreamBundle\Requester\AbstractRequester;
use PLejeune\StreamBundle\Tool\StreamTool;
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
     * @param                $provider
     *
     * @return integer
     * @throws \Exception
     */
    public function getFromGame(StreamCategory $streamCategory, $provider)
    {
        $requester = $this->getRequester($provider);

        $streams = $requester->updateFromCategory($streamCategory);

        return $streams;
    }

    /**
     * Retrieve the requester from the providers
     *
     * @param string $provider
     *
     * @return AbstractRequester
     * @throws \Exception
     */
    private function getRequester($provider)
    {
        $classname = sprintf('PLejeune\\StreamBundle\\Requester\\%sRequester', ucfirst(strtolower($provider)));
        var_dump($classname);
        if (!class_exists($classname)) throw new \Exception('unhandled_provider');
        $object = new $classname($this->container->get('doctrine'), $this->container->get('plejeune.api'), $this->container->get('plejeune.stream.twig'));
        return $object;
    }

    /**
     * Create a stream based on his name and URL
     *
     * @param string $url
     * @param string $name
     * @param bool   $highlighted
     *
     * @return bool true if created, false if not
     * @throws \Exception
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
        $stream->setUpdated(new \DateTime());
        $stream->setTags([]);

        $exist = $this->container->get("doctrine")->getRepository(Stream::class)->findOneBy(array(
            'identifier' => $stream->getIdentifier(),
            'platform' => $stream->getPlatform(),
        ));

        if ($exist !== null) {
            return false;
        }

        $this->container->get("doctrine")->getManager()->persist($stream);
        $this->container->get("doctrine")->getManager()->flush();

        $this->refresh([$stream], $stream->getPlatform());

        return true;
    }

    /**
     * Update streams
     *
     * @param Stream[] $streams
     * @param string   $platform
     *
     * @throws \Exception
     */
    public function refresh(array $streams, $platform)
    {
        $requester = $this->getRequester($platform);

        $requester->refresh($streams);
    }
}
