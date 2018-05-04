<?php

namespace common\eventHandlers;

use nextrip\asyncJob\models\AsyncJob;

class JobHandler extends \nextrip\asyncJob\helpers\JobHandler {
    
    /**
     * @param \nextrip\asyncJob\models\AsyncJob
     */
    public static function run($job) {
        $state = AsyncJob::STATE_ERROR;
        switch ($job->type) {
            case AsyncJob::TYPE_SAVE_USER_AVATAR:
                $state = (new SaveUserAvatarJob(['asyncJob'=>$job]))->run();
                break;
            default:
                break;
        }
        return $state;
    }
    
    
} 

