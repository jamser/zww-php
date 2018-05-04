<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\call\Caller;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\search\CallerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="caller-apply-search form-inline">

    <?php $form = ActiveForm::begin([
        'action' => ['/caller/apply-list'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>
    
    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'phone') ?>
    
    <?= $form->field($model, 'sex')->dropDownList([
        null=>'所有',
        0=>'未知',
        1=>'男',
        2=>'女'
    ]); ?>

    <?= $form->field($model, 'status')->dropDownList([null=>'选择']+Caller::$status_list) ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('重置', Url::to(['/caller/apply-list']),['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
