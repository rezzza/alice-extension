<?php

namespace Rezzza\AliceExtension\Adapter\Guzzle;

interface GuzzleResetFixture
{
    public function setFixturesResetter(Endpoint $resetter);
}

