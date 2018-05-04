<?php

namespace common\models\gift;

use Yii;

/**
 * This is the model class for table "gift_send_detail".
 *
 * @property string $id
 * @property string $gift_id
 * @property string $user_id
 * @property string $to_user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $send_record_id
 * @property string $num
 * @property string $virtual_money_amount
 */
class SendDetail extends \nextrip\helpers\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gift_send_detail';
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
            [['gift_id', 'user_id', 'to_user_id','send_record_id', 'num', 'virtual_money_amount'], 'required'],
            [['gift_id', 'user_id', 'to_user_id', 'send_record_id', 'num', 'virtual_money_amount'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gift_id' => '礼物ID',
            'user_id' => '用户ID',
            'to_user_id' => '至用户ID',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'send_record_id' => '发送记录ID',
            'num' => '数量',
            'virtual_money_amount' => '虚拟金额',
        ];
    }
    
    /**
     * 添加记录
     * @param integer $giftId 礼物ID
     * @param integer $userId 用户ID
     * @param integer $toUserId 至用户ID
     * @param integer $sendRecordId 发送记录ID
     * @param integer $num 数量
     * @param integer $virtualMoneyAmount 虚拟金额
     */
    public static function add($giftId, $userId, $toUserId, $sendRecordId, $num, $virtualMoneyAmount) {
        $model = new static;
        $model->gift_id = (int)$giftId;
        $model->user_id = (int)$userId;
        $model->to_user_id = (int)$toUserId;
        $model->send_record_id = (int)$sendRecordId;
        $model->num = (int)$num;
        $model->virtual_money_amount = (int)$virtualMoneyAmount;
        $model->save(false);
        return $model;
    }
}
