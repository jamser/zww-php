<?php

namespace common\models\user;

use Yii;

/**
 * 用户余额变更日志
 * This is the model class for table "user_blance_change_log".
 *
 * @property string $id
 * @property string $user_id
 * @property integer $change_value
 * @property string $old_value
 * @property string $new_value
 * @property string $remark
 * @property string $created_at
 */
class BlanceChangeLog extends \nextrip\helpers\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_blance_change_log';
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
    
    /**
     * 添加记录
     * @param integer $userId 用户ID
     * @param integer $changeValue 改变值
     * @param integer $oldValue 旧值
     * @param integer $newValue 新值
     * @param string $remark 备注
     * @param integer $createdAt 创建时间
     * @return static
     */
    public static function add($userId, $changeValue, $oldValue, $newValue, $remark, $createdAt) {
        $model = new static;
        $model->user_id = (int)$userId;
        $model->change_value = (int)$changeValue;
        $model->old_value = (int)$oldValue;
        $model->new_value = (int)$newValue;
        $model->remark = $remark;
        $model->created_at = (int)$createdAt;
        $model->save(false);
        return $model;
    }
}
