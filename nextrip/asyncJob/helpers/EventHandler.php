<?php

namespace nextrip\asyncJob\helpers;

use nextrip\asyncJob\models\AsyncJob;

class EventHandler extends \yii\base\Object {
    
    /**
     * 在插入异步任务后执行
     * @param \nextrip\helpers\Event $event
     */
    public function afterInsert($event) {
        $asyncJob = $event->sender;
        /* @var $asyncJob \nextrip\asyncJob\models\AsyncJob */
        if($asyncJob->state==AsyncJob::STATE_READY) {
            $asyncJob->addMnsJob();
        }
    }
    
    /**
     * 在更新异步任务后执行
     * @param \nextrip\helpers\Event $event
     */
    public function afterUpdate() {
        $asyncJob = $event->sender;
        /* @var $asyncJob \nextrip\asyncJob\models\AsyncJob */
        
        if($asyncJob->getCustomData('addMnsJob') && $asyncJob->state==AsyncJob::STATE_READY) {
            $asyncJob->addMnsJob();
        }
    }
    
    public function afterDelete() {
        
    }
    
}