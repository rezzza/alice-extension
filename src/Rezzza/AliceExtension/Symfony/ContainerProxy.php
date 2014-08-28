<?php

namespace Rezzza\AliceExtension\Symfony;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ScopeInterface;

class ContainerProxy implements ContainerInterface
{
    private $container;

    public function __construct(KernelInterface $kernel)
    {
        $this->container = $kernel->getContainer();
    }

    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        return $this->container->get($id, $invalidBehavior);
    }

    public function getParameter($id)
    {
        return $this->container->getParameter($id);
    }

    public function has($id)
    {
        return $this->container->has($id);
    }

    public function set($id, $service, $scope = self::SCOPE_CONTAINER)
    {
        throw new \Exception('Unsupported method');
    }

    public function hasParameter($name)
    {
        throw new \Exception('Unsupported method');
    }

    public function setParameter($name, $value)
    {
        throw new \Exception('Unsupported method');
    }

    public function enterScope($name)
    {
        throw new \Exception('Unsupported method');
    }

    public function leaveScope($name)
    {
        throw new \Exception('Unsupported method');
    }

    public function addScope(ScopeInterface $scope)
    {
        throw new \Exception('Unsupported method');
    }

    public function hasScope($name)
    {
        throw new \Exception('Unsupported method');
    }

    public function isScopeActive($name)
    {
        throw new \Exception('Unsupported method');
    }
}
