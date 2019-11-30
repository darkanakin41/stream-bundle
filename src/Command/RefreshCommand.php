<?php

namespace Darkanakin41\StreamBundle\Command;

use Darkanakin41\StreamBundle\Entity\Stream;
use Darkanakin41\StreamBundle\Nomenclature\ProviderNomenclature;
use Darkanakin41\StreamBundle\Service\StreamService;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

    public function __construct(ManagerRegistry $managerRegistry, StreamService $streamService, string $name = null)
    {
        parent::__construct($name);
        $this->managerRegistry = $managerRegistry;
        $this->streamService = $streamService;
    }


    protected function configure()
    {
        $this->setDescription('Retrieve active stream for enabled game categories');
        $this->setHelp('Retrieve active stream for enabled game categories');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Darkanakin41 Stream Refresh',
            '============',
            '',
        ]);

        $progressBar = new ProgressBar($output, count(ProviderNomenclature::getAllConstants()) * self::NB_ITERATION);
        $progressBar->setFormat('Iteration : %current%/%max% (%message%) [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        $progressBar->start();
        foreach (ProviderNomenclature::getAllConstants() as $provider) {
            $progressBar->setMessage(ucfirst($provider));
            $progressBar->display();
            for ($i = 0; $i < self::NB_ITERATION; $i++) {
                $streams = $this->managerRegistry->getRepository(Stream::class)->findBy(['platform' => $provider], ['updated' => 'ASC'], 100);
                try{
                    $this->streamService->refresh($streams, $provider);
                }catch(Exception $e){}
                $progressBar->advance();
            }
        }
        $progressBar->finish();

        $output->writeln("");
    }

}
