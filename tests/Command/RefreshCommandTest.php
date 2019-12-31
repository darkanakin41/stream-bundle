<?php


namespace Darkanakin41\StreamBundle\Tests\Command;

use AppTestBundle\Entity\Stream;
use AppTestBundle\Entity\StreamCategory;
use Darkanakin41\StreamBundle\Command\RefreshCommand;
use Darkanakin41\StreamBundle\Command\RetrieveCommand;
use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\StreamBundle\Nomenclature\StatusNomenclature;
use Darkanakin41\StreamBundle\Requester\TwitchRequester;
use Darkanakin41\StreamBundle\Tests\AbstractTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class RefreshCommandTest
 * @package Darkanakin41\StreamBundle\Tests\Command
 * @group debug
 */
class RefreshCommandTest extends AbstractTestCase
{

    public function testExecute()
    {
        $stream1 = new Stream();
        $stream1->setTitle('Coucou');
        $stream1->setStatus(StatusNomenclature::ONLINE);
        $stream1->setViewers(123456);
        $stream1->setIdentifier('darkanakin41');
        $stream1->setName('Darkanakin41');
        $stream1->setPlatform(PlatformNomenclature::TWITCH);
        $stream1->setUserId("38721257");

        // Minecraft
        $stream2 = $this->getOnlineStream(27471);
        $stream2->setCategory(null);

        // Fortnite
        $stream3 = $this->getOnlineStream(33214);
        $stream3->setCategory(null);

        // Factorio
        $stream4 = $this->getOnlineStream(130942);
        $stream4->setCategory(null);

        $application = new Application(static::$kernel);
        $application->setAutoExit(false);

        $this->getDoctrine()->getManager()->persist($stream1);
        $this->getDoctrine()->getManager()->persist($stream2);
        $this->getDoctrine()->getManager()->persist($stream3);
        $this->getDoctrine()->getManager()->persist($stream4);
        $this->getDoctrine()->getManager()->flush();

        $command = $application->find(RefreshCommand::$defaultName);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        /** @var Stream[] $streams */
        $streams = $this->getDoctrine()->getRepository(Stream::class)->findAll();
        foreach ($streams as $stream) {
            $this->assertNotNull($stream->getUpdated());
        }
    }

    private function getOnlineStream($categoryId){
        /** @var TwitchRequester $requester */
        $requester = self::$container->get(TwitchRequester::class);

        $category = new StreamCategory();
        $category->setTitle("Minecraft");
        $category->setPlatformKeys(['twitch_0' => $categoryId]);

        $result = $requester->updateFromCategory($category);
        return reset($result);
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
