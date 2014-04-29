<?php

namespace Rezzza\AliceExtension\Alice;

use Nelmio\Alice\ProcessorInterface;

interface ProcessorRegistry
{
    public function get($className);

    public function has($className);
}
