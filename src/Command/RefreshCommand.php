<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Command;

use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\StreamBundle\Service\StreamService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RefreshCommand extends Command
{
    const NB_ITERATION = 10;

    protected static $defaultName = 'darkanakin41:stream:refresh';

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
        $this->config = $parameterBag->get('darkanakin41.stream.config');
    }

    protected function configure()
    {
        $this->setDescription('Retrieve active stream for enabled game categories');
        $this->setHelp('Retrieve active stream for enabled game categories');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(array(
            'Darkanakin41 Stream Refresh',
            '============',
            '',
        ));

        $progressBar = new ProgressBar($output, count(PlatformNomenclature::getAllConstants()) * self::NB_ITERATION);
        $progressBar->setFormat('Iteration : %current%/%max% (%message%) [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        $progressBar->start();
        foreach (PlatformNomenclature::getAllConstants() as $provider) {
            $progressBar->setMessage(ucfirst($provider));
            $progressBar->display();
            for ($i = 0; $i < self::NB_ITERATION; ++$i) {
                $streams = $this->managerRegistry->getRepository($this->config['stream_class'])->findBy(array('platform' => $provider), array('updated' => 'ASC'), 100);
                try {
                    $this->streamService->refresh($streams, $provider);
                } catch (\Exception $e) {
                }
                $progressBar->advance();
            }
        }
        $progressBar->finish();

        $output->writeln('');
    }
}
