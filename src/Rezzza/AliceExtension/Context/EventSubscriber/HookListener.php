<?php

namespace Rezzza\AliceExtension\Context\EventSubscriber;

use Behat\Behat\Event\FeatureEvent;
use Behat\Behat\Event\ScenarioEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Rezzza\AliceExtension\Alice\AliceFixturesExecutor;

class HookListener implements EventSubscriberInterface
{
    private $executor;

    private $lifetime;

    public function __construct(AliceFixturesExecutor $executor, $lifetime)
    {
        $this->executor = $executor;
        $this->lifetime = $lifetime;
    }

    public static function getSubscribedEvents()
    {
        $events = array(
           'afterFeature',
           'afterScenario'
        );

        return array_combine($events, $events);
    }

    /**
     * Listens to "feature.after" event.
     *
     * @param \Behat\Behat\Event\FeatureEvent $event
     */
    public function afterFeature(FeatureEvent $event)
    {
        if ('feature' !== $this->lifetime) {
            return;
        }

        $this->executor->purge();
    }

    /**
     * Listens to "scenario.after" event.
     *
     * @param \Behat\Behat\Event\ScenarioEvent $event
     */
    public function afterScenario(ScenarioEvent $event)
    {
        if ('scenario' !== $this->lifetime) {
            return;
        }

        $this->executor->purge();
    }
}
