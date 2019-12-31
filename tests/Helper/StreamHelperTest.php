<?php


namespace Darkanakin41\StreamBundle\Tests\Helper;


use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\StreamBundle\Helper\StreamHelper;
use PHPUnit\Framework\TestCase;

class StreamHelperTest extends TestCase
{
    public function testGetProvider()
    {
        $twitch = 'https://www.twitch.tv/zerator/videos';
        $this->assertEquals(PlatformNomenclature::TWITCH, StreamHelper::getProvider($twitch));

        $youtube = 'https://www.youtube.com/watch?v=-L2JlFGkFXw';
        $this->assertEquals(PlatformNomenclature::YOUTUBE, StreamHelper::getProvider($youtube));

        $other = 'https://www.scoopturn.com/';
        $this->assertEquals(PlatformNomenclature::OTHER, StreamHelper::getProvider($other));
    }

    public function testGetIdentifiant()
    {
        $twitch = 'https://www.twitch.tv/zerator/videos';
        $this->assertEquals('zerator', StreamHelper::getIdentifiant($twitch));

        $youtube = 'https://www.youtube.com/watch?v=-L2JlFGkFXw';
        $this->assertEquals('-L2JlFGkFXw', StreamHelper::getIdentifiant($youtube));

        $other = 'https://www.scoopturn.com/';
        $this->assertEquals(null, StreamHelper::getIdentifiant($other));
    }
}
