<?php

use frontend\assets\CallAsset;
use frontend\assets\WechatUiAsset;

/* @var $this \yii\web\View */
/* @var $model \common\models\ActiveDataProvider */
$this->title = '查看任务';

CallAsset::register($this);
WechatUiAsset::register($this);

?>

<?=$this->render('/base/header',['return'=>false]);?>

<div>
    <?=$this->render('_task',[
        'order'=>$model
    ]);?>
</div>
