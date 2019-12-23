<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Endpoint;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class AbstractEndpoint
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
        $this->initialize();
    }

    abstract protected function initialize();

    protected function getParameterBag(): ParameterBagInterface
    {
        return $this->parameterBag;
    }
}
