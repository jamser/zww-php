<?php

namespace WechatSdk\pay;

/**
 * 
 * 订单查询输入对象
 * @author widyhu
 *
 */
class OrderQuery extends DataBase {

    /**
     * 设置微信的订单号，优先使用
     * @param string $value 
     * */
    public function SetTransaction_id($value) {
        $this->values['transaction_id'] = $value;
    }

    /**
     * 获取微信的订单号，优先使用的值
     * @return 值
     * */
    public function GetTransaction_id() {
        return $this->values['transaction_id'];
    }

    /**
     * 判断微信的订单号，优先使用是否存在
     * @return true 或 false
     * */
    public function IsTransaction_idSet() {
        return array_key_exists('transaction_id', $this->values);
    }

    /**
     * 设置商户系统内部的订单号，当没提供transaction_id时需要传这个。
     * @param string $value 
     * */
    public function SetOut_trade_no($value) {
        $this->values['out_trade_no'] = $value;
    }

    /**
     * 获取商户系统内部的订单号，当没提供transaction_id时需要传这个。的值
     * @return 值
     * */
    public function GetOut_trade_no() {
        return $this->values['out_trade_no'];
    }

    /**
     * 判断商户系统内部的订单号，当没提供transaction_id时需要传这个。是否存在
     * @return true 或 false
     * */
    public function IsOut_trade_noSet() {
        return array_key_exists('out_trade_no', $this->values);
    }

    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
     * @param string $value 
     * */
    public function SetNonce_str($value) {
        $this->values['nonce_str'] = $value;
    }

    /**
     * 获取随机字符串，不长于32位。推荐随机数生成算法的值
     * @return 值
     * */
    public function GetNonce_str() {
        return $this->values['nonce_str'];
    }

    /**
     * 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
     * @return true 或 false
     * */
    public function IsNonce_strSet() {
        return array_key_exists('nonce_str', $this->values);
    }

}
