<?php

namespace Rezzza\AliceExtension\Adapter\ORM;

use Rezzza\AliceExtension\Doctrine\ORMPurger;

interface ORMResetFixture
{
    public function setORMPurger(ORMPurger $purger);
}
