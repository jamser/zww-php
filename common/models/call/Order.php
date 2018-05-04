<?php

namespace common\models\call;

use Yii;
use common\models\User;
use common\models\user\Account;
use common\modules\wechat\models\UnionId;
use common\modules\wechat\models\Mp;
use common\models\order\Pay;
use common\models\order\Log;

/**
 * This is the model class for table "prodcall_order".
 *
 * @property string $id
 * @property string $user_id 用户ID
 * @property string $caller_user_id 呼叫用户ID
 * @property integer $booking_date 预约日期
 * @property string $booking_time_start 预约开始时间
 * @property string $booking_time_end 预约结束时间
 * @property string $money_amount
 * @property integer $status
 * @property integer $user_confirm 用户确认
 * @property integer $caller_confirm 卖家确认
 * @property string $remark 备注
 * @property string $pay_id 支付ID
 * @property integer $pay_time 支付时间
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $dispatched_at 分发时间
 * @property integer $dispatch_type 分发类型
 * 
 * @property User $user 用户
 * @property User $callerUser caller模型
 */
class Order extends \nextrip\helpers\ActiveRecord
{
    /**
     * 退款驳回
     */
    const STATUS_REFUND_REJECTED = -400;
    
    /**
     * 已退款
     */
    const STATUS_REFUNDED = -300;
    
    /**
     * 已申请退款
     */
    const STATUS_APPLY_FOR_REFUND = -200;
    
    /**
     * 已取消状态
     */
    const STATUS_CANCEL = -100;
    
    /**
     * 待支付状态
     */
    const STATUS_UNPAY = 0;

    /**
     * 接收到支付通知
     */
    const STATUS_PAY_NOTIFY = 100;
    
    /**
     * 支付已确认
     */
    const STATUS_PAY_CONFIRM = 200;
    
    /**
     * 订单已确认 等待唤醒
     */
    const STATUS_PAY_CONFIRMED = 300;
    
    /**
     * 等待分发
     */
    const STATUS_WAIT_FOR_DISPATCH = 400;
    
    /**
     * 等待服务方确认
     */
    const STATUS_WAIT_FOR_SERVER_CONFIRM = 500;
    
    /**
     * 等待服务
     */
    const STATUS_WAIT_FOR_SERVICE = 600;
    
    /**
     * 服务方在完成任务后进行确认  需要上传通话记录
     */
    const STATUS_CALLER_CONFIRM_AFTER_SERVICE = 700;
    
    /**
     * 等待用户确认
     */
    const STATUS_WAIT_FOR_USER_CONFIRM = 800;
    
    /**
     * 用户确认没有服务
     */
    const STATUS_USER_CONFIRM_NO_SERVICE = 850;
    
    
    /**
     * 已出行 等待评价
     */
    const STATUS_WAIT_FOR_USER_RATE = 900;
    
    /**
     * 已完成状态
     */
    const STATUS_DONE = 1000;
    
    /**
     * 订单过期时间 默认为两个小时
     */
    const EXPIRE_TIME = 7200;
    
    /**
     * caller确认过期时间
     */
    const CALLER_CONFIRM_EXPIRE_TIME = 43200;
    
    /**
     * caller确认时间离服务时间限制 7200表示Caller在服务前一天 晚上10点前要确认
     */
    const CALLER_CONFIRM_BEFORE_SERVICE_DAY = 7200;
    
    const DISPATCH_TYPE_NONE = 0;//未分发
    const DISPATCH_TYPE_SYSTEM = 1;//系统自动分配
    const DISPATCH_TYPE_USER = 2;//用户自动选择
    const DISPATCH_TYPE_ADMIN = 3;//管理员手动分配
    const DISPATCH_TYPE_CALLER = 4;//由caller自动接受任务
    
    /**
     * 在更新状态后触发事件
     */
    const EVENT_AFTER_CHANGE_STATUS = 'afterChangeStatus';
    const EVENT_AFTER_PAY_SUCCESS = 'afterPaySuccess';
    
    public static $status_list = [
        self::STATUS_REFUNDED => '已退款',
        self::STATUS_APPLY_FOR_REFUND => '申请退款',
        self::STATUS_CANCEL => '取消',
        self::STATUS_UNPAY => '待付款',
        self::STATUS_PAY_NOTIFY => '付款通知',
        self::STATUS_PAY_CONFIRM => '付款待确认',
        self::STATUS_PAY_CONFIRMED => '付款已确认',
        self::STATUS_WAIT_FOR_DISPATCH => '等待分发',
        self::STATUS_WAIT_FOR_SERVER_CONFIRM => '等待达人确认',
        self::STATUS_WAIT_FOR_SERVICE => '等待服务',
        self::STATUS_CALLER_CONFIRM_AFTER_SERVICE => '等待达人确认服务',
        self::STATUS_WAIT_FOR_USER_RATE => '待评价',
        self::STATUS_DONE => '已完成',
    ];
    
