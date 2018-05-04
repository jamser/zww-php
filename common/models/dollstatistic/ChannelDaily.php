<?php

namespace common\models\dollstatistic;

use Yii;

/**
 * This is the model class for table "channel_daily".
 *
 * @property string $id
 * @property string $channel
 * @property string $day
 * @property string $charge_amount
 * @property string $charge_order_num
 * @property integer $charge_user_num
 * @property string $charge_user_avg_amount
 * @property string $registration_user_avg_amout
 */
class ChannelDaily extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'channel_daily';
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
            [['channel', 'day'], 'required'],
            [['day'], 'safe'],
            [['charge_amount', 'charge_user_avg_amount', 'registration_user_avg_amout'], 'number'],
            [['charge_order_num', 'charge_user_num'], 'integer'],
            [['channel'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'channel' => 'Channel',
            'day' => 'Day',
            'charge_amount' => 'Charge Amount',
            'charge_order_num' => 'Charge Order Num',
            'charge_user_num' => 'Charge User Num',
            'charge_user_avg_amount' => 'Charge User Avg Amount',
            'registration_user_avg_amout' => 'Registration User Avg Amout',
        ];
    }
}
