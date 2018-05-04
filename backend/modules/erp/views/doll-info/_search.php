<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\erp\models\DollInfoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="doll-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'dollName') ?>

    <?= $form->field($model, 'dollTotal') ?>

    <?= $form->field($model, 'img_url') ?>

    <?php // echo $form->field($model, 'addTime') ?>

    <?php // echo $form->field($model, 'dollCode') ?>

    <div class="form-group">
        <?= Html::submitButton('查找', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
