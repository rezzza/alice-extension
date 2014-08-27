<?php

namespace Rezzza\AliceExtension\Adapter\Elastica;

use Doctrine\Common\EventSubscriber;
use Doctrine\Fixture\Event\FixtureEvent;
use Doctrine\Fixture\Event\ImportFixtureEventListener;
use Doctrine\Fixture\Event\PurgeFixtureEventListener;
use FOS\ElasticaBundle\Index\Resetter;
use FOS\ElasticaBundle\Persister\ObjectPersister;

use Rezzza\AliceExtension\Fixture\ElasticaFixture;

class ElasticaEventSubscriber implements EventSubscriber, ImportFixtureEventListener, PurgeFixtureEventListener
{
    private $persister;

    private $resetter;

    public function __construct(Persister $persister, Resetter $resetter)
    {
        $this->persister = $persister;
        $this->resetter = $resetter;
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
        $fixture = $event->getFixture();

        if (false === $fixture instanceof ElasticaFixture) {
            return;
        }

        $fixture->setObjetPersister($this->persister);
    }

    public function purge(FixtureEvent $event)
    {
        $fixture = $event->getFixture();

        if (false === $fixture instanceof ElasticaFixture) {
            return;
        }

        $fixture->setIndexResetter($this->resetter);
    }
}