    public static $dispatchTypeList = [
        self::DISPATCH_TYPE_NONE=>'未分发',
        self::DISPATCH_TYPE_SYSTEM=>'系统分发',
        self::DISPATCH_TYPE_USER=>'用户分发',
        self::DISPATCH_TYPE_ADMIN=>'管理员分发',
        self::DISPATCH_TYPE_CALLER=>'达人主动挑选'
    ];
    
    public function init() {
        parent::init();
        $this->on(static::EVENT_AFTER_PAY_SUCCESS, ['\common\eventHandlers\PcallOrderHandler', 'afterPaySuccess']);
        $this->on(static::EVENT_AFTER_CHANGE_STATUS, ['\common\eventHandlers\PcallOrderHandler', 'afterChangeStatus']);
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%prodcall_order}}';
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
            [['remark'], 'default', 'value'=>''],
            [['booking_date', 'booking_time_start', 'booking_time_end'], 'required'],
            [['booking_time_start', 'booking_time_end', 'booking_date'],'integer'],
            [['booking_time_start'],'in', 'range'=> Caller::getCanBookStartTimes()],
            [['booking_time_end'],'in', 'range'=> Caller::getCanBookEndTimes()],
            [['booking_date'], 'in', 'range'=>array_keys(Caller::getCanBookDayTimes())],
            [['remark'], 'string'],
        ];
    }
    
    public function checkEndTime($attribute, $params) {
        if($this->$attribute <= $this->booking_time_start ) {
            $this->addError($attribute, '结束时间必须大于开始时间');
            return false;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'caller_user_id' => '呼叫者ID',
            'booking_date' => '预约日期',
            'booking_time_start' => '预约开始时间',
            'booking_time_end' => '预约结束时间',
            'money_amount' => '金额',
            'status' => '状态',
            'remark'=>'备注',
            'pay_id'=>'支付ID',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * 获取可以预约的日期
     * @return array
     */
    public static function getBookingDates() {
        $todayTime = strtotime('today');
        $i = 2;
        $ret = [];
        while($i<7) {
            $date = date('Y-m-d',$todayTime + 86400*$i);
            $ret[$date] = $date;
            $i++;
        }
        return $ret;
    }
    
    public static function getTimeOptions() {
        $todayTime = strtotime('today');
        $time = $todayTime + 6*3600;
        $options = [];
        while($time<= $todayTime + 9.5*3600) {
            $options[$time-$todayTime] = date('H:i', $time);
            $time += 300;
        }
        return $options;
    }
    
    /**
     * 获取订单号
     * @param string $mchId 微信商户ID
     * @return 订单号
     */
    public function getTradeNo($mchId) {
        return $mchId.date("YmdHis", $this->created_at).$this->id;
    }
    
    
    /**
     * 获取未支付订单数量
     * @param int $userId
     * @return int
     */
    public static function getUnpayCount($userId) {
        //更新超时订单
        $unpayOrders = static::find()->where([
            'user_id'=>(int)$userId,
            'status'=>static::STATUS_UNPAY
        ])->all();
        $unpayCount = 0;
        foreach($unpayOrders as $unpayOrder) {
            /* @var $unpayOrder Order */
            if(time() - $unpayOrder->created_at > static::EXPIRE_TIME) {
                $unpayOrder->changeStatus(static::STATUS_CANCEL, (int)Yii::$app->user->id, '超过限定的时间未付款， 系统自动取消了订单');
            }
            $unpayCount ++ ;
        }
        return $unpayCount;
    }
    
    /**
     * 更新订单状态
     * @param int $status 订单状态
     * @param int $fromUserId 来自用户ID 
     * @param string $remark 需要记录的信息 以方便查看为什么订单的状态变化了
     */
    public function changeStatus($status, $fromUserId, $remark) {
        $oldStatus = $this->status;
        if($oldStatus!=$status) {
            $this->status = (int)$status;
            $this->updateAttributes(['status']);
        }
        $event = new \nextrip\helpers\Event([
            'customData'=>[
                'oldStatus'=>$oldStatus,
                'fromUserId'=>$fromUserId,
                'remark'=>$remark
            ]
        ]);
        $this->trigger(self::EVENT_AFTER_CHANGE_STATUS, $event);
    }
    
    public function getUser() {
        return $this->hasOne(User::className(), ['id'=>'user_id']);
    }
    
    public function getCallerUser() {
        return $this->hasOne(User::className(), ['id'=>'caller_user_id']);
    }
    
    /**
     * 获取用户的微信OPENID
     * @return string
     * @throws \Exception
     */
    public function getUserWechatOpenId() {
        $unionIdVendor = UserVendor::fetchByUserId($this->user_id, UserVendor::WECHAT_UNION);
        if(!$unionIdVendor) {
            throw new \Exception("找不到{$this->user_id}的微信UnionId");
        }
        $mp = Mp::getDefaultMp();
        $account = UnionId::getAccount($unionIdVendor->openId, $mp->localAppId);
        if(!$account) {
            throw new \Exception("找不到{$this->user_id} {$unionIdVendor->openId} 的微信OpenId");
        }
        return $account->openId;
    }
    
    /**
     * 获取起床达人的微信OPENID
     * @return string
     * @throws \Exception
     */
    public function getCallerWechatOpenId() {
        $unionIdVendor = UserVendor::fetchByUserId($this->caller_user_id, UserVendor::WECHAT_UNION);
        if(!$unionIdVendor) {
            throw new \Exception("找不到{$this->caller_user_id}的微信UnionId");
        }
        $mp = Mp::getDefaultMp();
        $account = UnionId::getAccount($unionIdVendor->openId, $mp->localAppId);
        if(!$account) {
            throw new \Exception("找不到{$this->caller_user_id} {$unionIdVendor->openId} 的微信OpenId");
        }
        return $account->openId;
    }
    
    /**
     * 判断是否改变状态
     * @param integer $status 新的状态
     * @return boolean
     */
    public function canChangeStatus($status) {
        switch ($status) {
            case self::STATUS_WAIT_FOR_SERVER_CONFIRM:
                $canPayStatus = [static::STATUS_UNPAY, static::STATUS_PAY_NOTIFY, static::STATUS_PAY_CONFIRM, static::STATUS_PAY_CONFIRMED];
                if(!in_array($this->status, $canPayStatus)) {
                    return false;
                }
                
                break;

            default:
                break;
        }
        return true;
    }
    
    public function getBookingTime() {
        
    }
    
    /**
     * 判断是否能够分配达人
     * @param PcallUser $callerCallUser 用户
     */
    public function canDispatch($callerCallUser) {
        $ret = [];
        if(!in_array($this->status,[self::STATUS_PAY_CONFIRMED,self::STATUS_WAIT_FOR_DISPATCH,self::STATUS_WAIT_FOR_SERVER_CONFIRM])) {
            $ret = [
                'code'=>'ORDER_STATUS_ERROR',
                'msg'=>"当前订单状态为 ".self::$status_list[$this->status]." 不能进行分配"
            ];
            return $ret;
        }
        $callUser = $this->callUser;
        if(!$callerCallUser->gender || $callUser->gender==$callerCallUser->gender) {
            $ret = [
                'code'=>'GENDER_ERROR',
                'msg'=>"达人性别为空或与当前用户相同"
            ];
            return $ret;
        }
        return ['code'=>'OK'];
    }
    
    /**
     * 分配订单给达人
     * @param PcallUser $callerCallUser 用户
     */
    public function dispatch($callerCallUser, $dispathType, $operatorUserId, $remark) {
        $this->caller_user_id = (int)$callerCallUser->user_id;
        $this->dispatch_type = (int)$dispathType;
        $this->dispatched_at = time();
        $this->updateAttributes(['caller_user_id','dispatch_type', 'dispatched_at']);
        //更新后发送通知给达人
        $this->changeStatus(self::STATUS_WAIT_FOR_SERVER_CONFIRM, $operatorUserId, $remark);
    }
    
    /**
     * 是否能支付
     * @return bool
     */
    public function canPay() {
        $canPayStatus = [
            static::STATUS_UNPAY
        ];
        return in_array($this->status, $canPayStatus);
    }
    
    /**
     * 支付成功
     * @param Pay $pay 支付
     * @param integer $operatorUserId 操作人ID
     * @param string $remark 备注
     * @return boolean
     */
    public function paySuccess($pay, $operatorUserId, $remark) {
        if($this->canChangeStatus(Order::STATUS_PAY_CONFIRMED)) {
            $oldStatus = $pay->status;
            #可支付 更新为支付已确认
            $this->updateAttributes([
                'status'=>Order::STATUS_PAY_CONFIRMED,
                'pay_id'=> $pay->id,
                'pay_time'=>$pay->pay_time
            ]);
            Log::add($this->id, $operatorUserId, $remark);
            //$this->on(self::EVENT_AFTER_PAY_SUCCESS, ['\common\eventHandlers\PcallOrderHandler', 'afterPaySuccess']);
            $this->trigger(self::EVENT_AFTER_PAY_SUCCESS, new \nextrip\helpers\Event([
                'customData'=>[
                    'oldStatus'=>$oldStatus,
                    'fromUserId'=>$operatorUserId,
                    'remark'=>$remark
                ]
            ]));
            return true;
        } else {
            Log::add($this->id, $operatorUserId, "当前订单状态为 {$this->status} 不能更新订单状态. 备注:{$remark}");
            return false;
        }
    }
    
}
