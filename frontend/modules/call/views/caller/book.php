<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use common\models\order\Pay;

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $model common\models\call\Caller */

$this->title = '预约';
$this->params['bodyCssClass'] = 'gray-bg';
?>

<div class="caller-view page panel">
    <div class="caller-index-header">
        <div class="margin20 avatar">
            <img src="<?=$caller->user->getAvatar()?>" width="80" height="80"/>
        </div>
        
        <div class="mt10">
            <span class="nickname"><?=$caller->user->username?></span>
            <span class="icon <?=$caller->user->sex==1?'icon-venus':'icon-mars'?>"></span>
        </div>
        
        <div class="mt10 info">
            <span class=""><?php $age = $caller->user->getAge();echo $age ? $age.'岁' : ''?></span>
            <span class=""><?=$caller->user->about?></span>
        </div>
        
        <div class="mt10 service_time">
            服务时间: <?=$caller->service_time;?>
        </div>
        
    </div>
    
    <div class="mt10">
        <form action="<?=Url::to(['/call/caller/book','id'=>$caller->user_id])?>" method="POST" id="booking_form">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->getRequest()->csrfToken;?>"/>
            <input type="hidden" name="callerUserId" value="<?=$caller->user_id?>" />
            <div class="weui-cells booking-first-row" id="booking_row">
                <div class="weui-cells__title">
                    添加预约
                    <a href="javascript:;"  class="btn-remove-booking right">删除</a>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd"><label for="" class="weui-label">预约日期</label></div>
                    <div class="weui-cell__bd">
                        <select name="bookDate[]" class="weui-select book-date">
                            <option value="">请选择</option>
                            <?php
                            foreach($canSelectDays as $dayTime=>$day):?>
                            <option class="date-<?=$dayTime?>" value="<?=$dayTime?>"><?=$day?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd"><label class="weui-label">预约时段</label></div>
                    <select name="bookStartTime[]" class="weui-select book-starttime">
                        <option value="">起止时间</option>
                        <?php 
                        $today = strtotime('today');
                        foreach ($startTimes  as $time): ?>
                            <option value="<?=$time?>"><?=date('H时i分', $today+$time)?></option>
                        <?php endforeach;?>
                    </select>
                     至 
                    <select name="bookEndTime[]" class="weui-select book-endtime">
                        <option value="">结束时间</option>
                        <?php 
                        $today = strtotime('today');
                        foreach ($endTimes  as $time): ?>
                            <option value="<?=$time?>"><?=date('H时i分', $today+$time)?></option>
                        <?php endforeach;?>
                    </select>
                </div>

                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <label for="" class="weui-label">备注</label>
                        <textarea class="weui-textarea book-remark" placeholder="" rows="2" name="remark[]"></textarea>
                    </div>
                </div>
                
                <div class="weui-cell error-tips red hidden">
                    
                </div>
                <input type="hidden" name="price[]" value="<?=$unitPrice?>" />
            </div>
        </form>
        <div class="weui-btn-area">
            <input type="hidden" name="totalPrice" id="total_price" value="<?=sprintf("%0.2f",$totalPrice)?>"/>
            <button type="button" class="weui-btn weui-btn_plain-primary" id="btn_add_booking">添加预约</button>
            <button type="button" class="weui-btn weui-btn_primary" id="btn_submit_order">
                提交预约 ￥<span class="price-amount" id="price_amount"><?=sprintf("%0.2f",$totalPrice)?></span>
            </button>
        </div>
    </div>
    
</div>

<script>
<?php $this->beginBlock('pageScript');?>

function toDecimal2(x) { 
  var f = parseFloat(x); 
  if (isNaN(f)) { 
    return false; 
  } 
  var f = Math.round(x*100)/100; 
  var s = f.toString(); 
  var rs = s.indexOf('.'); 
  if (rs < 0) { 
    rs = s.length; 
    s += '.'; 
  } 
  while (s.length <= rs + 2) { 
    s += '0'; 
  } 
  return s; 
} 

var unitPrice = <?=sprintf("%0.2f",$unitPrice)?>;
$('#btn_add_booking').on('click', function(){
    $('#booking_form').append('<div class="weui-cells">'+$('#booking_row').html()+'</div>');
    var price = Number($('#total_price').val())+unitPrice;
    $('#total_price').val(price.toFixed(2));
    $('#price_amount').html(toDecimal2(price));
});

$('#booking_form').on('click', '.btn-remove-booking', function(){
    var oldDate = $(this).parents('.weui-cells').find('.weui-select').val();
    if(oldDate) {
        $('.date-'+oldDate).removeClass('hidden');
    }
    $(this).parents('.weui-cells').remove();
    var price = Number($('#total_price').val()) - unitPrice;
    $('#total_price').val(price);
    $('#price_amount').html(toDecimal2(price));
}).on('change', '.weui-select', function(e){
    var value = $(this).val();
    $('.date-'+value).addClass('hidden');
    var oldDate = $(this).data('olddate');
    if(oldDate) {
        $('.date-'+oldDate).removeClass('hidden');
    }
    $(this).data('olddate', value);
});

$('#btn_submit_order').on('click',function(e){
    if($(this).hasClass('loading')) {
        return false;
    }
    var loadingEle = weui.loading('loading');
    $(this).addClass('loading');
    var formData = $('#booking_form').serializeArray();
    console.log(formData);
    $.ajax({
        url:'<?= Url::to(['/api/order/caller-book'])?>',
        data:formData,
        type:'POST',
        dataType:'json',
        success:function(r) {
            console.log(r);
            $('.error-tips').addClass('hidden');
            if(r.code==0) {//通过
                window.location.href='/pay/caller-book-success?id='+r.result.payId;
                //PayModule.multiPayOrder(r.result.orderIds.join());
            } else if(r.code==10003) {//表单验证失败
                loadingEle.hide();
                for(var k in r.result.errors) {
                    if(!r.result.errors[k]) {
                        continue;
                    }
                    $('.error-tips:eq('+k+')').html(r.result.errors[k]).removeClass('hidden');
                }
            } else {//其他直接提示
                loadingEle.hide();
                weui.alert(r.msg);
            }
        },
        error:function() {
            loadingEle.hide();
            weui.alert('网络失败!');
        }
    });
}) 
<?php $this->endBlock();
$this->registerJs($this->blocks['pageScript']);
?>
</script>