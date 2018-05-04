<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "t_statistic".
 *
 * @property integer $id
 * @property integer $register_people_count
 * @property integer $android_people
 * @property integer $ios_people
 * @property integer $order_number
 * @property string $price
 * @property string $today_date
 */
class TStatistic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_statistic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['register_people_count', 'android_people', 'ios_people', 'order_number'], 'integer'],
            [['price'], 'number'],
            [['today_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'register_people_count' => 'Register People Count',
            'android_people' => 'Android People',
            'ios_people' => 'Ios People',
            'order_number' => 'Order Number',
            'price' => 'Price',
            'today_date' => 'Today Date',
        ];
    }
}
