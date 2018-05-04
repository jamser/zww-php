<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\call\Caller;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\call\CallerApplyReview */
/* @var $caller common\models\call\Caller */
/* @var $form yii\widgets\ActiveForm */

$this->title = '申请审核';
?>

<div class="caller-apply-search">

    <?php $form = ActiveForm::begin([
        'action' => ['/caller/review', 'id'=>$caller->id],
        'method' => 'post',
    ]); ?>

    <?= $form->field($model, 'pass')->dropDownList([null=>'请选择']+[Caller::STATUS_REVIEW_PASS=>'通过',Caller::STATUS_REVIEW_REJECTED=>'拒绝']) ?>
    
    <?= $form->field($model, 'remark')->textarea() ?>

    <div class="form-group">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
