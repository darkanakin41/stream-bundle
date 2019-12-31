<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Command;

use Darkanakin41\StreamBundle\DependencyInjection\Darkanakin41StreamExtension;
use Darkanakin41\StreamBundle\Model\Stream;
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

    public static $defaultName = 'darkanakin41:stream:refresh';

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
                /** @var Stream[] $streams */
                $streams = $this->managerRegistry->getRepository($this->config['stream_class'])->findToUpdate($provider);
                try {
                    $this->streamService->refresh($streams, $provider);
                } catch (\Exception $e) { // @codeCoverageIgnore
                }
                foreach ($streams as $stream) {
                    if (null !== $stream->getCategory()) {
                        $this->managerRegistry->getManager()->persist($stream->getCategory());
                    }
                    $this->managerRegistry->getManager()->persist($stream);
                }
                $this->managerRegistry->getManager()->flush();
                $progressBar->advance();
            }
        }
        $progressBar->finish();

        $output->writeln('');
    }
}
