<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\DependencyInjection;

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

        $rootNode
            ->children()
            ->scalarNode('stream_class')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('category_class')->isRequired()->cannotBeEmpty()->end()
            ->arrayNode('platform')
                ->children()
                    ->arrayNode('google')
                        ->children()
                            ->scalarNode('application_key')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('referer')->isRequired()->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('twitch')
                        ->children()
                            ->scalarNode('client_id')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('client_secret')->isRequired()->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
