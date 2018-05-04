<?php

namespace common\eventHandlers;

use common\models\finance\WithdrawApply;

/* 
 * file 事件处理
 */
class WithdrawHandler extends \yii\base\Object {
    
    /**
     * 提现审核通过后
     * @param \nextrip\helpers\Event $event
     */
    public static function afterReviewPass($event) {
        $withdraw = $event->sender;
        /* @var WithdrawApply $withdraw */
    }
    
    /**
     * 提现审核拒绝后
     * @param \nextrip\helpers\Event $event
     */
    public static function afterReviewRejected($event) {
        $withdraw = $event->sender;
        /* @var WithdrawApply $withdraw */
    }
   
    
}