<?php

namespace PLejeune\StreamBundle\Command;

use PLejeune\StreamBundle\Entity\Stream;
use PLejeune\StreamBundle\Nomenclature\ProviderNomenclature;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('plejeune:stream:refresh');
        $this->setDescription('Récupère les streams actifs pour les jeux activés');
        $this->setHelp('Récupère les streams actifs pour les jeux activés');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $streamService = $this->getContainer()->get('plejeune.stream');

        foreach(ProviderNomenclature::getAllConstants() as $provider){
            $output->writeln(sprintf(sprintf("%s : Récupération des streams à mettre à jour", ucfirst($provider))));
            $streams = $doctrine->getRepository(Stream::class)->findBy(['platform' => $provider], ['updated' => 'ASC'], 100);
            $streamService->refresh($streams, $provider);
            $output->writeln(sprintf(sprintf("%s : %d streams mis à jour", ucfirst($provider), count($streams))));
        }
    }

}
