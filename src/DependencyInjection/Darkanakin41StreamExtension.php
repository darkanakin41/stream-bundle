<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Darkanakin41StreamExtension extends Extension
{
    const CONFIG_KEY = 'darkanakin41.stream.config';

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        if (!class_exists($config['stream_class'])) {
            throw new InvalidConfigurationException('Please provide a valid stream_class value in configuration');
        }
        if (!class_exists($config['category_class'])) {
            throw new InvalidConfigurationException('Please provide a valid category_class value in configuration');
        }

        $container->setParameter(self::CONFIG_KEY, $config);
    }

    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('twig')) {
            return;
        }

        $container->prependExtensionConfig('twig', array('paths' => array(__DIR__.'/../Resources/views' => 'Darkanakin41Stream')));
    }
}
