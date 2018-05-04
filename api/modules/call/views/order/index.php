<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\assets\WechatPayAsset;
use frontend\assets\CallAsset;
use frontend\assets\WechatUiAsset;
use yii\widgets\LinkPager;
use common\models\PcallOrder;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\PcallOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;

WechatPayAsset::register($this);
CallAsset::register($this);
WechatUiAsset::register($this);
?>

<?=$this->render('/base/header',['return'=>false]);?>


<div class="pcall-order-index" id="pcall_order_index">
    
    <?php 
    $models = $dataProvider->getModels();
    foreach($models as $model): ?>
        <?=$this->render('_adminView',[
            'order'=>$model
        ]);?>
    <?php endforeach;?>
    <?=
    LinkPager::widget([
          'pagination' => $dataProvider->getPagination(),
        ]);
    ?>
</div>


<script>
<?php $this->beginBlock('pageScript');?>
function changeOrderStatus(id, status, remark) {
    $.ajax({
        url:'/call/order/update-status',
        data:{
            id:id,
            status:status,
            remark:remark
        },
        type:'POST',
        dataType:'json',
        success:function(r){
            if(r.code==='OK') {
                alert('操作成功');
            } else {
                alert(r.msg);
            }
        },
        error:function() {
            alert('网络错误');
        }
    });
}

function orderConfirm(id) {
    $.ajax({
        url:'/call/order/confirm',
        data:{
            id:id
        },
        type:'GET',
        dataType:'json',
        success:function(r){
            if(r.code==='OK') {
                alert('操作成功');
            } else {
                alert(r.msg);
            }
        },
        error:function() {
            alert('网络错误');
        }
    });
}

$('#pcall_order_index').on('click', '.btn-pay-confirmed', function(e){
    e.preventDefault();
    var orderViewEle = $(this).parents('.order-view');
    var orderId = orderViewEle.data('id');
    changeOrderStatus(orderId, <?=PcallOrder::STATUS_PAY_CONFIRMED?>, '');
}).on('click', '.btn-refund', function(e){
    e.preventDefault();
    var orderViewEle = $(this).parents('.order-view');
    var orderId = orderViewEle.data('id');
    changeOrderStatus(orderId, <?=PcallOrder::STATUS_APPLY_FOR_REFUND?>, '');
}).on('click','.btn-user-confirm-service', fcuntion(e){
    e.preventDefault();
    var orderViewEle = $(this).parents('.order-view');
    var orderId = orderViewEle.data('id');
    orderConfirm(orderId, <?=PcallOrder::STATUS_WAIT_FOR_USER_CONFIRM?>, '用户确认服务已经完成');
}).on('click','.btn-caller-confirm-service', fcuntion(e){
    e.preventDefault();
    var orderViewEle = $(this).parents('.order-view');
    var orderId = orderViewEle.data('id');
    changeOrderStatus(orderId, <?=PcallOrder::STATUS_CALLER_CONFIRM_AFTER_SERVICE?>, '达人确认服务已经完成');
});
<?php $this->endBlock();
$this->registerJs($this->blocks['pageScript']);
?>
</script>
