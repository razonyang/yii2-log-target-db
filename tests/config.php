<?php

return [
    'id' => 'test',
    'basePath' => __DIR__,
    'controllerMap' => [
        'migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'migrationPath' => [
                '@yii/log/migrations/',
            ],
            'migrationNamespaces' => [
                'RazonYang\Yii2\Log\Db\Migration',
            ],
        ],
    ],
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=localhost;dbname=test',
            'username' => 'root',
            'password' => '',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => \RazonYang\Yii2\Log\Db\Target::class,
                    'levels' => ['error', 'warning'],
                    'logTable' => 'log',
                ],
            ],
        ],
    ],
];
