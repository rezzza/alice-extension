<?php

namespace Rezzza\AliceExtension\Alice;

interface AliceFixture
{
    public function setAliceFixtures(AliceFixtures $fixtures);

    public function setAlice(Loader $alice);
}
