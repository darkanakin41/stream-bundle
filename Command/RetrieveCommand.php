<?php

namespace PLejeune\StreamBundle\Command;

use PLejeune\StreamBundle\Entity\StreamCategory;
use PLejeune\StreamBundle\Nomenclature\ProviderNomenclature;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RetrieveCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('plejeune:stream:retrieve');
        $this->setDescription('Récupère les streams actifs pour les jeux activés');
        $this->setHelp('Récupère les streams actifs pour les jeux activés');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $streamService = $this->getContainer()->get('plejeune.stream');

        $output->writeln(sprintf("Recuperation des catégories de stream à mettre à jour START"));
        $categories = $doctrine->getRepository(StreamCategory::class)->findBy(['refresh' => true]);

        $output->writeln(sprintf("%d catégories", count($categories)));
        foreach($categories as $key => $category){
            $output->writeln(sprintf("%d/%d %s : Recuperation des nouveaux streams", $key + 1, count($categories), $category->getTitle()));

            foreach(ProviderNomenclature::getAllConstants() as $provider){
                $creations = $streamService->getFromGame($category, $provider);
                $output->writeln(sprintf("==> %s : %d nouveaux streams", ucfirst($provider), $creations));
            }

            $output->writeln(sprintf("%d/%d %s : Recuperation des nouveaux streams DONE", $key + 1, count($categories), $category->getTitle()));
        }
    }

}
