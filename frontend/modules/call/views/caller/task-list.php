<?php

use yii\widgets\LinkPager;
use frontend\assets\CallAsset;
use frontend\assets\WechatUiAsset;

/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = '我的任务列表';

CallAsset::register($this);
WechatUiAsset::register($this);
?>


<?=$this->render('/base/header',['return'=>false]);?>

<div>
    <?php 
    $models = $dataProvider->getModels();
    foreach($models as $model): ?>
        <?=$this->render('_task',[
            'order'=>$model
        ]);?>
    <?php endforeach;?>
    
    <?=
    LinkPager::widget([
          'pagination' => $dataProvider->getPagination(),
        ]);
    ?>
</div>