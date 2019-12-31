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
class StreamCategoryRepositoryTest extends AbstractTestCase
{

    public function testFindRandom()
    {
        $category1 = new StreamCategory();
        $category1->setTitle("coucou");
        $category1->setDisplayed(true);
        $category1->setRefresh(false);
        $category1->setPlatformKeys(['youtube_0' => 'toto']);
        $category2 = new StreamCategory();
        $category2->setTitle("coucou");
        $category2->setDisplayed(true);
        $category2->setRefresh(false);
        $category2->setPlatformKeys(['youtube_1' => 123456]);

        $this->getDoctrine()->getManager()->persist($category1);
        $this->getDoctrine()->getManager()->persist($category2);
        $this->getDoctrine()->getManager()->flush();

        $stream = $this->getDoctrine()->getRepository(StreamCategory::class)->findByKey(PlatformNomenclature::TWITCH, 123456);
        $this->assertNull($stream);

        $category3 = new StreamCategory();
        $category3->setTitle("coucou");
        $category3->setDisplayed(true);
        $category3->setRefresh(false);
        $category3->setPlatformKeys(['twitch_1' => 123456]);

        $this->getDoctrine()->getManager()->persist($category3);
        $this->getDoctrine()->getManager()->flush();

        $stream = $this->getDoctrine()->getRepository(StreamCategory::class)->findByKey(PlatformNomenclature::TWITCH, 123456);
        $this->assertNotNull($stream);
    }

}
