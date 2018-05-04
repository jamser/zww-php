<?php 
use yii\helpers\Html;
use common\models\PcallOrder;
use yii\widgets\DetailView;
/* @var $this \yii\web\View */
/* @var $order PcallOrder */
?>
<div class="order-view" data-id="<?=$order->id?>" id="order_view_<?=$order->id?>">
    
    <div class="row">
        <div class="order-info col-xs-12">
            <?= DetailView::widget([
                'model' => $order,
                'attributes' => [
                    'id',
                    [
                        'label'=>'用户信息',
                        'value'=> sprintf("ID %s 昵称 %s 手机号 %s", $order->user_id, $order->callUser->nickname, $order->callUser->phone)
                    ],
                    [
                        'label'=>'接单用户信息',
                        'value'=> !$order->caller_id ? '未分配接单人' : sprintf("ID %s 昵称 %s 手机号 %s", $order->caller_id, $order->callerCallUser->nickname, $order->callerCallUser->phone)
                    ],
                    [
                        'label'=>'预约时间',
                        'value'=> $order->booking_date.'日 '.date('H:i', strtotime('today')+ $order->booking_time_start).' 到 '.date('H:i', strtotime('today')+ $order->booking_time_end)
                    ],
                    'money_amount',
                    'remark',
                    [
                        'label'=>'状态',
                        'value'=>PcallOrder::$status_list[$order->status]
                    ],
                    'pay_id',
                    [
                        'label'=>'支付时间',
                        'value'=>$order->pay_time ? date('Y-m-d H:i:s', $order->pay_time) : '未支付'
                    ],
                    [
                        'label'=>'指派类型',
                        'value'=>  PcallOrder::$dispatchTypeList[$order->dispatch_type]
                    ],
                    [
                        'label'=>'指派时间',
                        'value'=>$order->dispatched_at ? date('Y-m-d H:i:s', $order->dispatched_at) : '未指派'
                    ],
                    [
                        'label'=>'创建时间',
                        'value'=>$order->created_at ? date('Y-m-d H:i:s', $order->created_at) : ''
                    ],
                ],
            ]) ?>
        </div>
    </div>
    <?php 
    $buttons = [];
    switch ($order['status']) {
        case PcallOrder::STATUS_REFUNDED://已退款
            break;
        case PcallOrder::STATUS_APPLY_FOR_REFUND://已申请退款
            $buttons[] = Html::a('发起退款','#', [
                'class'=>'btn btn-default right ml10 btn-refund',
            ]);
            $buttons[] = Html::a('修改为已退款','#', [
                'class'=>'btn btn-default right ml10 btn-refund-success',
            ]);
            $buttons[] = Html::a('退款驳回','#', [
                'class'=>'btn btn-default right ml10 btn-refund-rejected',
            ]);
            break;
        case PcallOrder::STATUS_CANCEL;//已取消
            break;
        case PcallOrder::STATUS_UNPAY://待付款
            $buttons[] = Html::a('取消订单','#', [
                'class'=>'btn btn-default right ml10 btn-cancel',
            ]);
            break;
        case PcallOrder::STATUS_PAY_NOTIFY://收到支付通知
        case PcallOrder::STATUS_PAY_CONFIRM://支付已确认
            $buttons[] = Html::a('确认收到付款','#', [
                'class'=>'btn btn-default right ml10 btn-pay-confirmed',
            ]);
            break;
        case PcallOrder::STATUS_PAY_CONFIRMED://已确认
            $buttons[] = Html::a('发起退款','#', [
                'class'=>'btn btn-default right ml10 btn-refund',
            ]);
            
            if(!$order->caller_id) {
                $buttons[] = Html::a('分配达人','/call/order/dispatch?id='.$order->id, [
                    'class'=>'btn btn-default btn-dispatch right ml10',
                ]);
            } else {
                $buttons[] = Html::a('重新分配达人','#', [
                    'class'=>'btn btn-default btn-dispatch right ml10',
                ]);
            }
            break;
        case PcallOrder::STATUS_WAIT_FOR_DISPATCH://等待分配
            if(!$order->caller_id) {
                $buttons[] = Html::a('分配达人','#', [
                    'class'=>'btn btn-default btn-dispatch right ml10',
                ]);
            } else {
                $buttons[] = Html::a('重新分配达人','#', [
                    'class'=>'btn btn-default btn-dispatch right ml10',
                ]);
            }
            break;
        case PcallOrder::STATUS_WAIT_FOR_SERVER_CONFIRM://等待服务方确认
            $buttons[] = Html::a('取消订单','#', [
                'class'=>'btn btn-default right ml10 btn-cancel',
            ]);
            break;
        case PcallOrder::STATUS_WAIT_FOR_SERVICE://等待服务
            $buttons[] = Html::a('取消订单','#', [
                'class'=>'btn btn-default right ml10 btn-cancel',
            ]);
            break;
        case PcallOrder::STATUS_CALLER_CONFIRM_AFTER_SERVICE://等待服务方在服务后进行确认
            break;
        case PcallOrder::STATUS_WAIT_FOR_USER_CONFIRM://等待用户确认
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