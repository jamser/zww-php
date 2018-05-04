<?php

namespace nextrip\smsCode;

use Yii;
use Excpetion;


if (!defined("TOP_SDK_WORK_DIR"))
{
    define("TOP_SDK_WORK_DIR", Yii::getAlias('@frontend')."/runtime/top/");
}

if (!defined("TOP_SDK_DEV_MODE"))
{
	define("TOP_SDK_DEV_MODE", true);
}

include(Yii::getAlias('@nextrip')."/exts/topSdk/TopSdk.php");

/**
 * 短信验证码数据表
 * @property int $id
 * @property string $type 类型
 * @property int $user_id 用户ID
 * @property string $phone_num 手机号码
 * @property string $ip 请求IP
 * @property string $code 验证码
 * @property string $send_params 序列化的发送参数
 * @property int $send_time 发送时间
 * @property int $send_result 发送结果 成功为1 失败为0
 * @property string $error 错误信息  code-msg
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class SmsCodeModel extends \nextrip\helpers\ActiveRecord {

    public static function tableName() {
        return 'sms_code';
    }
    
    public function behaviors() {
        return [
            yii\behaviors\TimestampBehavior::className()
        ];
    }

    public function rules() {
        return [];
    }

    /**
     * 获取类型范围
     * @return array
     */
    public static function typeRange() {
        return array(
            self::TYPE_BIND,
            self::TYPE_REGISTER,
            self::TYPE_RESET_PASSWORD
        );
    }
    
    public function getSign() {
        switch ($this->type) {
            case self::TYPE_REGISTER:
                return '注册验证';
            case self::TYPE_RESET_PASSWORD:
                return '身份验证';
            default :
                throw new Exception('未定义对应的短信签名');
        }
    }
    
    public function getTemplateCode() {
        switch ($this->type) {
            case self::TYPE_REGISTER:
                return 'SMS_5047470';
            case self::TYPE_RESET_PASSWORD:
                return 'SMS_5047468';
            default :
                throw new Exception('未定义对应的短信模板');
        }
    }
    
    public function getTemplateParams() {
        return [
            'code'=>$this->code,
            'product'=>'超级赞'
        ];
    }
    
    /**
     * 发送验证码 
     * @param bool $realSend 是否真实发送
     * @reutrn int 成功返回1  失败返回0
     */
    public function send($realSend=true) {
        $smsParams = unserialize($this->send_params);
        
        $c = new \TopClient;
        $c ->appkey = Yii::$app->params['aliDayu']['apiKey'] ;
        $c ->secretKey = Yii::$app->params['aliDayu']['apiSecret'] ;
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req ->setExtend( "" );
        $req ->setSmsType( "normal" );
        $req ->setSmsFreeSignName( $smsParams['sign'] );
        $req ->setSmsParam(json_encode($smsParams['templateParams']) );
        $req ->setRecNum( $this->phone_num );
        $req ->setSmsTemplateCode( $smsParams['templateCode'] );
        if($realSend) {
            $resp = $c ->execute( $req );
        } else {
            $resp = new \stdClass();
            $resp->code = 0;
        }
        // {"alibaba_aliqin_fc_sms_num_send_response":{"result":{"err_code":"0","model":"102464310527^1103150421694","success":true},"request_id":"z29ftn0ayamx"}}
        /**
         * {
    "error_response":{
        "code":50,
        "msg":"Remote service error",
        "sub_code":"isv.invalid-parameter",
        "sub_msg":"非法参数"
    }
}
         */
        Yii::info('log alidayu resp : '.  var_export($resp,1));
        $this->send_time = time();
        if($resp->code) {
            $this->send_result = 0;
            $this->error = $resp->code.':'.$resp->msg;
            $this->addError('phone_num', $this->error);
        } else {
            $this->send_result = 1;
        }
        $this->updateAttributes(['send_time', 'send_result', 'error']);
        return $this->send_result;
    }
}