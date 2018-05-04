<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wechat_unionid".
 *
 * @property string $id
 * @property string $open_id
 * @property string $union_id
 * @property string $app_id
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class WechatUnionid extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wechat_unionid';
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
            [['open_id', 'union_id', 'app_id', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['open_id', 'union_id'], 'string', 'max' => 128],
            [['app_id'], 'string', 'max' => 32],
            [['open_id', 'app_id', 'union_id'], 'unique', 'targetAttribute' => ['open_id', 'app_id', 'union_id'], 'message' => 'The combination of Open ID, Union ID and App ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'open_id' => 'Open ID',
            'union_id' => 'Union ID',
            'app_id' => 'App ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
