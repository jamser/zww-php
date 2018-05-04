<?php

namespace WechatSdk\mp;

use WechatSdk\Config;

/**
 * 全局通用 AccessToken
 */
class AccessToken {

    /**
     * 应用ID
     *
     * @var string
     */
    protected $appId;

    /**
     * 应用secret
     *
     * @var string
     */
    protected $appSecret;

    /**
     * token
     *
     * @var array
     */
    protected $token;

    protected static $cacheKeys =  [];
    
    /**
     * @var \WechatSdk\StorageBase
     */
    protected $storage;
    
    // API
    const API_TOKEN_GET = 'https://api.weixin.qq.com/cgi-bin/token';

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $storeageClass = Config::$storeClass;
        $this->storage = new $storeageClass($appId);
    }

    /**
     * 获取Token
     *
     * @return string
     */
    public function getToken() {
        $tokenArr = $this->getTokenArr();
        return $tokenArr['access_token'];
    }

    /**
     * 获取授权数组
     * @return array 
     */
    protected function getTokenArr() {
        $time = time();
        if(!$this->token) {
            $this->token = $this->storage->getAccessToken();
        }
        if ($this->token && $this->token['expire_time']>$time) {
           //已经有授权
        } else {
            $lockClass = Config::$lockClass;
            if($lockClass::get('mpAccessToken:'.$this->appId, 10, 0)) {
                //获取锁成功 向微信请求授权
                try {
                    $this->token = $this->getTokenFromEndpoint();
                } catch (Exception $ex) {
                    $lockClass::del('mpAccessToken:'.$this->appId);
                    throw new Exception($ex->getMessage(), $ex->getCode());
                }
            } else {
                $wait = 0;
                while(($wait++)<3) {
                    sleep(1);
                    $this->token = $this->storage->getAccessToken();
                    if($this->token && $this->token['expire_time']>$time) {
                        return $this->token;
                    }
                }
                //获取锁失败 抛出异常
                throw new Exception("获取微信授权失败");
            }
        }
        return $this->token;
    }
    
    protected function getTokenFromEndpoint() {
        $params = array(
            'appid' => $this->appId,
            'secret' => $this->appSecret,
            'grant_type' => 'client_credential',
        );
        $http = new Http();
        $http->connectTimeout = 4;
        $http->timeout = 8;
        $tokenArr = $http->get(self::API_TOKEN_GET, $params);
        $tokenArr['expire_time'] = time() + $tokenArr['expires_in'];
        $this->storage->saveAccessToken($tokenArr);
        return $tokenArr;
    }
}
