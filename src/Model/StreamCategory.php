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
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var array
     * @ORM\Column(name="tags", type="array")
     */
    private $platformKeys;

    /**
     * @var bool
     * @ORM\Column(name="highlighted", type="boolean")
     */
    private $refresh;

    /**
     * @var bool
     * @ORM\Column(name="highlighted", type="boolean")
     */
    private $displayed;

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
    public function getRefresh()
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
    public function getDisplayed()
    {
        return $this->displayed;
    }
}
