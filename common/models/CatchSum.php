<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "catch_sum".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $member_name
 * @property integer $catch_num
 */
class CatchSum extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catch_sum';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'catch_num'], 'integer'],
            [['member_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'member_name' => 'Member Name',
            'catch_num' => 'Catch Num',
        ];
    }
}
