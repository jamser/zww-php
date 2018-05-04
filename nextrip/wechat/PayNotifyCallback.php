<?php

namespace common\extensions\wechat;

use Yii;
use WechatSdk\pay\Notify;
use WechatSdk\pay\OrderQuery;
use WechatSdk\pay\Api;

class PayNotifyCallback extends Notify {

    //查询订单
    public function Queryorder($transaction_id) {
        $input = new OrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = Api::orderQuery($input);
        Yii::trace("query:" . json_encode($result));
        if (array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg) {
        Yii::trace("call back:" . json_encode($data));

        if (!array_key_exists("transaction_id", $data)) {
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if (!$this->Queryorder($data["transaction_id"])) {
            $msg = "订单查询失败";
            return false;
        }
        return true;
    }

}
