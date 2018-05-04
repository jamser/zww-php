<?php

namespace common\models\user;

use Yii;

/**
 * This is the model class for table "user_from_channel".
 *
 * @property string $id
 * @property string $user_id 用户ID
 * @property string $channel 渠道
 * @property string $created_at 创建时间
 */
class FromChannel extends \nextrip\helpers\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_from_channel';
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
                'class'=> \yii\behaviors\TimestampBehavior::className(),
                'updatedAtAttribute'=>false
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'channel'], 'required'],
            [['user_id', 'created_at'], 'integer'],
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
            'channel' => '渠道',
            'created_at' => '创建时间',
        ];
    }
}
