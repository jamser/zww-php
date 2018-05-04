<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('@channel', dirname(dirname(__DIR__)) . '/channel');
Yii::setAlias('@florea', dirname(dirname(__DIR__)) . '/florea');

if(YII_ENV==='prod') {
    Yii::setAlias('@frontendHost', 'nexttrip.cc');
} else {
    Yii::setAlias('@frontendHost', 'yii2-call.com');
}


require dirname(__DIR__).'/base/function.php';
