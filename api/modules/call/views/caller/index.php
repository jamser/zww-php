<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\assets\CallAsset;
use frontend\assets\WechatUiAsset;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\PcallCallerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


CallAsset::register($this);
WechatUiAsset::register($this);

$this->title = '我的任务列表';
?>

<?=$this->render('/base/header',['return'=>false]);?>


<div class="pcall-caller-apply-index">

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
