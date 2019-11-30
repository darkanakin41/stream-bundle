<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Endpoint;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

abstract class AbstractEndpoint
{
    /**
     * @var ParameterBag
     */
    private $parameterBag;

    public function __construct(ParameterBag $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    abstract protected function initialize();

    protected function getParameterBag(): ParameterBag
    {
        return $this->parameterBag;
    }
}
