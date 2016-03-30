<?php

namespace Rezzza\AliceExtension\Alice;

use Symfony\Component\Yaml\Yaml as YamlParser;

class InlineFixtures implements AliceFixtures
{
    private $className;

    private $keyName;

    private $data;

    public function __construct($className, $keyName, array $data)
    {
        $this->className = $className;
        $this->keyName = $keyName;
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function load()
    {
        $rows = array();

        foreach ($this->data as $d) {
            $name = $d[$this->keyName];
            unset($d[$this->keyName]);
            $rows[$name] = $this->normalize($d);
        }

        return array($this->className => $rows);
    }

    public function normalize($data)
    {
        $result = array();

        foreach ($data as $key => $value) {
            // Always parse with Yaml to support array, true, false and null values
            $result[$key] = YamlParser::parse($value);
        }

        return $result;
    }
}
