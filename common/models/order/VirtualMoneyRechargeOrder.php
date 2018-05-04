<?php

namespace common\models\order;

use Yii;
use common\models\user\Wallet;

/**
 * This is the model class for table "virtual_money_recharge_order".
 *
 * @property string $id
 * @property string $user_id
 * @property string $virtual_money_amount
 * @property string $money_amount
 * @property integer $status
 * @property integer $pay_id 支付ID
 * @property integer $pay_time 支付时间
 * @property string $created_at
 * @property string $updated_at
 * 
 * @property Wallet $userWallet 用户钱包
 */
class VirtualMoneyRechargeOrder extends \nextrip\helpers\ActiveRecord
{
    const STATUS_UNPAY = 0;
    
    const STATUS_PAY_SUCCESS = 50;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'virtual_money_recharge_order';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->db_php;
    }
    
    public function behaviors() {
        return [
            \yii\behaviors\TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'virtual_money_amount', 'money_amount'], 'required'],
            [['user_id', 'virtual_money_amount', 'status'], 'integer'],
            [['money_amount'],'number'],
            [['money_amount'],'in', 'range'=> Wallet::RECHARGE_MONEY_OPTIONS],
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
            'virtual_money_amount' => '虚拟金额',
            'money_amount' => '真实金额',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    public function canChangeStatus($status) {
        $ret = true;
        switch ($status) {
            case self::STATUS_PAY_SUCCESS:
                $canPayStatus = [
                    self::STATUS_UNPAY, self::STATUS_PAY_SUCCESS
                ];
                $ret = in_array($this->status, $canPayStatus);
                break;

            default:
                break;
        }
        return $ret;
    }
    
    /**
     * 支付成功
     * @param Pay $pay 支付
     * @param integer $operatorUserId 操作人ID
     * @param string $remark 备注
     * @return boolean
     */
    public function paySuccess($pay, $operatorUserId, $remark) {
        if($this->canChangeStatus(self::STATUS_PAY_SUCCESS)) {
            #可支付 更新为支付已确认
            $db = static::getDb();
            $transaction = $db->beginTransaction();
            try {
                $updateRet = $this->updateAttributes([
                    'status'=>self::STATUS_PAY_SUCCESS,
                    'pay_id'=> $pay->id,
                    'pay_time'=>$pay->pay_time
                ]);
                if($updateRet) {
                    #增加用户钱包虚拟金额
                    $db->createCommand('UPDATE '.Wallet::tableName().' SET '
                            . ' virtual_money=virtual_money+'.$this->virtual_money_amount
                            .' WHERE user_id='.$this->user_id
                            )->execute();
                }
                Log::add($this->id, $operatorUserId, "成功支付回调. 更新结果:". var_export($updateRet,1)." 备注:{$remark}");
            } catch (\Exception $ex) {
                $transaction->rollBack();
                throw new \Exception($ex->getMessage());
            }
            
            $transaction->commit();
            return true;
        } else {
            Log::add($this->id, $operatorUserId, "当前订单状态为 {$this->status} 不能更新订单状态. 备注:{$remark}");
            return false;
        }
    }
}
