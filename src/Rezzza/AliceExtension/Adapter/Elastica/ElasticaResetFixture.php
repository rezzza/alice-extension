<?php

namespace Rezzza\AliceExtension\Adapter\Elastica;

use FOS\ElasticaBundle\Index\Resetter;

interface ElasticaResetFixture
{
    public function setIndexResetter(Resetter $resetter);
}
