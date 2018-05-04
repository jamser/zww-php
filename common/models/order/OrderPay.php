<?php
namespace common\models\order;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * OrderPay model
 *
 * @property integer $id
 * @property integer $user_id 用户ID
 * @property integer $pay_id 支付ID 
 * @property integer $order_id 订单ID
 * @property integer $prod 产品
 * @property integer $created_at 创建时间
 */
class OrderPay extends \nextrip\helpers\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_pay}}';
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
            'user_id'=>'用户ID',
            'order_id'=>'订单ID',
            'pay_id'=>'支付ID',
            'created_at'=>'创建时间',
        ];
    }
    
    /**
     * 批量添加
     * @param [] $rows 添加的数据数组
     */
    public static function addBatch($rows) {
        $values = [];
        foreach($rows as $row) {
            $values[] = sprintf('(%s, %s, %s, %s, %s)', (int)$row['user_id'], (int)$row['pay_id'], (int)$row['order_id'], (int)$row['prod'], (int)$row['created_at']);
        }
        $sql = 'INSERT INTO '.static::tableName().' (user_id, pay_id, order_id, prod, created_at) VALUES '. implode(',', $values);
        static::getDb()->createCommand($sql)->execute();
    }
}
