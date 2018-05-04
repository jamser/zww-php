<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "account".
 *
 * @property integer $id
 * @property integer $coins
 * @property integer $superTicket
 * @property integer $coinFirstCharge
 */
class Account extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'coins', 'superTicket', 'coinFirstCharge'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'coins' => 'Coins',
            'superTicket' => 'Super Ticket',
            'coinFirstCharge' => 'Coin First Charge',
        ];
    }
}
