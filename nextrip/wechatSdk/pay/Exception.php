<?php

namespace WechatSdk\pay;

/**
 * 
 * 微信支付API异常类
 * @author widyhu
 *
 */
class Exception extends \Exception {

    public function errorMessage() {
        return $this->getMessage();
    }

}
