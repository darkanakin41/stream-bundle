<?php


namespace Darkanakin41\StreamBundle\Tests\Model;


use Darkanakin41\CoreBundle\Tests\Model\AbstractEntityTestCase;
use Darkanakin41\StreamBundle\Model\StreamCategory;

class StreamCategoryTest extends AbstractEntityTestCase
{

    public function nullableFieldProvider()
    {
        return [
            ['title', 'toto'],
        ];
    }

    public function defaultValueProvider()
    {
        return [
            ['refresh', false, true],
            ['displayed', false, true],
            ['platformKeys', [], ['toto' => 'titi']],
        ];
    }

    /**
     * @return StreamCategory
     */
    protected function getEntity()
    {
        return $this->getMockForAbstractClass(StreamCategory::class);
    }
}
