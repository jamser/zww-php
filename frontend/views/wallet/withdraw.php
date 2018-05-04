<?php


/* @var $this \yii\web\View */
/* @var $wallet \common\models\user\Wallet */

use yii\helpers\Url;

$this->params['bodyCssClass'] = 'gray-bg';
$this->title = '提现';
?>
<div class="wallet-blance-withdraw">
    <form method="POST" action="<?=Url::to(['/wallet/withdraw'])?>">
        <div class="weui-cells">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>"/>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">金额</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" name="data[amount]" type="number" placeholder="" >
                </div>
            </div>
            <div class="weui-cells__tips red"><?=$form->getFirstError('amount')?></div>
            <div class="weui-cells__tips">账户余额 <?=sprintf('%0.2f',$wallet->blance/100)?> 元 , 可提现 <?=sprintf('%0.2f',$wallet->can_withdraw/100)?> 元</div>

            <div class="weui-cell weui-cell_vcode">
                <div class="weui-cell__hd">
                    <label class="weui-label">验证码</label>
                </div>
                <div class="weui-cell__bd">
                    <input class="weui-input" name="data[sms_code]" placeholder="">
                </div>
                <div class="weui-cell__ft">
                    <button class="weui-vcode-btn" type="button" id="btn_send_code">获取验证码</button>
                </div>
            </div>
            <div class="weui-cells__tips red"><?=$form->getFirstError('sms_code')?></div>
        </div>
        
        <div class="margin10" >
            <button type="submit" class="mt20 weui-btn weui-btn_primary">提现</button>
        </div>
    </form>
</div>

<script>
<?php $this->beginBlock('pageScript');?>
    
$('#btn_send_code').on('click', function(e){
    sendCode();
});
var sendCodeTimeout=0;
function timer() {
    if(sendCodeTimeout>0) {
        $('#btn_send_code').html(sendCodeTimeout+'秒重发').attr('disabled', true);
         setTimeout(function(){
            sendCodeTimeout--;
            timer();
        },1000);
    } else {
        $('#btn_send_code').html('获取验证码').removeAttr('disabled');
    }
}
function sendCode() {
    if(sendCodeTimeout<=0) {
        //允许发送
        $('#btn_send_code').attr('disabled', true).html('发送中..');
        $.ajax({
            url:'/api/common/send-sms-code',
            data:{
                _csrf:'<?=Yii::$app->request->csrfToken?>',
                phoneNum:'<?=$account->value?>',
                type:'applyWithdrawals'
            },
            type:'POST',
            dataType:'json',
            success:function(r) {
                if(r.code===0) {
                    sendCodeTimeout = 60;
                    $('#btn_send_code').attr('disabled', true);
                    timer();
                } else {
                    weui.alert(r.msg);
                    $('#btn_send_code').removeAttr('disabled').html('获取验证码');
                }
            },
            error:function(r) {
                weui.alert('网络连接出错了...请重试');
                $('#btn_send_code').removeAttr('disabled').html('获取验证码');
            },
        });
    }
}
<?php
$this->endBlock();
$this->registerJs($this->blocks['pageScript']);
?>
</script>
