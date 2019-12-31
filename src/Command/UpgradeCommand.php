<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Command;

use Darkanakin41\StreamBundle\DependencyInjection\Darkanakin41StreamExtension;
use Darkanakin41\StreamBundle\Model\Stream;
use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\StreamBundle\Requester\TwitchRequester;
use Darkanakin41\StreamBundle\Service\StreamService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UpgradeCommand extends Command
{
    const NB_ITERATION = 10;
    const NB_PER_ITERATION = 100;

    public static $defaultName = 'darkanakin41:stream:upgrade';

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var StreamService
     */
    private $streamService;

    /**
     * @var array
     */
    private $config;

    public function __construct(ManagerRegistry $managerRegistry, StreamService $streamService, ParameterBagInterface $parameterBag, string $name = null)
    {
        parent::__construct($name);
        $this->managerRegistry = $managerRegistry;
        $this->streamService = $streamService;
        $this->config = $parameterBag->get(Darkanakin41StreamExtension::CONFIG_KEY);
    }

    protected function configure()
    {
        $this->setDescription('Refresh stream data');
        $this->setHelp('Refresh stream data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $outputStyle = new OutputFormatterStyle('white', 'green', ['bold']);
        $output->getFormatter()->setStyle("success", $outputStyle);

        $output->writeln(array(
            'Darkanakin41 Stream Upgrade',
            '============',
            '',
        ));
        $synthesis = [];

        for ($i = 0; $i < self::NB_ITERATION; ++$i) {
            $output->writeln(sprintf('[%s] Iteration %d : <info>START</info>', PlatformNomenclature::TWITCH, $i));
            /** @var Stream[] $streams */
            $streams = $this->managerRegistry->getRepository($this->config['stream_class'])->findBy(array('platform' => PlatformNomenclature::TWITCH, 'userId' => null), [], self::NB_PER_ITERATION);
            $requester = $this->streamService->getRequester(PlatformNomenclature::TWITCH);
            $table = new Table($output->section());
            $table->setHeaders(['Platform', 'Stream', 'Status']);
            foreach ($streams as $stream) {
                $action = "";
                if (empty($stream->getIdentifier())) {
                    $this->managerRegistry->getManager()->remove($stream);
                    $action = '<error>Removed</error>';
                } else {
                    try {

                        /** @var TwitchRequester $requester */
                        $data = $requester->getUserData($stream->getIdentifier());

                        if (null === $data) {
                            $this->managerRegistry->getManager()->remove($stream);
                            $action = '<error>Removed</error>';
                        } else {
                            $stream->setUserId($data['id']);
                            $stream->setIdentifier($data['login']);
                            $this->managerRegistry->getManager()->persist($stream);
                            $action = '<success>Upgraded</success>';
                        }
                    } catch (\Exception $e) {
                        if (false !== stripos($e->getMessage(), 'Invalid login names, emails or IDs in request')) {
                            $this->managerRegistry->getManager()->remove($stream);
                            $action = '<error>Removed</error>';
                        }
                    }
                }
                if (!isset($synthesis[$action])) {
                    $synthesis[$action] = 0;
                }
                $synthesis[$action]++;
                $table->addRow([ucfirst(PlatformNomenclature::TWITCH), $stream->getName(), $action]);
            }
            $table->render();
            $this->managerRegistry->getManager()->flush();
        }

        $output->writeln('');

        $output->writeln('Synthesis');
        $table->setHeaders(array_keys($synthesis));
        $table->addRow(array_values($synthesis));
    }
}
