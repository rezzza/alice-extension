<?php

namespace Rezzza\AliceExtension\Alice;

use Doctrine\Fixture\Configuration;
use Doctrine\Fixture\Executor;
use Doctrine\Fixture\Filter\ChainFilter;
use Doctrine\Fixture\Loader\ClassLoader;

use Rezzza\AliceExtension\Alice\Loader as AliceLoader;
use Rezzza\AliceExtension\Adapter\SubscriberFactoryRegistry;

class AliceFixturesExecutor
{
    protected $fixturesFile;

    protected $alice;

    protected $yamlLoaded = false;

    protected $fixtureClass;

    protected $adapterName;

    protected $adapterRegistry;

    public function __construct(SubscriberFactoryRegistry $adapterRegistry, AliceLoader $alice, $fixturesFile = null)
    {
        $this->adapterRegistry = $adapterRegistry;
        $this->alice = $alice;
        $this->fixturesFile = $fixturesFile;
    }

    public function changeAdapter($name, $fixtureClass)
    {
        $this->adapterName = $name;
        $this->fixtureClass = $fixtureClass;
    }

    public function import($className, $columnKey, array $data)
    {
        $this->guardAgainstEmptyAdapterConfig();
        $fixtures = array(
            new InlineFixtures($className, $columnKey, $data)
        );

        if (null !== $this->fixturesFile && !$this->yamlLoaded) {
            array_unshift($fixtures, new YamlFixtures($this->fixturesFile));
            $this->yamlLoaded = true;
        }

        $fixtures = new MultipleFixtures($fixtures);
        $configuration = new Configuration();
        $eventManager  = $configuration->getEventManager();
        $eventSubscribers = array(
            new Fixture\AliceFixturesEventSubscriber($this->alice, $fixtures),
            $this->adapterRegistry->get($this->adapterName)->create()
        );

        foreach ($eventSubscribers as $eventSubscriber) {
            $eventManager->addEventSubscriber($eventSubscriber);
        }

        $this->execute($configuration, Executor::IMPORT);
    }

    public function purge()
    {
        $this->guardAgainstEmptyAdapterConfig();
        $configuration = new Configuration();
        $eventManager  = $configuration->getEventManager();

        $eventManager->addEventSubscriber(
            $this->adapterRegistry->get($this->adapterName)->create()
        );

        $this->reset();

        $this->execute($configuration, Executor::PURGE);
    }

    private function execute($configuration, $flag)
    {
        $executor      = new Executor($configuration);
        $classLoader   = new ClassLoader(array($this->fixtureClass));
        $filter        = new ChainFilter();

        $executor->execute($classLoader, $filter, $flag);
    }

    private function reset()
    {
        $this->yamlLoaded = false;
    }

    private function guardAgainstEmptyAdapterConfig()
    {
        if (null === $this->adapterName || null === $this->fixtureClass) {
            throw new \LogicException('Cannot perform operation without adapter defined. Please use changeAdapter method');
        }
    }
}
