<?php

namespace Rezzza\AliceExtension\Symfony\Bundle\Processor;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Rezzza\AliceExtension\Alice\ProcessorRegistry;

class DIProcessorRegistry implements ProcessorRegistry
{
    private $container;

    private $processorIds;

    public function __construct(ContainerInterface $container, array $processorIds = array())
    {
        $this->container = $container;
        $this->processorIds = $processorIds;
    }

    public function get($className)
    {
        if (!$this->has($className)) {
            throw new \InvalidArgumentException(sprintf('The processor "%s" is not registered with the service container.', $className));
        }

        $processors = array();

        foreach ($this->processorIds[$className] as $processorId) {
            $processors[] = $this->container->get($processorId);
        }

        return $processors;
    }

    public function has($className)
    {
        return isset($this->processorIds[$className]);
    }
}
