<?php

namespace Rezzza\AliceExtension\Alice\Fixture;

use Doctrine\Common\EventSubscriber;
use Doctrine\Fixture\Loader\ClassLoader;
use Doctrine\Fixture\Filter\ChainFilter;
use Doctrine\Fixture\Event\FixtureEvent;
use Rezzza\AliceExtension\Alice\EventListener\AliceLoadFixturesEventListener;
use Rezzza\AliceExtension\Alice\EventListener\TerminateFixtureEventListener;

class AliceExecutorEventSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            AliceLoadFixturesEventListener::BULK_TERMINATE,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function bulkTerminate(AliceFixtureEvent $event)
    {
        $configuration = $event->getConfiguration();
        $eventManager = $configuration->getEventManager();
        $fixturesClass = $event->getFixturesClass();
        $fixturesList = $this->getFixtureList(
            $configuration,
            new ClassLoader($fixturesClass),
            new ChainFilter
        );

        foreach ($fixturesList as $fixture) {
            $eventManager->dispatchEvent(TerminateFixtureEventListener::TERMINATE, new FixtureEvent($fixture));

            $fixture->terminate();
        }
    }

    private function getFixtureList($configuration, ClassLoader $loader, ChainFilter $filter)
    {
        $calculatorFactory = $configuration->getCalculatorFactory();
        $fixtureList = array_filter(
            $loader->load(),
            function ($fixture) use ($filter) {
                return $filter->accept($fixture);
            }
        );

        $calculator = $calculatorFactory->getCalculator($fixtureList);

        return $calculator->calculate($fixtureList);
    }
}
