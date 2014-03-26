<?php

namespace Rezzza\AliceExtension\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Gherkin\Node\TableNode;

use Rezzza\AliceExtension\Alice\AliceAwareInterface;
use Rezzza\AliceExtension\Alice\AliceLoader;
use Rezzza\AliceExtension\Doctrine\ORMInitializer;

class AliceContext extends BehatContext implements AliceAwareInterface
{
    private $loader;

    public function setLoader(AliceLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @Given /^I load "(?P<className>[^"]*)" fixtures where column "(?P<columnKey>[^"]*)" is the key:$/
     */
    public function iLoadFixtures($className, $columnKey, TableNode $table)
    {
        $this->loader->load(
            $className,
            $columnKey,
            $table->getHash()
        );
    }

    /**
     * Reload fixtures between each scenario
     *
     * @AfterScenario
     */
    public function purgeORM(ScenarioEvent $event)
    {
        $this->loader->purge();
    }
}
