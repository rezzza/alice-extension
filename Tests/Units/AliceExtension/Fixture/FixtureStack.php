<?php

namespace Rezzza\AliceExtension\Fixture\Tests\Units;

use mageekguy\atoum;
use Rezzza\AliceExtension\Fixture\FixtureStack as TestedClass;

class FixtureStack extends atoum\test
{
    public function test_without_cfg()
    {
        $this->given(
            $stack = new TestedClass()
        )
        ->array($stack->unstack(TestedClass::DEFAULT_KEY))->isEmpty()
        ->array($stack->unstack('unknown_key'))->isEmpty()
        ;
    }

    public function test_no_default()
    {
        $this->given(
            $stack = new TestedClass(array(), array(
                'key' => 'value',
                'key2' => 'value2',
            ))
        )
        ->array($stack->unstack(TestedClass::DEFAULT_KEY))->isEmpty()
        ->array($stack->unstack('unknown_key'))->isEmpty()
        ->array($stack->unstack('key'))->isEqualTo(array('value'))
        ->array($stack->unstack('key'))->isEmpty()
        ->array($stack->unstack('key2'))->isEqualTo(array('value2'))
        ->array($stack->unstack('key2'))->isEmpty()
        ;
    }

    public function test_with_default()
    {
        $this->given(
            $stack = new TestedClass(array(
                'key',
            ), array(
                'key' => 'value',
            ))
        )
        ->array($stack->unstack(TestedClass::DEFAULT_KEY))->isEqualTo(array('value'))
        ->array($stack->unstack('key'))->isEmpty()
        ;
    }

    public function test_with_bad_default()
    {
        $this->given(
            $stack = new TestedClass(array(
                'bad_key',
            ), array(
                'key' => 'value',
            ))
        )
        ->array($stack->unstack(TestedClass::DEFAULT_KEY))->isEmpty()
        ->array($stack->unstack('key'))->isEqualTo(array('value'))
        ;
    }

    public function test_with_multiple_default()
    {
        $this->given(
            $stack = new TestedClass(array(
                'key', 'key2',
            ), array(
                'key'  => 'value',
                'key2' => 'value2',
            ))
        )
        ->array($stack->unstack(TestedClass::DEFAULT_KEY))->isEqualTo(array('value', 'value2'))
        ->array($stack->unstack('key'))->isEmpty()
        ->array($stack->unstack('key2'))->isEmpty()
        ;
    }

    public function test_with_multiple_default_but_already_unstacked()
    {
        $this->given(
            $stack = new TestedClass(array(
                'key', 'key2',
            ), array(
                'key'  => 'value',
                'key2' => 'value2',
            ))
        )
        ->array($stack->unstack('key'))->isEqualTo(array('value'))
        ->array($stack->unstack(TestedClass::DEFAULT_KEY))->isEqualTo(array('value2'))
        ->array($stack->unstack('key2'))->isEmpty()
        ;
    }
}
