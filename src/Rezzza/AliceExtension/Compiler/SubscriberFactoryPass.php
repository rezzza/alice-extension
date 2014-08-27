<?php

namespace Rezzza\AliceExtension\Compiler;

use Symfony\Component\DependencyInjection\Reference,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class SubscriberFactoryPass implements CompilerPassInterface
{
    /**
     * Processes container.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('behat.alice.subscriber_factory.registry')) {
            return;
        }

        $factoryIds = array();

        foreach ($container->findTaggedServiceIds('behat.alice.subscriber.factory') as $id => $attributes) {
            $factoryIds[$attributes[0]['alias']] = $id;
        }

        $container->findDefinition('behat.alice.subscriber_factory.registry')->replaceArgument(1, $factoryIds);
    }
}
