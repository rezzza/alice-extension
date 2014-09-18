<?php

use Doctrine\Common\EventSubscriber as DoctrineEventSubscriber;
use Doctrine\Fixture\Event\FixtureEvent;
use Doctrine\Fixture\Event\ImportFixtureEventListener;
use Doctrine\Fixture\Event\PurgeFixtureEventListener;

namespace Rezzza\AliceExtension\Adapter\Guzzle;

class EventSubscriber implements DoctrineEventSubscriber, ImportFixtureEventListener, PurgeFixtureEventListener
{
    private $endpoint;

    public function __construct(Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;
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

        if (false === $fixture instanceof GuzzleFixture) {
            return;
        }

        // TODO
    }

    public function purge(FixtureEvent $event)
    {
        $fixture = $event->getFixture();

        if (false === $fixture instanceof GuzzleFixture) {
            return;
        }

        // TODO
    }
}
