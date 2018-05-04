<?php

namespace backend\models\search;

use Yii;
use common\models\User;
use common\models\call\Order;
use common\models\call\Caller;
use yii\data\ActiveDataProvider;

/**
 * 订单搜索
 */
class CallOrderSearch extends Order {
    
    public $phone;
    
    public $username;
    
    public $sex;
    
    public $caller_phone;
    
    public $caller_username;
    
    public $caller_sex;
    
    public $booking_date_start;
    public $booking_date_end;
    
    public function search($params)
    {
        $query = Order::find()->alias('order');
        $query->joinWith('user user');
        $query->joinWith('phoneAccount phoneAccount');
        $query->joinWith('callerUser callUser');
        $query->joinWith('callPhoneAccount callPhoneAccount');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'order.id' => SORT_DESC,
                ],
                'attributes' => [
                    'order.id',
                ],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'order.id' => $this->id,
            
            'order.user_id' => $this->user_id,
            'user.username' => $this->username,
            'phoneAccount.value' => $this->phone,
            'user.sex' => $this->sex,
            
            'order.caller_user_id' => $this->caller_user_id,
            'callUser.username' => $this->caller_username,
            'callPhoneAccount.value' => $this->caller_phone,
            'callerUser.sex' => $this->caller_sex,
            
            'order.status' => $this->status,
            'order.pay_id' => $this->pay_id,
            'order.pay_time' => $this->pay_time,
            
            'order.booking_time_start' => $this->booking_time_start,
            'order.booking_time_end' => $this->booking_time_end,
        ]);
        
        if($this->booking_date_start) {
            $query->andFilterWhere('order.booking_date>='. strtotime($this->booking_date_start));
        }
        if($this->booking_date_end) {
            $query->andFilterWhere('order.booking_date<='. strtotime($this->booking_date_end));
        }

        return $dataProvider;
    }
    
    public function attributeLabels() {
        return parent::attributeLabels() + [
            'username'=>'用户名',
            'sex'=>'性别',
            'phone'=>'手机',
            
            'caller_username'=>'用户名',
            'caller_sex'=>'性别',
            'caller_phone'=>'手机',
            
            'booking_date_start'=>'预约开始日期',
            'booking_date_end'=>'预约结束日期',
        ];
    }
}

