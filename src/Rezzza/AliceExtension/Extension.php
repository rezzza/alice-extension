<?php

namespace Rezzza\AliceExtension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Rezzza\AliceExtension\Fixture\FixtureStack;

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
            $container->setParameter('behat.alice.fixtures.default', $config['fixtures']['default']);
            $container->setParameter('behat.alice.fixtures.key_paths', $config['fixtures']['key_paths']);
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
    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('fixtures')
                    ->beforeNormalization()
                        ->ifTrue(function($v) {
                            return is_scalar($v);
                        })
                        ->then(function($v){
                            return array(
                                'default'   => array('app'),
                                'key_paths' => array('app' => $v));
                        })
                    ->end()
                    ->validate()
                        ->ifTrue(function($v) {
                            foreach ($v['default'] as $default) {
                                if (!array_key_exists($default, $v['key_paths'])) {
                                    return true;
                                }
                            }
                        })
                        ->thenInvalid("You can't define a default which is not present in key_paths.")
                    ->end()
                    ->children()
                        ->arrayNode('default')
                            ->beforeNormalization()
                                ->ifTrue(function($v) {
                                    return is_scalar($v);
                                })
                                ->then(function($v) {
                                    return array($v);
                                })
                            ->end()
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('key_paths')
                            ->useAttributeAsKey('key_path')
                            ->validate()
                                ->ifTrue(function($v) {
                                    return array_key_exists(FixtureStack::DEFAULT_KEY, $v);
                                })
                                ->thenInvalid('You cannot add a key_path with key “'.FixtureStack::DEFAULT_KEY.'“, this is a reserved word.')
                            ->end()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
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
            ->end()
        ;
    }

    /**
     * @return array
     */
    public function getCompilerPasses()
    {
        return array(
            new Compiler\ResolveFixturesPathPass,
            new Compiler\SubscriberFactoryPass,
        );
    }
}

