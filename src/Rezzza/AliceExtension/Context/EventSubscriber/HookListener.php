<?php

namespace Rezzza\AliceExtension\Context\EventSubscriber;

use Behat\Behat\Event\FeatureEvent;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Behat\Event\OutlineExampleEvent;
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
           'beforeFeature',
           'beforeScenario',
           'beforeOutlineExample'
        );

        return array_combine($events, $events);
    }

    /**
     * Listens to "feature.before" event.
     *
     * @param \Behat\Behat\Event\FeatureEvent $event
     */
    public function beforeFeature(FeatureEvent $event)
    {
        if ('feature' !== $this->lifetime) {
            return;
        }

        $this->executor->purge();
    }

    /**
     * Listens to "scenario.before" event.
     *
     * @param \Behat\Behat\Event\ScenarioEvent $event
     */
    public function beforeScenario(ScenarioEvent $event)
    {
        if ('scenario' !== $this->lifetime) {
            return;
        }

        $this->executor->purge();
    }

    /**
     * Listens to "outline.example.before" event.
     *
     * @param \Behat\Behat\Event\OutlineExampleEvent $event
     */
    public function beforeOutlineExample(OutlineExampleEvent $event)
    {
        if ('scenario' !== $this->lifetime) {
            return;
        }

        $this->executor->purge();
    }
}
