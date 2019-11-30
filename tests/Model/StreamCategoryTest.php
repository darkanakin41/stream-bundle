<?php


namespace Darkanakin41\StreamBundle\Tests\Model;


use Darkanakin41\StreamBundle\Model\StreamCategory;
use PHPUnit\Framework\TestCase;

class StreamCategoryTest extends TestCase
{

    public function testId(){
        $category = $this->getStreamCategory();
        $this->assertNull($category->getId());
    }

    public function testTitle(){
        $category = $this->getStreamCategory();
        $this->assertNull($category->getTitle());

        $category->setTitle("TOTO");
        $this->assertSame("TOTO", $category->getTitle());
    }


    public function testPlatformKeys(){
        $category = $this->getStreamCategory();
        $this->assertNull($category->getPlatformKeys());

        $category->setPlatformKeys(['toto' => 'titi']);
        $this->assertSame(['toto' => 'titi'], $category->getPlatformKeys());
    }


    public function testRefresh(){
        $category = $this->getStreamCategory();
        $category->setRefresh(true);
        $this->assertTrue($category->getRefresh());

        $category->setRefresh(false);
        $this->assertFalse($category->getRefresh());
    }


    public function testDisplayed(){
        $category = $this->getStreamCategory();
        $category->setDisplayed(true);
        $this->assertTrue($category->getDisplayed());

        $category->setDisplayed(false);
        $this->assertFalse($category->getDisplayed());
    }

    /**
     * @return StreamCategory
     */
    protected function getStreamCategory(){
        return $this->getMockForAbstractClass(StreamCategory::class);
    }
}
