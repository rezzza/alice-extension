Alice Extension for Behat
=========================

[![Build Status](https://travis-ci.org/rezzza/alice-extension.svg?branch=master)](https://travis-ci.org/rezzza/alice-extension)

Make [Alice](https://github.com/nelmio/alice) work with [Behat](https://github.com/behat/behat).

You can import fixtures through a yaml file and from a behat step.


Installation
------------

Through Composer :

        $ composer require --dev "rezzza/alice-extension:~0.1"

Configure your behat.yml :
```yml
default:
    extensions:
        Rezzza\AliceExtension\Extension:
            fixtures: /path/to/your/fixtures.yml
            lifetime: (scenario|feature)
```

To write your `fixtures.yml` please report to [Alice documentation](https://github.com/nelmio/alice#creating-fixtures)

This extension need `Symfony2Extension` to work. Have a look to [its documentation](http://extensions.behat.org/symfony2/index.html)

Usage
-----

In your behat context you can activate `AliceContext`.

```php
<?php

namespace Vendor\My\Features;

use Behat\MinkExtension\Context\MinkContext;

use Rezzza\AliceExtension\Context\AliceContext;

class FeatureContext extends MinkContext
{
    public function __construct(array $parameters)
    {
        $this->useContext('alice', new AliceContext($parameters));
    }
}
?>
```

So you can write in your features :
```
Feature: Test My feature

    Background: Write fixtures
        Given I load "Vendor\My\Entity" fixtures where column "key" is the key:
            | key      | id | name |
            | fixture1 | 1  | jean |
            | fixture2 | 2  | marc |
```

If you use yaml file, you should consider put your default values in it thanks to [template inheritance](https://github.com/nelmio/alice#fixture-inheritance).

And use inline fixtures to override values you need.

Hook for specific entities
..................................
Sometimes you need to apply specific operations for objects persisted. You can do it through the Symfony2 Bundle packed with this extension.

Activate the bundle:
```php
<?php
/***/
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            /***/
            new Rezzza\AliceExtension\Symfony\Bundle\RezzzaAliceExtensionBundle()
            /***/
        );
    }
}
?>
```
Then in your Symfony2 app you will be able to build some Alice processors via service. It should extends `Nelmio\Alice\ProcessorInterface` and registred via the tag `alice_extension.processor`

Lifetime
--------
2 lifetime options are available.

* scenario : will reset fixtures after each scenario. You have to use **background** step to describe your fixtures
* feature : will reset fixtures after each feature. You have to use **scenario** step to describe your fixtures

FAQ
---
* *I want to use this with Doctrine ODM !*
* You should do a PR

* *I wanto to use this with PostgreSQL !*
* You should do a PR
