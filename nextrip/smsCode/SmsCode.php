<?php

namespace nextrip\smsCode;

use Yii;
use nextrip\helpers\Helper;

/**
 * 发送验证码组件
 * 配置方式:
 *      添加阿里大鱼apiKey api发送参数
 *      在配置文件params-local中添加
 *          ...
 *              aliDayu=>[
 *                  'apiKey'=>'',//阿里大鱼的apiKey
 *                  'apiSecret'=>''//阿里大鱼的apiSecret
 *              ]
 *          ...
 * 使用方式 : 
 *  1.发送验证码
 *      $smsCode = new SmsCode([
 *          'userId=>1,
 *          'type'=>'register',
 *          'phoneNum'=>'13012345678',
 *      ]);
 *      if($smsCode->send()) {
 *          //发送成功...
 *      } else {
 *          //发送失败...
 *          echo $smsCode->errorMsg;
 *      }
 *      
 *  2.对验证码进行验证
 *      $smsCode = new SmsCode([...]);
 *      if($smsCode->verify()) {
 *          //验证成功...
 *      } else {
 *          //验证失败...
 *          echo $smsCode->errorMsg;
 *      }
 */
class SmsCode extends \yii\base\Component {
    
    /**
     * 用户ID
     * @var int 
     */
    public $userId;
    
    /**
     * 发送类型
     * @var string
     */
    public $type;
    
    /**
     * 需要发送的手机号码
     * @var string 
     */
    public $phoneNum;
    
    /**
     * 错误码
     * @var string
     */
    public $errorCode;
    
    /**
     * 错误信息
     * @var string
     */
    public $errorMsg;
    
    /**
     * 验证码有效时间 默认为10分钟
     * @var int
     */
    public $codeDuration=600;
    
    /**
     * 手机号码发送限制 key为时间值 value为发送的次数 
     * @var array 
     */
    public $phoneNumSendLimit = [
            '60'=>2,//60秒内最多允许对该手机号发送2条
            '3600'=>15,//3600秒内最多允许对该手机号发送15条短信
            '7200'=>20,
            '86400'=>30
        ];
    
    /**
     * 使用session判断发送限制
     * @var array
     */
    public $sessionSendLimit = [
            '60'=>1,
            '3600'=>15,
            '7200'=>20,
            '86400'=>30
        ];
    
    /**
     * 使用IP判断发送限制
     * @var type 
     */
    public  $ipSendLimit = [
            '60'=>6,
            '3600'=>45,
            '7200'=>60,
            '86400'=>90
        ];
    
    /**
     * 用于缓存在缓存中判断用户发送记录的key
     * @var type 
     */
    protected $scKey;
    
    public function init() {
        parent::init();
        if($this->userId===null) {
            throw new \Exception('请指定一个数字格式的userId');
        }
        
        if(!$this->type || !is_string($this->type) || mb_strlen($this->type, 'utf-8')>255) {
            throw new \Exception('type 必须是不为空的字符串,并且长度不能超过255');
        }
    }
    
