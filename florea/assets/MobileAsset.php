<?php

namespace florea\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MobileAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/weui.min.css',
        'vendors/icomoon/style.css',
    ];
    public $js = [
    ];
    public $depends = [
    ];
}
