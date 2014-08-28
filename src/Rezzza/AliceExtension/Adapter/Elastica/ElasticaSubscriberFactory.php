<?php

namespace Rezzza\AliceExtension\Adapter\Elastica;

use FOS\ElasticaBundle\Index\Resetter;

class ElasticaSubscriberFactory
{
    private $persister;

    private $resetter;

    public function __construct(Persister $persister, Resetter $resetter)
    {
        $this->persister = $persister;
        $this->resetter = $resetter;
    }

    public function create()
    {
        return new ElasticaEventSubscriber($this->persister, $this->resetter);
    }
}
