<?php

namespace Rezzza\AliceExtension\Adapter\Guzzle;

class GuzzleSubscriberFactory
{
    private $endpoint;

    public function __construct(Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function create()
    {
        return new EventSubscriber($this->endpoint);
    }
}
