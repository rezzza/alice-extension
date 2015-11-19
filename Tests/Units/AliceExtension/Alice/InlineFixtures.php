<?php

namespace Rezzza\AliceExtension\Alice\Tests\Units;

use mageekguy\atoum;
use \Rezzza\AliceExtension\Alice\InlineFixtures as TestedClass;

class InlineFixtures extends atoum\test
{
    public function test_normalize_primitive_data()
    {
        $this->given(
                $testedClass = new TestedClass('aa', 'bbb', array('ccc'))
            )->then(
                $result = $testedClass->normalize(array('abcd', ''))
            )
            ->array($result)
            ->contains('abcd')
            ->contains('')
            ->hasSize(2)
        ;

        $this->given(
                $testedClass = new TestedClass('aa', 'bbb', array('ccc'))
            )->then(
                $result = $testedClass->normalize(array(1))
            )
            ->array($result)
            ->contains(1)
            ->hasSize(1)
        ;

        $this->given(
                $testedClass = new TestedClass('aa', 'bbb', array('ccc'))
            )->then(
                $result = $testedClass->normalize(array())
            )
            ->array($result)
            ->isEmpty()
        ;
    }

    public function test_normalize_yaml_notation()
    {
        $this->given(
                $testedClass = new TestedClass('aa', 'bbb', array('ccc'))
            )->then(
                $result = $testedClass->normalize(array('[1,2,"c"]'))
            )
            ->array($result[0])
            ->contains(1)
            ->contains(2)
            ->contains('c')
            ->hasSize(3)
        ;

        $this->given(
                $testedClass = new TestedClass('aa', 'bbb', array('ccc'))
            )->then(
                $result = $testedClass->normalize(array('{a: 1, b: 24,c: null}'))
            )
            ->array($result)
            ->array($result[0])
            ->hasKey('a')
            ->hasKey('b')
            ->contains(1)
            ->contains("24")
            ->contains(null)
            ->hasSize(3)
        ;

        $this->given(
                $testedClass = new TestedClass('aa', 'bbb', array('ccc'))
            )->then(
                $result = $testedClass->normalize(array('["a","b","c"]'))
            )
            ->array($result[0])
            ->contains('a')
            ->contains('b')
            ->contains('c')
            ->hasSize(3)
        ;

        $this->given(
                $testedClass = new TestedClass('aa', 'bbb', array('ccc'))
            )->then(
                $result = $testedClass->normalize(array('{"a": 1, "b": "24"}'))
            )
            ->array($result)
            ->array($result[0])
            ->hasKey('a')
            ->hasKey('b')
            ->contains(1)
            ->contains("24")
            ->hasSize(2)
        ;
    }

    public function test_normalize_json_syntax_error()
    {
        $this->given(
                $testedClass = new TestedClass('aa', 'bbb', array('ccc'))
            )->exception(function() use ($testedClass) {
                $testedClass->normalize(array('{"dzqd"}'));
            })
            ->isInstanceOf('Symfony\Component\Yaml\Exception\ParseException')
            ->message
            ->contains('Unexpected characters');
    }
}
