<?php

namespace common\models\user;

use Yii;

/**
 * 用户虚拟货币变更日志
 * This is the model class for table "user_virtual_money_change_log".
 *
 * @property string $id
 * @property string $user_id
 * @property integer $change_value
 * @property string $old_value
 * @property string $new_value
 * @property string $remark
 * @property string $created_at
 */
class VirtualMoneyChangeLog extends \nextrip\helpers\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_virtual_money_change_log';
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
    public function rules()
    {
        return [
            [['user_id', 'change_value', 'old_value', 'new_value', 'remark', 'created_at'], 'required'],
            [['user_id', 'change_value', 'old_value', 'new_value', 'created_at'], 'integer'],
            [['remark'], 'string'],
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
            'change_value' => '改变值',
            'old_value' => '旧值',
            'new_value' => '新值',
            'remark' => '备注',
            'created_at' => '创建时间',
        ];
    }
}
