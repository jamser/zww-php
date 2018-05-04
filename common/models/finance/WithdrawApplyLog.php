<?php

namespace common\models\finance;

use Yii;

/**
 * This is the model class for table "finance_withdraw_apply_log".
 *
 * @property string $id
 * @property string $user_id
 * @property string $withdraw_id
 * @property integer $type
 * @property string $remark
 * @property string $admin_id
 * @property string $created_at
 * @property string $updated_at
 */
class WithdrawApplyLog extends \nextrip\helpers\ActiveRecord
{
    const TYPE_REJECTED = -10;
    const TYPE_REVIEW_PASS = 10;
    const TYPE_PAY = 100;
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'finance_withdraw_apply_log';
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
            \yii\behaviors\TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'withdraw_id'], 'required'],
            [['user_id', 'withdraw_id', 'type', 'admin_id'], 'integer'],
            [['remark'], 'string', 'max' => 1024],
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
            'withdraw_id' => '提现ID',
            'type' => '类型',
            'remark' => '备注',
            'admin_id' => '管理员ID',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * 添加记录
     * @param integer $userId
     * @param integer $withdrawId
     * @param integer $type
     * @param string $remark
     * @param integer $adminId
     * @return \static
     */
    public static function add($userId, $withdrawId, $type, $remark, $adminId) {
        $model = new static([
            'user_id'=>(int)$userId,
            'withdraw_id'=>(int)$withdrawId,
            'type'=>(int)$type,
            'remark'=>$remark,
            'admin_id'=>(int)$adminId,
        ]);
        $model->save(false);
        return $model;
    }
}
