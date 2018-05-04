<?php
namespace frontend\controllers;

use frontend\modules\api\controllers\Controller;
use Yii;
use frontend\models\Member;
use frontend\models\MemberPref;
use frontend\models\MemberToken;
use frontend\models\MemberSmscode;
use frontend\models\ChargeRules;
use frontend\models\SendSms;

class LoginController extends Controller{
    public $enableCsrfValidation = false;
    //登录接口
    public function actionLogin(){
        $request = Yii::$app->request;
        $mobile = $request->post('mobile');
        $psd = $request->post('password');
        if(empty($mobile)){
            $array = array('success'=>false,'statusCode'=>403,'message'=>"请输入账号密码");
            return json_encode($array);
        }else{
            $userinfo = Member::find()->where(['mobile'=>$mobile])->asArray()->one();//用户信息，t_member
            $userid = $userinfo['id'];
            $passwd = $userinfo['password'];
            $userinfo = json_encode($userinfo);
            if($psd == $passwd){
                $token = $this->getHash();
                $model = new MemberToken();
                $model->token = $token;
                $model->member_id = $userid;
                $model->save();
                $tokeninfo = MemberToken::find()->where(['member_id'=>$userid])->asArray()->one();
                $token = $tokeninfo['token'];
                $prefinfo = MemberPref::find()->where(['member_id'=>$userid])->asArray()->one();
                $prefinfo = json_encode($prefinfo);
                $array = array('message'=>'登录成功','token'=>$token,'success'=>true,'resultData'=>$userinfo,'prefset'=>$prefinfo,'statusCode'=>200);
                return json_encode($array);
            }else{
                $array = array('success'=>false,'statusCode'=>403,'message'=>"用户名或密码错误");
                return json_encode($array);
            }
        }
    }

