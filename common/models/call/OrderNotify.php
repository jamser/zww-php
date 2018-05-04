<?php

namespace common\models\call;

use Yii;
use common\modules\user\models\User;
use common\modules\user\models\UserVendor;
use common\modules\wechat\models\UnionId;
use common\modules\wechat\models\Mp;

/**
 * This is the model class for table "prodcall_order".
 *
 * @property integer $id
 * @property integer $order_id 订单ID
 * @property integer $type 类型
 * @property integer $notify_type 通知类型
 * @property integer $created_at
 * 
 */
class OrderNotify extends \yii\db\ActiveRecord
{
   
    const TYPE_NOTIFY_CALLER_CONFIRM_AFTER_PAY = 1;
    const TYPE_NOTIFY_USER_AFTER_CALLER_CONFIRMED = 2;
    const TYPE_NOTIFY_USER_AFTER_SERVICE = 3;
    const TYPE_NOTIFY_CALLER_AFTER_SERVICE = 4;
    
    const TYPE_LIST = [
        self::TYPE_NOTIFY_CALLER_CONFIRM_AFTER_PAY => '支付后通知Caller确认',
        self::TYPE_NOTIFY_USER_AFTER_CALLER_CONFIRMED => 'Caller确认后通知用户',
        self::TYPE_NOTIFY_USER_AFTER_SERVICE => '服务后通知用户确认',
        self::TYPE_NOTIFY_CALLER_AFTER_SERVICE => '服务后通知Caller确认',
    ];
    
    const NOTIFY_TYPE_WEIXIN = 1;//微信
    const NOTIFY_TYPE_SMS = 2;//短信
    
    const NOTIFY_LIST = [
        self::NOTIFY_TYPE_WEIXIN => '微信通知',
        self::NOTIFY_TYPE_SMS => '短信通知',
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%prodcall_order_notify}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->db_php;
    }
    
    public function behaviors() {
        return [
            [
                'class'=>\yii\behaviors\TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ],
            
        ];
    }

    /**
     * 添加记录
     * @param type $orderId
     * @param type $type
     * @param type $notifyType
     * @return \static
     */
    public static function add($orderId, $type, $notifyType) {
        $model = new static;
        $model->order_id = (int)$orderId;
        $model->type = (int)$type;
        $model->notify_type = (int)$notifyType;
        $model->save(false);
        return $model;
    }
}
