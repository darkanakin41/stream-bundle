<?php

namespace Darkanakin41\StreamBundle\Tests\Requester;

use AppTestBundle\Entity\Stream;
use AppTestBundle\Entity\StreamCategory;
use Darkanakin41\StreamBundle\Requester\TwitchRequester;

/**
 * Class YoutubeRequesterTest
 * @package Darkanakin41\StreamBundle\Tests\Requester
 */
class TwitchRequesterTest extends AbstractRequesterTest
{

    public function testUpdateFromCategory()
    {
        $category = new StreamCategory();
        $category->setPlatformKeys(['twitch_0' => 512804, 'youtube_0' => 'AH QUE COUCOU']);

        $result = $this->getRequester()->updateFromCategory($category);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * @depends testUpdateFromCategory
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testRefresh()
    {
        $category = new StreamCategory();
        $category->setPlatformKeys(['twitch_0' => 512804, 'youtube_0' => 'AH QUE COUCOU']);

        $result = $this->getRequester()->updateFromCategory($category);
        $streams = array_slice($result, 20);

        foreach($streams as $stream){
            $stream->setUpdated(null);
        }

        $stream = new Stream();
        $streams[] = $stream;

        $this->getRequester()->refresh($streams);

        foreach($streams as $stream){
            if($stream->getUserId() === null){
                $this->assertNull($stream->getIdentifier());
            }
            $this->assertNotNull($stream->getUpdated());
        }
    }

    /**
     * @return TwitchRequester
     */
    protected function getRequester()
    {
        /** @var TwitchRequester $service */
        $service = self::$container->get(TwitchRequester::class);
        return $service;
    }

    public function testGetUserDataFromUsername()
    {
        $resultats = $this->getRequester()->getUserData('darkanakin41');

        $this->assertEquals(38721257, $resultats['id']);
        $this->assertEquals('darkanakin41', $resultats['login']);
    }
}
