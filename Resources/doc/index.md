Getting Started With TecnocreacionesAjaxFOSUserBundle
==================================

The TecnocreacionesAjaxFOSUserBundle is a bundle that provides authentication and registration with ajax, uses Symfony2 security component and requires FOSUserBundle this up and running for proper functioning.

## Prerequisites

This version of the bundle requires Symfony 2.1+.
[SecurityBundle](http://symfony.com/doc/current/book/security.html).
[FOSUserBundle 2.0+](https://github.com/FriendsOfSymfony/FOSUserBundle).
[FOSRestBundle](http://symfony.com/doc/current/bundles/FOSRestBundle/index.html).

## Installation

Installation is quick and simple, just 2 steps:

1. Download TecnocreacionesAjaxFOSUserBundle using composer
2. Enable the Bundle

### Step 1: Download FOSUserBundle using composer

Add FOSUserBundle in your composer.json:

```js
{
    "require": {
        "tecnocreaciones/ajax-fos-user-bundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update tecnocreaciones/ajax-fos-user-bundle
```

Composer will install the bundle to your project's `vendor/tecnocreaciones` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Tecnocreaciones\Bundle\AjaxFOSUserBundle\TecnocreacionesAjaxFOSUserBundle(),
    );
}
```

### Next Steps

Now that you have completed the basic installation and configuration of the
AjaxFOSUserBundle, you are ready to learn about more advanced features and usages
of the bundle.

The following documents are available:

- [Register a user, retrieve the password and log in from a single view](all_in_one_page.md)
