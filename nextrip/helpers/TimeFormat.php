<?php
namespace nextrip\helpers;
/**
 * 时间格式化 
 */
class TimeFormat {
    
    /**
     * 获取一个默认的时间格式
     * @param integer $time 时间
     * @return string 
     */
    public static function getDefault($time) {
        $currentTime = time();
        if(!$time) {
            $formatDate = '';
        } else {
            $passTime = $currentTime - $time;
             if($passTime > 30*86400) {//超过30天
                $formatDate = date('Y-m-d', $time);
            } else if($passTime > 86400) {//超过1天
                $formatDate = ceil($passTime/86400).'天前';
            } else if($passTime > 3600) {
                $formatDate = ceil($passTime/3600).'小时前';
            } else if($passTime > 60) {
                $formatDate = ceil($passTime/60).'分钟前';
            } else {
                $formatDate = $passTime.'秒前';
            }
        }
        return $formatDate;
    }
}

