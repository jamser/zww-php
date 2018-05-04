<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Gift */

$this->title = '更新礼物: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Gifts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gift-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
