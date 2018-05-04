<?php
namespace api\modules\wechat\controllers;

use Yii;
use yii\web\Controller;
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);
include '../modules/wechat/sdks/Wxpay/example/WxPay.JsApiPay.php';
include '../modules/wechat/sdks/Wxpay/example/log.php';
include '../modules/wechat/sdks/Wxpay/lib/WxPay.Api.php';
include '../modules/wechat/sdks/Wxpay/lib/WxPay.Notify.php';

class PayController extends Controller{
    //统一下单接口
    public function actionOrder(){
        $request = Yii::$app->request;
        $describe = $request->post('describe');
        $attach = $request->post('attach');
        $fee =$request->post('fee');
        $tag = $request->post('tag');
        $tools = new \JsApiPay();
        $openId = $tools->GetOpenid();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($describe);//商品或支付单简要描述
        $input->SetAttach($attach);//附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        $input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee($fee);//设置订单总金额，只能为整数，详见支付金额
        $input->SetTime_start(date("YmdHis"),time());//订单生成时间，格式为yyyyMMddHHmmss
        $input->SetTime_expire(date("YmdHis", time() + 600));//订单失效时间，格式为yyyyMMddHHmmss
        $input->SetGoods_tag($tag);//商品标记，代金券或立减优惠功能的参数
        $input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");//接收微信支付异步通知回调地址
        $input->SetTrade_type("JSAPI");//设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
        $input->SetOpenid($openId);//接口获取到用户的Openid
        $order = \WxPayApi::unifiedOrder($input);//统一下单
        echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
        printf_info($order);
        $jsApiParameters = $tools->GetJsApiParameters($order);

        //获取共享收货地址js函数参数
        $editAddress = $tools->GetEditAddressParameters();
    }

    //支付回调处理
    public function actionNotify($data, &$msg){
        \Log::DEBUG("call back:" . json_encode($data));
        $notfiyOutput = array();

        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if(!$this->QueryOrder($data["transaction_id"])){
            $msg = "订单查询失败";
            return false;
        }
        return true;
    }

    //查询订单
    private function QueryOrder($transaction_id){
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = \WxPayApi::orderQuery($input);
        \Log::DEBUG("query:" . json_encode($result));
        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return true;
        }
        return false;
    }
}