<?php


namespace Darkanakin41\StreamBundle\Tests\Twig;


use AppTestBundle\Entity\Stream;
use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\StreamBundle\Tests\AbstractTestCase;
use Darkanakin41\StreamBundle\Twig\StreamExtension;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class StreamExtensionTest
 * @package Darkanakin41\StreamBundle\Tests\Twig
 */
class StreamExtensionTest extends AbstractTestCase
{

    const FILTERS = [];
    const FUNCTIONS = [
        'darkanakin41_stream_render_video',
        'darkanakin41_stream_render_chat',
        'darkanakin41_stream_language',
        'darkanakin41_stream_has_chat',
        'darkanakin41_stream_preview',
    ];

    public function testGetFilters()
    {
        $service = $this->getExtension();
        $this->assertCount(count(self::FILTERS), $service->getFilters());

        foreach ($service->getFilters() as $f) {
            $this->assertTrue(in_array($f->getName(), self::FILTERS), $f->getName().' should not exist');
        }
    }

    /**
     * @return StreamExtension
     */
    private function getExtension()
    {
        /** @var StreamExtension $service */
        $service = self::$container->get(StreamExtension::class);
        return $service;
    }

    public function testGetFunctions()
    {
        $service = $this->getExtension();
        $this->assertCount(count(self::FUNCTIONS), $service->getFunctions());

        foreach ($service->getFunctions() as $f) {
            $this->assertTrue(in_array($f->getName(), self::FUNCTIONS), $f->getName().' should not exist');
        }
    }

    public function testRenderVideo()
    {
        $stream = new Stream();
        $stream->setIdentifier("darkanakin41");
        $stream->setPlatform(PlatformNomenclature::TWITCH);
        $stream->setUserId(123456);

        $html = $this->getExtension()->renderVideo($stream);

        $this->assertNotEmpty($html);

        $crawler = new Crawler();
        $crawler->addHtmlContent($html);
        $this->assertEquals(1, $crawler->filter("div#stream-video-".$stream->getUserId())->count());


        $stream->setPlatform(PlatformNomenclature::YOUTUBE);

        $html = $this->getExtension()->renderVideo($stream);

        $this->assertNotEmpty($html);

        $crawler = new Crawler();
        $crawler->addHtmlContent($html);
        $this->assertEquals(1, $crawler->filter("iframe#live_embed_player_flash")->count());

        $stream->setPlatform(PlatformNomenclature::OTHER);

        $html = $this->getExtension()->renderVideo($stream);

        $this->assertEmpty($html);
    }

    public function testRenderChat()
    {
        $stream = new Stream();
        $stream->setIdentifier("darkanakin41");
        $stream->setPlatform(PlatformNomenclature::TWITCH);
        $stream->setUserId(123456);

        $html = $this->getExtension()->renderChat($stream);
        $this->assertNotEmpty($html);

        $crawler = new Crawler();
        $crawler->addHtmlContent($html);
        $this->assertEquals(1, $crawler->filter("iframe#stream-chat-".$stream->getUserId())->count());

        $stream->setPlatform(PlatformNomenclature::YOUTUBE);
        $html = $this->getExtension()->renderChat($stream);
        $this->assertEmpty($html);

        $stream->setPlatform(PlatformNomenclature::OTHER);
        $html = $this->getExtension()->renderChat($stream);
        $this->assertEmpty($html);
    }

    public function testHasChat()
    {
        $stream = new Stream();
        $stream->setIdentifier("darkanakin41");
        $stream->setPlatform(PlatformNomenclature::TWITCH);
        $stream->setUserId(123456);

        $this->assertTrue($this->getExtension()->hasChat($stream));

        $stream->setPlatform(PlatformNomenclature::YOUTUBE);

        $this->assertFalse($this->getExtension()->hasChat($stream));

        $stream->setPlatform(PlatformNomenclature::OTHER);

        $this->assertFalse($this->getExtension()->hasChat($stream));
    }

    public function testLanguage()
    {
        $stream = new Stream();
        foreach (StreamExtension::LANGUAGE_MAPPING as $key => $value) {
            $stream->setLanguage($key);
            $this->assertSame($value, $this->getExtension()->language($stream));
        }
        $stream->setLanguage("FR");
        $this->assertSame("fr", $this->getExtension()->language($stream));
    }

    public function testPreview()
    {
        $stream = new Stream();
        $stream->setPreview("https://static-cdn.jtvnw.net/previews-ttv/live_user_darkanakin41-{width}x{height}.jpg");

        $url = $this->getExtension()->preview($stream);

        $expected = str_ireplace(['{width}', '{height}'], [620, 380], $stream->getPreview());
        $this->assertEquals($expected, $url);
    }

}
