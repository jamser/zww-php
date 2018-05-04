<?php

namespace frontend\modules\api\models;

use Yii;
use common\models\User;
use common\models\call\Caller;
use common\models\call\Order;

class CallerBookForm extends \yii\base\Model {
    
    /**
     * 用户
     * @var User 
     */
    public $user;
    
    /**
     * 预约用户
     * @var Caller 
     */
    public $caller;
    
    /**
     * 日期
     * @var type 
     */
    public $bookDate;
    
    /**
     * 预约开始时间
     * @var type 
     */
    public $bookStartTime;
    
    /**
     * 预约结束时间
     * @var type 
     */
    public $bookEndTime;
    
    /**
     * 备注
     * @var type 
     */
    public $remark;
    
    /**
     * 金额
     * @var type 
     */
    public $price;
    
    public function rules() {
        return [
            [['bookDate', 'bookStartTime', 'bookEndTime'], 'required', 'message'=>'请选择 {attribute}'],
            [['price'], 'required', 'message'=>'获取价格失败'],
            [['price'], 'checkPrice'],
            [['bookDate', 'bookStartTime', 'bookEndTime'], 'integer'],
            [['bookDate'], 'in', 'range'=> array_keys(Caller::getCanBookDayTimes()), 'message'=>'预约日期不在可选范围内'],
            //[['bookDate'], 'checkDate'],
            [['bookStartTime'], 'in', 'range'=> Caller::getCanBookStartTimes(), 'message'=>'预约时间不在可选范围内'],
            [['bookEndTime'], 'in', 'range'=> Caller::getCanBookEndTimes(), 'message'=>'预约时间不在可选范围内'],
            [['remark'], 'string', 'max'=>200, 'encoding'=>'utf-8'],
            ['bookEndTime', 'compare', 'compareAttribute' => 'bookStartTime', 'operator' => '>', 'message'=>'预约结束时间须大于起止时间'],
        ];
    }
    
    public function attributeLabels() {
        return [
            'price'=> '价格',
            'remark'=>'备注',
            'bookDate'=>'预约日期',
            'bookStartTime'=>'起止时间',
            'bookEndTime'=>'结束时间',
        ];
    }
    
    /*
    public function checkDate($attribute, $params) {
        //从订单库里获取是否有对应日期的订单
    }*/
    
    public function checkPrice($attribute, $params) {
        $this->price = round($this->price, 2);
        if($this->price !== round($this->caller->price,2)) {
            $this->addError($attribute, '获取价格失败');
        }
    }
    
    public function saveOrder() {
        $order = new Order([
            'user_id'=>$this->user->id,
            'caller_user_id'=>$this->caller->user_id,
            'booking_date'=>$this->bookDate,
            'booking_time_start'=>$this->bookStartTime,
            'booking_time_end'=>$this->bookEndTime,
            'money_amount'=>$this->caller->price,
            'status'=>Order::STATUS_UNPAY,
            'remark'=>$this->remark,
        ]);
        $order->save(false);
        return $order;
    }
    
}

