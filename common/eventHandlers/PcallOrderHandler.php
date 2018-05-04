<?php

namespace common\eventHandlers;

use Yii;
use common\models\call\Order;
use common\models\call\OrderNotify;

use common\models\User;
use nextrip\wechat\models\Mp;
use common\models\call\Caller;

use nextrip\securityLog\models\SecurityLog;

class PcallOrderHandler extends \yii\base\Object {
    
    /**
     * 支付成功后的事件
     * @param \yii\base\Event $event
     */
    public static function afterPaySuccess($event) {
        $order = $event->sender;
        /* @var $order Order */
        Yii::$app->mns->topicPublishMessage('pcallOrderTopic', json_encode([
            'event'=>$event->name,
            'orderId'=>$order->id
        ]));
        
        if($event->name===Order::EVENT_AFTER_PAY_SUCCESS) {
            //定时检查用户是否已经确认
            Yii::$app->mns->queueSendMessage('pcallOrderQueue', json_encode([
                'event'=>'checkCallerConfirm',
                'orderId'=>$order->id
            ], JSON_UNESCAPED_UNICODE), min([Order::CALLER_CONFIRM_EXPIRE_TIME, max(0, $order->booking_date- Order::CALLER_CONFIRM_BEFORE_SERVICE_DAY - time())]));
            
        }
    }
    
    /**
     * 定时检查订单Caller是否确认
     * @param Order $order 订单
     */
    public static function checkCallerConfirm($order, $fromUserId) {
        if(in_array($order->status, [Order::STATUS_WAIT_FOR_SERVER_CONFIRM])) {
            #超过支付指定时间 未确认
            if(time()-$order->pay_time >= Order::CALLER_CONFIRM_EXPIRE_TIME) {
                $order->changeStatus(Order::STATUS_APPLY_FOR_REFUND, $fromUserId, '超时未确认, 自动取消订单');
            }
            #服务前一天晚上10点
            if($order->booking_date - Order::CALLER_CONFIRM_BEFORE_SERVICE_DAY <= time()) {
                $order->changeStatus(Order::STATUS_APPLY_FOR_REFUND, $fromUserId, '服务前一天未及时确认, 自动取消订单');
            }
        }
    }
    
    /**
     * Caller确认成功后的事件
     * @param \yii\base\Event $event
     */
    public static function afterCallerConfirmSuccess($event) {
        $order = $event->sender;
        /* @var $order Order */
        if(!OrderNotify::findOne(['order_id'=>$order->id,'type'=>  OrderNotify::TYPE_NOTIFY_USER_AFTER_CALLER_CONFIRMED])) {
            if(! ($user = User::findAcModel($order->user_id) )) {
                throw new \Exception("找不到 用户{$order->user_id} 的基础资料");
            }
            if(! ($callerUser = User::findAcModel($order->caller_user_id) )) {
                throw new \Exception("找不到 用户{$order->caller_user_id} 的基础资料");
            }
            $mp = Mp::findAcModel('default');
            $startTime = date('H点i分', $order->booking_date + $order->booking_time_start);
            $endTime =  date('H点i分', $order->booking_date + $order->booking_time_end);
            $callerName = \common\models\Setting::getValueByKey('caller');
            $tplData = [
                'first'=>"您好，{$callerName} {$callerUser->username} 已经确认将在 ".date('Y-m-d',$order->booking_date)." 日 {$startTime}-{$endTime} 叫你起床",
                'keyword1'=>$order->id,
                'keyword2'=>"{$callerName} {$callerUser->nickname} 已接单",
                'remark'=>'点击查看订单'
            ];
                /**
                 * {{first.DATA}}
信息ID：{{keyword1.DATA}}
详情：{{keyword2.DATA}}
{{remark.DATA}}
在发送时，需要将内容中的参数（{{.DATA}}内为参数）赋值替换为需要的信息
内容示例
您好，审核结果如下
信息ID：30140603320008
详情：备案内容通过审核
感谢您的使用。
                 */
            $templateId = 'Upr5ajNfbszPwMZTnSIRiXMEpbL_oM5e0s0yQ2qEzmI';
            $ret = $mp->sendTemplateMsg($order->getCallerWechatOpenId(), $templateId, $tplData, "{$mp->domain}/call/order/view?id={$order->id}");
            if(!$ret) {
                throw new \Exception('发送模板消息给'.$order->getUserWechatOpenId().($mp->sendTplMsgError ? $mp->sendTplMsgError : '发送模板消息失败'));
            } else {
                OrderNotify::add($order->id, OrderNotify::TYPE_NOTIFY_USER_AFTER_CALLER_CONFIRMED, OrderNotify::NOTIFY_TYPE_WEIXIN);
            }
        }
        
    }
    
