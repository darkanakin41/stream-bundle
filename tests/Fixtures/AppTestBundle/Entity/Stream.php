<?php

namespace AppTestBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * AppTestBundle\Entity\Stream
 *
 * @ORM\Table(name="stream")
 * @ORM\Entity(repositoryClass="AppTestBundle\Repository\StreamRepository")
 */
class Stream extends \Darkanakin41\StreamBundle\Model\Stream
{
    /**
     * @var StreamCategory
     * @ORM\ManyToOne(targetEntity="AppTestBundle\Entity\StreamCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     */
    private $category;

    /**
     * @return StreamCategory
     */
    public function getCategory(): ?StreamCategory
    {
        return $this->category;
    }

    /**
     * @param StreamCategory $category
     *
     * @return Stream
     */
    public function setCategory($category = null): Stream
    {
        $this->category = $category;
        return $this;
    }

}
