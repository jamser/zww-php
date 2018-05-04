<?php

namespace common\models;

use Yii;
use Exception;
use nextrip\smsCode\SmsCode;

class SendSmsCodeForm extends \yii\base\Model {
    
    /**
     * 手机号码
     * @var string 
     */
    public $phoneNum;
    
    /**
     * 类型
     * @var string 
     */
    public $type;
    
    public $user;
    
    public function rules() {
        return [
            [['phoneNum', 'type'],'required'],
            ['phoneNum', 'match', 'pattern' => '/^1[3|4|5|7|8][0-9]{9}$/', 'message' => '请输入正确的手机号码.'],
            ['type', 'in', 'range'=>['applyCaller','applyWithdrawals']]
        ];
    }
    
    public function attributeLabels() {
        return [
            'phoneNum'=>'手机号码',
            'type'=>'类型'
        ];
    }
    
    public function getSmsParams() {
        $params = Yii::$app->params['smsCode'];
        if(empty($params['templateParams'])) {
            $params['templateParams'] = [];
        }
        return $params;
    }
    
    public function send() {
        if($this->validate()) {
            $smsSender = new SmsCode([
                'userId'=>$this->user ? (int)$this->user->id : 0,
                'type'=>$this->type,
                'phoneNum'=>$this->phoneNum
            ]);
            if($smsSender->send($this->getSmsParams(), YII_ENV==='prod')) {
                return true;
            }
            $errorMsg = $smsSender->errorMsg;
            $this->addError('phoneNum',$errorMsg);
        }
        return false;
    }
}