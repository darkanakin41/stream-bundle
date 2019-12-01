<?php


namespace Darkanakin41\StreamBundle\Tests;

use Darkanakin41\StreamBundle\Model\StreamCategory;

class TestStreamCategory extends StreamCategory
{
    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
