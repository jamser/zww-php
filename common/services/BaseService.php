<?php

namespace common\services;

/**
 * 服务基础类
 */
class BaseService extends \yii\base\Object {
    
    /**
     * 错误码
     * @var integer 
     */
    public $error_code;
    
    /**
     * 错误信息
     * @var string 
     */
    public $error_msg;
    
    /**
     * 设置错误
     * @param integer $code 错误码
     * @param string $msg 错误消息
     */
    public function setError($code, $msg) {
        $this->error_code = $code;
        $this->error_msg = $msg;
    }
}

