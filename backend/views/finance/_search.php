<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\finance\WithdrawApply;

/* @var $this yii\web\View */
/* @var $model backend\models\search\Withdraw */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="withdraw-apply-search form-inline">

    <?php $form = ActiveForm::begin([
        'action' => ['user-withdraw-list'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>
    
    <?= $form->field($model, 'phone') ?>
    
    <?= $form->field($model, 'username') ?>

    <?php // $form->field($model, 'amount') ?>

    <?= $form->field($model, 'status')->dropDownList([null=>'全部']+WithdrawApply::STATUS_LIST) ?>

    <?= $form->field($model, 'out_trade_no') ?>

    <?php echo $form->field($model, 'applyBeginTime') ?>
    <?php echo $form->field($model, 'applyEndTime') ?>

    <?php echo $form->field($model, 'payBeginTime') ?>
    <?php echo $form->field($model, 'payEndTime') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('重置', ['user-withdraw-list'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
