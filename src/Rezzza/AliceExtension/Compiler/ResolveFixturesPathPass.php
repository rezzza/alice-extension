<?php

namespace Rezzza\AliceExtension\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResolveFixturesPathPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $sfContainer = $container->get('symfony2_extension.kernel')->getContainer();
        $fixturesPath = $container->getParameter('behat.alice.fixtures.key_paths');
        $appPath = $sfContainer->getParameter('kernel.root_dir');

        if (null === $fixturesPath) {
            return;
        }

        $fixturesPath = array_map(function($v) use ($appPath) {
            return $appPath.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.$v;
        }, $fixturesPath);

        $container->setParameter('behat.alice.fixtures.key_paths', $fixturesPath);
    }
}
