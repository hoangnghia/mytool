[![LUYA](https://img.shields.io/badge/Powered%20by-LUYA-brightgreen.svg)](https://luya.io)
[![Total Downloads](https://poser.pugx.org/luyadev/luya-module-addressbook/downloads)](https://packagist.org/packages/luyadev/luya-module-contactform)
[![Latest Stable Version](https://poser.pugx.org/luyadev/luya-module-addressbook/v/stable)](https://packagist.org/packages/luyadev/luya-module-contactform)
[![Join the chat at https://gitter.im/luyadev/luya](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/luyadev/luya)

# LUYA address book module

This module is the result of the comprehensive LUYA module lessons.
+ how to [create the
addressbook frontend and admin module](https://github.com/luyadev/luya/blob/master/docs/guide/lesson-module.md).
+ how to [create an Active Window](https://github.com/luyadev/luya/blob/master/docs/guide/lesson-module.md)
and how to add it to the CRUD view of the group model.

The module itself provides a simple way to manage multiple address records and to render them in the frontend.
It also allows to to group the different contacts and send an email to all members of a group.

## Installation

Require the address module via composer
```
composer require luyadev/luya-module-addressbook
```
and install/update with `composer install` oder `composer update`

Add both address book modules (admin and frontend) to your project config:

```php
'modules' => [
    /* ... */

    'addressbookadmin' => 'luya\addressbook\admin\Module',
    'addressbook' => 'luya\addressbook\frontend\Module',
]
```

Don't forget to register the modules with `/vendor/bin/luya import` and set the permissions:

![Settings permissions](https://raw.githubusercontent.com/luyadev/luya/master/docs/guide/img/addressbook-permissions.gif "Settings permissions")

## Integrate the frontend module

Create a module page in CMS and choose the `addressbook` module:

![Creating the module page](https://raw.githubusercontent.com/luyadev/luya/master/docs/guide/img/addressbook-createpage.gif "Create a module page")

## Demo

The Active Window in the admin module will allow you to send an email to all group members:

![Showing the ActiveWindow](https://raw.githubusercontent.com/luyadev/luya/master/docs/guide/img/aws-result.gif "Showing the ActiveWindow")

The frontend module will display all contacts for each group and you can get additional detailed infos by clicking on a contact:

![address book frontend](https://raw.githubusercontent.com/luyadev/luya/master/docs/guide/img/addressbook-demo.gif "address book frontend")


