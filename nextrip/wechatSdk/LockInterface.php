<?php

namespace WechatSdk;

/**
 * 唯一锁机制 防止重复请求授权 建议使用redis来获取/删除锁
 */
interface LockInterface {
   
    /**
     * 获取锁
     * @return string 
     */
    public static function get($name, $ttl, $timeout = 0);
    
    /**
     * 删除锁
     * @return array 
     */
    public static function del($name);
}

