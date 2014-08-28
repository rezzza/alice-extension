<?php

use mageekguy\atoum\asserter;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Behat context class.
 */
class FeatureContext implements SnippetAcceptingContext, KernelAwareContext
{
    private $kernel;

    public function __construct()
    {
        $this->asserter = new asserter\generator;
    }

    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Then /^I should have (\d+) entity "([^"]*)" stored$/
     */
    public function iShouldHaveNEntityStored($nb, $className)
    {
        $repository = $this->getORMRepositoryFor($className);
        $entities = $repository->findAll();

        $this->asserter
            ->integer(count($entities))
                ->isEqualTo((int) $nb)
        ;
    }

    /**
     * @Then entity :className with primary key :id should have :method method equals to :value
     */
    public function entityWithPrimaryKeyShouldHavePropertyEqualsTo($className, $id, $method, $value)
    {
        $repository = $this->getORMRepositoryFor($className);
        $entity = $repository->find($id);

        $this->asserter
            ->variable($entity->{$method}())
                ->isEqualTo($value)
        ;
    }

    private function getORMRepositoryFor($className)
    {
        return $this->kernel->getContainer()->get('doctrine')->getManager()->getRepository($className);
    }
}
