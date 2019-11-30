<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class IsLiveEvent extends Event
{
    public const NAME = 'darkanakin41.stream.islive';

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPlatform(): string
    {
        return $this->platform;
    }

    public function setPlatform(string $platform): void
    {
        $this->platform = $platform;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): void
    {
        $this->logo = $logo;
    }
}
