<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\doll\Monitor */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="monitor-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'dollId')->textInput() ?>

    <?= $form->field($model, 'alert_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'alert_number')->textInput() ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_date')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'modified_date')->textInput() ?>

    <?= $form->field($model, 'modified_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
