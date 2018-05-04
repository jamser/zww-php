<?php

use yii\helpers\Html;
use frontend\assets\CallAsset;

/* @var $this yii\web\View */
/* @var $model common\models\PcallCaller */

$this->title = '更新资料';
CallAsset::register($this);
?>

<?=$this->render('/base/header');?>

<div class="pcall-main-outer">
    <div class="pcall-caller-apply-update pt20">

        <?= $this->render('_form', [
            'model' => $model,
            'call_user' => $call_user
        ]) ?>

    </div>
</div>

