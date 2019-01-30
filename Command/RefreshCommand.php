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

        foreach (ProviderNomenclature::getAllConstants() as $provider) {
            for ($i = 0; $i < 10; $i++) {
                $output->writeln(sprintf(sprintf("[%s] %s :: Itération %d :: Récupération des streams à mettre à jour", (new \DateTime())->format('Y-m-d H:i:s'), ucfirst($provider), $i + 1)));
                $streams = $doctrine->getRepository(Stream::class)->findBy(['platform' => $provider], ['updated' => 'ASC'], 100);
                $streamService->refresh($streams, $provider);
                $output->writeln(sprintf(sprintf("[%s] %s :: Itération %d :: %d streams mis à jour", (new \DateTime())->format('Y-m-d H:i:s'), ucfirst($provider), $i + 1, count($streams))));
            }
        }
    }

}
