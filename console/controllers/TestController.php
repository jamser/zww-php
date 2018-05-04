<?php

namespace console\controllers;

use Yii;

class TestController extends \yii\console\Controller {
    
    public function actionSetRedis($key, $value, $expire) {
        $redis = Yii::$app->get('redis');
        $redis->set($key, $value);
        if($expire) {
            $redis->expire($expire);
        }
    }
    
    public function actionGetRedis($key) {
        $value = Yii::$app->get('redis')->get($key);
        var_export($value);
    }
    
}
