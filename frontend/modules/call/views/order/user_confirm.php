<?php

use yii\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var $model \frontend\modules\call\UserConfirm */

$this->title = "评价服务";


?>


<div class="pcall-user-confirm-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'comment')->textarea() ?>

    <div class="form-group">
        <label class="control-label">
            送礼物给Ta
        </label>
        <div>
            <img src="" />
        </div>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton('提交', ['class' => 'weui-btn weui-btn_primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

