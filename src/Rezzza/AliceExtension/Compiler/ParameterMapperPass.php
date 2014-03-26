<?php

namespace Rezzza\AliceExtension\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Map parameter from Symfony2 container to behat container
 */
class ParameterMapperPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $mapping = $container->getParameter('behat.alice.mapping_parameters');
        $sfContainer = $container->get('behat.symfony2_extension.kernel')->getContainer();

        foreach ($mapping as $sfParameter) {
            $container->setParameter(
                'sf2.'.$sfParameter,
                $sfContainer->getParameter($sfParameter)
            );
        }
    }
}
