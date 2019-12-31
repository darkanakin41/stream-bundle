<?php


namespace Darkanakin41\StreamBundle\Tests\Endpoint;


use Darkanakin41\StreamBundle\Endpoint\YoutubeEndpoint;

class YoutubeEndpointTest extends AbstractEndpointTest
{

    /**
     * @return YoutubeEndpoint
     */
    protected function getEndpoint()
    {
        /** @var YoutubeEndpoint $service */
        $service = self::$container->get(YoutubeEndpoint::class);
        return $service;
    }

    public function testGetChannelId(){
        $resultats = $this->getEndpoint()->getChannelId('darkanakin41');

        $this->assertTrue(isset($resultats->getItems()[0]));
        $channelData = $resultats->getItems()[0];
        $this->assertEquals('UCHFq2w-LbRfiemewtlJkoIA', $channelData->getId());
    }

    public function testGetChannelData(){
        $resultats = $this->getEndpoint()->getChannelData('UCHFq2w-LbRfiemewtlJkoIA');

        $this->assertTrue(isset($resultats->getItems()[0]));
        $channelData = $resultats->getItems()[0];
        $this->assertEquals('Pierre Lejeune', $channelData->getSnippet()->getTitle());
    }

    public function testGetChannelVideos(){
        $resultats = $this->getEndpoint()->getChannelVideos('UCHFq2w-LbRfiemewtlJkoIA');

        $this->assertGreaterThan(0, $resultats->getItems());
    }

    public function testGetVideoData(){
        $resultats = $this->getEndpoint()->getVideosData(['UCHFq2w-LbRfiemewtlJkoIA']);

        $this->assertGreaterThan(0, $resultats->getItems());
    }
}
