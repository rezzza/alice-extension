<?php

namespace Rezzza\AliceExtension\Fixture;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Fixture\Persistence\ManagerRegistryFixture;
use Nelmio\Alice\Loader\Base as AliceLoader;

use Rezzza\AliceExtension\Alice\AliceFixture;
use Rezzza\AliceExtension\Alice\AliceFixtures;
use Rezzza\AliceExtension\Doctrine\ORMPurger;

class TestFixture implements ManagerRegistryFixture, AliceFixture
{
    private $managerRegistry;

    private $fixtures;

    private $alice;

    public function import()
    {
        $objects = $this->alice->load($this->fixtures->load());
        $persister = new \Nelmio\Alice\ORM\Doctrine($this->managerRegistry->getManager());
        $persister->persist($objects);
    }

    public function purge()
    {
        $purger = new ORMPurger($this->managerRegistry->getManager());
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $purger->purge();
    }

    public function setManagerRegistry(ManagerRegistry $registry)
    {
        $this->managerRegistry = $registry;
    }

    public function setAliceFixtures(AliceFixtures $fixtures)
    {
        $this->fixtures = $fixtures;
    }

    public function setAlice(AliceLoader $alice)
    {
        $this->alice = $alice;
    }
}
