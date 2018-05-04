<?php
return [
    'language'=>'zh-CN',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
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
