<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cost_sum".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $member_name
 * @property integer $buy_num
 * @property integer $catch_num
 * @property integer $coins
 */
class CostSum extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cost_sum';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'buy_num', 'catch_num', 'coins'], 'integer'],
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
            'buy_num' => 'Buy Num',
            'catch_num' => 'Catch Num',
            'coins' => 'Coins',
        ];
    }
}
