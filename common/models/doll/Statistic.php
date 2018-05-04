<?php

namespace common\models\doll;

use Yii;

/**
 * This is the model class for table "doll_statistic".
 *
 * @property integer $id
 * @property integer $registration_num 注册数量
 * @property string $android_registration_num 安卓注册数量
 * @property string $ios_registration_num IOS注册数量
 * @property string $charge_num 充值数量
 * @property string $charge_amount 充值金额
 * @property string $old_user_charge_amount 老用户充值金额
 * @property string $new_user_charge_amount 新用户充值金额
 * @property string $old_user_charge_num 老用户充值数
 * @property string $new_user_charge_num 新用户充值数
 * @property string $old_user_charge_order_num 老用户充值订单数
 * @property string $new_user_charge_order_num 新用户充值订单数
 * @property string $play_count 玩游戏次数
 * @property string $grab_count 抓取次数
 * @property string $day 日期
 */
class Statistic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'doll_statistic';
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
            [['registration_num', 'play_count', 'day'], 'required'],
            [['registration_num', 'android_registration_num', 'ios_registration_num', 'charge_num', 'play_count', 'grab_count'], 'integer'],
            [['charge_amount'], 'number'],
            [['day'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'registration_num' => '注册数',
            'android_registration_num' => '安卓注册数',
            'ios_registration_num' => 'Ios注册数',
            'charge_num' => '充值订单数量',
            'charge_amount' => '充值金额',
            'play_count' => '玩游戏次数',
            'grab_count' => '抓取次数',
            'day' => 'Day',
        ];
    }
}
