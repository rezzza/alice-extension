<?php

namespace Rezzza\AliceExtension\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResolveFixturesPathPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $sfContainer = $container->get('behat.symfony2_extension.kernel')->getContainer();
        $fixturesPath = $container->getParameter('behat.alice.fixtures');
        $appPath = $sfContainer->getParameter('kernel.root_dir');

        $container->setParameter('behat.alice.fixtures', $fixturesPath ? $appPath.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.$fixturesPath : null);
    }
}
