<?php

namespace Rezzza\AliceExtension\Adapter\ORM;

use Nelmio\Alice\ORM\Doctrine as ORMPersister;

interface ORMPersistFixture
{
    public function setORMPersister(ORMPersister $persister);
}
