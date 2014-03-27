<?php

namespace Rezzza\AliceExtension\Alice\Fixture;

use Doctrine\Common\EventSubscriber;
use Doctrine\Fixture\Event\FixtureEvent;
use Doctrine\Fixture\Event\ImportFixtureEventListener;

use Rezzza\AliceExtension\Alice\AliceFixture;
use Rezzza\AliceExtension\Alice\AliceFixtures;

class AliceFixturesEventSubscriber implements EventSubscriber, ImportFixtureEventListener
{
    private $fixtures;

    public function __construct(AliceFixtures $fixtures)
    {
        $this->fixtures = $fixtures;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            ImportFixtureEventListener::IMPORT
        );
    }

    /**
     * {@inheritdoc}
     */
    public function import(FixtureEvent $event)
    {
        $fixture = $event->getFixture();

        if ( ! ($fixture instanceof AliceFixture)) {
            return;
        }

        $fixture->setAliceFixtures($this->fixtures);
    }
}

