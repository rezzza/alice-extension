<?php

namespace Rezzza\AliceExtension\Alice;

interface AliceAwareInterface
{
    public function setExecutor(AliceFixturesExecutor $loader);
}
