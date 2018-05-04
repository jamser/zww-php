<?php

namespace common\models\user;

use Yii;

/**
 * This is the model class for table "user_change_log".
 *
 * @property string $id
 * @property string $user_id
 * @property string $field
 * @property string $old_value
 * @property string $new_value
 * @property string $remark
 * @property string $created_at
 */
class ChangeLog extends \nextrip\helpers\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_change_log';
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
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'field', 'old_value', 'new_value', 'remark', 'created_at'], 'required'],
            [['user_id', 'created_at'], 'integer'],
            [['remark'], 'string'],
            [['field'], 'string', 'max' => 32],
            [['old_value', 'new_value'], 'string', 'max' => 255],
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
            'field' => '字段',
            'old_value' => '旧值',
            'new_value' => '新值',
            'remark' => '备注',
            'created_at' => '创建时间',
        ];
    }
    
    /**
     * 获取变化日志数量
     * @param integer $userId 用户id
     * @param string $field 属性
     * @param integer $timeLimit 时间限制
     * @return integer
     */
    public static function getChangeCount($userId, $field, $timeLimit=null) {
        return static::find()->where('user_id='.(int)$userId.' AND `field`=:field '.($timeLimit ? ' AND created_at>'.(int)$timeLimit : ''), ['field'=>$field])->count();
    }
    
    /**
     * 添加记录
     * @param sring $field 字段名称
     * @param integer $userId 用户ID
     * @param string $oldValue 旧值
     * @param string $newValue 新值
     * @param string $remark 备注
     * @return \static
     */
    public static function add($field, $userId, $oldValue, $newValue, $remark='') {
        $model = new static;
        $model->old_value = $oldValue;
        $model->new_value = $newValue;
        $model->user_id = (int)$userId;
        $model->field = $field;
        $model->remark = $remark;
        $model->save(false);
        return $model;
    }
}
