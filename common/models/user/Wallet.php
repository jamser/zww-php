<?php

namespace common\models\user;

use Yii;

/**
 * 用户账号余额
 * This is the model class for table "user_wallet".
 *
 * @property integer $id
 * @property integer $user_id 用户ID
 * @property integer $blance 余额
 * @property integer $income 收入
 * @property integer $withdraw 提现
 * @property integer $can_withdraw 可提现金额
 * @property integer $spend 消费
 * @property integer $virtual_money 虚拟货币余额
 * @property integer $created_at
 * @property integer $updated_at
 */
class Wallet extends \nextrip\helpers\ActiveRecord
{
    const RECHARGE_MONEY_OPTIONS = [
        6,30,100,500
    ];
    
    /**
     * auto cache config
     * @var array
     */
    protected static $autoCacheConfig = [
        'enable' => false,//set to false auto cache will be disabled
        'duration' => 14400,//cache duration(second)
        'useAttribute'=>'user_id',//support mixed attributes , Eg:['type', 'name']
        'cacheId'=>'cache',//cache component id
    ] ;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_wallet';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->db_php;
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'blance' => '账户余额',
            'virtual_money' => '虚拟货币余额',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * 获取充值金额
     * @param integer $minVirtualMoney 最小充值金额
     * @return []
     */
    public static function getRechargeAmounts() {
        $arr = static::RECHARGE_MONEY_OPTIONS;
        $options = [];
        foreach($arr as $amount) {
            $options[$amount] = static::caclVirtualMoney($amount);
        }
        return $options;
    }
    
    /**
     * 计算虚拟金额
     * @param integer $trueMoney 真实金额 元
     * @return integer
     */
    public static function caclVirtualMoney($trueMoney) {
        return round($trueMoney*10);
    }
    
    /**
     * 计算真实金额
     * @param integer $virtualMoney 虚拟金额
     * @return string
     */
    public static function caclTrueMoney($virtualMoney) {
        return sprintf('%0.2f',$virtualMoney/10);
    }
    
}
