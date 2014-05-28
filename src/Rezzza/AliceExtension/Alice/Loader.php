<?php

namespace Rezzza\AliceExtension\Alice;

use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Loader\Base;
use Nelmio\Alice\ORMInterface;

class Loader extends Base
{
    private $objectManager;

    private $processorRegistry;

    public function __construct(ObjectManager $objectManager, ProcessorRegistry $processorRegistry, $locale = "en_US", array $providers = array())
    {
        parent::__construct($locale, $providers);
        $this->objectManager = $objectManager;
        $this->processorRegistry = $processorRegistry;
    }

    public function load($data)
    {
        $objects = parent::load($data);
        $this->persist(new \Nelmio\Alice\ORM\Doctrine($this->objectManager), $objects);
    }

    private function persist(ORMInterface $persister, array $objects)
    {
        foreach ($objects as $obj) {
            $className = get_class($obj);
            $processors = $this->processorRegistry->get($className);

            foreach ($processors as $processor) {
                $processor->preProcess($obj);
            }
        }

        $persister->persist($objects);

        foreach ($objects as $obj) {
            $className = get_class($obj);

            if ($this->processorRegistry->has($className)) {
                $processors = $this->processorRegistry->get($className);

                foreach ($processors as $processor) {
                    $processor->postProcess($obj);
                }
            }
        }
    }
}
