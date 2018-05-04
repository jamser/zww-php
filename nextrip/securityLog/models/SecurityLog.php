<?php

namespace nextrip\securityLog\models;

use Yii;
use nextrip\helpers\Helper;

/**
 * This is the model class for table "user_security_log".
 *
 * @property string $id
 * @property string $user_id
 * @property string $type
 * @property string $message
 * @property string $url
 * @property string $ip
 * @property string $http_user_agent
 * @property string $created_at
 */
class SecurityLog extends \nextrip\helpers\ActiveRecord
{
    /**
     * 尝试错误密码
     */
    const TYPE_TRY_ERROR_PASSWORD = 'tryErrorPwd';
    
    /**
     * 订单状态变化
     */
    const TYPE_ORDER_STATUS_CHANGE = 'orderChangeStatus';
    
    /**
     * 订单付款通知
     */
    const TYPE_ORDER_PAY_NOTIFY = 'orderPayNotify';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_security_log';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'=>\yii\behaviors\TimestampBehavior::className(),
                'updatedAtAttribute'=>false
            ]
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'type' => '类型',
            'message' => '信息',
            'url' => 'Url',
            'ip' => 'Ip',
            'http_user_agent' => 'Http User Agent',
            'created_at' => '创建时间',
        ];
    }
    
    /**
     * 获取数量统计
     * @param integer $userId 用户ID
     * @param integer $type 类型
     * @param string $timeCondition 如 : '>123456789 ' 或 ' <=123456789 ' 或 'between 123 AND  1234456789'
     * @return integer
     */
    public static function getCount($userId, $type, $timeCondition=null) {
        $find = static::find()->where([
            'user_id'=>(int)$userId,
            'type'=>$type,
        ]);
        if($timeCondition) {
            $find->andWhere('created_at'.$timeCondition);
        }
        return $find->count();
    }
    
    /**
     * 保存日志
     * @param string $type 日志类型
     * @param int $userId 用户ID
     * @param string $message 消息
     */
    public static function add($type, $userId, $message) {
        $model = new static;
        $model->user_id = (int)$userId;
        $model->type = $type;
        
        $app = Yii::$app;
        $request = $app->getRequest();
        if($request->getIsConsoleRequest()) {
            $model->url = "{$app->id}/".($app->module ? "{$app->module->id}/" : '')."{$app->controller->id}/{$app->action->id}";
            $model->ip = '';
            $model->http_user_agent = $request->getUserAgent();
        } else {
            $model->url = $request->getAbsoluteUrl();
            $model->ip = Helper::getIp();
            $model->http_user_agent = 'console';
        }
        
        $model->message = $message;
        $model->save(false);
    }
    
    /**
     * 批量添加数据
     * @param integer $userId 用户ID
     * @param array $attributes $key=>$value 属性的数据
     *  
     */
    public static function addBatch($userId, $attributes) {
        $db = static::getDb();
        $table = static::tableName();
        $rows = [];
        $time = time();
        foreach($attributes as $key=>$value) {
            $rows[] = [
                'user_id'=>$userId,
                'field_name'=>$key,
                'field_value'=>$value,
                'create_at'=>$time
            ];
        }
        if($rows) {
            $db->createCommand()->batchInsert($table, ['user_id', 'field_name', 'field_value', 'created_at'], $rows)->execute();
        }
    }
    
}
