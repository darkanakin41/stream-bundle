<?php


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

    /**
     * @return ParameterBag
     */
    protected function getParameterBag(): ParameterBag
    {
        return $this->parameterBag;
    }
}
