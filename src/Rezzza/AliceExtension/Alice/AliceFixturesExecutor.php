<?php

namespace Rezzza\AliceExtension\Alice;

use Doctrine\Fixture\Configuration;
use Doctrine\Fixture\Executor;
use Doctrine\Fixture\Filter\ChainFilter;
use Doctrine\Fixture\Loader\ClassLoader;

use Rezzza\AliceExtension\Alice\Loader as AliceLoader;
use Rezzza\AliceExtension\Adapter\SubscriberFactoryRegistry;
use Rezzza\AliceExtension\Fixture\FixtureStack;
use Rezzza\AliceExtension\Alice\EventListener\AliceLoadFixturesEventListener;
use Rezzza\AliceExtension\Alice\Fixture\AliceFixtureEvent;
use Rezzza\AliceExtension\Alice\Fixture\AliceExecutorEventSubscriber;

class AliceFixturesExecutor
{
    protected $fixtureStack;

    protected $alice;

    protected $fixtureClass;

    protected $adapterName;

    protected $adapterRegistry;

    protected $defaultLoading;

    CONST DEFAULT_LOADING_IMPLICIT = 'implicit';
    CONST DEFAULT_LOADING_EXPLICIT = 'explicit';

    public function __construct(SubscriberFactoryRegistry $adapterRegistry, AliceLoader $alice, FixtureStack $fixtureStack, $defaultLoading = self::DEFAULT_LOADING_IMPLICIT)
    {
        $this->adapterRegistry = $adapterRegistry;
        $this->alice           = $alice;
        $this->fixtureStack    = $fixtureStack;
        $this->defaultLoading  = $defaultLoading;
    }

    public function changeAdapter($name, $fixtureClass)
    {
        $this->adapterName = $name;
        $this->fixtureClass = $fixtureClass;
    }

    public function import($className, $columnKey, array $data)
    {
        $this->guardAgainstEmptyAdapterConfig();
        $fixtures = array();

        if ($this->defaultLoading === self::DEFAULT_LOADING_IMPLICIT) {
            // add defaults fixtures.
            $fixtures = $this->createYamlFixtures(FixtureStack::DEFAULT_KEY);
        }

        $fixtures[] = $this->createInlineFixtures($className, $columnKey, $data);

        $this->importFixtures(new MultipleFixtures($fixtures));
    }

    public function importFixtureKeyPath($key)
    {
        $fixtures = $this->createYamlFixtures($key);

        if (!empty($fixtures)) {
            $this->importFixtures(new MultipleFixtures($fixtures));
        }
    }

    public function purge()
    {
        $this->guardAgainstEmptyAdapterConfig();

        $configuration = $this->createExecutorConfiguration(array(
            $this->adapterRegistry->get($this->adapterName)->create()
        ));

        $this->resetFixtureStack();

        $this->execute($configuration, Executor::PURGE);
    }

    public function terminate()
    {
        $eventSubscribers = array(
            new AliceExecutorEventSubscriber,
            $this->adapterRegistry->get($this->adapterName)->create()
        );

        $configuration = $this->createExecutorConfiguration($eventSubscribers);
        $eventManager  = $configuration->getEventManager();

        $eventManager->dispatchEvent(
            AliceLoadFixturesEventListener::BULK_TERMINATE,
            new AliceFixtureEvent($configuration, array($this->fixtureClass))
        );
    }

    private function importFixtures(MultipleFixtures $fixtures)
    {
        $eventSubscribers = array(
            new Fixture\AliceFixturesEventSubscriber($this->alice, $fixtures),
            $this->adapterRegistry->get($this->adapterName)->create()
        );

        $configuration = $this->createExecutorConfiguration($eventSubscribers);

        $this->execute($configuration, Executor::IMPORT);
    }

    private function createYamlFixtures($key)
    {
        return array_map(
            function ($v) { return new YamlFixtures($v); },
            $this->fixtureStack->unstack($key)
        );
    }

    private function createInlineFixtures($className, $columnKey, $data)
    {
        return new InlineFixtures($className, $columnKey, $data);
    }

    private function execute($configuration, $flag)
    {
        $executor      = new Executor($configuration);
        $classLoader   = new ClassLoader(array($this->fixtureClass));
        $filter        = new ChainFilter();

        $executor->execute($classLoader, $filter, $flag);
    }

    private function resetFixtureStack()
    {
        $this->fixtureStack->reset();
    }

    private function createExecutorConfiguration(array $eventSubscribers)
    {
        $configuration = new Configuration();
        $eventManager  = $configuration->getEventManager();

        foreach ($eventSubscribers as $eventSubscriber) {
            $eventManager->addEventSubscriber($eventSubscriber);
        }

        return $configuration;
    }

    private function guardAgainstEmptyAdapterConfig()
    {
        if (null === $this->adapterName || null === $this->fixtureClass) {
            throw new \LogicException('Cannot perform operation without adapter defined. Please use changeAdapter method');
        }
    }
}
