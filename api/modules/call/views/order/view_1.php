<?php

use yii\helpers\Html;
use comm\trade\models\Order;
/* @var $this yii\web\View */
/* @var $model comm\trade\models\Order */

$this->title = '查看订单';
$this->params['breadcrumbs'][] = $this->title;
?>

<div id="order_index">
    <?= $this->render('_view', ['order'=>$model->toArray(), 'buttons'=>[Html::a('订单列表', '/trade/order/index', ['class'=>'btn btn-default'])]]); ?>
</div>

<div class="weui_dialog_alert" id="ask_dialog" style="display:none">
    <div class="weui_mask"></div>
    <div class="weui_dialog">
        <div class="weui_dialog_hd"><strong class="weui_dialog_title">请长按识别下面的二维码咨询</strong></div>
        <div class="weui_dialog_bd" id="ask_qrcode">
        </div>
        <div class="weui_dialog_ft">
            <a href="javascript:;" class="weui_btn_dialog primary">确定</a>
        </div>
    </div>
</div>

<script>
<?php $this->beginBlock('pay');?>
$('#order_index').on('click', '.btn-pay', function(e){
    e.preventDefault();
    var orderId = $(this).closest('.order-view').data('id');
    PayModule.payOrder(orderId);
}).on('click','.btn-contact', function(e){
    e.preventDefault();
    var orderId = $(this).closest('.order-view').data('id');
    $.get('/apiv1/common/get-qrcode', {type:'orderAsk', id:orderId}, function(r){
        if(isResponseOk(r)) {
            $('#ask_qrcode').html('<img src="'+r.result+'" width="250" height="250"/>');
        } else {
            $('#ask_qrcode').html('获取微信二维码失败: '+r.msg+'<br/>请搜索微信公众号 NextTrip未来旅行关注咨询');
        }
        $('#ask_dialog').show().on('click', '.weui_btn_dialog', function () {
            $('#ask_dialog').off('click').hide();
        });
    },'json');
});

<?php
if($pay && $model->status==Order::STATUS_UNPAY):
    echo 'PayModule.payOrder('.$model->id.', '.($jsApiParams?$jsApiParams:'null').');';
endif;
$this->endBlock(); 
$this->registerJs($this->blocks['pay']);
?>
</script>