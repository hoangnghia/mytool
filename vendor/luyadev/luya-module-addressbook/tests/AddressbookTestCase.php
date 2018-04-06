<?php

namespace luya\addressbook\tests;

use luya\testsuite\cases\WebApplicationTestCase;

define("YII_DEBUG", true);

class AddressbookTestCase extends WebApplicationTestCase
{
    public function getConfigArray()
    {
        return [
            'id' => 'mytestapp',
            'basePath' => dirname(__DIR__),
            'modules' => [
                'addressbook' => 'luya\addressbook\frontend\Module',
                'addressbookadmin' => 'luya\addressbook\admin\Module'
            ],
            "components" => [
                "db" => [
                    "class" => "yii\db\Connection",
                    "dsn" => "sqlite::memory:"
                ]
            ]
        ];
    }
}
