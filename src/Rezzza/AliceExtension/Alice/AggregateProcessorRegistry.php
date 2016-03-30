<?php

namespace Rezzza\AliceExtension\Alice;

class AggregateProcessorRegistry implements ProcessorRegistry
{
    /** @var ProcessorRegistry[] */
    private $registries = array();

    /** @var \Nelmio\Alice\ProcessorInterface[] */
    private $processors = array();

    /**
     * @param ProcessorRegistry[] $registries
     */
    public function __construct(array $registries = array())
    {
        foreach ($registries as $registry) {
            $this->addRegistry($registry);
        }
    }

    /**
     * @inheritdoc
     */
    public function get($className)
    {
        if (!$this->has($className)) {
            $processors = array();

            foreach ($this->registries as $registry) {
                if ($registry->has($className)) {
                    $processors = array_merge($processors, $registry->get($className));
                }
            }
            $this->processors[$className] = $processors;
        }

        return $this->processors[$className];
    }

    /**
     * @inheritdoc
     */
    public function has($className)
    {
        return isset($this->processors[$className]);
    }

    private function addRegistry(ProcessorRegistry $registry)
    {
        $this->registries[] = $registry;
    }
}
