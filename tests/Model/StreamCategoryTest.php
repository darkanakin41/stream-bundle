<?php


namespace Darkanakin41\StreamBundle\Tests\Model;


use Darkanakin41\CoreBundle\Tests\Model\AbstractEntityTestCase;
use Darkanakin41\StreamBundle\Model\StreamCategory;

class StreamCategoryTest extends AbstractEntityTestCase
{
    /**
     * @inheritDoc
     */
    public function nullableFieldProvider()
    {
        return [
            ['title', 'toto'],
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function defaultValueProvider()
    {
        return [
            ['refresh', false, true],
            ['displayed', false, true],
            ['platformKeys', [], ['toto' => 'titi']],
        ];
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
    protected function getEntity()
    {
        return $this->getMockForAbstractClass(StreamCategory::class);
    }
}
