<?php

namespace common\models\dollstatistic;

use Yii;

/**
 * This is the model class for table "pay_daily".
 *
 * @property integer $id
 * @property string $day
 * @property string $registration_num
 * @property string $charge_amount
 * @property string $charge_user_num
 * @property string $charge_order_num
 * @property string $new_user_charge_num
 * @property string $new_user_charge_order_num
 * @property string $new_user_charge_amount
 * @property string $old_user_charge_num
 * @property string $old_user_charge_order_num
 * @property string $old_user_charge_amount
 */
class PayDaily extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pay_daily';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbStatistic');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'day'], 'required'],
            [['id', 'registration_num', 'charge_user_num', 'charge_order_num', 'new_user_charge_num', 'new_user_charge_order_num', 'old_user_charge_num', 'old_user_charge_order_num'], 'integer'],
            [['day'], 'safe'],
            [['charge_amount', 'new_user_charge_amount', 'old_user_charge_amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'day' => 'Day',
            'registration_num' => 'Registration Num',
            'charge_amount' => 'Charge Amount',
            'charge_user_num' => 'Charge User Num',
            'charge_order_num' => 'Charge Order Num',
            'new_user_charge_num' => 'New User Charge Num',
            'new_user_charge_order_num' => 'New User Charge Order Num',
            'new_user_charge_amount' => 'New User Charge Amount',
            'old_user_charge_num' => 'Old User Charge Num',
            'old_user_charge_order_num' => 'Old User Charge Order Num',
            'old_user_charge_amount' => 'Old User Charge Amount',
        ];
    }
}
