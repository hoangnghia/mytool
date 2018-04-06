<?php

namespace app\modules\user\frontend;

/**
 * User Admin Module.
 *
 * File has been created with `module/create` command. 
 * 
 * @author
 * @since 1.0.0
 */
class Module extends \luya\base\Module
{
    public $urlRules = [
        ['pattern' => 'news', 'route' => 'user/default/hello']
    ];
}