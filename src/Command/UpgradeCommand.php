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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UpgradeCommand extends Command
{
    const NB_ITERATION = 10;

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
        $output->writeln(array(
            'Darkanakin41 Stream Upgrade',
            '============',
            '',
        ));

        for ($i = 0; $i < self::NB_ITERATION; ++$i) {
            /** @var Stream[] $streams */
            $streams = $this->managerRegistry->getRepository($this->config['stream_class'])->findBy(array('platform' => PlatformNomenclature::TWITCH, 'userId' => null));
            $requester = $this->streamService->getRequester(PlatformNomenclature::TWITCH);
            foreach ($streams as $stream) {
                try {
                    /** @var TwitchRequester $requester */
                    $data = $requester->getUserData($stream->getIdentifier());

                    if (null === $data) {
                        $this->managerRegistry->getManager()->remove($stream);
                        $action = 'removed';
                    } else {
                        $stream->setUserId($data['id']);
                        $stream->setIdentifier($data['login']);
                        $this->managerRegistry->getManager()->persist($stream);
                        $action = 'upgraded';
                    }
                } catch (\Exception $e) {
                    if (false !== stripos($e->getMessage(), 'Invalid login names, emails or IDs in request')) {
                        $this->managerRegistry->getManager()->remove($stream);
                        $action = 'removed';
                    }
                }
                $output->writeln(sprintf('[%s] Stream %s : %s', PlatformNomenclature::TWITCH, $stream->getName(), $action));
            }
            $this->managerRegistry->getManager()->flush();
        }

        $output->writeln('');
    }
}
