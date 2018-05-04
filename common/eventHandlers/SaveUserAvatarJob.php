<?php

namespace common\eventHandlers;

use Yii;

use common\models\User;

use nextrip\helpers\File;
use nextrip\asyncJob\models\AsyncJob;

use OSS\OssClient;

class SaveUserAvatarJob extends \yii\base\Object {
    
    /**
     * 异步任务
     * @var AsyncJob 
     */
    public $asyncJob;
    
    /**
     * 运行用户保存头像任务
     * @return integer 任务状态
     */
    public function run() {
        $state = AsyncJob::STATE_FINISHED;
        $asyncJob = $this->asyncJob;
        if($asyncJob->state<0) {
            return $state;
        }
        $userId = (int)$asyncJob->unique_key;
        $user = User::findAcModel($userId);
        if(!$user) {
            goto FINISH_SAVE_AVATAR;
        }
        
        if($user->avatar && strpos($user->avatar, 'dnrcdn.com/')===false) {
            if(strpos($user->avatar, 'http')!==0) {//开头不包含http 需要添加http协议
                $user->avatar = 'http:'.$user->avatar;
            }
            $tempFile = Yii::getAlias('@runtime').'/'.date('Ym/d').'/'.uniqid(randStr(6)).'.jpg';
            $time = time();
            $fileContent = $imgSize = null;
            if( ($fileContent=File::grabFileAndSave($user->avatar, $tempFile))  && ($imgSize = getimagesize($tempFile)) ) {//保存图片成功
                $ossConfig = Yii::$app->params['aliOss']['img'];
                $ossClient = new OssClient($ossConfig['accessKeyId'], $ossConfig['accessKeySecret'], $ossConfig['endpoint'], true);
                
                $object = 'user/'.$user->id.'/'.date('Ym').'/avatar_'.$time.  randStr(4).'.jpg';
                $ossClient->uploadFile($ossConfig['bucket'], $object, $tempFile);
                $user->avatar = $ossConfig['publicHost'].'/'.$object;
                $user->updateAttributes(['avatar']);
            } else {//保存图片失败
                Yii::error("保存用户 {$user->id} 头像图片 {$user->avatar} 失败 , 抓取 file content : ".$fileContent.' ; 图片尺寸 : '.  var_export($imgSize,1));
                $state = AsyncJob::STATE_FAILED;
                goto FINISH_SAVE_AVATAR;
            }
        }

        FINISH_SAVE_AVATAR:
        if(!empty($tempFile)) {
            @unlink($tempFile);
        }
        $asyncJob->updateAttributes([
            'state'=>$state
        ]);
        return $state;
    }
}

