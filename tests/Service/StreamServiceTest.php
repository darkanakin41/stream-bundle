<?php


namespace Darkanakin41\StreamBundle\Tests\Service;


use AppTestBundle\Entity\StreamCategory;
use Darkanakin41\StreamBundle\Exception\UnknownPlatformException;
use Darkanakin41\StreamBundle\Model\Stream;
use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\StreamBundle\Service\StreamService;
use Darkanakin41\StreamBundle\Tests\AbstractTestCase;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class StreamServiceTest
 * @package Darkanakin41\StreamBundle\Tests\Service
 */
class StreamServiceTest extends AbstractTestCase
{
    public function testGestFromGame()
    {

        $category = new StreamCategory();
        $category->setPlatformKeys(['twitch_0' => 512804, 'youtube_0' => 'AH QUE COUCOU']);

        $result = $this->getService()->getFromGame($category, PlatformNomenclature::TWITCH);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * @return StreamService
     */
    private function getService()
    {
        /** @var StreamService $service */
        $service = self::$container->get(StreamService::class);
        return $service;
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function testCreate()
    {
        $url = "https://www.twitch.tv/darkanakin41";
        $name = "Darkanakin41";
        $result = $this->getService()->create($url, $name);

        $this->assertInstanceOf(Stream::class, $result);

        $this->getDoctrine()->getManager()->persist($result);
        $this->getDoctrine()->getManager()->flush();

        $result = $this->getService()->create($url, $name);

        $this->assertNotNull($result->getId());

        $result = $this->getService()->create(null, $name);
        $this->assertNull($result);

        $result = $this->getService()->create('https://www.google.com/', $name);
        $this->assertNull($result);

        $result = $this->getService()->create('https://www.twitch.tv/fdgfsqgnqjiogfsnogfsqfgsq', $name);
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function testGetRequesterException()
    {
        $this->expectException(UnknownPlatformException::class);
        $result = $this->getService()->getRequester(PlatformNomenclature::OTHER);
        $this->assertNull($result);
    }
}
