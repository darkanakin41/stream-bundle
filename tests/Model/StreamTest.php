<?php


namespace Darkanakin41\StreamBundle\Tests\Model;


use Darkanakin41\CoreBundle\Tests\Model\AbstractEntityTestCase;
use Darkanakin41\StreamBundle\Model\Stream;
use Darkanakin41\StreamBundle\Model\StreamCategory;
use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\StreamBundle\Nomenclature\StatusNomenclature;

class StreamTest extends AbstractEntityTestCase
{
    /**
     * @return Stream
     */
    protected function getEntity()
    {
        return $this->getMockForAbstractClass(Stream::class);
    }

    /**
     * @inheritDoc
     */
    public function nullableFieldProvider()
    {
        return [
            ['name', 'toto'],
            ['identifier', 'toto'],
            ['title', 'toto'],
            ['language', 'toto'],
            ['logo', 'toto'],
            ['viewers', 123],
            ['updated', new \DateTime()],
            ['platform', PlatformNomenclature::TWITCH],
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultValueProvider()
    {
        return [
            ['tags', [], ["TOTO"]],
            ['highlighted', false, true],
            ['status', StatusNomenclature::OFFLINE, StatusNomenclature::ONLINE],
        ];
    }

    public function testChannel()
    {
        $entity = $this->getEntity();
        $this->assertNull($entity->getCategory());

        $category = $this->getCategory();
        $category->setTitle("toto");
        $entity->setCategory($category);

        $this->assertNotNull($entity->getCategory());
        $this->assertEquals($category->getTitle(), $entity->getCategory()->getTitle());
    }

    /**
     * @inheritDoc
     */
    public function notNullableFieldProvider()
    {
        return [];
    }

    /**
     * @return StreamCategory
     */
    protected function getCategory()
    {
        return $this->getMockForAbstractClass(StreamCategory::class);
    }
}
