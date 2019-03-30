<?php

namespace PLejeune\StreamBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class IsLiveEvent extends Event
{

    public const NAME = 'plejeune.stream.islive';

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $platform;
    /**
     * @var string
     */
    private $identifier;
    /**
     * @var string
     */
    private $logo;

    /**
     * IsLiveEvent constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPlatform(): string
    {
        return $this->platform;
    }

    /**
     * @param string $platform
     */
    public function setPlatform(string $platform): void
    {
        $this->platform = $platform;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getLogo(): string
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     */
    public function setLogo(string $logo): void
    {
        $this->logo = $logo;
    }
    
}
