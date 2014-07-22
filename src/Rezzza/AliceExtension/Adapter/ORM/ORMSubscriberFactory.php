<?php

namespace Rezzza\AliceExtension\Adapter\ORM;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Fixture\Persistence\ManagerRegistryEventSubscriber;

class ORMSubscriberFactory
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function create()
    {
        return new ManagerRegistryEventSubscriber($this->doctrine);
    }
}
