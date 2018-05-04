<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'language'=>'zh-CN',
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules'=>[
        'call'=>'frontend\modules\call\Module',
        'wechat'=>'nextrip\wechat\Module',
        'api' => 'frontend\modules\api\Module',
    ],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'loginUrl'=>['user/login'],
            'identityCookie' => [
                //'domain' => '.yii2-call.com',
                'path' => '/',
                'name' => '_identity',
                'httpOnly' => true,
            ],
        ],
//        'session'=>[
//            'cookieParams'=>[
//                'domain' => '.yii2-call.com',
//            ]
//        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['xxx'],
                    'levels' => ['error', 'warning'],
                    'logVars' => ['*'],
                    'logFile' => '@runtime/logs/xxx.log',
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
//        'urlManager' => [
//            'enablePrettyUrl' => true,
//            'showScriptName' => false,
//            'rules' => [
//            ],
//        ],
    ],
    'params' => $params,
];
