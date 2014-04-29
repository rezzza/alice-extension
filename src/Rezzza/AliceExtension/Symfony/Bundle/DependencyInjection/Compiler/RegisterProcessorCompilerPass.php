<?php

namespace Rezzza\AliceExtension\Symfony\Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterProcessorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('alice_extension.processor.di_registry')) {
            return;
        }

        $processors = array();

        foreach ($container->findTaggedServiceIds('alice_extension.processor') as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['entityClass'])) {
                    throw new \RuntimeException(sprintf('Processor "%s" should have attribute "entityClass"', $id));
                }

                $key = $tag['entityClass'];
                if (!isset($processors[$key])) {
                    $processors[$key] = array();
                }

                $processors[$key][] = $id;
            }
        }

        $container->findDefinition('alice_extension.processor.di_registry')->replaceArgument(1, $processors);
    }
}
