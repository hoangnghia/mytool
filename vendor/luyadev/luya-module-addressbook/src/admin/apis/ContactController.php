<?php

namespace luya\addressbook\admin\apis;

/**
 * Contact Controller.
 * 
 * File has been created with `crud/create` command on LUYA version 1.0.0-RC3. 
 */
class ContactController extends \luya\admin\ngrest\base\Api
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'luya\addressbook\models\Contact';
}