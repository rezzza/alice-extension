<?php

namespace Rezzza\AliceExtension\Alice;

class InlineFixtures implements AliceFixtures
{
    private $keyName;

    private $data;

    public function __construct($keyName, array $data)
    {
        $this->keyName = $keyName;
        $this->data = $data;
    }

    public function load()
    {
        $rows = array();

        foreach ($this->data as $d) {
            $name = $d[$this->keyName];
            unset($d[$this->keyName]);
            $rows[$name] = $d;
        }

        return $rows;
    }
}
