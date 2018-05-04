<?php

namespace WechatSdk\pay;

/**
 * 	配置账号信息
 */
interface ConfigInterface {

    public function getAppId();
    
    public function getAppSecret();
    
    public function getMchId();
    
    public function getKey();
    
    public function getSslCertPath();
    
    public function getSslKeyPath();
    
    public function getCurlProxyHost();
    
    public function getCurlProxyPort();
    
    public function getReportLevel();
    
    public function getNotifyUrl();
}