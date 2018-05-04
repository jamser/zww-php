<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this \yii\web\View */

$this->title = '提现审核';
?>

<div class="form">
    <form action="<?= \yii\helpers\Url::current()?>" method="post">
        
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'type')->dropDownList([
            null=>'请选择',
            'pass'=>'通过',
            'rejected'=>'拒绝'
        ]) ?>

        <?= $form->field($model, 'remark')->textarea(['row' => 3]) ?>
        
        <div class="form-group">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        
    </form>
</div>