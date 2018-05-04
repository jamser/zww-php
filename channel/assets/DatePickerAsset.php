<?php

namespace channel\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class DatePickerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '/3rd/datepicker/css/bootstrap-datetimepicker.min.css',
    ];
    public $js = [
        '/3rd/datepicker/js/bootstrap-datetimepicker.min.js',
        '/3rd/datepicker/js/locales/bootstrap-datetimepicker.zh-CN.js',
    ];
    
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset'
    ];
}
