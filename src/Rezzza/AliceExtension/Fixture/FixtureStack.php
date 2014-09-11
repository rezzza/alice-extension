<?php

namespace Rezzza\AliceExtension\Fixture;

class FixtureStack
{
    private $defaults;
    private $keyPaths;
    private $stack;

    CONST DEFAULT_KEY = 'default';

    public function __construct(array $defaults = array(), array $keyPaths = array())
    {
        $this->defaults = $defaults;
        $this->keyPaths = $keyPaths;

        $this->reset();
    }

    public function unstack($key)
    {
        if ($key === self::DEFAULT_KEY) {
            $data = array();
            foreach ($this->defaults as $keyPath) {
                $data = array_merge($data, $this->unstack($keyPath));
            }

            return $data;
        }

        if (!array_key_exists($key, $this->stack)) {
            return array();
        }

        $data = array($this->stack[$key]);
        unset($this->stack[$key]);

        return $data;
    }

    public function reset()
    {
        $this->stack = $this->keyPaths;
    }
}
