<?php

namespace WechatSdk;

interface CacheInterface {
    
    /**
     * 默认的缓存写入器
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $lifetime
     */
    public function set($key, $value, $lifetime = 7200);

    /**
     * 默认的缓存读取器
     *
     * @param string $key
     * @param mixed  $default
     */
    public function get($key, $default = false);

    /**
     * 删除缓存
     * @param string $key 缓存key
     */
    public function delete($key);
}
