<?php

namespace WechatSdk\helper;

class Url {
    
    /**
     * 在退出前执行的函数
     * @var array 
     */
    public static $beforeExit = [];//[[func], [array format params]]
    
    /**
     * 获取当前URL
     * @param array $params 需要替换的参数
     * @return string
     */
    public static function current($params=[]) {
        $https = filter_input(INPUT_SERVER, 'HTTPS');
        $serverPort = filter_input(INPUT_SERVER, 'SERVER_PORT');
        $httpHost = filter_input(INPUT_SERVER, 'HTTP_HOST');
        $requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        
        $getParams = filter_input_array(INPUT_GET);
        foreach($params as $key=>$val) {
            if($val !== null) {
                $getParams[$key] = $val;
            } else if(isset($getParams[$key])) {
                unset($getParams[$key]);
            }
        }
        $protocol = ( ($https && $https !== 'off') || $serverPort === 443) ? 'https://' : 'http://';
        $urlParts = explode('?', $requestUri, 2);
        return $protocol . $httpHost. array_shift($urlParts). ($getParams ? '?'. http_build_query($getParams) : '');
    }

    /**
     * 页面跳转 
     * @param string $url 跳转的链接
     */
    public static function redirect($url) {
        header("location: $url");
        if(static::$beforeExit) {
            call_user_func_array(static::$beforeExit[0], static::$beforeExit[1]);
        }
        exit;
    }
    
    /**
     * 在退出前执行
     * @param function $func
     * @param type $params
     */
    public static function setBeforeExit($func, $params) {
        static::$beforeExit = [
            $func, $params
        ];
    }
}

