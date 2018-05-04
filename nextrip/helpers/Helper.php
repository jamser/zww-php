<?php

namespace nextrip\helpers;

class Helper {

    /**
     * 产生一个随机字符串
     * @param int $length
     * @return string
     */
    public static function randStr($length) {
        $codeRand = "0123456789asdfghjklmyuiopqwertnbvcxzASDFGHJKLMYUIOPQWERTNBVCXZ";
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $key = rand(0, 61);
            $string .=$codeRand[$key];
        }
        return $string;
    }
    


    /**
     * 产生一个随机数字字符串
     * @param int $length
     * @return string
     */
    public static function randNumStr($length) {
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $key = rand(0, 9);
            $string .= $key;
        }
        return $string;
    }
    
    

    /**
     * 获取当前的IP
     * @return string
     */
    public static function getIp() {
        $onlineip = '';
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $onlineip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $onlineip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $onlineip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $onlineip = $_SERVER['REMOTE_ADDR'];
        }
        return $onlineip;
    }
}