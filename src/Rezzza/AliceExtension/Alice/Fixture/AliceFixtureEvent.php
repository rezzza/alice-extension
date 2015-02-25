<?php

namespace Rezzza\AliceExtension\Alice\Fixture;

use Doctrine\Common\EventArgs;
use Doctrine\Fixture\Configuration;

class AliceFixtureEvent extends EventArgs
{
    private $configuration;

    private $fixturesClass;

    public function __construct(Configuration $configuration, array $fixturesClass)
    {
        $this->configuration = $configuration;
        $this->fixturesClass = $fixturesClass;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function getFixturesClass()
    {
        return $this->fixturesClass;
    }
}
