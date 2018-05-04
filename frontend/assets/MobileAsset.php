<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MobileAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://res.wx.qq.com/open/libs/weui/1.1.0/weui.min.css',
        'vendors/icomoon/style.css',
        'css/bootstrap.min.css',
        'css/m.css?v=12',
    ];
    public $js = [
        'js/zepto.min.js',
        'js/fastclick.js',
        'js/weui.min.js',
        'js/template.js',
        'js/functions.js',
        'js/flashmsg.js',
        'js/m.js?v=04096',
    ];
    public $depends = [
    ];
}