    public function getHash(){
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()+-';
        $random = $chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)];//Random 5 times
        $content = uniqid().$random;   // 类似  5443e09c27bf4aB4uT
        return sha1($content);
    }

    //退出登录
    public function actionLogoff(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        $token = $request->post('token');
        $tok = MemberToken::find()->where(['member_id'=>$id])->asArray()->one();
        $tok = $tok['token'];
        if($token != $tok || $token == null){
            $array = array('success'=>'false','statusCode'=>403,'message'=>"退出登录失败");
            return json_encode($array);
        }else{
            MemberToken::deleteAll(['member_id'=> $id]);
            $array = array('success'=>'true','statusCode'=>200,'message'=>"退出登录成功");
            return json_encode($array);
        }
    }

    function send_post($url, $post_data)
    {
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST', // or GET
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60
            ) // 超时时间（单位:s）

        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    //给用户发送短信验证码
    public function sendSms()
    {
        $request = Yii::$app->request;
        $mobile = $request->post('mobile');
        $result = MemberSmscode::find()->where(['mobile'=>$mobile])->asArray()->one();
        $smscode = $result['smscode'];
        $templateParam = array('code'=>$smscode,'product'=>'365抓娃娃');
        $send = new SendSms();
        $signName = '';//短信签名
        $templateCode = '';//短信模板Code
        $response = $send->sendSms($signName, $templateCode, $mobile,$templateParam);
        return $response;
    }

    //获取验证码
    public function actionGetSmsCode(){
        $request = Yii::$app->request;
        $mobile = $request->post('mobile');
        if(empty($mobile)){
            $array = array('success'=>false,'statusCode'=>403,'message'=>'请输入手机号');
            return json_encode($array);
        }else{
            $result = MemberSmscode::find()->where(['mobile'=>$mobile])->asArray()->one();//非空代表之前号码已经获取过验证码
            if(empty($result)){
                //$this->sendSms($mobile);//发送验证码到用户手机(测试阶段先省略)
                $smsCode = rand(100000,999999);
                $start_time = date("Y-m-d H:i:s",time());
                $times = time()+60*3;
                $end_time = date("Y-m-d H:i:s",$times);
                $model = new MemberSmscode();
                $model->mobile = $mobile;
                $model->smscode = $smsCode;
                $model->valid_start_time = $start_time;
                $model->valid_end_time = $end_time;
                $model->save();
                $array = array('success'=>true,'statusCode'=>200,'message'=>'获取验证码成功');
                return json_encode($array);
            }else{
//                $this->sendSms($mobile);
                $smsCode = rand(100000,999999);
                $start_time = date("Y-m-d H:i:s",time());
                $times = time()+60*5;
                $end_time = date("Y-m-d H:i:s",$times);
                $model = new MemberSmscode();
                $model->mobile = $mobile;
                $model->smscode = $smsCode;
                $model->valid_start_time = $start_time;
                $model->valid_end_time = $end_time;
                $model->update();
                $array = array('success'=>true,'statusCode'=>200,'message'=>'获取验证码成功');
                return json_encode($array);
            }
        }
    }

    //注册
    public function actionRegister(){
        $request = Yii::$app->request;
        $mobile = $request->post('mobile');
        $password = $request->post('password');
        $smsCode = $request->post('SmsCode');
        $inviteCode = rand(10000000, 99999999);
        $userinfo = Member::find()->where(['mobile'=>$mobile])->asArray()->one();
        if(empty($userinfo)){
            $result = MemberSmscode::find()->where(['mobile'=>$mobile])->asArray()->one();//验证码
            $smscode = $result['smscode'];
            $end_time = $result['valid_end_time'];
            $end_time = strtotime($end_time);
            if($smsCode != $smscode){
                $array = array('success'=>false,'statusCode'=>403,'message'=>'验证码不正确');
                return json_encode($array);
            }elseif($end_time<time()){
                $array = array('success'=>false,'statusCode'=>403,'message'=>'验证码已过期');
                return json_encode($array);
            }else{
                $model = new Member();
                $model->memberID = (string)($inviteCode);
                $model->mobile = $mobile;
                $model->password = $password;
                $model->register_date = date("Y-m-d H:i:s",time());
                if($model->validate()){
                    $model->save();
                    $arr = array('success'=>true,'statusCode'=>200,'message'=>'注册成功');
                    return json_encode($arr);
                }else{
                    $arr = array('success'=>false,'statusCode'=>403,'message'=>'注册失败');
                    return json_encode($arr);
                }
            }
        }else{
            $array = array('success'=>false,'statusCode'=>403,'message'=>'手机号已注册');
            return json_encode($array);
        }
    }

    //修改密码授权
    public function authBySmsCode(){
        $request = Yii::$app->request;
        $mobile = $request->post('mobile');
        $smsCode = $request->post('smsCode');
        $userinfo = Member::find()->where(['mobile'=>$mobile])->asArray()->one();
        $id = $userinfo['id'];
        $result = MemberSmscode::find()->where(['mobile'=>$mobile])->asArray()->one();
        $smscode = $result['smscode'];
        $end_time = $result['valid_end_time'];
        $end_time = strtotime($end_time);
        if($smsCode != $smscode){
            $array = array('success'=>false,'statusCode'=>403,'message'=>'验证码不正确');
            return json_encode($array);
        }elseif($end_time>time()){
            $array = array('success'=>false,'statusCode'=>403,'message'=>'验证码已过期');
            return json_encode($array);
        }else{
            $token = $this->getHash();
            $model = new MemberToken();
            $model->token = $token;
            $model->member_id = $id;
            $model->save();
            $array = array('success'=>true,'statusCode'=>200,'message'=>'授权成功','token'=>$token);
            return json_encode($array);
        }
    }

    //修改密码
    public function actionUpdatePwd(){
        $request = Yii::$app->request;
        $mobile = $request->post('mobile');
        $password = $request->post('password');
        $token = $request->post('token');
        $result = Member::find()->where(['mobile'=>$mobile])->asArray()->one();
        $id = $result['id'];
        if(empty($result)){
            $array = array('success'=>false,'statusCode'=>403,'message'=>'用户不存在');
            return json_encode($array);
        }else{
            $tok = MemberToken::find()->where(['member_id'=>$id])->asArray()->one();
            $token1 = $tok['token'];
            if($token != $token1){
                $array = array('success'=>false,'statusCode'=>403,'message'=>'授权码错误');
                return json_encode($array);
            }else{
                $sql = "update t_member set password = $password WHERE mobile = $mobile";
                $result = Yii::$app->db->createCommand($sql)->execute();
                if($result){
                    $array = array('success'=>true,'statusCode'=>200,'message'=>'修改密码成功');
                    return json_encode($array);
                }else{
                    $array = array('success'=>false,'statusCode'=>403,'message'=>'修改密码失败');
                    return json_encode($array);
                }

            }
        }
    }

    //微信支付
    public function actionPay(){
        $request = Yii::$app->post;
        $price = $request->post('price');
        $id = $request->post('memberid');
        $token = $request->post('token');
        $coins = ChargeRules::find()->where(['charge_price'=>$price])->asArray()->one();
        $coin = $coins['coins_charge']+$coins['coins_offer'];//金币数量
        $user = Member::find()->where(['member_id'=>$id])->asArray()->one();
        $coins1 = $user['coins'];
        $coin = $coin + $coins1;
        $tok = MemberToken::find()->where(['token'=>$token])->asArray()->one();
        $end_time = $tok['valid_end_time'];
        $end_time = strtotime($end_time);
        if($end_time>time()){
            $array = array('success'=>false,'statusCode'=>403,'message'=>'token已失效');
            return json_encode($array);
        }else{
            $sql = "update t_member set coins = $coin WHERE id = $id";
            $result = Yii::$app->db->createCommand($sql)->execute();
            if($result){
                $array = array('success'=>true,'statusCode'=>200,'message'=>'充值成功');
                return json_encode($array);
            }else{
                $array = array('success'=>false,'statusCode'=>403,'message'=>'充值失败');
                return json_encode($array);
            }
        }
    }

    public function actionTest(){
        $request_url = 'http://192.168.2.96/ali/aliyun-openapi-php-sdk-master/api/GetDeviceShadow.php';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        print_r($result);
    }
}