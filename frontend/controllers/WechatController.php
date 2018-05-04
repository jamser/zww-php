<?php
namespace api\controllers;

use nextrip\wechat\models\Mp;
use Yii;
use yii\web\Controller;
use frontend\models\WechatUser;

class WechatController extends Controller{
    //用户授权接口：获取access_token、openId等；获取并保存用户资料到数据库
    public function actionAccesstoken()
    {
        $request = Yii::$app->request;
        $code = $request->post('code');
        $state = $request->post('state');
        $appid = $this->params['appid'];
        $appsecret = $this->params['appsecret'];
        $request_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';
        //初始化一个curl会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        //获取token和openid成功，数据解析
        $access_token = $result['access_token'];
        $refresh_token = $result['refresh_token'];
        $openid = $result['openid'];

        //请求微信接口，获取用户信息
        $userInfo = $this->getUserInfo($access_token,$openid);

        $user_check = WechatUser::find()->where(['openid'=>$openid])->one();
        if ($user_check) {
            //更新用户资料
            $user_check->nickname = $userInfo['nickname'];
            $user_check->sex = $userInfo['sex'];
            $user_check->headimgurl = $userInfo['headimgurl'];
            $user_check->country = $userInfo['country'];
            $user_check->province = $userInfo['province'];
            $user_check->city = $userInfo['city'];
            $user_check->access_token = $access_token;
            $user_check->refresh_token = $refresh_token;
            $user_check->update();
        } else {
            //保存用户资料
            $user = new WechatUser();
            $user->nickname = $userInfo['nickname'];
            $user->sex = $userInfo['sex'];
            $user->headimgurl = $userInfo['headimgurl'];
            $user->country = $userInfo['country'];
            $user->province = $userInfo['province'];
            $user->city = $userInfo['city'];
            $user->access_token = $access_token;
            $user->refresh_token = $refresh_token;
            $user->openid = $openid;
            $user->save();
        }
        //前端网页的重定向
        if ($openid) {
            return $this->redirect($state.$openid);
        } else {
            return $this->redirect($state);
        }
    }

    //从微信获取用户资料
    public function getUserInfo($access_token,$openid)
    {
        $request_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        //初始化一个curl会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        return $result;
    }

    //获取用户资料接口
    public function actionUserinfo()
    {
        if(isset($_REQUEST["openid"])){
            $openid = $_REQUEST["openid"];
            $user = WechatUser::find()->where(['openid'=>$openid])->one();
            if ($user) {
                $result['error'] = 0;
                $result['msg'] = '获取成功';
                $result['user'] = $user;
            } else {
                $result['error'] = 1;
                $result['msg'] = '没有该用户';
            }
        } else {
            $result['error'] = 1;
            $result['msg'] = 'openid为空';
        }
        return $result;
    }

    private function response($text)
    {
        return json_decode($text, true);
    }

    //统一下单接口
    public function actionOrder(){
        $appid = $this->params['appid'];
        $mch_id = $_GET['mch_id'];//商户号
        $nonce_str = $_GET['nonce_str'];//随机字符串
        $sign = $_GET['sign'];//签名
        $body = $_GET['body'] = "365抓娃娃支付";//商品描述
        $out_trade_no = $_GET['out_trade_no'];//商品订单号
        $total_fee = $_GET['total_fee'];//总金额
        $spbill_create_ip = $_GET['spbill_create_ip'];//终端IP，订单生成的机器IP
        $notify_url = $_GET['notify_url'];//通知地址，支付完成后微信发送该链接信息，可以判断用户是否支付成功，改变订单状态
        $trade_type = 'APP';//交易类型
        $request_url = 'https://api.mch.weixin.qq.com/pay/unifiedorder?appid='.$appid.'&mch_id='.$mch_id.'&nonce_str='.$nonce_str.'&sign='.$sign.'
             &body='.$body.'&out_trade_no='.$out_trade_no.'&total_fee='.$total_fee.'&spbill_create_ip='.$spbill_create_ip.'&notify_url='.$notify_url.'
              &trade_type='.$trade_type.'';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        $prepay_id = $result['prepat_id'];
        if(empty($prepay_id)){
            $array = array('success'=>false,'statusCode'=>403,'message'=>'统一支付接口获取预支付订单出错');
            return \GuzzleHttp\json_encode($array);
        }else{
            return $result;//返回trade_type,prepay_id
        }
    }

    //支付回调接口
    public function actionNotify(){
        $request = Yii::$app->request;
        $prepay_id = $request->post('prepay_id');

    }

    //查询订单接口
    public function actionOrderQuery(){
        $appid = $this->params['appid'];
        $mch_id = $_GET['mch_id'];//商户号
        $transaction_id = $_GET['transaction_id'];//微信订单号
        $out_trade_no = $_GET['out_trade_no'];//商户订单号,订单号需要一个就可以了
        $nonce_str = $_GET['nonce_str'];//随机字符串
        $sign = $_GET['sign'];
        $request_url = 'https://api.mch.weixin.qq.com/pay/orderquery?appid='.$appid.'&mch_id='.$mch_id.'&transaction_id='.$transaction_id.'
         &nonce_str='.$nonce_str.'&sign='.$sign.'';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        return $result;//返回订单的详细信息
    }

