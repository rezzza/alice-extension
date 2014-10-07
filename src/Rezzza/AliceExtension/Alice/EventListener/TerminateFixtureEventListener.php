<?php

namespace Rezzza\AliceExtension\Alice\EventListener;

use Doctrine\Fixture\Event\FixtureEvent;

interface TerminateFixtureEventListener
{
    const TERMINATE = 'terminate';

    function terminate(FixtureEvent $event);
}
