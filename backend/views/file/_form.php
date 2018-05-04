<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\File;
/* @var $this yii\web\View */
/* @var $model common\models\File */
/* @var $form yii\widgets\ActiveForm */
$typeOptions = [null=>'请选择'] + File::getAllTypes();
?>

<div class="file-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=$form->errorSummary($model)?>
    
    <?= $form->field($model, 'type')->dropdownList($typeOptions) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '保存' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
