<?php

namespace Rezzza\AliceExtension\Symfony;

use Symfony\Component\HttpKernel\KernelInterface;

class ContainerProxy
{
    private $container;

    public function __construct(KernelInterface $kernel)
    {
        $this->container = $kernel->getContainer();
    }

    public function get($id)
    {
        return $this->container->get($id);
    }

    public function getParameter($id)
    {
        return $this->container->getParameter($id);
    }
}
