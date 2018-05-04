<?php

namespace WechatSdk\mp;

use WechatSdk\Config;

/**
 * 微信 JSSDK
 */
class Js {

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
     * @var array 
     */
    protected $ticket;


    /**
     * @var \WechatSdk\StorageInterface 
     */
    protected $storage;

    /**
     * 当前URL
     *
     * @var string
     */
    protected $url;

    const API_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi';

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
     * 获取JSSDK的配置数组
     *
     * @param array $APIs
     * @param bool  $debug
     * @param bool  $json
     *
     * @return array
     */
    public function config(array $APIs, $debug = false, $json = true) {
        $signPackage = $this->getSignaturePackage();

        $config = array_merge(array('debug' => $debug), $signPackage, array('jsApiList' => $APIs, 'ticket' => $this->getTicket()));

        return $json ? json_encode($config) : $config;
    }

    /**
     * 获取jsticket
     *
     * @return string
     */
    public function getTicket() {
        $jsapiTicket = $this->getTicketArr();
        return $jsapiTicket['ticket'];
    }

    /**
     * 获取授权数组
     * @return array 
     */
    protected function getTicketArr() {
        $time = time();
        if(!$this->ticket) {
            $this->ticket = $this->storage->getJsTicket();
        }
        if ($this->ticket && $this->ticket['expire_time']>$time) {
           //已经有授权
        } else {
            $lockClass = Config::$lockClass;
            if($lockClass::get('mpJsTicket:'.$this->appId, 10, 0)) {
                //获取锁成功 向微信请求授权
                try {
                    $this->ticket = $this->getTicketFromEndpoint();
                } catch (Exception $ex) {
                    $lockClass::del('mpJsTicket:'.$this->appId);
                    throw new Exception($ex->getMessage(), $ex->getCode());
                }
            } else {
                $wait = 0;
                while(($wait++)<3) {
                    sleep(1);
                    $this->ticket = $this->storage->getJsTicket();
                    if($this->ticket && $this->ticket['expire_time']>$time) {
                        return $this->ticket;
                    }
                }
                //获取锁失败 抛出异常
                throw new Exception("获取微信JsTicket失败");
            }
        }
        return $this->ticket;
    }
    
    protected function getTicketFromEndpoint() {
        $http = new Http(new AccessToken($this->appId, $this->appSecret));
        $http->connectTimeout = 4;
        $http->timeout = 8;
        $jsapiTicket = $http->get(self::API_TICKET);
        $jsapiTicket['expire_time'] = time() + $jsapiTicket['expires_in'];
        $this->storage->saveJsTicket($jsapiTicket);
        return $jsapiTicket;
    }
    
    /**
     * 签名
     *
     * @param string $url
     * @param string $nonce
     * @param int    $timestamp
     *
     * @return array
     */
    public function getSignaturePackage($url = null, $nonce = null, $timestamp = null) {
        $url = $url ? $url : $this->getUrl();
        $nonce = $nonce ? $nonce : $this->getNonce();
        $timestamp = $timestamp ? $timestamp : time();
        $ticket = $this->getTicket();

        $sign = array(
            'appId' => $this->appId,
            'nonceStr' => $nonce,
            'timestamp' => $timestamp,
            'url' => $url,
            'signature' => $this->getSignature($ticket, $nonce, $timestamp, $url),
        );

        return $sign;
    }

    /**
     * 生成签名
     *
     * @param string $ticket
     * @param string $nonce
     * @param int    $timestamp
     * @param string $url
     *
     * @return string
     */
    public function getSignature($ticket, $nonce, $timestamp, $url) {
        return sha1("jsapi_ticket={$ticket}&noncestr={$nonce}&timestamp={$timestamp}&url={$url}");
    }

    /**
     * 设置当前URL
     *
     * @param string $url
     *
     * @return Js
     */
    public function setUrl($url) {
        $this->url = $url;

        return $this;
    }

    /**
     * 获取当前URL
     *
     * @return string
     */
    public function getUrl() {
        if ($this->url) {
            return $this->url;
        }

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://';

        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * 获取随机字符串
     *
     * @return string
     */
    public function getNonce() {
        return uniqid('rand_');
    }

}
