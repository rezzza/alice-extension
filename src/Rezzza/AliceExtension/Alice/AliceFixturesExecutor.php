<?php

namespace Rezzza\AliceExtension\Alice;

use Doctrine\Fixture\Configuration;
use Doctrine\Fixture\Executor;
use Doctrine\Fixture\Filter\ChainFilter;
use Doctrine\Fixture\Loader\ClassLoader;

use Rezzza\AliceExtension\Alice\Loader as AliceLoader;
use Rezzza\AliceExtension\Adapter\SubscriberFactoryRegistry;
use Rezzza\AliceExtension\Fixture\FixtureStack;

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

        if ($this->defaultLoading === self::DEFAULT_LOADING_IMPLICIT) {
            // add defaults fixtures.
            $fixtures = array_map(function($v) {
                return new YamlFixtures($v);
            }, $this->fixtureStack->unstack(FixtureStack::DEFAULT_KEY));
        }

        $fixtures[] = new InlineFixtures($className, $columnKey, $data);

        $this->importFixtures($fixtures);
    }

    public function importFixtureKeyPath($key)
    {
        $fixtures = array_map(function($v) {
            return new YamlFixtures($v);
        }, $this->fixtureStack->unstack($key));

        if (!empty($fixtures)) {
            $this->importFixtures($fixtures);
        }
    }

    private function importFixtures(array $fixtures)
    {
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
        $this->fixtureStack->reset();
    }

    private function guardAgainstEmptyAdapterConfig()
    {
        if (null === $this->adapterName || null === $this->fixtureClass) {
            throw new \LogicException('Cannot perform operation without adapter defined. Please use changeAdapter method');
        }
    }
}
