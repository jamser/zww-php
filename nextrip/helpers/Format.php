<?php

namespace nextrip\helpers;

class Format {

    /**
     * 把数组形式的值变为字符串形式
     * @param mixed $value 要转换的值
     * @param mixed $defaultValue  默认值
     * @return string
     */
    public static function toStr($value, $defaultValue = '') {
        return $value ? ( (is_array($value) || is_object($value)) ? serialize($value) : $value) : $defaultValue;
    }

    /**
     * 把字符串形式的值变为数组形式
     * @param string $value 要转换的值
     * @param [] $defaultValue 默认值
     * @return [] 返回处理完成的值
     */
    public static function toArr($value, $defaultValue = []) {
        return $value ? ( (is_array($value) || is_object($value)) ? $value : @unserialize($value)) : $defaultValue;
    }

    /**
     * 把字符串形式的值变为数组形式
     * @param mixed $value 要转换的值
     * @param bool $assoc json_decode参数
     * @param mixed $defaultValue 返回默认值
     * @return array
     */
    public static function jsonToArr($value, $assoc = false, $defaultValue = []) {
        return $value ? ( (is_array($value) || is_object($value)) ? $value : json_decode($value, $assoc)) : $defaultValue;
    }

    /**
     * 把数组形式的字段变为字符串形式
     * @param mixed $value 要转换的值
     * @param integer $options json_encode的options参数
     * @param mixed $defaultValue 默认值
     * @return string 返回处理完成的值
     */
    public static function toJsonStr($value, $options = 0, $defaultValue = '') {
        return $value ? ( (is_array($value) || is_object($value)) ? json_encode($value, $options) : $value) : $defaultValue;
    }

    /**
     * 数组转换为对象
     * @param array $array
     * @return object
     */
    public static function array2object($array) {

        if (is_array($array)) {
            $obj = new StdClass();

            foreach ($array as $key => $val) {
                $obj->$key = $val;
            }
        } else {
            $obj = $array;
        }

        return $obj;
    }

    /**
     * 对象转换为数组
     * @param object
     * @return array
     */
    public static function object2array($object) {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        } else {
            $array = $object;
        }
        return $array;
    }

    /**
     * 获取一个 通过数组值为索引 的数组
     * @param array $array 
     * @return array
     */
    public static function indexByValue($array) {
        $return = [];
        foreach ($array as $val) {
            $return[$val] = $val;
        }
        return $return;
    }
    
    /**
     * 获取一个 自然数为索引 的数组
     * @param array $array 
     * @return array
     */
    public static function indexByNum($array) {
        $return = [];
        foreach ($array as $val) {
            $return[] = $val;
        }
        return $return;
    }
    
    /**
     * 转换秒数为可读的时间
     * @param int $second
     * @return string
     */
    public static function makeReadableSecond($second) {
        $min = round($second/60);
        $newSecond = $second%60;
        return ($min ? "第{$min}分" : '').("第{$newSecond}秒");
    }

}
