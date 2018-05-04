<?php 
use yii\helpers\Html;
use common\models\PcallOrder;
/* @var $order PcallOrder */
?>
<div class="order-view" data-id="<?=$order->id?>">
    
    <div class="row">
        <div class="col-xs-9">
            <div class="order-title">
                <?=$order->booking_date?>日叫用户 <?=Html::encode($order->callUser->nickname)?> 起床
            </div>
        </div>
        <div class="col-xs-3">
            <div class="price-amount center">￥<span class="num"><?=$order->money_amount?></span></div>
            
            <div class="order-status center"><?=PcallOrder::$status_list[$order->status]?></div>
        </div>
    </div>
    <div class="row">
        <div class="order-info col-xs-12">

            <div class="">预订时间：<?=date('H:i', $order->booking_time_start+  strtotime('today'))?> 到 <?=date('H:i', $order->booking_time_end+  strtotime('today'))?></div>

        </div>
    </div>
    <?php 
    $buttons = [];
    switch ($order['status']) {
        case PcallOrder::STATUS_REFUNDED://已退款
            break;
        case PcallOrder::STATUS_APPLY_FOR_REFUND://已申请退款
            $buttons[] = Html::a('联系客服','#', [
                'class'=>'btn btn-default right ml10',
            ]);
            break;
        case PcallOrder::STATUS_CANCEL;//已取消
            break;
        case PcallOrder::STATUS_UNPAY://待付款
            $buttons[] = Html::a('支付','/trade/order/pay?id='.$order['id'], [
                'class'=>'btn btn-default btn-pay right ml10',
                'id'=>'btn_pay_'.$order['id']
            ]);
            $buttons[] = Html::a('联系客服','#', [
                'class'=>'btn btn-default btn-contact right ml10',
            ]);
            break;
        case PcallOrder::STATUS_PAY_NOTIFY://收到支付通知
        case PcallOrder::STATUS_PAY_CONFIRM://支付已确认
//            $buttons[] = Html::a('申请退款','#', [
//                'class'=>'btn btn-default right ml10',
//            ]);
            $buttons[] = Html::a('联系客服','#', [
                'class'=>'btn btn-default btn-contact right ml10',
            ]);
            break;
        case PcallOrder::STATUS_PAY_CONFIRMED://已确认
//            $buttons[] = Html::a('申请退款','#', [
//                'class'=>'btn btn-default right ml10',
//            ]);
            $buttons[] = Html::a('联系客服','#', [
                'class'=>'btn btn-default btn-contact right ml10',
            ]);
            break;
        case PcallOrder::STATUS_WAIT_FOR_USER_RATE://待评价
//            $buttons[] = Html::a('评价','#', [
//                'class'=>'btn btn-default right ml10',
//            ]);
            break;
        case PcallOrder::STATUS_DONE://已完成
            break;
        default:
            break;
    }
    if($buttons):
    ?>
    <div class="mt20 order-action clearfix">
        <?php 
        foreach($buttons as $button):
            echo $button;
        endforeach;
        ?>
    </div>
    <?php endif;?>
</div>
