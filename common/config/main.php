<?php
return [
    'language'=>'zh-CN',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        //'@mdm/admin' => '@bm/user',
        '@nextrip'=>'@common/../nextrip',
        '@WechatSdk'=>'@nextrip/wechatSdk',
        '@staticAssetUrl'=>''
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'db'=>'db_php'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
