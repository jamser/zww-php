<?php

namespace nextrip\wechat\helpers;

use Yii;
use Exception;

use WechatSdk\StorageBase;

use nextrip\helpers\Format;
use nextrip\wechat\models\Mp;

/**
 * 扩展微信sdk存储类
 */
class Storage extends StorageBase {
    
    protected $appId;
    
    public function __construct($appId) {
        $this->appId = $appId;
        
    }
    
    /**
     * 保存授权
     * @param array $accessTokenArr 授权数组
     */
    public function saveAccessToken($accessTokenArr) {
        if(!($mp = Mp::getByAppId($this->appId))) {
            throw new Exception('找不到对应的公众账号 : '.$this->appId);
        }
        $mp->access_token = Format::toStr($accessTokenArr);
        $mp->updateAttributes(['access_token']);
        //保存到redis中 避免cache可能失效的问题
    }
    
    /**
     * 获取授权
     * @return array
     */
    public function getAccessToken() {
        if(!($mp = Mp::getByAppId($this->appId))) {
            throw new Exception('找不到对应的公众账号 : '.$this->appId);
        }
        return Format::toArr($mp->access_token);
    }
    
        
    /**
     * 从存储媒介中删除授权
     * @param array $accessTokenArr 当前授权 需要进行比对才删除 防止误删除
     * @return bool 
     */
    public function delAccessToken($accessTokenArr) {
        
    }
    
    /**
     * 保存js ticket 
     * @param array $jsTicketArr
     */
    public function saveJsTicket($jsTicketArr) {
        if(!($mp = Mp::getByAppId($this->appId))) {
            throw new Exception('找不到对应的公众账号');
        }
        $mp->js_ticket = Format::toStr($jsTicketArr);
        $mp->updateAttributes(['js_ticket']);
    }
    
    /**
     * 获取js ticket
     * @return array 
     */
    public function getJsTicket() {
        if(!($mp = Mp::getByAppId($this->appId))) {
            throw new Exception('找不到对应的公众账号');
        }
        return Format::toArr($mp->js_ticket);
    }
    
    
    /**
     * 从存储媒介中删除js ticket
     * @param array $jsTicketArr 当前jsticket 需要进行比对才删除 防止误删除
     * @return bool 
     */
    public function delJsTicket($jsTicketArr) {
        
    }
}