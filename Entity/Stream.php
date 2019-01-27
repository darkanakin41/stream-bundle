<?php

namespace PLejeune\StreamBundle\Entity;

/**
 * Stream
 */
class Stream
{
    /**
     * @var int
     */
    private $id;

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
     * @var string|null
     */
    private $title;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string|null
     */
    private $language;

    /**
     * @var int|null
     */
    private $viewers;

    /**
     * @var string|null
     */
    private $logo;

    /**
     * @var string|null
     */
    private $preview;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var boolean
     */
    private $highlighted;

    /**
     * @var \PLejeune\StreamBundle\Entity\StreamCategory
     */
    private $category;


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
     * Set name.
     *
     * @param string $name
     *
     * @return Stream
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set platform.
     *
     * @param string $platform
     *
     * @return Stream
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform.
     *
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Set identifier.
     *
     * @param string $identifier
     *
     * @return Stream
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return Stream
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return Stream
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set language.
     *
     * @param string|null $language
     *
     * @return Stream
     */
    public function setLanguage($language = null)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language.
     *
     * @return string|null
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set viewers.
     *
     * @param int|null $viewers
     *
     * @return Stream
     */
    public function setViewers($viewers = null)
    {
        $this->viewers = $viewers;

        return $this;
    }

    /**
     * Get viewers.
     *
     * @return int|null
     */
    public function getViewers()
    {
        return $this->viewers;
    }

    /**
     * Set preview.
     *
     * @param string|null $preview
     *
     * @return Stream
     */
    public function setPreview($preview = null)
    {
        $this->preview = $preview;

        return $this;
    }

    /**
     * Get preview.
     *
     * @return string|null
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * Set tags.
     *
     * @param array $tags
     *
     * @return Stream
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags.
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set category.
     *
     * @param \PLejeune\StreamBundle\Entity\StreamCategory|null $category
     *
     * @return Stream
     */
    public function setCategory(\PLejeune\StreamBundle\Entity\StreamCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return \PLejeune\StreamBundle\Entity\StreamCategory|null
     */
    public function getCategory()
    {
        return $this->category;
    }
    /**
     * @var \DateTime
     */
    private $updated;


    /**
     * Set updated.
     *
     * @param \DateTime $updated
     *
     * @return Stream
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @return string|null
     */
    public function getLogo(): ?string
    {
        return $this->logo;
    }

    /**
     * @param string|null $logo
     * @return Stream
     */
    public function setLogo(?string $logo): Stream
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHighlighted(): bool
    {
        return $this->highlighted;
    }

    /**
     * @param bool $highlighted
     * @return Stream
     */
    public function setHighlighted(bool $highlighted): Stream
    {
        $this->highlighted = $highlighted;
        return $this;
    }
}
