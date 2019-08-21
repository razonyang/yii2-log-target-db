Yii2 enhanced database log target
=================================

[![Build Status](https://travis-ci.org/razonyang/yii2-log-target-db.svg?branch=master)](https://travis-ci.org/razonyang/yii2-log-target-db)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/razonyang/yii2-log-target-db/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/razonyang/yii2-log-target-db/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/razonyang/yii2-log-target-db/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/razonyang/yii2-log-target-db/?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/razonyang/yii2-log-target-db.svg)](https://packagist.org/packages/razonyang/yii2-log-target-db)
[![Total Downloads](https://img.shields.io/packagist/dt/razonyang/yii2-log-target-db.svg)](https://packagist.org/packages/razonyang/yii2-log-target-db)
[![LICENSE](https://img.shields.io/github/license/razonyang/yii2-log-target-db)](LICENSE)

Because the built-in database log target can not figure out the context of same request, especially in the case of concurrency,
so that the log is very confusing, it is hard to diagnose errors.

According this problem, what this extension do is that record the request ID via `dechex($_SERVER['REQUEST_TIME_FLOAT'] * 1000000)`.

Installation
------------

```
composer require razonyang/yii2-log-target-db
```

Usage
-----

```php
return [
    // console configuration
    'controllerMap' => [
        'migrate' => [
            'migrationPath' => [
                // ...
                '@yii/log/migrations/',
            ],
            'migrationNamespaces' => [
                // ...
                'RazonYang\Yii2\Log\Db\Migration',
            ],
        ],
    ],

    // common/web/console configuration
    'components' => [
        'log' => [
            'targets' => [
                'db' => [
                    'class' => \RazonYang\Yii2\Log\Db\Target::class,
                    'levels' => ['error', 'warning'],
                    'db' => 'db',
                    'logTable' => '{{%log}}',
                ],
            ],
        ],
    ],
];
```

then:

```shell
$ yii migrate
```
