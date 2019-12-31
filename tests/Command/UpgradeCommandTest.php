<?php


namespace Darkanakin41\StreamBundle\Tests\Command;

use AppTestBundle\Entity\Stream;
use AppTestBundle\Entity\StreamCategory;
use Darkanakin41\StreamBundle\Command\UpgradeCommand;
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
class UpgradeCommandTest extends AbstractTestCase
{

    public function testExecute()
    {
        $stream1 = new Stream();
        $stream1->setName('darkanakin41');
        $stream1->setTitle('Coucou');
        $stream1->setStatus(StatusNomenclature::OFFLINE);
        $stream1->setPlatform(PlatformNomenclature::TWITCH);
        $stream1->setIdentifier('darkanakin41');

        $stream2 = new Stream();
        $stream2->setName('Unknown');
        $stream2->setTitle('Coucou');
        $stream2->setStatus(StatusNomenclature::OFFLINE);
        $stream2->setPlatform(PlatformNomenclature::TWITCH);
        $stream2->setIdentifier('aaaasdsqdfdsqfdsqdfs');

        $stream3 = new Stream();
        $stream3->setName('Asiatique');
        $stream3->setTitle('Coucou');
        $stream3->setStatus(StatusNomenclature::OFFLINE);
        $stream3->setPlatform(PlatformNomenclature::TWITCH);
        $stream3->setIdentifier('__지노__');

        $application = new Application(static::$kernel);
        $application->setAutoExit(false);

        $this->getDoctrine()->getManager()->persist($stream1);
        $this->getDoctrine()->getManager()->persist($stream2);
        $this->getDoctrine()->getManager()->persist($stream3);
        $this->getDoctrine()->getManager()->flush();

        $id1 = $stream1->getId();
        $id2 = $stream2->getId();
        $id3 = $stream3->getId();

        $command = $application->find(UpgradeCommand::$defaultName);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        /** @var Stream|null $s */
        $s = $this->getDoctrine()->getRepository(Stream::class)->find($id1);
        $this->assertNotNull($s);
        $this->assertNotNull($s->getUserId());

        /** @var Stream|null $s */
        $s = $this->getDoctrine()->getRepository(Stream::class)->find($id2);
        $this->assertNull($s);

        /** @var Stream|null $s */
        $s = $this->getDoctrine()->getRepository(Stream::class)->find($id3);
        $this->assertNull($s);
    }
}
