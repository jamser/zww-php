<?php 
use yii\helpers\Html;
use common\models\call\Order;
/* @var $order Order */
?>

<div class="weui-form-preview order-view mt20" data-id="<?=$order->id?>">
    <div class="weui-form-preview__hd">
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">金额</label>
            <em class="weui-form-preview__value"><?=$order->money_amount?></em>
        </div>
    </div>
    <div class="weui-form-preview__bd">
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">预约日期</label>
            <span class="weui-form-preview__value"><?=date('Y-m-d', $order->booking_date)?></span>
        </div>
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">预约时段</label>
            <span class="weui-form-preview__value">
                <?=date('H时i分', strtotime('today')+$order->booking_time_start)?> 到 
                <?=date('H时i分', strtotime('today')+$order->booking_time_end)?> 
            </span>
        </div>
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">预约用户</label>
            <span class="weui-form-preview__value"><?=Html::encode($order->callerUser->username)?></span>
        </div>
    </div>
    <div class="weui-form-preview__ft">
        <?php 
        $buttons = [];
        switch ($order['status']) {
            case Order::STATUS_REFUNDED://已退款
                break;
            case Order::STATUS_APPLY_FOR_REFUND://已申请退款
                ?>
                <a class="weui-form-preview__btn weui-form-preview__btn_primary" href="javascript:">联系客服</a>
                <?php
                break;
            case Order::STATUS_CANCEL;//已取消
                break;
            case Order::STATUS_UNPAY://待付款
                ?>
                <a class="weui-form-preview__btn weui-form-preview__btn_default" href="javascript:">联系客服</a>
                <a class="weui-form-preview__btn weui-form-preview__btn_primary" href="/call/order/pay?id=<?=$order['id']?>">支付</a>
                <?php
                break;
            case Order::STATUS_PAY_NOTIFY://收到支付通知
            case Order::STATUS_PAY_CONFIRM://支付已确认
                ?>
                <a class="weui-form-preview__btn weui-form-preview__btn_primary" href="javascript:">联系客服</a>
                <?php
                break;
            case Order::STATUS_PAY_CONFIRMED://已确认
                ?>
                <a class="weui-form-preview__btn weui-form-preview__btn_primary" href="javascript:">联系客服</a>
                <?php
                break;
            case Order::STATUS_WAIT_FOR_SERVICE://等待服务
                ?>
                <a class="weui-form-preview__btn weui-form-preview__btn_default" href="javascript:">联系客服</a>
                <?php
                //该状态下 只要超过了预约的时间 双方就可以进行确认
                if(time() > $order->booking_date+$order->booking_time_start) {
                    if(!Yii::$app->user->isGuest && Yii::$app->user->id==$order->user_id) {
                        //买家
                        echo Html::a('确认服务已完成','#', [
                            'class'=>'weui-form-preview__btn weui-form-preview__btn_primary',
                        ]);
                    }
                    if(!Yii::$app->user->isGuest && Yii::$app->user->id==$order->caller_user_id) {
                        //卖家
                        echo Html::a('确认服务已完成','#', [
                            'class'=>'weui-form-preview__btn weui-form-preview__btn_primary',
                        ]);
                    }
                }
                break;
            case Order::STATUS_WAIT_FOR_USER_RATE://待评价
    //            $buttons[] = Html::a('评价','#', [
    //                'class'=>'btn btn-default right ml10',
    //            ]);
                break;
            case Order::STATUS_DONE://已完成
                break;
            default:
                break;
        }
        ?>
    </div>
</div>