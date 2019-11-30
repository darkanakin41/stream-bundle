<?php

namespace Darkanakin41\StreamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stream
 * @ORM\MappedSuperclass()
 */
class Stream
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="integer")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="platform", type="string")
     */
    private $platform;

    /**
     * @var string
     * @ORM\Column(name="identifier", type="string")
     */
    private $identifier;

    /**
     * @var string|null
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="status", type="string")
     */
    private $status;

    /**
     * @var string|null
     * @ORM\Column(name="language", type="string", nullable=true)
     */
    private $language;

    /**
     * @var int|null
     * @ORM\Column(name="viewers", type="integer", nullable=true)
     */
    private $viewers;

    /**
     * @var string|null
     * @ORM\Column(name="logo", type="string", nullable=true)
     */
    private $logo;

    /**
     * @var string|null
     * @ORM\Column(name="preview", type="string", nullable=true)
     */
    private $preview;

    /**
     * @var array
     * @ORM\Column(name="tags", type="array")
     */
    private $tags;

    /**
     * @var boolean
     * @ORM\Column(name="highlighted", type="boolean")
     */
    private $highlighted;

    /**
     * @var StreamCategory*
     * @ORM\ManyToOne(targetEntity=Darkanakin41\StreamBundle\Entity\StreamCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
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
     * @param StreamCategory|null $category
     *
     * @return Stream
     */
    public function setCategory(StreamCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return StreamCategory|null
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
