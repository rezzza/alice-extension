<?php

namespace Rezzza\AliceExtension\Fixture;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Fixture\Persistence\ManagerRegistryFixture;
use Nelmio\Alice\Loader\Base as AliceLoader;

use Rezzza\AliceExtension\Alice\AliceFixture;
use Rezzza\AliceExtension\Alice\AliceFixtures;
use Rezzza\AliceExtension\Doctrine\ORMPurger;

class ORMFixture implements ManagerRegistryFixture, AliceFixture
{
    private $managerRegistry;

    private $fixtures;

    private $alice;

    public function import()
    {
        $em = $this->managerRegistry->getManager();

        $this->alice
            ->changePersister(new \Nelmio\Alice\ORM\Doctrine($em))
            ->load($this->fixtures->load())
        ;

        // Ensure to close the connection to avoid mysql timeout
        $em->getConnection()->close();
    }

    public function purge()
    {
        $em = $this->managerRegistry->getManager();
        $purger = new ORMPurger($em);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $purger->purge();

        // Ensure to close the connection to avoid mysql timeout
        $em->getConnection()->close();
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
