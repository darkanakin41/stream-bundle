<?php


namespace Darkanakin41\StreamBundle\Tests\Command;

use AppTestBundle\Entity\Stream;
use AppTestBundle\Entity\StreamCategory;
use Darkanakin41\StreamBundle\Command\RetrieveCommand;
use Darkanakin41\StreamBundle\Tests\AbstractTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class RetrieveCommandTest
 * @package Darkanakin41\StreamBundle\Tests\Command
 */
class RetrieveCommandTest extends AbstractTestCase
{

    public function testExecute()
    {
        $categoryRefreshed = $this->createCategory(true);
        $categoryNotRefreshed = $this->createCategory(false);

        $application = new Application(static::$kernel);
        $application->setAutoExit(false);

        $command = $application->find(RetrieveCommand::$defaultName);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $videos = $this->getDoctrine()->getRepository(Stream::class)->findBy(['category' => $categoryRefreshed]);
        $this->assertNotEmpty($videos);

        $videos = $this->getDoctrine()->getRepository(Stream::class)->findBy(['category' => $categoryNotRefreshed]);
        $this->assertEmpty($videos);
    }

    /**
     * @param $refresh
     *
     * @return StreamCategory
     */
    private function createCategory($refresh)
    {
        $category = new StreamCategory();
        $category->setTitle("FIFA 20");
        $category->setRefresh($refresh);
        $category->setPlatformKeys(['twitch_0' => 512804]);

        $this->getDoctrine()->getManager()->persist($category);
        $this->getDoctrine()->getManager()->flush();

        return $category;
    }
}
