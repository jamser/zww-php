<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $model common\models\call\Caller */

$this->title = $user->username.' 的个人主页';
?>

<div class="caller-view page panel">
    <div class="caller-index-header" id="caller_index_header">
        <div class="margin20 avatar">
            <img src="<?=$user->getAvatar()?>" width="80" height="80"/>
        </div>
        
        <div class="mt10">
            <span class="nickname"><?=$user->username?></span>
            <span class="icon <?=$user->sex==1?'icon-venus':'icon-mars'?>"></span>
        </div>
        
        <div class="mt10 info">
            <span class=""><?php $age = $user->getAge();echo $age ? $age.'岁' : ''?></span>
            <span class=""><?=$user->about?></span>
        </div>
        
        <div class="mt10 service_time">
            服务时间: <?=$model->service_time;?>
        </div>
        
        <div class="mt10">
            <?php if(false && $model->user_id == Yii::$app->user->id):?>
                <a href="<?= Url::to(['/call/caller/update'])?>" class="weui-btn weui-btn_mini weui-btn_primary">更新资料</a>
            <?php else:?>
                <a href="<?= Url::to(['/call/caller/book','id'=>$model->user_id])?>" class="weui-btn weui-btn_mini weui-btn_primary">预约</a>
                <a href="#" class="weui-btn weui-btn_mini weui-btn_warn btn-send-gift">送礼物</a>
            <?php endif;?>
            
        </div>
    </div>
    
    <div class="weui-loadmore weui-loadmore_line">
        <span class="weui-loadmore__tips">封面相册</span>
    </div>
    
    <div class="caller-view-imgs mt20">
        <?php
        $covers = $model->getArrayFormatAttribute('covers');
        foreach($covers as $cover) { ?>
        <div class="margin10">
            <img src="<?=$cover['url']?>" width="100%"/>
        </div>
        <?php
        }
        ?>
    </div>
    
    <div class="weui-loadmore weui-loadmore_line">
        <span class="weui-loadmore__tips">The End</span>
    </div>
</div>

<div id="gift_actionsheet" style="display:none">
    <div class="weui-mask"></div>
    <div class="weui-actionsheet weui-actionsheet_toggle">
        <div class="weui-actionsheet__menu">
            <?php 
            $gifts = \common\models\gift\Gift::getTop(5);
            $virtualMoneyName = \common\models\Setting::getValueByKey('virtualMoneyName');
            foreach($gifts as $gift):
            ?>
            <div class="weui-actionsheet__cell clearfix" style="border-bottom:1px solid #eee">
                <div class="left ml10">
                    <img src="<?=$gift->url?>" width="40" height="40"/>
                </div>
                
                <div style="line-height: 40px;color:#666;margin-left:10px;" class="left">
                    <?=Html::encode($gift->name)?> <?=Html::encode($gift->virtual_price).$virtualMoneyName?>
                </div>
                
                <div style="" class="right mr10 num-action">
                    <a href="javascript:;" class="left weui-btn weui-btn_mini weui-btn_default btn-decrease" style="margin-top:5px">-</a>
                    <div class="left ml10 mr10 num" data-giftid="<?=$gift->id?>" style="line-height: 40px;color:#666;"> 0 </div>
                    <a href="javascript:;" class="right weui-btn weui-btn_mini weui-btn_default btn-increase" style="margin-top:5px">+</a>
                </div>
            </div>
            <?php endforeach;?>
        </div>
        <div class="weui-actionsheet__action">
            <div class="weui-actionsheet__cell" id="btn_confirm_send_gift">确定赠送</div>
        </div>
    </div>
</div>

<script>