    /**
     * 产生一个随机数字字符串
     * @param int $length
     * @return string
     */
    public function randNumStr($length) {
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $key = rand(0, 9);
            $string .= $key;
        }
        return $string;
    }

    /**
     * 产生一个随机字符串
     * @param int $length
     * @return string
     */
    function randStr($length) {
        $codeRand = "0123456789asdfghjklmyuiopqwertnbvcxzASDFGHJKLMYUIOPQWERTNBVCXZ";
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $key = rand(0, 61);
            $string .=$codeRand[$key];
        }
        return $string;
    }

    public function test() {
        Yii::error('session请求验证码过于频繁：'.  json_encode($this,JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * 发送验证码
     * @param array $smsParams 如 : [sign=>'签名', 'templateCode'=>'模板代码','templateParams'=>'模板参数' ]
     * @param bool 是否真实发送
     * @return bool 若发送失败 可通过errorMsg获取错误信息
     */
    public function send($smsParams, $realSend=true) {
        if(!isset($smsParams['sign'], $smsParams['templateCode'], $smsParams['templateParams'])) {
            throw new \Exception('smsParams 的sign templateCode templateParams 必须设置');
        }
        if(!$this->checkPhoneNumSendRecord()) {
            Yii::error('手机号验证码请求过于频繁：'.  json_encode($this,JSON_UNESCAPED_UNICODE));
            $this->errorMsg = '该手机号码请求验证码过于频繁，请稍后再试';
            return false;
        }
        
        if($this->userId) {
            if(!$this->checkUserIdSendRecord()) {
                Yii::error('用户请求验证码过于频繁：'.  json_encode($this,JSON_UNESCAPED_UNICODE));
                $this->errorMsg = '请求验证码过于频繁，请稍后再试';
                return false;
            }
        } else {
            if(!$this->checkSessionSendRecord()) {
                Yii::error('session请求验证码过于频繁：'.  json_encode($this,JSON_UNESCAPED_UNICODE));
                $this->errorMsg = '请求验证码过于频繁，请稍后再试';
                return false;
            }
        }
        
        if(!$this->checkIpSendRecord()) {
            Yii::error('IP请求验证码过于频繁：'.  json_encode($this,JSON_UNESCAPED_UNICODE));
            $this->errorMsg = '你所在的IP请求验证码过于频繁，请稍后再试';
            return false;
        }
        
        $sessionKey = 'smscode_'.$this->type.'_'.$this->phoneNum;
        $lastCode = Yii::$app->getSession()->get($sessionKey);
        $lastCodeData = $lastCode ? unserialize($lastCode) : [];
        $time = time();
        if($lastCodeData && ($time - $lastCodeData['time']<120)) {
            $code = $lastCodeData['code'];
        } else {
            $code = $this->randNumStr(6);
        }
        
        Yii::$app->getSession()->set($sessionKey, serialize([
                'code'=>$code,
                'time'=>$time
            ]));
        
        $smsCodeModel = new SmsCodeModel();
        $smsCodeModel->code = $code;
        $smsCodeModel->ip = mb_substr(Helper::getIp(), 0, 15, 'utf-8');
        $smsCodeModel->type = $this->type;
        $smsCodeModel->phone_num = $this->phoneNum;
        $smsCodeModel->user_id = (int)$this->userId;
        $smsParams['templateParams']['code'] = $code;
        $smsCodeModel->send_params = serialize($smsParams);
        
        $smsCodeModel->save(false);
        if($smsCodeModel->send($realSend)) {
            $this->addSesssionSendRecord($smsCodeModel);
        } else {
            $firstErrors = $smsCodeModel->getFirstErrors();
            $firstError = array_shift($firstErrors);
            Yii::error('发送验证码失败：'.  var_export($smsCodeModel->getAttributes(),1)."\n 错误信息:".var_export($smsCodeModel->getErrors(),1));
            $this->errorMsg = '发送验证码失败：'.$firstError.'，请稍后再试';
            return false;
        }
        return true;
    }
    
    /**
     * 验证验证码是否有效
     * @param sting $code 验证码
     * @return boolean 
     */
    public function verifyCode($code) {
        $data = Yii::$app->getSession()->get('smscode_'.$this->type.'_'.$this->phoneNum);
        $codeData = $data ? unserialize($data) : [];
        $time = time();
        if(!$codeData || $code!==$codeData['code']) {
            $this->errorMsg = '验证码无效，请重新进行验证';
            return false;
        } else if($time - $codeData['time']>$this->codeDuration) {
            $this->errorMsg = '短信验证码已过期 ， 请重新进行验证';
            return false;
        }
        return true;
    }
    
    /**
     *  检查发送记录
     * @param array $records
     * @param array $sendLimit
     * @return boolean
     */
    protected function checkSendLimit($records, $sendLimit) {
        $time = time();
        $allow = true;
        foreach($records as $record) {
            foreach($sendLimit as $interval => $limit) {
                if($time - $record['send_time'] < $interval ) {
                    $sendLimit[$interval]--;
                    if($sendLimit[$interval]<0) {
                        $allow = false;
                        break 2;
                    }
                }
            }
        }
        return $allow;
    }


    /**
     * 检查手机号码发送记录
     * @return boolean
     */
    public function checkPhoneNumSendRecord() {
        if(!$this->phoneNumSendLimit) {
            return true;
        }
        $limitTimes = array_keys($this->phoneNumSendLimit);
        $maxTime = time() - max($limitTimes);
        $phoneNumSendRecords = SmsCodeModel::find()->where([
            'phone_num'=>$this->phoneNum,
            'type'=>  $this->type
        ])->andWhere('send_time>'.$maxTime)->orderBy('send_time DESC')
                ->limit(max($this->phoneNumSendLimit))->asArray()->all();

        return $this->checkSendLimit($phoneNumSendRecords, $this->phoneNumSendLimit);
    }
    
    /**
     * 检查用户ID限制发送
     * @return bool
     */
    public function checkUserIdSendRecord() {
        if(!$this->sessionSendLimit || !$this->userId) {
            return true;
        }
        
        $limitTimes = array_keys($this->sessionSendLimit);
        $maxTime = time() - max($limitTimes);
        $phoneNumSendRecords = SmsCodeModel::find()->where([
            'user_id'=>(int)$this->userId,
            'type'=>  $this->type
        ])->andWhere('send_time>'.$maxTime.' AND send_result=1')->orderBy('send_time DESC')
                ->limit(max($this->sessionSendLimit))->asArray()->all();

        return $this->checkSendLimit($phoneNumSendRecords, $this->sessionSendLimit);
    }
    
    /**
     * 通过session限制发送
     * @return boolean
     */
    public function checkSessionSendRecord() {
        if(!$this->sessionSendLimit) {
            return true;
        }
        $cookies = Yii::$app->getRequest()->getCookies();//注意此处是request
        $this->scKey = $cookies->get('sckey', null);//设置默认值
       
        $limitTimes = array_keys($this->sessionSendLimit);
        $maxTime = max($limitTimes);
        
        if(!$this->scKey) {
            $cookies = Yii::$app->getResponse()->getCookies();
            $this->scKey = $this->randStr(16);
            $cookies->add(new \yii\web\Cookie([
                'name' => 'sckey',
                'value' => $this->randStr(16),
                'expire'=>time()+$maxTime
            ]));
            return true;
        }
        
        $cache = Yii::$app->cache;
        $sendRecordIds = $cache->get('smscode_session_'.$this->scKey.'_'.$this->type);
        $phoneNumSendRecords = $sendRecordIds ? SmsCodeModel::find()->where([
            'id'=>$sendRecordIds,
            'type'=>  $this->type
        ])->asArray()->all() : [];
        return $this->checkSendLimit($phoneNumSendRecords, $this->sessionSendLimit);
    }
    
    /**
     * 缓存验证码记录
     * @param SmsCode $smsCodeRecord  验证码记录
     */
    public function addSesssionSendRecord($smsCodeRecord) {
        if(!$this->sessionSendLimit || !$this->scKey) {
            return true;
        }
        
        $limitTimes = array_keys($this->sessionSendLimit);
        $maxTime = max($limitTimes);
        
        $cache = Yii::$app->cache;
        $cacheKey = 'smscode_session_'.$this->scKey.'_'.$this->type;
        $ids = $cache->get($cacheKey);
        $ids[] = $smsCodeRecord->id;
        $cache->set($cacheKey, $ids, $maxTime);
        
    }
    
    /**
     * 通过ip限制发送
     * @return boolean
     */
    public function checkIpSendRecord() {
        if(!$this->ipSendLimit) {
            return true;
        }
        $ip = Helper::getIp();
        
        $limitTimes = array_keys($this->ipSendLimit);
        $maxTime = time() - max($limitTimes);
        $phoneNumSendRecords = SmsCodeModel::find()->where([
            'ip'=>$ip,
            'type'=>  $this->type
        ])->andWhere('send_time>'.$maxTime)->orderBy('send_time DESC')
                ->limit(max($this->ipSendLimit))->asArray()->all();

        return $this->checkSendLimit($phoneNumSendRecords, $this->sessionSendLimit);
    }
}