<?php

namespace Darkanakin41\StreamBundle\Entity;

/**
 * StreamCategory
 */
class StreamCategory
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var array
     */
    private $platformKeys;

    /**
     * @var bool
     */
    private $refresh;

    /**
     * @var bool
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