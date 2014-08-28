<?php

namespace Rezzza\AliceExtension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Behat\Testwork\ServiceContainer\ExtensionManager;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;

class Extension implements ExtensionInterface
{
    public function getConfigKey()
    {
        return 'alice';
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * @param array            $config    Extension configuration hash (from behat.yml)
     * @param ContainerBuilder $container ContainerBuilder instance
     *
     * @return null
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/Resources'));
        $loader->load('services.xml');

        if (isset($config['fixtures'])) {
            $container->setParameter('behat.alice.fixtures', $config['fixtures']);
        }

        if (isset($config['lifetime'])) {
            $container->setParameter('behat.alice.lifetime', $config['lifetime']);
        }

        $container->setParameter('behat.alice.faker.locale', $config['faker']['locale']);
        $container->setParameter('behat.alice.faker.providers', $config['faker']['providers']);

        $adapters = array();
        foreach ($config['adapters'] as $name => $adapter) {
            $adapters[$name] = $adapter['fixture_class'];

            if (isset($adapter['mapping'])) {
                $container->setParameter('behat.alice.elastica_mapping', $adapter['mapping']);
            }

            if (isset($adapter['index_service'])) {
                $container->setParameter('behat.alice.elastica_index', $adapter['index_service']);
            }
        }
        $container->setParameter('behat.alice.adapters', $adapters);
    }

    /**
     * @param ArrayNodeDefinition $builder
     *
     * @return null
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('fixtures')->end()
                ->scalarNode('lifetime')->end()
                ->arrayNode('faker')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('locale')->defaultValue('en_US')->end()
                        ->arrayNode('providers')
                            ->beforeNormalization()
                                ->always(function($v) {
                                    return array_map(function($class) {
                                        return new $class();
                                    }, $v);
                                })
                            ->end()
                            ->prototype('variable')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('adapters')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('elastica')
                            ->children()
                                ->scalarNode('fixture_class')
                                    ->defaultValue('Rezzza\AliceExtension\Fixture\ElasticaFixture')
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('index_service')
                                    ->cannotBeEmpty()
                                ->end()
                                ->arrayNode('mapping')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('orm')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('fixture_class')
                                    ->defaultValue('Rezzza\AliceExtension\Fixture\ORMFixture')
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('faker')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('locale')->defaultValue('en_US')->end()
                        ->arrayNode('providers')
                            ->beforeNormalization()
                                ->always(function($v) {
                                    return array_map(function($class) {
                                        return new $class();
                                    }, $v);
                                })
                            ->end()
                            ->prototype('variable')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function process(ContainerBuilder $container)
    {
        $resolveFixturesCompiler = new Compiler\ResolveFixturesPathPass;
        $resolveFixturesCompiler->process($container);

        $subscriberFactoryCompiler = new Compiler\SubscriberFactoryPass;
        $subscriberFactoryCompiler->process($container);
    }
}
