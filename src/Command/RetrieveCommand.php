<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Command;

use Darkanakin41\StreamBundle\Model\StreamCategory;
use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Darkanakin41\StreamBundle\Service\StreamService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RetrieveCommand extends Command
{
    protected static $defaultName = 'darkanakin41:stream:retrieve';

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var StreamService
     */
    private $streamService;

    public function __construct(ManagerRegistry $managerRegistry, StreamService $streamService, string $name = null)
    {
        parent::__construct($name);
        $this->managerRegistry = $managerRegistry;
        $this->streamService = $streamService;
    }

    protected function configure()
    {
        $this->setDescription('Récupère les streams actifs pour les jeux activés');
        $this->setHelp('Récupère les streams actifs pour les jeux activés');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(array(
            'Darkanakin41 Stream Retrieve',
            '============',
            '',
        ));

        /** @var StreamCategory[] $categories */
        $categories = $this->managerRegistry->getRepository(StreamCategory::class)->findBy(array('refresh' => true));

        $created = 0;

        $progressBar = new ProgressBar($output, count($categories));
        $progressBar->setFormat('Categories to process : %current%/%max% [%bar%] %message% %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $progressBar->setMessage(sprintf('(Streams créés : %d)', $created));

        ProgressBar::setPlaceholderFormatterDefinition(
            'created',
            function () use ($created) {
                return $created;
            }
        );

        $progressBar->start();
        foreach ($categories as $key => $category) {
            foreach (PlatformNomenclature::getAllConstants() as $provider) {
                try {
                    $created += $this->streamService->getFromGame($category, $provider);
                } catch (\Exception $e) {
                }
            }

            $progressBar->setMessage(sprintf('(Streams créés : %d)', $created));
            $progressBar->advance();
        }
        $progressBar->finish();

        $output->writeln('');
    }
}
