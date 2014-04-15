<?php

namespace Rezzza\AliceExtension\Alice\Tests\Units;

use \atoum\AtoumBundle\Test\Units;
use \Rezzza\AliceExtension\Alice\InlineFixtures as TestedClass;

class InlineFixtures extends Units\Test
{
    public function test_normalize_primitive_data()
    {
        $testedClass = new TestedClass('aa', 'bbb', array('ccc'));

        $resultA = $testedClass->normalize(array('abcd', ''));

        $this
                ->array($resultA)
                ->contains('abcd')
                ->contains('')
                ->hasSize(2)
        ;

        $resultB = $testedClass->normalize(array(1));
        $this
                ->array($resultB)
                ->contains(1)
                ->hasSize(1)
        ;

        $resultC = $testedClass->normalize(array());
        $this
                ->array($resultC)
                ->isEmpty()
        ;
    }

    public function test_normalize_json_data()
    {
        $testedClass = new TestedClass('aa', 'bbb', array('ccc'));

        $resultA = $testedClass->normalize(array('["a","b","c"]'));
        $this
                ->array($resultA)
        ;

        $this
                ->array($resultA[0])
                ->contains('a')
                ->contains('b')
                ->contains('c')
                ->hasSize(3)
        ;

        $resultB = $testedClass->normalize(array('{"a": 1, "b": "24"}'));

        $this
                ->array($resultB)
        ;

        $this
                ->array($resultB[0])
                ->hasKey('a')
                ->hasKey('b')
                ->contains(1)
                ->contains("24")
                ->hasSize(2)
        ;
    }

}
