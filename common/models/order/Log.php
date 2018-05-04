<?php
namespace common\models\order;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * Log model
 *
 * @property integer $id
 * @property integer $order_id 订单ID
 * @property integer $operator_user_id 操作人ID
 * @property string $remark 备注
 * @property integer $created_at 创建时间
 */
class Log extends \nextrip\helpers\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_log}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->db_php;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'=>TimestampBehavior::className(),
                'updatedAtAttribute'=>false
            ]
        ];
    }
    
    
    public function attributeLabels() {
        return [
            'id'=>'ID',
            'order_id'=>'订单ID',
            'operator_user_id'=>'操作人ID',
            'remark'=>'备注',
            'created_at'=>'创建时间',
        ];
    }
    
    /**
     * 添加日志
     * @param integer $orderId 订单ID
     * @param string $operatorUserId 操作人
     * @param string $remark 备注
     */
    public static function add($orderId, $operatorUserId, $remark) {
        $model = new static([
            'order_id'=>(int)$orderId,
            'operator_user_id'=>$operatorUserId,
            'remark'=>$remark
        ]);
        $model->save(false);
    }
}
