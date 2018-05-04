<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use common\models\order\Pay ;

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $model common\models\call\Caller */
/* @var $orders common\models\call\Order */
/* @var $pay common\models\order\Pay */

$this->title = '预约成功！请支付';
$this->params['bodyCssClass'] = 'gray-bg';
?>

<div class="weui-msg" id="book_success">
    <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
    <div class="weui-msg__text-area">
        <h2 class="weui-msg__title">预约已成功</h2>
        <p class="weui-msg__desc">
            
            你已成功预约 
            <span id="book_date">
                <?= implode(',', $dates)?>
            </span> 
            日起床服务<br/>
            <?php if($pay->status==Pay::STATUS_UNPAY):?>
                请在<?=date('m月d日 H时i分', $pay->expire_time)?>前 
                （<?=(Pay::EXPIRE_TIME)/60?>分钟内）完成支付
            <?php elseif($pay->status==Pay::STATUS_PAYING):?>
            <?php elseif($pay->status==Pay::STATUS_PAID):?>
            <?php elseif($pay->status==Pay::STATUS_FAILED):?>
                支付失败
            <?php elseif($pay->status==Pay::STATUS_EXPIRE):?>
                已经过期了
            <?php endif;?>
        </p>
    </div>
    <div class="weui-msg__opr-area">
        <p class="weui-btn-area">
            <?php if($pay->status==Pay::STATUS_EXPIRE):?>
                <a href="javascript:;" class="weui-btn weui-btn_default" >已过期</a>
            <?php elseif($pay->status==Pay::STATUS_UNPAY):?>
                <a href="javascript:;" class="weui-btn weui-btn_primary" id="btn_pay">立即付款 ￥<?=round($pay->money_amount,2)?></a>
            <?php elseif($pay->status==Pay::STATUS_PAYING):?>
                <a href="javascript:;" class="weui-btn weui-btn_default">正在支付中..</a>
            <?php elseif($pay->status==Pay::STATUS_PAID):?>
                <a href="javascript:;" class="weui-btn weui-btn_default">支付已成功</a>
            <?php elseif($pay->status==Pay::STATUS_FAILED):?>
                <a href="javascript:;" class="weui-btn weui-btn_primary" id="btn_pay">重新支付 ￥<?=round($pay->money_amount,2)?></a>
            <?php endif;?>
        </p>
    </div>
</div>

<?php
echo $this->render('payjs', [
    'jsApiParams'=> json_encode($jsApiParams),
    'payId'=>$pay->id,
    'autopay'=> $pay->canPay()
]);?>
<script>
<?php $this->beginBlock('pageScript');?>
$('#btn_pay').click(function(e){
    <?php if($pay->canPay()):?>
    callpay();
    <?php endif;?>
});
<?php $this->endBlock();
$this->registerJs($this->blocks['pageScript']);?>
</script>