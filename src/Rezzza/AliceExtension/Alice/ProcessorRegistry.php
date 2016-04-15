<?php

namespace Rezzza\AliceExtension\Alice;

interface ProcessorRegistry
{
    /**
     * @param string $className
     * @return \Nelmio\Alice\ProcessorInterface[]
     */
    public function get($className);

    /**
     * @param string $className
     * @return bool
     */
    public function has($className);
}
