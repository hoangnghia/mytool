<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            // 'dsn' => 'mysql:host=localhost;dbname=DB_NAME',
            'dsn' => 'mysql:host=localhost;dbname=ngocdung;unix_socket=/Applications/AMPPS/var/mysql.sock', // OSX MAMP
            // 'dsn' => 'mysql:host=localhost;dbname=DB_NAME;unix_socket=/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock', // OSX XAMPP
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',

            // in productive enviroments you can enable the schema caching
            // 'enableSchemaCache' => true,
            // 'schemaCacheDuration' => 43200,
        ]
    ]
];
