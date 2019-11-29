<?php

namespace Darkanakin41\StreamBundle\Command;

use Darkanakin41\StreamBundle\Entity\Stream;
use Darkanakin41\StreamBundle\Nomenclature\ProviderNomenclature;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshCommand extends ContainerAwareCommand
{

    private const NB_ITERATION = 10;

    protected function configure()
    {
        $this->setName('darkanakin41:stream:refresh');
        $this->setDescription('Récupère les streams actifs pour les jeux activés');
        $this->setHelp('Récupère les streams actifs pour les jeux activés');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $streamService = $this->getContainer()->get('darkanakin41.stream');


        $progressBar = new ProgressBar($output, count(ProviderNomenclature::getAllConstants()) * self::NB_ITERATION);
        $progressBar->setFormat('Itération : %current%/%max% (%message%) [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        $progressBar->start();
        foreach (ProviderNomenclature::getAllConstants() as $provider) {
            $progressBar->setMessage(ucfirst($provider));
            $progressBar->display();
            for ($i = 0; $i < self::NB_ITERATION; $i++) {
                $streams = $doctrine->getRepository(Stream::class)->findBy(['platform' => $provider], ['updated' => 'ASC'], 100);
                $streamService->refresh($streams, $provider);
                $progressBar->advance();
            }
        }
        $progressBar->finish();

        $output->writeln("");
    }

}