    //关闭订单接口
    public function actionCloseOrder(){
        $appid = $this->params['appid'];
        $mch_id = $_GET['mch_id'];
        $out_trade_no = $_GET['out_trade_no'];
        $nonce_str = $_GET['nonce_str'];
        $sign = $_GET['sign'];
        $request_url = 'https://api.mch.weixin.qq.com/pay/closeorder?appid='.$appid.'&mch_id='.$mch_id.'&out_trade_no='.$out_trade_no.'&nonce_str='.$nonce_str.'
                    &sign='.$sign.'';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        return $result;
    }

    //申请退款接口
    public function actionRefund(){
        $appid = $this->params['appid'];
        $mch_id = $_GET['mch_id'];//商户号
        $nonce_str = $_GET['nonce_str'];//随机字符串
        $sign = $_GET['sign'];//签名
        $transaction_id = $_GET['transaction_id'];//微信订单号
        $out_trade_no = $_GET['out_trande_no'];//商户订单号
        $out_refund_no = $_GET['out_refund_no'];//商户退款单号
        $total_fee = $_GET['total_fee'];//订单总额
        $refund_fee = $_GET['refund_fee'];//退款金额
        $request_url = 'https://api.mch.weixin.qq.com/secapi/pay/refund?appid='.$appid.'&mch_id='.$mch_id.'&nonce_str='.$nonce_str.'&sign='.$sign.'
                   &transaction_id='.$transaction_id.'&out_refund_no='.$out_refund_no.'&total_fee='.$total_fee.'
                    &refund_fee='.$refund_fee.'';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        return $result;
    }

    //查询退款接口
    public function actionRefundQuery(){
        $appid = $this->params['appid'];
        $mch_id = $_GET['mch_id'];
        $nonce_str = $_GET['nonce_str'];
        $sign = $_GET['sign'];
        $transaction_id = $_GET['transaction_id'];
        $request_url = 'https://api.mch.weixin.qq.com/pay/refundquery?appid='.$appid.'&mch_id='.$mch_id.'$nonce_str='.$nonce_str.'
                    &sign='.$sign.'&transaction_id='.$transaction_id.'';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        return $result;
    }

    //下载对账单接口
    public function actionDownloadBill(){
        $appid = $this->params['appid'];
        $mch_id = $_GET['mch_id'];
        $nonce_str = $_GET['nonce_str'];
        $sign = $_GET['sign'];
        $bill_date = $_GET['bill_date'];
        $bill_type = $_GET['bill_type'];
        $request_url = 'https://api.mch.weixin.qq.com/pay/downloadbill?appid='.$appid.'&mch_id='.$mch_id.'&nonce_str='.$nonce_str.'
                   &sign='.$sign.'&bill_date='.$bill_date.'&bill_type='.$bill_type.'';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        return $result;
    }

    //交易保障接口
    public function actionReport(){
        $mch_id = $_GET['mch_id'];
        $nonce_str = $_GET['nonce_str'];

    }

    //密钥
    public function getSign($appid,$body,$device_info,$mch_id,$nonce_str){
        $key = '';
        $strA = 'appid='.$appid.'&body='.$body.'&device_info='.$device_info.'&mch_id='.$mch_id.'&nonce_str='.$nonce_str.'';
        $SignTemp = $strA = "&key='192006250b4c09247ec02edce69f6a2d'";//注：key为商户平台设置的密钥key
        $sign = md5($SignTemp).toUpperCase();//注：MD5签名方式
        $sign = hash_hmac("sha256",$SignTemp,$key).toUpperCase();//注：HMAC-SHA256签名方式
    }


    //获取微信公众号关注人信息
    public function actionMp(){
        print_r('123');die;
        $appid = $this->params['appid'];
        $appsecret = $this->params['appsecret'];
        $request_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret.'';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        print_r($result);
    }

    //模板消息
    public function actionMessage(){
        $access_token = '6_k9HzxQZCjSQX4VW3BDEQVsiM8HCdxDp3ZLvNM5wBtUxoIHQ4V_qAexwTJo6pXc_V5P9e_2guQ8_0bCCr87Y1yaG3j9wqGjSfp-aIv7OKIPgGsUNhlh_zNqZ0LBDK_OqHwK0TgizZfthZejhTGUKcAGAMAQ';
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;//access_token改成你的有效值
        $time = date('Y-m-d H:i:s',time());

        $data = array(
            'first' => array(
                'value' => '机器概率异常报警',
                'color' => '#FF0000'
            ),
            'keyword1' => array(
                'value' => $time,
                'color' => '#FF0000'
            ),
            'keyword2' => array(
                'value' => '机器Id',
                'color' => '#FF0000'
            ),
            'remark' => array(
                'value' => '请尽快检查机器',
                'color' => '#FF0000'
            )
        );
        $template_msg=array('touser'=>'opTs00ytey1f_xygGGxqvUoR9CSk','template_id'=>'qh1jlbmnW-CeSQV6Fi5LvB1CoPFC0s4odcOauP5fcvI ','topcolor'=>'#FF0000','data'=>$data);

        $curl = curl_init($url);
        $header = array();
        $header[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        // 不输出header头信息
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 伪装浏览器
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        // 保存到字符串而不是输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($curl, CURLOPT_POST, 1);
        // 请求数据
        curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($template_msg));
        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }

    public function actionShare(){
        return $this->render('share');
    }





































}