<?php

namespace Rezzza\AliceExtension\Alice;

use Nelmio\Alice\Loader\Base as AliceLoader;

interface AliceFixture
{
    public function setAliceFixtures(AliceFixtures $fixtures);

    public function setAlice(AliceLoader $alice);
}
