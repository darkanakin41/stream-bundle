<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * StreamCategory.
 *
 * @ORM\MappedSuperclass()
 */
abstract class StreamCategory
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var array
     * @ORM\Column(name="platform_keys", type="array")
     */
    private $platformKeys;

    /**
     * @var bool
     * @ORM\Column(name="refresh", type="boolean")
     */
    private $refresh;

    /**
     * @var bool
     * @ORM\Column(name="displayed", type="boolean")
     */
    private $displayed;

    public function __construct()
    {
        $this->refresh = false;
        $this->displayed = false;
        $this->platformKeys = array();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return StreamCategory
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set platformKeys.
     *
     * @param array $platformKeys
     *
     * @return StreamCategory
     */
    public function setPlatformKeys($platformKeys)
    {
        $this->platformKeys = $platformKeys;

        return $this;
    }

    /**
     * Get platformKeys.
     *
     * @return array
     */
    public function getPlatformKeys()
    {
        return $this->platformKeys;
    }

    /**
     * Set refresh.
     *
     * @param bool $refresh
     *
     * @return StreamCategory
     */
    public function setRefresh($refresh)
    {
        $this->refresh = $refresh;

        return $this;
    }

    /**
     * Get refresh.
     *
     * @return bool
     */
    public function isRefresh()
    {
        return $this->refresh;
    }

    /**
     * Set displayed.
     *
     * @param bool $displayed
     *
     * @return StreamCategory
     */
    public function setDisplayed($displayed)
    {
        $this->displayed = $displayed;

        return $this;
    }

    /**
     * Get displayed.
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->displayed;
    }
}
