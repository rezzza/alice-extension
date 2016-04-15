<?php

namespace Rezzza\AliceExtension\Fixture;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Fixture\Persistence\ManagerRegistryFixture;
use Nelmio\Alice\Persister\Doctrine as ORMPersister;

use Rezzza\AliceExtension\Alice\AliceFixture;
use Rezzza\AliceExtension\Alice\AliceFixtures;
use Rezzza\AliceExtension\Alice\Loader;
use Rezzza\AliceExtension\Doctrine\ORMPurger;
use Rezzza\AliceExtension\Adapter\ORM\ORMPersistFixture;
use Rezzza\AliceExtension\Adapter\ORM\ORMResetFixture;

class ORMFixture implements ManagerRegistryFixture, AliceFixture, ORMPersistFixture, ORMResetFixture
{
    /** @var ManagerRegistry */
    private $managerRegistry;

    /** @var ORMPersister */
    private $persister;

    /** @var ORMPurger */
    private $purger;

    /** @var AliceFixtures */
    private $fixtures;

    /** @var Loader */
    private $alice;

    public function import()
    {
        $this->alice->setPersister($this->persister);
        $this->alice->load($this->fixtures->load());

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

    public function setAlice(Loader $alice)
    {
        $this->alice = $alice;
    }
}
