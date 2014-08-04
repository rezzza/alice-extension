<?php

namespace Rezzza\AliceExtension\Adapter\ORM;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\EventSubscriber;
use Doctrine\Fixture\Event\FixtureEvent;
use Doctrine\Fixture\Event\ImportFixtureEventListener;
use Doctrine\Fixture\Event\PurgeFixtureEventListener;
use Doctrine\Fixture\Persistence\ManagerRegistryEventSubscriber;
use Nelmio\Alice\ORM\Doctrine as ORMPersister;

use Rezzza\AliceExtension\Doctrine\ORMPurger;

class ORMEventSubscriber extends ManagerRegistryEventSubscriber implements EventSubscriber,
                                                                           ImportFixtureEventListener,
                                                                           PurgeFixtureEventListener
{
    private $persister;

    private $purger;

    public function __construct(ManagerRegistry $doctrine, ORMPersister $persister, ORMPurger $purger)
    {
        parent::__construct($doctrine);
        $this->persister = $persister;
        $this->purger = $purger;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            ImportFixtureEventListener::IMPORT,
            PurgeFixtureEventListener::PURGE,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function import(FixtureEvent $event)
    {
        parent::import($event);
        $fixture = $event->getFixture();

        if (false === $fixture instanceof ORMPersistFixture) {
            return;
        }

        $fixture->setORMPersister($this->persister);
    }

    public function purge(FixtureEvent $event)
    {
        parent::purge($event);

        $fixture = $event->getFixture();

        if (false === $fixture instanceof ORMResetFixture) {
            return;
        }

        $fixture->setORMPurger($this->purger);
    }
}

