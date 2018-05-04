<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "charge_sum".
 *
 * @property integer $id
 * @property string $price
 * @property integer $member_id
 * @property string $member_name
 */
class ChargeSum extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'charge_sum';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price'], 'number'],
            [['member_id'], 'integer'],
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
            'price' => 'Price',
            'member_id' => 'Member ID',
            'member_name' => 'Member Name',
        ];
    }
}
