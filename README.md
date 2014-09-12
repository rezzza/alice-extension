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
            faker:
                locale: en_US #default
                providers: []
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
```feature
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
--------------------------

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

Adapters
--------

Currently we support :

* DoctrineORM
* ElasticSearch (through FOSElasticaBundle)

```yml
default:
    extensions:
        Rezzza\AliceExtension\Extension:
            adapters:
                elastica: ~
                orm: ~

```

For ElasticSearch we should use `mapping` config to indicate which ElasticSearch type alice should use to persist your mode:

```yml
default:
    extensions:
        Rezzza\AliceExtension\Extension:
            adapters:
                elastica:
                    index_service: fos_elastica.index.name_of_your_index
                    mapping:
                        myType: My\Fully\Model
                orm: ~

```

Then in your features you should use tag to specify which adapters alice should use :

```feature
@alice:elastica
Feature: Test My feature

    Background: Write fixtures
        Given I load "Vendor\My\Entity" fixtures where column "key" is the key:
            | key      | id | name |
            | fixture1 | 1  | jean |
            | fixture2 | 2  | marc |
```

Advanced Fixtures
-----------------

Fixtures can be managed through the configuration.

```yml
default:
    extensions:
        Rezzza\AliceExtension\Extension:
            fixtures:
                default: [users, products] # could be scalar if you want only one => users
                key_paths:
                    users: /src/path/to/your/fixtures.yml
                    products: /src/path/to/your/fixtures.yml
```

With this kind of configuration, when you'll call step below, it'll load **default** fixtures (**users** and **products** in this example).

```
Given I load "Acme\Bundle\Entity\User" fixtures where column "key" is the key:
    | key                  | emailAddress     | password |
    | user1 (extends user) | chuck@norris.com | password |
```

You are able to load fixtures manually:

```
Given I load "default" fixtures   # will load users AND products
Given I load "users" fixtures     # will load users
Given I load "products" fixtures  # will load products
```

Of course, fixtures are loaded once.


Faker Providers
---------------

Some providers are available on AliceExtension:

- NullProvider: `<null>`
- FixedDateTimeProvider: `<fixedDateTime("+1 hour")>`

You can add them (or your own) easily in behat.yml configuration:

```yml
default:
    extensions:
        Rezzza\AliceExtension\Extension:
            .....
            faker:
                locale: en_US #default
                providers:
                    - \Rezzza\AliceExtension\Providers\NullProvider
                    - \Rezzza\AliceExtension\Providers\FixedDateTimeProvider
                    - \Acme\Providers\YourOwnProvider
```


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
