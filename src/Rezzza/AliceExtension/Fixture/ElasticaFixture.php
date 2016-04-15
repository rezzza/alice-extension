<?php

namespace Rezzza\AliceExtension\Fixture;

use Doctrine\Fixture\Fixture;
use FOS\ElasticaBundle\Index\Resetter;
use Nelmio\Alice\Fixtures\Loader;

use Rezzza\AliceExtension\Alice\AliceFixture;
use Rezzza\AliceExtension\Alice\AliceFixtures;
use Rezzza\AliceExtension\Adapter\Elastica\ElasticaPersistFixture;
use Rezzza\AliceExtension\Adapter\Elastica\ElasticaResetFixture;
use Rezzza\AliceExtension\Adapter\Elastica\Persister;

class ElasticaFixture implements ElasticaPersistFixture, ElasticaResetFixture, AliceFixture, Fixture
{
    private $persister;

    private $resetter;

    private $fixtures;

    private $alice;

    public function import()
    {
        $this->alice->changePersister($this->persister);
        $this->alice->load($this->fixtures->load());
    }

    public function purge()
    {
        $this->resetter->resetAllIndexes();
    }

    public function setObjetPersister(Persister $persister)
    {
        $this->persister = $persister;
    }

    public function setIndexResetter(Resetter $resetter)
    {
        $this->resetter = $resetter;
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
