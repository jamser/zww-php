<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PcallOrder */
/* @var $model common\models\PcallUser */

$this->title = '更新订单';
?>
<div class="pcall-order-update">
    <?= $this->render('_form', [
        'model' => $model,
        'call_user' => $call_user
    ]) ?>

</div>
