<?php

namespace common\models\gift;

use Yii;

/**
 * This is the model class for table "gift_send_record".
 *
 * @property string $id
 * @property string $user_id
 * @property string $to_user_id
 * @property string $virtual_money_amount
 * @property string $created_at
 * @property string $updated_at
 */
class SendRecord extends \nextrip\helpers\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gift_send_record';
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
            \yii\behaviors\TimestampBehavior::className(),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'to_user_id', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'to_user_id', 'virtual_money_amount', 'created_at', 'updated_at'], 'integer'],
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
            'to_user_id' => '至对方用户ID',
            'virtual_money_amount' => '虚拟金额',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * 添加记录
     * @param integer $userId 用户ID
     * @param integer $toUserId 至用户ID
     * @param integer $virtualMoneyAmount 虚拟金额
     */
    public static function add($userId, $toUserId, $virtualMoneyAmount) {
        $model = new static;
        $model->user_id = (int)$userId;
        $model->to_user_id = (int)$toUserId;
        $model->virtual_money_amount = (int)$virtualMoneyAmount;
        $model->save(false);
        return $model;
    }
}
