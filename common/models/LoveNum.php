<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "love_num".
 *
 * @property integer $id
 * @property integer $loved_id
 * @property integer $love_id
 */
class LoveNum extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'love_num';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_php');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loved_id', 'love_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'loved_id' => 'Loved ID',
            'love_id' => 'Love ID',
        ];
    }
}
