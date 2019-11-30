<?php


namespace Darkanakin41\StreamBundle\Tests\Tool;


use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\StreamBundle\Tool\StreamTool;
use PHPUnit\Framework\TestCase;

class StreamToolTest extends TestCase
{
    public function testGetProvider()
    {
        $twitch = 'https://www.twitch.tv/zerator/videos';
        $this->assertEquals(PlatformNomenclature::TWITCH, StreamTool::getProvider($twitch));

        $youtube = 'https://www.youtube.com/watch?v=-L2JlFGkFXw';
        $this->assertEquals(PlatformNomenclature::YOUTUBE, StreamTool::getProvider($youtube));

        $other = 'https://www.scoopturn.com/';
        $this->assertEquals('OTHER', StreamTool::getProvider($other));
    }

    public function testGetIdentifiant()
    {
        $twitch = 'https://www.twitch.tv/zerator/videos';
        $this->assertEquals('zerator', StreamTool::getIdentifiant($twitch));

        $youtube = 'https://www.youtube.com/watch?v=-L2JlFGkFXw';
        $this->assertEquals('-L2JlFGkFXw', StreamTool::getIdentifiant($youtube));

        $other = 'https://www.scoopturn.com/';
        $this->assertEquals($other, StreamTool::getIdentifiant($other));
    }
}
