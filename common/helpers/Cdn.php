<?php

namespace common\helpers;

use Yii;

/**
 * CDN操作文件
 */
class Cdn {
    
    /**
     * 获取一个授权的URL
     * @param url $originalUrl 原来文件的URL
     * @param int $timeout 超时时间
     * @return string
     */
    public static function getAuthUrl($originalUrl, $timeout=7200) {
        $authKey = Yii::$app->params['cdnAuthKey'];
        $expireTime = time() + $timeout;
        $path = parse_url($originalUrl, PHP_URL_PATH);
        $md5hash = md5("$path-$expireTime-0-0-$authKey");
        return $originalUrl."?auth_key=$expireTime-0-0-$md5hash";
    }
    
}

