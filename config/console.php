<?php
$params = require __DIR__ . '/params.php';

$MYSQL_USERNAME = getenv("MYSQL_USERNAME");
$MYSQL_PASSWORD = getenv("MYSQL_PASSWORD");
$MYSQL_DB_DSN = getenv("MYSQL_DB_DSN");
$MYSQL_DB_DATA_DSN = getenv("MYSQL_DB_DATA_DSN");

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => $MYSQL_DB_DSN,
            'username' => $MYSQL_USERNAME,
            'password' => $MYSQL_PASSWORD,
            'charset' => 'utf8',
        ],
        'dbdata' => [
            'class' => 'yii\db\Connection',
            'dsn' => $MYSQL_DB_DATA_DSN,
            'username' => $MYSQL_USERNAME,
            'password' => $MYSQL_PASSWORD,
            'charset' => 'utf8',
        ],
    ],
    'params' => $params,
];

// 自定义别名
\Yii::setAlias('@system', dirname(__DIR__) . '/system');

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
