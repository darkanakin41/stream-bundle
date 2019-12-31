<?php

namespace Darkanakin41\StreamBundle\Tests\Requester;

use AppTestBundle\Entity\StreamCategory;
use Darkanakin41\StreamBundle\Requester\YoutubeRequester;

/**
 * Class YoutubeRequesterTest
 * @package Darkanakin41\StreamBundle\Tests\Requester
 */
class YoutubeRequesterTest extends AbstractRequesterTest
{

    /**
     * @return YoutubeRequester
     */
    protected function getRequester()
    {
        /** @var YoutubeRequester $service */
        $service = self::$container->get(YoutubeRequester::class);
        return $service;
    }

    public function testUpdateFromCategory(){
        $category = new StreamCategory();
        $result = $this->getRequester()->updateFromCategory($category);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
