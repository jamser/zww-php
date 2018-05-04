<?php

namespace WechatSdk\pay;

/**
 * 	配置账号信息
 */
class Config implements ConfigInterface {
    
    /**
     * 禁用上报
     */
    const REPORT_LEVEL_DISABLED = 0;
    /**
     * 仅失败上报
     */
    const REPORT_LEVEL_FAIL = 1;
    /**
     * 所有都上报
     */
    const REPORT_LEVEL_ALL = 2;
    
    /**
     * 微信应用ID
     * @var string
     */
    protected $appId;
    
    /**
     * 微信密钥
     * @var string
     */
    protected $appSecret;

    /**
     * 微信商户ID
     * @var sting
     */
    protected $mchId;

    /**
     * 微信API密钥
     * @var string
     */
    protected $key;

    protected $sslCertPath;

    protected $sslKeyPath;
    
    protected $curlProxyHost;

    protected $curlProxyPort;
    
    protected $reportLevel;

    protected $notifyUrl;

    public function __construct($appId, $appSecret, $mchId, $key,
            $sslCertPath=null, $sslKeyPath=null, $reportLevel=self::REPORT_LEVEL_FAIL, $notifyUrl=null,
            $curlProxyHost=null, $curlProxyPort=null) {
        
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->mchId = $mchId;
        $this->key = $key;
        
        $this->notifyUrl = $notifyUrl;
        $this->sslCertPath = $sslCertPath;
        $this->sslKeyPath = $sslKeyPath;
        $this->reportLevel = $reportLevel;
        $this->curlProxyHost = $curlProxyHost;
        $this->curlProxyPort = $curlProxyPort;
    }

    public function getAppId() {
        return $this->appId;
    }
    
    public function getAppSecret() {
        return $this->appSecret;
    }
    
    public function getMchId() {
        return $this->mchId;
    }
    
    public function getKey() {
        return $this->key;
    }
    
    public function getSslCertPath() {
        return $this->sslCertPath;
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
        return $this->notifyUrl;
    }
}