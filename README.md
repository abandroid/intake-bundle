Intake Bundle
=============

*By [endroid](http://endroid.nl/)*

[![Latest Stable Version](http://img.shields.io/packagist/v/endroid/intake-bundle.svg)](https://packagist.org/packages/endroid/intake-bundle)
[![Build Status](http://img.shields.io/travis/endroid/EndroidIntakeBundle.svg)](http://travis-ci.org/endroid/EndroidIntakeBundle)
[![Latest Stable Version](https://poser.pugx.org/endroid/intake-bundle/v/stable.png)](https://packagist.org/packages/endroid/intake-bundle)
[![Total Downloads](http://img.shields.io/packagist/dt/endroid/intake-bundle.svg)](https://packagist.org/packages/endroid/intake-bundle)
[![License](http://img.shields.io/packagist/l/endroid/intake-bundle.svg)](https://packagist.org/packages/endroid/intake-bundle)

Allows creation, administration and submission of intakes to determine if a
user masters a language at a specific level.

[![knpbundles.com](http://knpbundles.com/endroid/EndroidIntakeBundle/badge-short)](http://knpbundles.com/endroid/EndroidIntakeBundle)

## Requirements

* Symfony

## Installation

Use [Composer](https://getcomposer.org/) to install the bundle.

``` bash
$ composer require endroid/intake-bundle
```

Then enable the bundle via the kernel.

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Endroid\Bundle\Endroid\Bundle\IntakeBundle\EndroidIntakeBundle(),
    );
}
```

## Routing

Add the following section to your routing to be able to visit intake URLs.

``` yml
EndroidIntakeBundle:
    resource: "@EndroidIntakeBundle/Controller/"
    type:     annotation
    prefix:   /intake
```

## Versioning

Version numbers follow the MAJOR.MINOR.PATCH scheme. Backwards compatible
changes will be kept to a minimum but be aware that these can occur. Lock
your dependencies for production and test your code when upgrading.

## License

This bundle is under the MIT license. For the full copyright and license
information please view the LICENSE file that was distributed with this source code.
