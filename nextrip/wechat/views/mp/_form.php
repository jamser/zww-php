<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use nextrip\wechat\models\Mp;

/* @var $this yii\web\View */
/* @var $model nextrip\wechat\models\Mp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mp-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'app_id')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'app_secret')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'mch_id')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'pay_key')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'ssl_cert')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'ssl_key')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'auto_reply_token')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'auto_reply_encoding_aes_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'default_reply')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'default_welcome')->textarea(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
