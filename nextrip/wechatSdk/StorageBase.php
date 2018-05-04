<?php

namespace WechatSdk;

abstract class StorageBase {
   
    protected $appId;


    public function __construct($appId) {
        $this->appId = $appId;
    }
    
    /**
     * 保存授权到存储媒介中
     * @return string 
     */
    public abstract function saveAccessToken($tokenArr);
    
    /**
     * 从存储媒介中获取授权
     * @return array 
     */
    public abstract function getAccessToken();
    
    /**
     * 从存储媒介中删除授权
     * @param array $accessTokenArr 当前授权 需要进行比对才删除 防止误删除
     * @return array 
     */
    public abstract function delAccessToken($accessTokenArr); 
    
    /**
     * 保存js ticket到 存储媒介中
     *  
     */
    public abstract function saveJsTicket($jsTicketArr);
    
    /**
     * 从存储媒介中获取js ticket
     */
    public abstract function getJsTicket();
    
    /**
     * 从存储媒介中删除js ticket
     * @param array $jsTicketArr 当前jsticket 需要进行比对才删除 防止误删除
     * @return array 
     */
    public abstract function delJsTicket($jsTicketArr); 
    
}

