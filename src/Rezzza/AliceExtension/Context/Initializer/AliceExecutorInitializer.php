<?php

namespace Rezzza\AliceExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;

use Rezzza\AliceExtension\Alice\AliceAwareInterface;
use Rezzza\AliceExtension\Alice\AliceFixturesExecutor;

class AliceExecutorInitializer implements ContextInitializer
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
    public function initializeContext(Context $context)
    {
        if (!$context instanceof AliceAwareInterface) {
            return;
        }

        $context->setExecutor($this->executor);
    }
}
