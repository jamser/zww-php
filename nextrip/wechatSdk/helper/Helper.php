<?php

namespace WechatSdk\helper;

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
     * 判断是否是手机
     * @return bool 
     */
    public static function isMobile() {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (filter_input(INPUT_SERVER, 'HTTP_X_WAP_PROFILE')) {
            return true;
        }
        //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (($httpVia = filter_input(INPUT_SERVER, 'HTTP_VIA'))) {
            //找不到为flase,否则为true
            return stristr($httpVia, "wap") ? true : false;
        }
        //判断手机发送的客户端标志,兼容性有待提高
        if (($httpUserAgent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'))) {
            $clientkeywords = array(
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($httpUserAgent))) {
                return true;
            }
        }
        //协议法，因为有可能不准确，放到最后判断
        if ($httpAccept = filter_input(INPUT_SERVER, 'HTTP_ACCEPT')) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($httpAccept, 'vnd.wap.wml') !== false) && (strpos($httpAccept, 'text/html') === false || (strpos($httpAccept, 'vnd.wap.wml') < strpos($httpAccept, 'text/html')))) {
                return true;
            }
        }
        return false;
    }
}
