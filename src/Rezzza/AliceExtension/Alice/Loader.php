<?php

namespace Rezzza\AliceExtension\Alice;

use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Loader\Base;
use Nelmio\Alice\ORMInterface;

class Loader extends Base
{
    private $processorRegistry;

    private $persister;

    public function __construct(ProcessorRegistry $processorRegistry, $locale = "en_US", array $providers = array())
    {
        parent::__construct($locale, $providers);
        $this->processorRegistry = $processorRegistry;
    }

    public function changePersister($persister)
    {
        $this->persister = $persister;

        return $this;
    }

    public function load($data)
    {
        $this->persist(parent::load($data));
    }

    protected function getPersister()
    {
        return $this->persister;
    }

    private function persist(array $objects)
    {
        foreach ($objects as $obj) {
            $className = get_class($obj);
            $processors = $this->processorRegistry->get($className);

            foreach ($processors as $processor) {
                $processor->preProcess($obj);
            }
        }

        $this->getPersister()->persist($objects);

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
