<?php

namespace Rezzza\AliceExtension\Adapter;

use Symfony\Component\DependencyInjection\ContainerInterface;

class SubscriberFactoryRegistry
{
    private $container;

    private $factoryIds;

    public function __construct(ContainerInterface $container, array $factoryIds)
    {
        $this->container = $container;
        $this->factoryIds = $factoryIds;
    }

    public function get($name)
    {
        if (!isset($this->factoryIds[$name])) {
            throw new \InvalidArgumentException(sprintf('No factory registered with name "%s"', $name));
        }

        return $this->container->get($this->factoryIds[$name]);
    }
}
