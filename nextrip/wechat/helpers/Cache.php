<?php

namespace nextrip\wechat\helpers;

use Yii;
use WechatSdk\CacheInterface;

/**
 * 扩展微信sdk缓存类
 */
class Cache implements CacheInterface {

    /**
     * 获取缓存
     */
    public function getCache() {
        return Yii::$app->cache;
    }

    /**
     * 设置缓存
     */
    public function setCache($cache) {
        $this->cache = $cache;
    }

    /**
     * 默认的缓存写入器
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $lifetime
     */
    public function set($key, $value, $lifetime = 7200) {
        return $this->getCache()->set($key, $value, $lifetime);
    }

    /**
     * 默认的缓存读取器
     *
     * @param string $key
     * @param mixed  $default
     */
    public function get($key, $default = false) {
        $cacheValue = $this->getCache()->get($key);
        return $cacheValue === false ? $default : $cacheValue;
    }

    /**
     * 删除缓存
     * @param string $key 缓存key
     */
    public function delete($key) {
        return $this->getCache()->delete($key);
    }

}
