<?php

namespace Rezzza\AliceExtension\Alice;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Fixture\Configuration;
use Doctrine\Fixture\Executor;
use Doctrine\Fixture\Filter\ChainFilter;
use Doctrine\Fixture\Loader\ClassLoader;
use Doctrine\Fixture\Persistence\ManagerRegistryEventSubscriber;

use Nelmio\Alice\Loader\Base as AliceLoader;

class AliceFixturesExecutor
{
    protected $fixturesFile;

    protected $doctrine;

    public function __construct(ManagerRegistry $doctrine, AliceLoader $alice, $fixturesFile = null)
    {
        $this->doctrine = $doctrine;
        $this->alice = $alice;
        $this->fixturesFile = $fixturesFile;
    }

    public function import($className, $columnKey, array $data)
    {
        $fixtures = array(
            new InlineFixtures($className, $columnKey, $data)
        );

        if (null !== $this->fixturesFile) {
            array_unshift($fixtures, new YamlFixtures($this->fixturesFile));
        }

        $fixtures = new MultipleFixtures($fixtures);
        $configuration = new Configuration();
        $eventManager  = $configuration->getEventManager();

        $eventManager->addEventSubscriber(
            new ManagerRegistryEventSubscriber($this->doctrine)
        );
        $eventManager->addEventSubscriber(
            new Fixture\AliceFixturesEventSubscriber($this->alice, $fixtures)
        );

        $this->execute($configuration, Executor::IMPORT);
    }

    public function purge()
    {
        $configuration = new Configuration();
        $eventManager  = $configuration->getEventManager();

        $eventManager->addEventSubscriber(
            new ManagerRegistryEventSubscriber($this->doctrine)
        );

        $this->execute($configuration, Executor::PURGE);
    }

    protected function execute($configuration, $flag)
    {
        $executor      = new Executor($configuration);
        $classLoader   = new ClassLoader(array('Rezzza\AliceExtension\Fixture\TestFixture'));
        $filter        = new ChainFilter();

        $executor->execute($classLoader, $filter, $flag);
    }
}
