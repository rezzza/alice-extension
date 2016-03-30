<?php

namespace Rezzza\AliceExtension\Alice;

use Nelmio\Alice\Fixtures\Loader;

interface AliceFixture
{
    public function setAliceFixtures(AliceFixtures $fixtures);

    public function setAlice(Loader $alice);
}
