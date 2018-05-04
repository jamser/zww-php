<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "user_account".
 *
 * @property string $id
 * @property string $user_id
 * @property integer $type
 * @property string $value
 * @property string $created_at
 * @property string $updated_at
 */
class UserAccount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_account';
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
            [['user_id', 'type', 'value', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'type', 'created_at', 'updated_at'], 'integer'],
            [['value'], 'string', 'max' => 64],
            [['user_id', 'type'], 'unique', 'targetAttribute' => ['user_id', 'type'], 'message' => 'The combination of User ID and Type has already been taken.'],
            [['type', 'value'], 'unique', 'targetAttribute' => ['type', 'value'], 'message' => 'The combination of Type and Value has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'value' => 'Value',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