    /**
     * 异步处理 在支付成功后执行
     * @param Order $order 订单
     */
    public static function callAfterPaySuccess($order) {
        //发送微信通知给服务方 找到微信号 发送模板消息
        if($order->status>Order::STATUS_WAIT_FOR_SERVER_CONFIRM) {
            return;
        }
        if(!OrderNotify::findOne(['order_id'=>$order->id,'type'=>  OrderNotify::TYPE_NOTIFY_CALLER_CONFIRM_AFTER_PAY])) {
            if(!$callUser = User::findOne([
                'user_id'=>$order->caller_user_id
            ])) {
                throw new \Exception("找不到 用户{$order->user_id} 的基础资料");
            }
            $callerName = \common\models\Setting::getValueByKey('callerName');
            if(!($caller= Caller::findAcModel((int)$callUser->id))) {
                throw new \Exception("找不到 用户{$callUser->id} 对应的 {$callerName}");
            }
            $mp = Mp::findAcModel('default');
            $tplData = [
                'first'=>'您好，您收到了一个新任务，请尽快确认是否接受该任务, 超过12小时则自动放弃该订单',
                'keyword1'=>$order->id,
                'keyword2'=>$callUser->username,
                'keyword3'=>sprintf('%0.2f',$order->money_amount/100),
                'keyword4'=>date('Y-m-d',$order->booking_date).'日叫Ta起床',
                'keyword5'=>date('Y-m-d',$order->pay_time + 3600*12),
                'remark'=>'点击查看任务并进行确认是否接受'
            ];
            /**
     * {{first.DATA}}
订单编号：{{keyword1.DATA}}
客户昵称：{{keyword2.DATA}}
订单价格：{{keyword3.DATA}}
订单标题：{{keyword4.DATA}}
订单截止时间：{{keyword5.DATA}}
{{remark.DATA}}
     * 您好，您收到了一个新订单，请尽快接单处理
订单编号：WX02302301
客户昵称：达芙妮女士
订单价格：200元
订单标题：帮我的孩子找英语家教
订单截止时间：2015-04-10 23:00
点击查看订单详情
     */
            $templateId = 'rZ12mY-qqJo1xSbeh7VadNJRoP1BzX7SHyqS0YJsOgk';
            $ret = $mp->sendTemplateMsg($order->getCallerWechatOpenId(), $templateId, $tplData, "{$mp->domain}/call/caller/confirm-order?id={$order->id}");
            if(!$ret) {
                throw new \Exception('发送模板消息给'.$order->getCallerWechatOpenId().($mp->sendTplMsgError ? $mp->sendTplMsgError : '发送模板消息失败'));
            } else {
                OrderNotify::add($order->id, OrderNotify::TYPE_NOTIFY_CALLER_CONFIRM_AFTER_PAY, OrderNotify::NOTIFY_TYPE_WEIXIN);
            }
        }
    }
    
    /**
     * 修改状态后触发事件
     * @param \nextrip\helpers\Event $event
     */
    public static function afterChangeStatus($event) {
        $order = $event->sender;
        /* @var $order \common\models\PcallOrder */
        $userId = $event->customData['fromUserId'];
        $oldStatus = $event->customData['oldStatus'];
        $newStatus = $order->status;
        
        switch ($newStatus) {
            case Order::STATUS_APPLY_FOR_REFUND:
                //订单已申请退款
                break;
            case Order::STATUS_CANCEL://取消
                //订单要进行退款
                break;
            case Order::STATUS_WAIT_FOR_DISPATCH://等待分发任务
                break;
            case Order::STATUS_WAIT_FOR_SERVER_CONFIRM://等待服务方确认 发送通知告诉服务方有新订单待确认
                break;
            case Order::STATUS_WAIT_FOR_SERVICE://等待服务
                break;
            case Order::STATUS_CALLER_CONFIRM_AFTER_SERVICE://服务方确认 需要上传通话记录照片作为凭证 直接让用户在任务页面进行提交 不发送通知
                break;
            case Order::STATUS_USER_CONFIRM_NO_SERVICE://用户确认没有服务
                break;
            case Order::STATUS_WAIT_FOR_USER_RATE://等待用户评分
                break;
            case Order::STATUS_DONE://订单完成
                break;
            
            default:
                break;
        }
        
        $remark = $event->customData['remark'];
        $message = sprintf("当前用户 %s 把订单状态从 %s 修改为 %s , 修改原因 : %s ;", 
                $userId, $oldStatus, $newStatus, $remark);
        SecurityLog::add(SecurityLog::TYPE_ORDER_STATUS_CHANGE, $order->user_id, $message);
    }
    
    public static function afterChangeCallerStatus($event) {
        $caller = $event->sender;
        /* @var $order \common\models\PcallOrder */
        $userId = $event->customData['fromUserId'];
        $oldStatus = $event->customData['oldStatus'];
        $newStatus = $caller->status;
        
        switch ($newStatus) {
            case PcallCaller::STATUS_REVIEW_PASS://通过
                break;
            case PcallCaller::STATUS_REVIEW_REJECTED://拒绝
                break;
            case PcallCaller::STATUS_WAITING_FOR_REVIEW://等待审核
                break;
            default:
                break;
        }
        
        $logInfo = $event->customData['logInfo'];
        $message = sprintf("当前用户 %s 把达人申请状态从 %s 修改为 %s , 修改原因 : %s ;", 
                $userId, $oldStatus, $newStatus, $logInfo);
        SecurityLog::add(SecurityLog::TYPE_ORDER_STATUS_CHANGE, $caller->user_id, $message);
    
    }
} 

