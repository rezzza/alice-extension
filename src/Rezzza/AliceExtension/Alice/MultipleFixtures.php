<?php

namespace Rezzza\AliceExtension\Alice;

class MultipleFixtures implements AliceFixtures
{
    private $fixtureRows;

    private $className;

    public function __construct($className, array $fixtureRows)
    {
        $this->className = $className;
        $this->fixtureRows = $fixtureRows;
    }

    public function load()
    {
        $results = array();

        foreach ($this->fixtureRows as $rows) {
            $results = array_merge($results, $rows->load());
        }

        return array($this->className => $results);
    }
}
