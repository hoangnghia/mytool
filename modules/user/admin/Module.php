<?php

namespace app\modules\user\admin;

/**
 * User Admin Module.
 *
 * File has been created with `module/create` command. 
 * 
 * @author
 * @since 1.0.0
 */
class Module extends \luya\admin\base\Module
{
    public $apis = [
        'api-user-user' => 'app\modules\user\admin\apis\UserController',
        'api-user-sync-data' => 'app\modules\user\admin\apis\UserController',
    ];

    public function getMenu()
    {
        return (new \luya\admin\components\AdminMenuBuilder($this))
            ->node('Khách Hàng', 'accessibility')
            ->group('Danh sách')
            ->itemApi('Khách hàng', 'useradmin/user/index', 'label', 'api-user-user')
            ->group('Đồng bộ dữ liệu')
            ->itemApi('CareSoft to here', 'useradmin/user/sync-data-care-soft', 'sync', 'api-user-user')
            ->itemApi('GetResponse to here', 'useradmin/user/sync-data-get-response', 'sync', 'api-user-user')
            ->itemApi('CareSoft to GetResponse', 'useradmin/user/sync-data-care-soft-to-get-response', 'sync', 'api-user-user')
            ->itemApi('GetResponse to CareSoft', 'useradmin/user/sync-data-get-response-to-care-soft', 'sync', 'api-user-user');

    }
}