<?php

namespace Rezzza\AliceExtension\Alice\Fixture;

use Doctrine\Common\EventSubscriber;
use Doctrine\Fixture\Event\FixtureEvent;
use Doctrine\Fixture\Event\ImportFixtureEventListener;
use Nelmio\Alice\Fixtures\Loader;

use Rezzza\AliceExtension\Alice\AliceFixture;
use Rezzza\AliceExtension\Alice\AliceFixtures;

class AliceFixturesEventSubscriber implements EventSubscriber, ImportFixtureEventListener
{
    /** @var AliceFixtures */
    private $fixtures;

    /** @var Loader */
    private $alice;

    public function __construct(Loader $alice, AliceFixtures $fixtures)
    {
        $this->alice = $alice;
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
        $fixture->setAlice($this->alice);
    }
}

