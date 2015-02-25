<?php

namespace Rezzza\AliceExtension\Fixture;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Fixture\Persistence\ManagerRegistryFixture;
use Nelmio\Alice\Loader\Base as AliceLoader;
use Nelmio\Alice\ORM\Doctrine as ORMPersister;

use Rezzza\AliceExtension\Alice\AliceFixture;
use Rezzza\AliceExtension\Alice\AliceFixtures;
use Rezzza\AliceExtension\Doctrine\ORMPurger;
use Rezzza\AliceExtension\Adapter\ORM\ORMPersistFixture;
use Rezzza\AliceExtension\Adapter\ORM\ORMResetFixture;

class ORMFixture implements ManagerRegistryFixture, AliceFixture, ORMPersistFixture, ORMResetFixture
{
    private $managerRegistry;

    private $persister;

    private $purger;

    private $fixtures;

    private $alice;

    public function import()
    {
        $this->alice
            ->changePersister($this->persister)
            ->load($this->fixtures->load())
        ;

        // Ensure to close the connection to avoid mysql timeout
        $this->managerRegistry->getManager()->getConnection()->close();
    }

    public function purge()
    {
        $this->purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $this->purger->purge();

        // Ensure to close the connection to avoid mysql timeout
        $em = $this->managerRegistry->getManager();
        $em->clear();
        $em->getConnection()->close();
    }

    public function terminate()
    {
        $em = $this->managerRegistry->getManager();
        $em->clear();
        $em->getConnection()->close();
    }

    public function setManagerRegistry(ManagerRegistry $registry)
    {
        $this->managerRegistry = $registry;
    }

    public function setORMPersister(ORMPersister $persister)
    {
        $this->persister = $persister;
    }

    public function setORMPurger(ORMPurger $purger)
    {
        $this->purger = $purger;
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
