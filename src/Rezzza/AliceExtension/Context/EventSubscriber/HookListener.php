<?php

namespace Rezzza\AliceExtension\Context\EventSubscriber;

use Behat\Behat\EventDispatcher\Event\BeforeFeatureTested;
use Behat\Behat\EventDispatcher\Event\FeatureTested;
use Behat\Behat\EventDispatcher\Event\BeforeScenarioTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Rezzza\AliceExtension\Alice\AliceFixturesExecutor;

class HookListener implements EventSubscriberInterface
{
    private $executor;

    private $lifetime;

    private $adapters;

    private $defaultAdapter;

    public function __construct(AliceFixturesExecutor $executor, $lifetime, array $adapters, $defaultAdapter = 'orm')
    {
        $this->executor = $executor;
        $this->lifetime = $lifetime;
        $this->adapters = $adapters;
        $this->defaultAdapter = $defaultAdapter;
    }

    public static function getSubscribedEvents()
    {
        return array(
           FeatureTested::BEFORE => 'beforeFeature',
           ScenarioTested::BEFORE => 'beforeScenario'
        );
    }

    /**
     * Listens to "feature.before" event.
     *
     * @param BeforeFeatureTested $event
     */
    public function beforeFeature(BeforeFeatureTested $event)
    {
        if ('feature' !== $this->lifetime) {
            return;
        }

        list($adapter, $fixtureClass) = $this->extractAdapterConfig($event->getFeature()->getTags());

        $this->executor->changeAdapter($adapter, $fixtureClass);
        $this->executor->purge();
    }

    /**
     * Listens to "scenario.before" event.
     *
     * @param BeforeScenarioTested $event
     */
    public function beforeScenario(BeforeScenarioTested $event)
    {
        if ('scenario' !== $this->lifetime) {
            return;
        }

        list($adapter, $fixtureClass) = $this->extractAdapterConfig($event->getFeature()->getTags());

        $this->executor->changeAdapter($adapter, $fixtureClass);
        $this->executor->purge();
    }


    /**
     * Listens to "outline.example.before" event.
     *
     * @param \Behat\Behat\Event\OutlineExampleEvent $event
     */
    /*public function beforeOutlineExample(OutlineExampleEvent $event)
    {
        if ('scenario' !== $this->lifetime) {
            return;
        }

        $this->executor->purge();
    }*/

    private function isAliceTag($tag)
    {
        return 'alice' === current(explode(':', $tag));
    }

    private function extractAdapterConfig($tags)
    {
        $adapter = $this->defaultAdapter;

        foreach ($tags as $tag) {
            if ($this->isAliceTag($tag)) {
                $adapter = $this->extractAdapterName($tag);
            }
        }

        if (!isset($this->adapters[$adapter])) {
            throw new \LogicException(sprintf('No adapter registred with name "%s" in alice-extension', $adapter));
        }

        return array(
            $adapter,
            $this->adapters[$adapter]
        );
    }

    private function extractAdapterName($tag)
    {
        $res = explode(':', $tag);

        return $res[1];
    }
}
