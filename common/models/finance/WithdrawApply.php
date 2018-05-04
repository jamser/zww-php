<?php

namespace common\models\finance;

use Yii;
use common\models\User;

/**
 * 提现申请
 * This is the model class for table "finance_withdraw_apply".
 *
 * @property integer $id
 * @property integer $user_id 用户ID
 * @property integer $amount 金额
 * @property integer $status 状态
 * @property string $out_trade_no 支付交易流水号
 * @property integer $pay_time 支付时间
 * @property integer $created_at
 * @property integer $updated_at
 * 
 * @property User $user 用户模型
 */
class WithdrawApply extends \nextrip\helpers\ActiveRecord
{
    const SCENARIO_APPLY_WITHDRAWALS = 'applyWithdraw';
    
    const STATUS_DEFAULT = 0;
    const STATUS_REVIEW_PASS = 10;
    const STATUS_PAY_SUCCESS = 100;
    const STATUS_REVIEW_REJECTED = -10;
    
    const STATUS_LIST = [
        self::STATUS_DEFAULT => '待审核',
        self::STATUS_REVIEW_PASS => '审核通过',
        self::STATUS_REVIEW_REJECTED => '审核拒绝',
    ];
    
    const STATUS_UNPAY_LIST = [
        self::STATUS_DEFAULT => '待审核',
        self::STATUS_REVIEW_PASS => '审核通过',
    ];
    
    const EVENT_AFTER_REVIEW_PASS = 'afterReviewPass';
    const EVENT_AFTER_REVIEW_REJECTED = 'afterReviewRejected';
    
    public function init() {
        parent::init();
        $this->on(self::EVENT_AFTER_REVIEW_PASS, ['\common\eventHandlers\WithdrawHandler', 'afterReviewPass']);
        $this->on(self::EVENT_AFTER_REVIEW_REJECTED, ['\common\eventHandlers\WithdrawHandler', 'afterReviewRejected']);
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'finance_withdraw_apply';
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
            [['amount'], 'required', 'on'=>[static::SCENARIO_APPLY_WITHDRAWALS]],
            [['amount'],'integer', 'on'=>[static::SCENARIO_APPLY_WITHDRAWALS]],
            [['amount'],'checkAmount', 'on'=>[static::SCENARIO_APPLY_WITHDRAWALS]],
        ];
    }
    
    /**
     * 检查金额
     */
    public function checkAmount($attribute, $params) {
        if($this->amount<=0) {
            $this->addError($attribute,'提现金额必须大于0');
        } else if($this->amount > $this->user->wallet->can_withdraw) {
            $this->addError($attribute,'提现金额不能超过可提现金额'.($this->user->wallet->can_withdraw/100).'元');
        }
    }
    
    public function getUser() {
        return $this->hasOne(User::className(), ['id'=>'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'amount' => '金额',
            'status' => '状态',
            'out_trade_no' => '交易流水号',
            'pay_time' => '支付时间',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * 是否能审核
     * @return bool
     */
    public function canReview() {
        return in_array($this->status, [static::STATUS_DEFAULT]);
    }
    
    /**
     * 是否能支付
     * @return bool
     */
    public function canPay() {
        return in_array($this->status, [static::STATUS_REVIEW_PASS]);
    }
}
