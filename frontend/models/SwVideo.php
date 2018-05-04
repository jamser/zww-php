<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sw_video".
 *
 * @property integer $id
 * @property integer $roomId
 * @property string $sw_appid
 * @property string $sw_channel
 * @property integer $sw_uid
 */
class SwVideo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sw_video';
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
            [['roomId', 'sw_uid'], 'integer'],
            [['sw_appid', 'sw_channel'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'roomId' => 'Room ID',
            'sw_appid' => 'Sw Appid',
            'sw_channel' => 'Sw Channel',
            'sw_uid' => 'Sw Uid',
        ];
    }
}
