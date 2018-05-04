<?php

namespace nextrip\helpers;

use Yii;

/**
 * 锁类
 */
class Lock {

    /**
     * @return Redis 
     */
    public static function getRedis() {
        return Yii::$app->redis;
    }
    
    /**
     * 获取一个锁
     * @param string $name 锁名
     * @param integer $ttl 锁最大生存周期
     * @param integer $timeout 超时时间
     */
    public static function get($name, $ttl, $timeout = 0) {
        $key = 'lock_' . $name;
        $redis = static::getRedis();
        $value = $redis->incr($key);
        if ($value == 1) {
            $redis->expire($key, $ttl);
            return true;
        } else {
            while ($timeout > 0) {
                sleep(1);
                $timeout--;
            }
            return false;
        }
    }

    /**
     * 删除锁
     * @param string $name 锁名称
     */
    public static function del($name) {
        $key = 'lock_' . $name;
        $redis = static::getRedis();
        $redis->del($key);
    }

}
