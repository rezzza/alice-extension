<?php

namespace Rezzza\AliceExtension\Adapter\ORM;

use Doctrine\Common\Persistence\ManagerRegistry;
use Nelmio\Alice\ORM\Doctrine as ORMPersister;
use Rezzza\AliceExtension\Doctrine\ORMPurger;

class ORMSubscriberFactory
{
    private $doctrine;

    private $persister;

    private $purger;

    public function __construct(ManagerRegistry $doctrine, ORMPersister $persister, ORMPurger $purger)
    {
        $this->doctrine = $doctrine;
        $this->persister = $persister;
        $this->purger = $purger;
    }

    public function create()
    {
        return new ORMEventSubscriber($this->doctrine, $this->persister, $this->purger);
    }
}
