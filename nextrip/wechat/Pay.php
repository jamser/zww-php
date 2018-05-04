<?php

namespace common\extensions\wechat;

use Yii;
use WechatSdk\pay\JsApiPay;
use WechatSdk\pay\Api;
use WechatSdk\pay\Config;

/**
 * 微信公众平台
 */
class Pay extends \yii\base\Component implements Config{

    /**
     * app id
     * @var string
     */
    private $appId;
    
    /**
     * app密钥 
     * @var string
     */
    private $appSecret;
    
    /**
     * 付款key
     * @var string 
     */
    private $key;
    
    /**
     * 商户ID
     * @var string 
     */
    private $mchId;
    
    /**
     * 设置商户证书路径 
     * @var string
     */
    private $sslCertPath;
    
    private $sslKeyPath;
    
    public $curlProxyHost = "0.0.0.0"; //"10.152.18.220";
    
    public $curlProxyPort = 0;
    
    public $reportLevel = 1; 
    
    public $notifyUrl;
    
    public function setAppId($value) {
        $this->appId = $value;
    }
    
    public function getAppId() {
        return $this->appId;
    }
    
    public function setAppSecret($value) {
        $this->appSecret = $value;
    }
    
    public function getAppSecret() {
        return $this->appSecret;
    }
    
    public function setKey($value) {
        $this->key = $value;
    }
    
    public function getKey() {
        return $this->key;
    }
    
    public function setMchId($value) {
        $this->mchId = $value;
    }
    
    public function getMchId() {
        return $this->mchId;
    }
    
    public function setSslCertPath($value) {
        $realPath = Yii::getAlias($value);
        if(!file_exists($realPath)) {
            throw new \Exception('找不到微信支付证书');
        }
        $this->sslCertPath = $realPath;
    }
    
    public function getSslCertPath() {
        return $this->sslCertPath;
    }
    
    public function setSslKeyPath($value) {
        $realPath = Yii::getAlias($value);
        if(!file_exists($realPath)) {
            throw new \Exception('找不到微信支付证书');
        }
        $this->sslKeyPath = $realPath;
    }
    
    public function getSslKeyPath() {
        return $this->sslKeyPath;
    }
    
    public function getCurlProxyHost() {
        return $this->curlProxyHost;
    }
    
    public function getCurlProxyPort() {
        return $this->curlProxyPort;
    }
    
    public function getReportLevel() {
        return $this->reportLevel;
    }
    
    public function getNotifyUrl() {
        if($this->notifyUrl && stripos($this->notifyUrl, 'http')!==0) {
            $this->notifyUrl  = Yii::$app->getRequest()->hostInfo.$this->notifyUrl;
        }
        return $this->notifyUrl;
    }

    /**
     * 
     * 获取jsapi支付的参数
     * @param array $UnifiedOrderResult 统一支付接口返回的数据
     * @throws WxPayException
     * 
     * @return json数据，可直接填入js函数作为参数
     */
    public function GetJsApiParameters($UnifiedOrderResult) {
        if (!array_key_exists("appid", $UnifiedOrderResult) || !array_key_exists("prepay_id", $UnifiedOrderResult) || $UnifiedOrderResult['prepay_id'] == "") {
            throw new \Exception("参数错误");
        }
        $jsapi = new JsApiPay($this);
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(Api::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign());
        $parameters = json_encode($jsapi->GetValues());
        return $parameters;
    }

    /**
     * 
     * 拼接签名字符串
     * @param array $urlObj
     * 
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj) {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 
     * 获取地址js参数
     * 
     * @return 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
     */
    public function GetEditAddressParameters($accessToken) {
        $data = [];
        $data["appid"] = $this->appId;
        $data["url"] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $time = time();
        $data["timestamp"] = "$time";
        $data["noncestr"] = "1234568";
        $data["accesstoken"] = $accessToken;
        ksort($data);
        $params = $this->ToUrlParams($data);
        $addrSign = sha1($params);

        $afterData = array(
            "addrSign" => $addrSign,
            "signType" => "sha1",
            "scope" => "jsapi_address",
            "appId" => $this->appId,
            "timeStamp" => $data["timestamp"],
            "nonceStr" => $data["noncestr"]
        );
        $parameters = json_encode($afterData);
        return $parameters;
    }

}