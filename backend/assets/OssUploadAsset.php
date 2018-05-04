<?php
namespace backend\assets;

use yii\web\AssetBundle;

class OssUploadAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
        '3rd/plupload-2.1.2/js/plupload.full.min.js',
        'js/sources/ossUpload.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
