<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

//ini_set('session.cookie_domain', '.yii2-call.com');

//PHP中的 hander() 设置，“*”号表示允许任何域向我们的服务端提交请求：
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
//也可以设置指定的域名，如域名 http://h5.jd.com ，那么就允许来自这个域名的请求：
//header("Access-Control-Allow-Origin: http://yii2-call.com");

return [
    'id' => 'app-channel',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'channel\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            //'loginUrl'=>false,
            'identityCookie' => [
//                'domain' => '.yii2-call.com',
                'path' => '/',
                'name' => '_identity',
                'httpOnly' => true,
            ],
        ],
        'session'=>[
            'cookieParams'=>[
//                'domain' => '.yii2-call.com',
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];
