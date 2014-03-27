<?php

namespace Rezzza\AliceExtension\Context\Initializer;

use Behat\Behat\Context\ContextInterface;
use Behat\Behat\Context\Initializer\InitializerInterface;

use Rezzza\AliceExtension\Alice\AliceAwareInterface;
use Rezzza\AliceExtension\Alice\AliceFixturesExecutor;

class AliceExecutorInitializer implements InitializerInterface
{
    private $executor;

    public function __construct(AliceFixturesExecutor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * Checks if initializer supports provided context.
     *
     * @param ContextInterface $context
     *
     * @return Boolean
     */
    public function supports(ContextInterface $context)
    {
        return $context instanceof AliceAwareInterface;
    }

    /**
     * Initializes provided context.
     *
     * @param ContextInterface $context
     */
    public function initialize(ContextInterface $context)
    {
        $context->setExecutor($this->executor);
    }
}
