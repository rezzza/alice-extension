<?php

namespace Rezzza\AliceExtension\Fixture;

use Nelmio\Alice\Loader\Base as AliceLoader;

use Rezzza\AliceExtension\Adapter\Guzzle\Endpoint;
use Rezzza\AliceExtension\Adapter\Guzzle\GuzzlePersistFixture;
use Rezzza\AliceExtension\Adapter\Guzzle\GuzzleResetFixture;
use Rezzza\AliceExtension\Alice\AliceFixture;
use Rezzza\AliceExtension\Alice\AliceFixtures;

class GuzzleFixture implements GuzzlePersistFixture, GuzzleResetFixture, AliceFixture, Fixture
{
    private $endpoint;

    private $fixtures;

    private $alice;

    public function import()
    {
        $this->alice->changePersister($this->endpoint);
        $this->alice->load($this->fixtures->load());
    }

    public function purge()
    {
        $this->endpoint->resetFixtures();
    }

    public function setFixturesPersister(Endpoint $Endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function setFixturesResetter(Endpoint $Endpoint)
    {
        $this->endpoint = $endpoint;
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
