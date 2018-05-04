<?php

namespace nextrip\asyncJob\models;

use Yii;
use Exception;

use nextrip\helpers\Format;
use nextrip\asyncJob\helpers\SaveUserAvatar;

/**
 * 异步任务
 * @property integer $id 自增ID
 * @property integer $type 类型
 * @property string $unique_key 唯一KEY
 * @property string $data 序列化的任务数据
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 * @property integer $state 状态值
 */
class AsyncJob extends \nextrip\helpers\ActiveRecord {
    
    /**
     * 准备好状态
     */
    const STATE_READY = 0;
    
    /**
     * 正在运行中状态
     */
    const STATE_RUNNING = 1;
    
    /**
     * 完成状态
     */
    const STATE_FINISHED = 2;
    
    /**
     * 失败状态
     */
    const STATE_FAILED = 3;
    
    /**
     * 错误状态
     */
    const STATE_ERROR = 4;

    /**
     * 保存用户头像
     */
    const TYPE_SAVE_USER_AVATAR = 1;//用户保存头像 如第三方的用户登录后 , 使用了第三方的头像  使用异步任务保存头像到云存储
    
    /**
     * auto cache config
     * @var array
     */
    protected static $autoCacheConfig = [
        'enable' => true,//set to false auto cache will be disabled
        'duration' => 14400,//cache duration(second)
        'useAttribute'=>[
            'type', 'unique_key'
        ],//support mixed attributes , Eg:['type', 'name']
        'cacheId'=>'cache',//cache component id
    ];
    
    public function init() {
        parent::init();
        
        $this->on(self::EVENT_AFTER_INSERT, ['\nextrip\asyncJob\helpers\EventHandler', 'afterInsert']);
        $this->on(self::EVENT_AFTER_UPDATE, ['\nextrip\asyncJob\helpers\EventHandler', 'afterUpdate']);
        $this->on(self::EVENT_AFTER_DELETE, ['\nextrip\asyncJob\helpers\EventHandler', 'afterDelete']);
    }
    
    public function behaviors() {
        return [
            \yii\behaviors\TimestampBehavior::className()
        ];
    }
    
    public static function tableName() {
        return 'async_job';
    }
    
    /**
     * 添加mns job
     */
    public function addMnsJob() {
        $body = serialize([
            $this->type,
            $this->unique_key
        ]);
        Yii::$app->get('mns')->sendMessage('nt-async-job', $body, $this->getArrayFormatAttribute('data', 'delay', 0));
    }
    
    /**
     * 添加一个任务 (不进行检查 如果需要检查判断 请使用 checkAndAdd) 
     * @param integer $type 任务类型
     * @param string $uniqueKey 不重复key
     * @param [] $data 数据
     * @param integer $delay 延迟秒数 默认为0
     * @param integer $state 任务状态 默认为 AsyncJob::STATE_READY
     * @return AsyncJob
     */
    public static function add($type, $uniqueKey, $data=[], $delay=0, $state=self::STATE_READY) {
        $model = new static;
        $model->type = (int)$type;
        $model->unique_key = (string)trim($uniqueKey);
        if($delay>0) {
            $data['delay'] = (int)$delay;
        }
        $model->data = \nextrip\helpers\Format::toStr($data);
        $model->state = (int)$state;
        $model->save(false);
        return $model;
    }
    
    /**
     * 检查任务并添加
     * @param integer $type 任务类型
     * @param string $uniqueKey 不重复key
     * @param [] $data 数据
     * @param bool $existUpdate 存在的时候是否更新 默认为true
     * @param integer $state 任务状态 默认为 AsyncJob::STATE_READY
     */
    public static function checkAndAdd($type, $uniqueKey, $data=[], $delay=0, $existUpdate=true, $state=self::STATE_READY) {
        if($delay>0) {
            $data['delay'] = (int)$delay;
        }
        $key = [$type, $uniqueKey];
        $isNewModel = false;
        if(!($model = static::findAcModel($key))) {
            Yii::trace('log check and add find not exist '.var_export($model,1));
            try {
                $model = static::add($type, $uniqueKey, $data, $state);
                $isNewModel = true;
            } catch (Exception $ex) {//避免由于重复key产生的错误
                $model = static::findAcModel($key);
                Yii::error($ex);
            }
        }
        if(!$isNewModel && $existUpdate) {
            $time = time();
            $model->setCustomData('addMnsJob', true);
            $model->updateAttributes([
                'data'=>Format::toStr($data),
                'state'=>$state,
                'created_at'=>$time,
                'updated_at'=>$time
            ]);
        }
        return $model;
    }
    
    /**
     * 运行任务
     * @param string $jobHandlerClass 任务处理类 继承于 \nextrip\asyncJob\helpers\JobHandler
     * @return integer 返回任务状态
     */
    public function run($jobHandlerClass) {
        return $jobHandlerClass::run($this);
    }
    
    
    /**
     * 失败后再试时间
     * @param integer $type 类型
     * @param integer $tryCount 尝试次数
     * @return integer  
     */
    public static function getFailRetryTime($type, $tryCount) {
        return $tryCount*$tryCount*300;
    }
    
     /**
     * 失败后再试次数
     * @param integer $type 类型
     * @return integer  
     */
    public static function getFailRetryCount($type) {
        return 4;
    }
}

