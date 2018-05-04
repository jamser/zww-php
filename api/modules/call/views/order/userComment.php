<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var $model \frontend\modules\call\UserConfirm */

$this->title = "评价服务";


?>

<div style="border-bottom:1px solid #eee;height:50px;line-height:50px;width:100%;font-size: 16px;text-align: center;color:#666">
    服务评价
</div>
<div class="pcall-user-confirm-form margin10">

    <div class="clearfix" style="padding-top:10px;">
        <div class="" style="float:left;width:50px;height:50px;border-radius: 4px;overflow: hidden;">
            <img src="/imgs/fbb.jpg" width="50"/>
        </div>
        <div class="" style="float:left;margin-left:10px;">
            <div class="" style="font-weight: 600">范冰冰</div>
            <div class="" style="margin-top:5px;">XXXX  XXXX</div>
        </div>
    </div>
    
    <?php $form = ActiveForm::begin(); ?>
    
    <div class="form-group" style="margin-top:10px;">
        <label class="control-label">
            赠送礼物
        </label>
        <div class="clearfix">
            <div class="" style="width:70px;float:left;margin:5px;padding:5px;">
                <img src="/imgs/rose-icon-1.png" width="50" height="50" style="margin:0 auto;"/>
                <div style="line-height: 20px;color:#666">
                    单枝玫瑰 <br/>￥1.00
                </div>
            </div>
            <div class="" style="width:70px;float:left;margin:5px;padding:5px;">
                <img src="/imgs/heart-of-roses-icon.png" width="50" height="50"  style="margin:0 auto;"/>
                <div style="line-height: 20px;color:#666">
                    心型玫瑰 <br/>￥99.00
                </div>
            </div>
        </div>
    </div>
    
    <div class="form-group" style="margin-top:10px;">
        <label class="control-label">
            评分
        </label>
        <div>
            <a href="#" alt="1分 太差了" class="active"><span class="icon icon-star"></span></a>
            <a href="#" alt="2分 不太好"><span class="icon icon-star"></span></a>
            <a href="#" alt="3分 一般"><span class="icon icon-star"></span></a>
            <a href="#" alt="4分 还不错"><span class="icon icon-star"></span></a>
            <a href="#" alt="5分 很好"><span class="icon icon-star"></span></a>
        </div>
    </div>
    
    <div class="form-group" style="margin-top:10px;">
        <label class="control-label">
            评价(选填)
        </label>
        <div>
            <textarea class="form-control"></textarea>
        </div>
    </div>
    
    
    <div class="form-group" style="margin-top:10px;">
        <?= Html::submitButton('提交', ['class' => 'weui-btn weui-btn_primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