<?php
$this->beginBlock('pageScript');?>
var virtualMoneyName = '<?=\common\models\Setting::getValueByKey('virtualMoneyName')?>';
var $giftActionSheet = $('#gift_actionsheet');
var $giftMask = $giftActionSheet.find('.weui-mask');
$giftActionSheet.on('click', '.btn-decrease', function(e){
    e.preventDefault();
    var parentNumEle = $(this).parents('.num-action');
    var num = Number(parentNumEle.find('.num').html());
    if(num>0) {
        parentNumEle.find('.num').html(num-1);
    }
    
}).on('click', '.btn-increase', function(e){
    e.preventDefault();
    var parentNumEle = $(this).parents('.num-action');
    var num = Number(parentNumEle.find('.num').html());
    parentNumEle.find('.num').html(num+1);
    
}).on('click', '#btn_confirm_send_gift', function(e) {
    e.preventDefault();
    var gifts = {};
    var giftNums = $('.num-action .num');
    var sumNum = 0;
    giftNums.each(function(){
        var giftId = $(this).data('giftid');
        var num = Number($(this).html());
        if(num>0) {
            gifts[giftId] = num;
        }
        sumNum += num;
    });
    if(!sumNum) {
        return;
    }
    console.log('送出礼物', gifts);
    $.ajax({
        url:'/api/user/send-gift',
        data:{
            gifts:gifts,
            callerUserId:<?=(int)$user->id?>
        },
        type:'POST',
        dataType:'json',
        success:function(r) {
            if(r.code===0) {
                weui.alert('赠送成功啦! Ta将会收到你的礼物提醒');
                $('#gift_actionsheet').hide();
            } else if(r.code===40001) {//金额不足
                var selectOptionHtml = '';
                for(var k in r.result.rechargeAmountOptions) {
                    selectOptionHtml += '<a href="javascript:;" class="btn-select-recharge-amount '+(selectOptionHtml.length>0 ? '' : 'active')+'"\n\
                        data-truemoney="'+k+'" data-virtualmoney="'+r.result.rechargeAmountOptions[k]+'">'+r.result.rechargeAmountOptions[k]+virtualMoneyName+'<br/> ￥'+k+'元</a>';
                }
                weui.dialog({
                    //title: 'dialog标题',
                    content: '<div>需 '+r.result.virtualPrice+virtualMoneyName+'。 当前余额不足! 请充值</div>\n\
    <div class="mt5">'+selectOptionHtml+'</div>',
                    className: 'recharge-dialog',
                    buttons: [{
                        label: '取消',
                        type: 'default',
                        onClick: function () {}
                    }, {
                        label: '充值',
                        type: 'primary',
                        onClick: function () {
                            recharge();
                        }
                    }]
                });
            } else {
                weui.alert(r.msg);
            }
        },
        error:function(r) {
            weui.alert('网络连接错误');
        }
    });
});
$('body').on('click', '.btn-select-recharge-amount', function(e){
    $('.btn-select-recharge-amount').removeClass('active');
    $(this).addClass('active');
});

$('#caller_index_header').on('click', '.btn-send-gift', function(e){
    e.preventDefault();
    $giftActionSheet.fadeIn(200);
    $giftMask.on('click',function () {
        $giftActionSheet.fadeOut(200);
    });
});

var rechargeLoading = false;
function recharge() {
    var trueMoney = $('.btn-select-recharge-amount.active').data('truemoney');
    var virtualMoney = $('.btn-select-recharge-amount.active').data('virtualmoney');
    if(rechargeLoading) {
        return false;
    }
    $.ajax({
        url:'/api/wallet/recharge',
        data:{
            trueMoney:trueMoney,
            virtualMoney:virtualMoney
        },
        type:'GET',
        dataType:'json',
        success:function(r) {
            rechargeLoading = false;
            if(r.code===0) {
                var payId = r.result.payId;
                PayModule.setPayCallback(function(payId, payResult, res){
                    if(payResult) {
                        weui.alert('充值成功');
                    }
                }).pay(payId);
            } else {
                weui.alert(r.msg);
            }
        },
        error:function(r) {
            rechargeLoading = false;
            weui.alert('网络连接错误');
        }
    });
}

<?php
$this->endBlock();
$this->registerJs($this->blocks['pageScript']);
?>
</script>
