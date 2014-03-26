<?php

namespace Rezzza\AliceExtension\Alice;

use Symfony\Component\Yaml\Yaml as YamlParser;
use Rezzza\AliceExtension\Doctrine\ORMPurger;

class AliceLoader
{
    protected $fixtures;

    protected $em;

    public function __construct($fixtures, $em)
    {
        $this->fixtures = $fixtures;
        $this->em = $em;
    }

    public function load($className, $columnName, $data)
    {
        $loader = new \Nelmio\Alice\Loader\Base;
        $data = array_merge(
            $this->loadDefaultFromYaml($className, $this->fixtures),
            $this->formatData($data, $columnName)
        );

        $objects = $loader->load(array($className => $data));

        $persister = new \Nelmio\Alice\ORM\Doctrine($this->em);
        $persister->persist($objects);
    }

    public function loadDefaultFromYaml($className, $file)
    {
        // Copy from Nelmio\Alice\Loader\Yaml to just process yaml
        ob_start();
        $loader = $this;

        // isolates the file from current context variables and gives
        // it access to the $loader object to inline php blocks if needed
        $includeWrapper = function () use ($file, $loader) {
            return include $file;
        };
        $data = $includeWrapper();

        if (1 === $data) {
            // include didn't return data but included correctly, parse it as yaml
            $yaml = ob_get_clean();
            $data = YamlParser::parse($yaml);
        } else {
            // make sure to clean up if theres a failure
            ob_end_clean();
        }

        if (!is_array($data)) {
            throw new \UnexpectedValueException('Yaml files must parse to an array of data');
        }

        if (!array_key_exists($className, $data)) {
            throw new \InvalidArgumentException(
                sprintf('Cannot found class "%s" in file %s', $className, $file)
            );
        }

        return $data[$className];
    }

    public function purge()
    {
        $purger = new ORMPurger($this->em);
        $purger->purge();
    }

    protected function formatData($data, $nameColumn)
    {
        $result = array();

        foreach ($data as $d) {
            $name = $d[$nameColumn];
            unset($d[$nameColumn]);
            $result[$name] = $d;
        }

        return $result;
    }
}
