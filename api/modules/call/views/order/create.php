<?php

use yii\helpers\Html;
use frontend\assets\CallAsset;
use yii\helpers\Url;

CallAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\PcallOrder */

$this->title = '预约叫我起床服务';
?>

<?=$this->render('/base/header',['return'=>false]);?>

<div class="pcall-main-outer">
    <div class="">
        <?= $this->render('_form', [
            'model' => $model,
            'call_user' => $call_user
        ]) ?>
    </div>
    
</div>