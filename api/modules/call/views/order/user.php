<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use frontend\assets\CallAsset;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '我的订单';
$this->params['breadcrumbs'][] = $this->title;
CallAsset::register($this);
?>
<?=$this->render('/base/header',['return'=>false]);?>

<div class="order-index" id="order_index">

    <?php 
    $orders = $dataProvider->getModels();
    if($orders) {
        foreach($orders as $order) {
            echo $this->render('_view', [
                'order'=>$order
            ]);
        }
    } else {
        echo Html::tag('div', '暂时还没有订单', [
            'class'=>'empty-summary'
        ]);
    }
    ?>

    <?=LinkPager::widget([
        'pagination'=>$dataProvider->getPagination()
    ])?>
</div>
<!--
<div class="weui-tabbar">
    <a href="javascript:;" class="weui-tabbar__item<?=$type=='all' ? '  weui-bar__item_on' : ''?>">
        <span style="display: inline-block;position: relative;">
            <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon">
            <span class="weui-badge" style="position: absolute;top: -2px;right: -13px;">8</span>
        </span>
        <p class="weui-tabbar__label">全部订单</p>
    </a>
    <a href="javascript:;" class="weui-tabbar__item<?=$type=='unpay' ? '  weui-bar__item_on' : ''?>">
        <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon">
        <p class="weui-tabbar__label">待付款</p>
    </a>
    <a href="javascript:;" class="weui-tabbar__item<?=$type=='confirmed' ? '  weui-bar__item_on' : ''?>">
        <span style="display: inline-block;position: relative;">
            <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon">
            <span class="weui-badge weui-badge_dot" style="position: absolute;top: 0;right: -6px;"></span>
        </span>
        <p class="weui-tabbar__label">待唤醒</p>
    </a>
    <a href="javascript:;" class="weui-tabbar__item">
        <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon">
        <p class="weui-tabbar__label">我</p>
    </a>
</div>
-->
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
            $('#ask_dialog').show().on('click', '.weui_btn_dialog', function () {
                    $('#ask_dialog').off('click').hide();
                });
        } else {
            FlashMsg.error(r.msg+' ; 请搜索微信公众号 NextTrip未来旅行关注');
        }
    },'json');
}).on('click', '.btn-user-confirm-service', function(){
    e.preventDefault();
    var orderId = $(this).closest('.order-view').data('id');
    if(!confirm('确认后订单服务费将转到对方账户,确认订单已经完成? ')) {
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
<?php $this->endBlock(); 
$this->registerJs($this->blocks['pay']);
?>

