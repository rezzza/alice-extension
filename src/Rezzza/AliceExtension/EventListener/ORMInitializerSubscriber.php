<?php

namespace Rezzza\AliceExtension\EventListener;

use Behat\Behat\Event\SuiteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Rezzza\AliceExtension\Doctrine\ORMInitializer;

class ORMInitializerSubscriber implements EventSubscriberInterface
{
    private $initializer;

    public static function getSubscribedEvents()
    {
        return array(
            'beforeSuite' => 'initDatabase'
        );
    }

    public function __construct(ORMInitializer $initializer)
    {
        $this->initializer = $initializer;
    }

    public function initDatabase(SuiteEvent $event)
    {
        $this->initializer->initDatabase();
    }
}
