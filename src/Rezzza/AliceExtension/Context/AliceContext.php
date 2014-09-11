<?php

namespace Rezzza\AliceExtension\Context;

use Behat\Behat\Context\Context as BehatContext;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Behat\Event\FeatureEvent;
use Behat\Gherkin\Node\TableNode;

use Rezzza\AliceExtension\Alice\AliceAwareInterface;
use Rezzza\AliceExtension\Alice\AliceFixturesExecutor;
use Rezzza\AliceExtension\Doctrine\ORMInitializer;

class AliceContext implements BehatContext, AliceAwareInterface
{
    private $executor;

    public function setExecutor(AliceFixturesExecutor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @Given /^I load "(?P<fixtureName>[^"]*)" fixtures$/
     */
    public function iLoadSpecificFixtureFile($fixtureName)
    {
        $this->executor->importFixtureKeyPath($fixtureName);
    }

    /**
     * @Given /^I load "(?P<className>[^"]*)" fixtures where column "(?P<columnKey>[^"]*)" is the key:$/
     */
    public function iLoadFixtures($className, $columnKey, TableNode $table)
    {
        $this->executor->import(
            $className,
            $columnKey,
            $table->getHash()
        );
    }
}
