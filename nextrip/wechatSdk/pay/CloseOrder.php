<?php

namespace WechatSdk\pay;

/**
 * 
 * 关闭订单输入对象
 * @author widyhu
 *
 */
class CloseOrder extends DataBase {

    /**
     * 设置商户系统内部的订单号
     * @param string $value 
     * */
    public function SetOut_trade_no($value) {
        $this->values['out_trade_no'] = $value;
    }

    /**
     * 获取商户系统内部的订单号的值
     * @return 值
     * */
    public function GetOut_trade_no() {
        return $this->values['out_trade_no'];
    }

    /**
     * 判断商户系统内部的订单号是否存在
     * @return true 或 false
     * */
    public function IsOut_trade_noSet() {
        return array_key_exists('out_trade_no', $this->values);
    }

    /**
     * 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
     * @param string $value 
     * */
    public function SetNonce_str($value) {
        $this->values['nonce_str'] = $value;
    }

    /**
     * 获取商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号的值
     * @return 值
     * */
    public function GetNonce_str() {
        return $this->values['nonce_str'];
    }

    /**
     * 判断商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号是否存在
     * @return true 或 false
     * */
    public function IsNonce_strSet() {
        return array_key_exists('nonce_str', $this->values);
    }

}
