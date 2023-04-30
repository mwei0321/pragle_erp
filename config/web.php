<?php
$params = require __DIR__ . '/params.php';

// define("ROOT_PATH", str_replace("\\", "/", dirname(__DIR__)) . '/');

// 读取env配置
if (!getenv("MYSQL_DB_DSN")) {
    require_once ROOT_PATH . 'system/common/Env.php';
    system\common\Env::loadFile(ROOT_PATH . '.env');
}

$MYSQL_USERNAME = getenv("MYSQL_USERNAME");
$MYSQL_PASSWORD = getenv("MYSQL_PASSWORD");
$MYSQL_DB_DSN = getenv("MYSQL_DB_DSN");
$MYSQL_DB_DATA_DSN = getenv("MYSQL_DB_DATA_DSN");
$MYSQL_DB_CENTER_FROM_DSN = getenv("MYSQL_DB_CENTER_FROM_DSN");
$MYSQL_DB_CENTER_TO_DSN = getenv("MYSQL_DB_CENTER_TO_DSN");

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone' => 'Asia/Shanghai',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey'  => 'aXt3ppoGrUc3LZrMEVlB_ezEKyPfVtmB',
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'text/json'        => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],

                // 记录SQL日志
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'logVars' => ['*'],
                    'categories' => ['yii\db\*', 'app\models\*'],
                    'logFile' => '@runtime/logs/sql' . date('Y-m-d') . '.log',
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
        'dbcenter' => [
            'class' => 'yii\db\Connection',
            'dsn' => $MYSQL_DB_CENTER_TO_DSN,
            'username' => $MYSQL_USERNAME,
            'password' => $MYSQL_PASSWORD,
            'charset' => 'utf8',
        ],
        'dbcenter_from' => [
            'class' => 'yii\db\Connection',
            'dsn' => $MYSQL_DB_CENTER_FROM_DSN,
            'username' => $MYSQL_USERNAME,
            'password' => $MYSQL_PASSWORD,
            'charset' => 'utf8',
        ],
        'dbcenter_to' => [
            'class' => 'yii\db\Connection',
            'dsn' => $MYSQL_DB_CENTER_TO_DSN,
            'username' => $MYSQL_USERNAME,
            'password' => $MYSQL_PASSWORD,
            'charset' => 'utf8',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<controller:\w+>/<action:\w+>/' => 'erpapi/<controller>/<action>',
            ],
        ],

        // 开启SQL日志

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        // 'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

// 自定义别名
\Yii::setAlias('@system', dirname(__DIR__) . '/system');

return $config;
