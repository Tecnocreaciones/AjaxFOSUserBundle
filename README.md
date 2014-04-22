TecnocreacionesAjaxFOSUserBundle
========================

The TecnocreacionesAjaxFOSUserBundle is a bundle that provides authentication and registration with ajax, uses Symfony2 security component and requires FOSUserBundle this up and running for proper functioning.

### Features include:

The following features these available when the request is made using Ajax (XMLHttpRequest):

- Log In.
- Register a new user.
- Send email to reset password.

Other features:

- Register a user, retrieve the password and log in from a single view.
- When making a request via ajax json response returns the correct HTTP status code.
- Flash messages are not saved in the session when the request is done via ajax, but returned as json.

**Note:** This bundle does *not* provide an authentication system but can
provide the user provider for the core [SecurityBundle](http://symfony.com/doc/current/book/security.html) and extending the functionality of [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle).

Documentation
-------------

The bulk of the documentation is stored in the `Resources/doc/index.md`
file in this bundle:

[Read the Documentation for master](https://github.com/Tecnocreaciones/AjaxFOSUserBundle/blob/master/Resources/doc/index.md)


Installation
------------

All the installation instructions are located in  the documentation.

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

About
-----

TecnocreacionesAjaxFOSUserBundle is a [tecnocreaciones](https://github.com/Tecnocreaciones) initiative Based [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle).
See also the list of [contributors](https://github.com/Tecnocreaciones/AjaxFOSUserBundle/contributors).

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/Tecnocreaciones/AjaxFOSUserBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.
