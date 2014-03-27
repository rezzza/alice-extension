<?php

namespace Rezzza\AliceExtension\Alice;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Fixture\Configuration;
use Doctrine\Fixture\Executor;
use Doctrine\Fixture\Filter\ChainFilter;
use Doctrine\Fixture\Loader\ClassLoader;
use Doctrine\Fixture\Persistence\ManagerRegistryEventSubscriber;

class AliceFixturesExecutor
{
    protected $fixturesFile;

    protected $doctrine;

    public function __construct($fixturesFile, ManagerRegistry $doctrine)
    {
        $this->fixturesFile = $fixturesFile;
        $this->doctrine = $doctrine;
    }

    public function import($className, $columnKey, array $data)
    {
        $fixtureRows = new MultipleFixtures(
            $className,
            array(
                new YamlFixtures($className, $this->fixturesFile),
                new InlineFixtures($columnKey, $data)
            )
        );

        $configuration = new Configuration();
        $eventManager  = $configuration->getEventManager();

        $eventManager->addEventSubscriber(
            new ManagerRegistryEventSubscriber($this->doctrine)
        );
        $eventManager->addEventSubscriber(
            new Fixture\AliceFixturesEventSubscriber($fixtureRows)
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
