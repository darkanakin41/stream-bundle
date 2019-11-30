<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Command;

use Darkanakin41\StreamBundle\Model\StreamCategory;
use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RetrieveCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('darkanakin41:stream:retrieve');
        $this->setDescription('Récupère les streams actifs pour les jeux activés');
        $this->setHelp('Récupère les streams actifs pour les jeux activés');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $streamService = $this->getContainer()->get('darkanakin41.stream');

        $categories = $doctrine->getRepository(StreamCategory::class)->findBy(array('refresh' => true));

        $created = 0;

        $progressBar = new ProgressBar($output, count($categories));
        $progressBar->setFormat('Catégories à traiter : %current%/%max% [%bar%] %message% %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
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
                $created += $streamService->getFromGame($category, $provider);
            }

            $progressBar->setMessage(sprintf('(Streams créés : %d)', $created));
            $progressBar->advance();
        }
        $progressBar->finish();

        $output->writeln('');
    }
}
