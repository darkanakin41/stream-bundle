<?php


namespace Darkanakin41\StreamBundle\Tests\Repository;


use AppTestBundle\Entity\Stream;
use AppTestBundle\Entity\StreamCategory;
use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\StreamBundle\Nomenclature\StatusNomenclature;
use Darkanakin41\StreamBundle\Tests\AbstractTestCase;

/**
 * Class StreamRepositoryTest
 * @package Darkanakin41\StreamBundle\Tests\Repository
 */
class StreamRepositoryTest extends AbstractTestCase
{

    public function testFindRandom()
    {
        $stream = new Stream();
        $stream->setTitle("toto");
        $stream->setName("coucou");
        $stream->setIdentifier("coucou");
        $stream->setPlatform(PlatformNomenclature::OTHER);
        $stream->setStatus(StatusNomenclature::ONLINE);

        $category = new StreamCategory();
        $category->setTitle("coucou");
        $category->setDisplayed(true);
        $category->setRefresh(false);
        $category->setPlatformKeys([]);

        $stream->setCategory($category);

        $this->getDoctrine()->getManager()->persist($stream->getCategory());
        $this->getDoctrine()->getManager()->persist($stream);
        $this->getDoctrine()->getManager()->flush();

        $stream = $this->getDoctrine()->getRepository(Stream::class)->findRandom();
        $this->assertNotNull($stream);
    }

}
