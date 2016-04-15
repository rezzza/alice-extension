<?php

namespace Rezzza\AliceExtension\Alice;

class MultipleFixtures implements AliceFixtures
{
    /** @var AliceFixtures[] */
    private $fixtureRows;

    public function __construct(array $fixtureRows)
    {
        $this->fixtureRows = $fixtureRows;
    }

    /**
     * @inheritdoc
     */
    public function load()
    {
        $results = array();

        foreach ($this->fixtureRows as $rows) {
            $results = array_merge_recursive($results, $rows->load());
        }

        return $results;
    }
}
