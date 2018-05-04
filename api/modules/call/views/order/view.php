<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\PcallOrder;
use frontend\assets\WechatPayAsset;
use frontend\assets\CallAsset;
use frontend\assets\WechatUiAsset;

/* @var $this yii\web\View */
/* @var $model common\models\PcallOrder */

$this->title = '查看订单';

WechatPayAsset::register($this);
CallAsset::register($this);
WechatUiAsset::register($this);
?>


<?=$this->render('/base/header',['return'=>false]);?>

<div id="order_index pt20">
    <?= $this->render('_view', ['order'=>$model, 'buttons'=>[Html::a('订单列表', '/call/order/index', ['class'=>'btn btn-default'])]]); ?>
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
}).on('click', '.btn-user-confirm-service', function(){
    e.preventDefault();
    var orderId = $(this).closest('.order-view').data('id');
    if(!confirm('确认后订单将结束,不可退款,确认订单已经完成? ')) {
        return false;
    }
    CallModule.userConfirmService(orderId, function(r){
        if(r.code==='OK') {
            Modal.showTips('确认成功');
        } else {
            Modal.showTips(r.msg);
        }
    });
}).on('click', '.btn-caller-confirm-service', function(){
    e.preventDefault();
    var orderId = $(this).closest('.order-view').data('id');
    CallModule.callerConfirmService(orderId, function(r){
        if(r.code==='OK') {
            Modal.showTips('确认成功');
        } else {
            Modal.showTips(r.msg);
        }
    });
});

<?php
if($pay && $model->status==PcallOrder::STATUS_UNPAY):
    echo 'PayModule.payOrder('.$model->id.', '.($jsApiParams?$jsApiParams:'null').');';
endif;
$this->endBlock(); 
$this->registerJs($this->blocks['pay']);
?>
</script>