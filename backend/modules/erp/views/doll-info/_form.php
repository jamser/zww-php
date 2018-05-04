<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\modules\erp\models\DollInfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="doll-info-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'dollName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dollTotal')->textInput() ?>

    <?= $form->field($model, "img_url")->fileInput()->label('请选择图片文件 ：') ?>
    <?php
    if($img_url){
        echo "<img src='$img_url' style='width: 100px;height: 100px'>";
    }
    ?>

    <?= $form->field($model, 'dollCode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'agency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'size')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dollCoins')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'deliverCoins')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'redeemCoins')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '确定' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
