<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Endpoint;

use Darkanakin41\StreamBundle\DependencyInjection\Darkanakin41StreamExtension;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class AbstractEndpoint
{
    /**
     * @var array
     */
    private $config;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->config = $parameterBag->get(Darkanakin41StreamExtension::CONFIG_KEY);
        $this->initialize();
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    abstract protected function initialize();
}
