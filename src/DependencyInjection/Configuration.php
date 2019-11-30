<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\DependencyInjection;

use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('darkanakin41_stream');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('darkanakin41_stream');
        }

        $supportedPlatforms = array(PlatformNomenclature::YOUTUBE, PlatformNomenclature::TWITCH);

        $rootNode
            ->children()
            ->scalarNode('stream_class')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('category_class')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('platform')
                ->arrayPrototype()
                    ->children()
                        ->integerNode('api_key')->isRequired()->cannotBeEmpty()->end()
                        ->integerNode('api_secret')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
