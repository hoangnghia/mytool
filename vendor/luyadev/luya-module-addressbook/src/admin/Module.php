<?php

namespace luya\addressbook\admin;

/**
 * Addressbook Admin Module.
 *
 * File has been created with `module/create` command on LUYA version 1.0.0-RC3. 
 */
class Module extends \luya\admin\base\Module
{
    public $apis = [
        'api-addressbook-contact' => 'luya\addressbook\admin\apis\ContactController',
        'api-addressbook-group' => 'luya\addressbook\admin\apis\GroupController',
    ];

    public function getMenu()
    {
        return (new \luya\admin\components\AdminMenuBuilder($this))
            ->node('Addressbook', 'extension')
            ->group('Settings')
            ->itemApi('Contact', 'addressbookadmin/contact/index', 'label', 'api-addressbook-contact')
            ->itemApi('Group', 'addressbookadmin/group/index', 'label', 'api-addressbook-group');
    }
}