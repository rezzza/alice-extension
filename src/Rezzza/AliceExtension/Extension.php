<?php

namespace Rezzza\AliceExtension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Behat\Behat\Extension\ExtensionInterface;

class Extension implements ExtensionInterface
{
    /**
     * @param array            $config    Extension configuration hash (from behat.yml)
     * @param ContainerBuilder $container ContainerBuilder instance
     *
     * @return null
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/Resources'));
        $loader->load('services.xml');

        if (isset($config['fixtures'])) {
            $container->setParameter('behat.alice.fixtures', $config['fixtures']);
        }
    }

    /**
     * @param ArrayNodeDefinition $builder
     *
     * @return null
     */
    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('fixtures')
                ->end()
            ->end()
        ->end();
    }

    /**
     * @return array
     */
    public function getCompilerPasses()
    {
        return array(
            new Compiler\ResolveFixturesPathPass()
        );
    }
}

