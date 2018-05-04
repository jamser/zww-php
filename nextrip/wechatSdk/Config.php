<?php

namespace WechatSdk;

/**
 * 一些配置
 */
class Config {
    
    public function __construct($cacheClass, $storeClass) {
        ;
    }
    
    /**
     * 缓存类 
     * @var string CacheInterface接口类
     */
    public static $cacheClass = '\nextrip\wechat\helpers\Cache';
    
    
    /**
     * 授权存储类
     * @var string 继承于StorageBase的存储类 
     */
    public static $storeClass = '\nextrip\wechat\helpers\Storage';
    
    /**
     * 锁类
     * @var string LockInterface 接口类 
     */
    public static $lockClass = '\nextrip\helpers\Lock';
    
}

