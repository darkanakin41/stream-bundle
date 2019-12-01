<?php


namespace Darkanakin41\StreamBundle\Tests;


use Darkanakin41\StreamBundle\Model\Stream;

class TestStream extends Stream
{
    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
