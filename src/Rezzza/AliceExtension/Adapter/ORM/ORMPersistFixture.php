<?php

namespace Rezzza\AliceExtension\Adapter\ORM;

use Nelmio\Alice\Persister\Doctrine as ORMPersister;

interface ORMPersistFixture
{
    public function setORMPersister(ORMPersister $persister);
}
