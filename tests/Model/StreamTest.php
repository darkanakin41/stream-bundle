<?php


namespace Darkanakin41\StreamBundle\Tests\Model;


use Darkanakin41\StreamBundle\Model\Stream;
use Darkanakin41\StreamBundle\Model\StreamCategory;
use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\StreamBundle\Nomenclature\StatusNomenclature;
use DateTime;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{

    public function testId(){
        $stream = $this->getStream();
        $this->assertNull($stream->getId());
    }

    public function testName(){
        $stream = $this->getStream();
        $this->assertNull($stream->getName());

        $stream->setName("TOTO");
        $this->assertSame("TOTO", $stream->getName());
    }

    public function testPlatform(){
        $stream = $this->getStream();
        $this->assertNull($stream->getPlatform());

        $stream->setPlatform(PlatformNomenclature::TWITCH);
        $this->assertSame(PlatformNomenclature::TWITCH, $stream->getPlatform());
    }

    public function testIdentifier(){
        $stream = $this->getStream();
        $this->assertNull($stream->getIdentifier());

        $stream->setIdentifier("TOTO");
        $this->assertSame("TOTO", $stream->getIdentifier());
    }

    public function testTitle(){
        $stream = $this->getStream();
        $this->assertNull($stream->getTitle());

        $stream->setTitle("TOTO");
        $this->assertSame("TOTO", $stream->getTitle());
    }

    public function testStatus(){
        $stream = $this->getStream();
        $this->assertNull($stream->getStatus());

        $stream->setStatus(StatusNomenclature::OFFLINE);
        $this->assertSame(StatusNomenclature::OFFLINE, $stream->getStatus());
    }

    public function testLanguage(){
        $stream = $this->getStream();
        $this->assertNull($stream->getLanguage());

        $stream->setLanguage("TOTO");
        $this->assertSame("TOTO", $stream->getLanguage());
    }

    public function testViewers(){
        $stream = $this->getStream();
        $this->assertNull($stream->getViewers());

        $stream->setViewers(123);
        $this->assertSame(123, $stream->getViewers());
    }

    public function testLogo(){
        $stream = $this->getStream();
        $this->assertNull($stream->getLogo());

        $stream->setLogo("TOTO");
        $this->assertSame("TOTO", $stream->getLogo());
    }

    public function testPreview(){
        $stream = $this->getStream();
        $this->assertNull($stream->getPreview());

        $stream->setPreview("TOTO");
        $this->assertSame("TOTO", $stream->getPreview());
    }

    public function testTags(){
        $stream = $this->getStream();
        $this->assertNull($stream->getTags());

        $stream->setTags(["TOTO"]);
        $this->assertSame(["TOTO"], $stream->getTags());
    }

    public function testHighlighted(){
        $stream = $this->getStream();
        $stream->setHighlighted(true);
        $this->assertTrue($stream->isHighlighted());

        $stream->setHighlighted(false);
        $this->assertFalse($stream->isHighlighted());
    }

    public function testUpdated(){
        $stream = $this->getStream();
        $this->assertNull($stream->getUpdated());

        $now = new DateTime();
        $stream->setUpdated($now);
        $this->assertSame($now, $stream->getUpdated());
    }

    public function testCategory(){
        $stream = $this->getStream();
        $this->assertNull($stream->getCategory());

        $category = $this->getStreamCategory();
        $category->setTitle("TEST");

        $stream->setCategory($category);
        $this->assertSame("TEST", $stream->getCategory()->getTitle());
    }

    /**
     * @return Stream
     */
    protected function getStream(){
        return $this->getMockForAbstractClass(Stream::class);
    }

    /**
     * @return StreamCategory
     */
    protected function getStreamCategory(){
        return $this->getMockForAbstractClass(StreamCategory::class);
    }
}
