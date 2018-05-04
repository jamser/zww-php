<?php
namespace common\models\order;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\User;
use common\models\call\Order;

/**
 * Pay model
 *
 * @property integer $id
 * @property integer $prod 产品
 * @property integer $user_id 用户ID
 * @property integer $money_amount 金额 
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 * @property integer $pay_time 支付时间
 * @property ineger $expire_time 过期时间
 * @property integer $status 支付状态
 * @property string $trade_no 交易NO
 * @property string $out_pay_id 外部支付ID
 * @property string $pay_title 支付标题
 * 
 * @property User $user 用户模型
 */
class Pay extends \nextrip\helpers\ActiveRecord
{
    const STATUS_EXPIRE = -20;//过期了
    const STATUS_FAILED = -10;//失败
    const STATUS_UNPAY = 0;//未支付
    const STATUS_PAYING = 10;//支付中
    const STATUS_PAID = 20;//已经支付过了
    const STATUS_PAY_SUCCESS = 30;//支付成功
    
    const EXPIRE_TIME = 900;//超时时间 900秒 15分钟
    
    const PROD_CALL_SERVICE = 1;//产品 call 服务
    const PROD_VIRTUAL_MONEY_RECHARGE = 2;//产品 虚拟货币充值
    
    /**
     * auto cache config
     * @var array
     */
    protected static $autoCacheConfig = [
        'enable' => false,//set to false auto cache will be disabled
        'duration' => 14400,//cache duration(second)
        'useAttribute'=>'id',//support mixed attributes , Eg:['type', 'name']
        'cacheId'=>'cache',//cache component id
    ] ;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pay}}';
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
            TimestampBehavior::className(),
        ];
    }
    
    
    public function attributeLabels() {
        return [
            'prod'=>'产品类型',
            'money_amount'=>'金额',
            'created_at'=>'创建时间',
            'updated_at'=>'更新时间',
            'status'=>'状态',
        ];
    }
    
    public function getUser() {
        return $this->hasOne(User::className(), ['user_id'=>'id']);
    }
    
    /**
     * 添加订单
     * @param Order[] $orders 订单列表
     * @param User $user 用户
     * @return Pay
     */
    public static function addOrders($orders, $user, $prod) {
        $total_money_amount = 0;
        $titlePrefix = '';
        foreach($orders as $order) {
            $total_money_amount += $order->money_amount;
            if($prod==self::PROD_CALL_SERVICE) {
                $titlePrefix .= ($titlePrefix ? ',' : '').date('m-d', $order->booking_date);
            }
        }
        $now = time();
        $pay = new Pay([
            'user_id'=>$user->id,
            'prod'=> $prod,
            'money_amount'=>$total_money_amount,
            'expire_time'=>$now+static::EXPIRE_TIME,
            'status'=>static::STATUS_UNPAY
        ]);
        
        if($prod==self::PROD_CALL_SERVICE) {
            $pay->pay_title = $titlePrefix.'起床服务';
        } else if($prod==self::PROD_VIRTUAL_MONEY_RECHARGE) {
            $pay->pay_title = $titlePrefix.'充值';
        } else {
            throw new \Exception('未定义产品 '.$prod.' 支付标题');
        }
        
        $db = static::getDb();
        $transaction = $db->beginTransaction();
        try {
            #保存支付
            $pay->save(false);
            #保存订单 和 支付关联信息
            if(!$pay->id) {
                throw new \Exception('支付信息保存失败');
            }
            $rows = [];
            foreach($orders as $order) {
                $rows[] = [
                    'user_id'=>$user->id, 
                    'order_id'=>$order->id, 
                    'prod'=>$pay->prod, 
                    'created_at'=>$now,
                    'pay_id'=>$pay->id
                ];
            }
            OrderPay::addBatch($rows);

            $pay->trade_no = date("YmdHis", $pay->created_at).$pay->id;
            $pay->updateAttributes(['trade_no']);
            
        } catch (\Exception $ex) {
            $transaction->rollBack();
            $pay->updateAttributes(['trade_no']);
            Yii::error("保存支付失败：{$ex->getFile()} - {$ex->getLine()} {$ex->getMessage()} {$ex->getTraceAsString()}");
            return false;
        }
        $transaction->commit();
        return $pay;
    }
    
    /**
     * 添加订单
     * @param Order[] $orders 订单列表
     * @param User $user 用户
     * @return Pay
     */
    public static function addCallServiceOrder($orders, $user) {
        $total_money_amount = 0;
        foreach($orders as $order) {
            $total_money_amount += $order->money_amount;
        }
        $now = time();
        $pay = new Pay([
            'user_id'=>$user->id,
            'prod'=> static::PROD_CALL_SERVICE,
            'money_amount'=>$total_money_amount,
            'expire_time'=>$now+static::EXPIRE_TIME,
            'status'=>static::STATUS_UNPAY
        ]);
        
        $db = static::getDb();
        $transaction = $db->beginTransaction();
        try {
            #保存支付
            $pay->save(false);
            #保存订单 和 支付关联信息
            if(!$pay->id) {
                throw new \Exception('支付信息保存失败');
            }
            $rows = $dates = [];
            foreach($orders as $order) {
                $rows[] = [
                    'user_id'=>$user->id, 
                    'order_id'=>$order->id, 
                    'prod'=>$pay->prod, 
                    'created_at'=>$now,
                    'pay_id'=>$pay->id
                ];
                $dates[] = date('m-d', $order->booking_date);
            }
            OrderPay::addBatch($rows);
            $pay->pay_title = implode(',', $dates).' 起床服务';
            $pay->trade_no = date("YmdHis", $pay->created_at).$pay->id;
            $pay->updateAttributes(['trade_no']);
            
        } catch (\Exception $ex) {
            $transaction->rollBack();
            $pay->updateAttributes(['trade_no']);
            Yii::error("保存支付失败：{$ex->getFile()} - {$ex->getLine()} {$ex->getMessage()} {$ex->getTraceAsString()}");
            return false;
        }
        $transaction->commit();
        return $pay;
    }
    
    /**
     * @return Order[]
     */
    public function getOrders() {
        $orderIds = [];
        $orderPays = OrderPay::find()->where('pay_id='.(int)$this->id)->all();
        foreach($orderPays as $orderPay) {
            $orderIds[$orderPay->order_id] = $orderPay->order_id;
        }
        if($this->prod==self::PROD_CALL_SERVICE) {
            $orders = Order::findAllAcModels($orderIds);
        } else if($this->prod==self::PROD_VIRTUAL_MONEY_RECHARGE) {
            $orders = VirtualMoneyRechargeOrder::findAllAcModels($orderIds);
        } else {
            throw new \Exception('未定义产品'.$this->prod.' 订单数据');
        }
        return $orders;
    }
    
    public function getTradeNo($mchId='') {
        return $mchId.$this->trade_no;
    }
    
    public function canPay() {
        $status = [
            Pay::STATUS_FAILED, Pay::STATUS_UNPAY
        ];
        return in_array($this->status, $status);
    }
    
    public function changeStatus($status, $params, $operatorUserId, $remark) {
        if($status==self::STATUS_FAILED) {
            //支付失败
        } else if($status==self::STATUS_PAY_SUCCESS) {
            //支付成功
        }
        $pay->updateAttributes([
            'status'=>(int)$status
        ]);
    }
    
    /**
     * 失败回调
     * @param string $outPayId 外部支付订单
     * @param integer $payTime 支付时间
     * @param integer $operatorUserId 操作人ID
     * @param string $remark 备注
     * @return []
     */
    public function failCallback($operatorUserId, $remark) {
        
        $orders = $this->getOrders();
        $db = static::getDb();
        $transaction = $db->beginTransaction();
        try {
            $this->updateAttributes([
                'status' => static::STATUS_FAILED,
            ]);
            
            foreach($orders as $order) {
                Log::add($order->id, $operatorUserId, $remark);
            }
            
            Yii::error("获取支付结果为失败:".implode(',', array_keys($orders))." 操作人 {$operatorUserId} ; 备注 {$remark}");

        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw new \Exception($ex->getMessage(), -1);
        }
        $transaction->commit();
    }
    
    /**
     * 成功回调
     * @param string $outPayId 外部支付订单
     * @param integer $payTime 支付时间
     * @param integer $operatorUserId 操作人ID
     * @param string $remark 备注
     * @return []
     */
    public function successCallback($outPayId, $payTime, $operatorUserId, $remark) {
        
        $orders = $this->getOrders();
        $db = static::getDb();
        $transaction = $db->beginTransaction();
        try {
            $this->updateAttributes([
                'status' => static::STATUS_PAY_SUCCESS,
                'pay_time' => $payTime,
                'out_pay_id' => $outPayId
            ]);

            foreach($orders as $order) {
                /* @var $order Order */
                $order->paySuccess($this, $operatorUserId, $remark);
            }
            Yii::info("获取支付结果为成功:".implode(',', array_keys($orders))." 操作人 {$operatorUserId} ; 备注 {$remark}");
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw new \Exception($ex->getMessage(), -1);
        }
        $transaction->commit();
    }
    
    public static function getCanPayStatus() {
        return [
            static::STATUS_EXPIRE,
            static::STATUS_FAILED,
            static::STATUS_UNPAY,
            static::STATUS_PAYING,
            static::STATUS_PAY_SUCCESS,
        ];
    }
}
