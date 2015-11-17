<?php

namespace Rezzza\AliceExtension\Adapter\ORM;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\EventSubscriber;
use Doctrine\Fixture\Event\FixtureEvent;
use Doctrine\Fixture\Event\ImportFixtureEventListener;
use Doctrine\Fixture\Event\PurgeFixtureEventListener;
use Doctrine\Fixture\Persistence\ManagerRegistryEventSubscriber;
use Nelmio\Alice\ORM\Doctrine as ORMPersister;
use Nelmio\Alice\ORMInterface;

use Rezzza\AliceExtension\Doctrine\ORMPurger;
use Rezzza\AliceExtension\Alice\EventListener\TerminateFixtureEventListener;

class ORMEventSubscriber extends ManagerRegistryEventSubscriber implements EventSubscriber,
                                                                           ImportFixtureEventListener,
                                                                           PurgeFixtureEventListener,
                                                                           TerminateFixtureEventListener
{
    private $persister;

    private $purger;

    public function __construct(ManagerRegistry $doctrine, ORMInterface $persister, ORMPurger $purger)
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
            TerminateFixtureEventListener::TERMINATE
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

    public function terminate(FixtureEvent $event)
    {
        // only to set the managerRegistry to ORMFixture... No other way for now
        parent::import($event);
    }
}

