<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pay_count_daily".
 *
 * @property string $id
 * @property string $day
 * @property string $user_num
 * @property string $registration_num
 * @property string $pay_user_num
 * @property string $pay_1
 * @property string $pay_2
 * @property string $pay_3
 * @property string $pay_4
 * @property string $pay_5
 * @property string $pay_6
 * @property string $pay_7
 * @property string $pay_8
 * @property string $pay_gt_8
 */
class PayCountDaily extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pay_count_daily';
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
            [['day', 'user_num', 'registration_num', 'pay_user_num', 'pay_1', 'pay_2', 'pay_3', 'pay_4', 'pay_5', 'pay_6', 'pay_7', 'pay_8', 'pay_gt_8'], 'required'],
            [['day'], 'safe'],
            [['user_num', 'registration_num', 'pay_user_num', 'pay_1', 'pay_2', 'pay_3', 'pay_4', 'pay_5', 'pay_6', 'pay_7', 'pay_8', 'pay_gt_8'], 'integer'],
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
            'user_num' => 'User Num',
            'registration_num' => 'Registration Num',
            'pay_user_num' => 'Pay User Num',
            'pay_1' => 'Pay 1',
            'pay_2' => 'Pay 2',
            'pay_3' => 'Pay 3',
            'pay_4' => 'Pay 4',
            'pay_5' => 'Pay 5',
            'pay_6' => 'Pay 6',
            'pay_7' => 'Pay 7',
            'pay_8' => 'Pay 8',
            'pay_gt_8' => 'Pay Gt 8',
        ];
    }
}
