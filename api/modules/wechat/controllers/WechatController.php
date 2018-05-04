<?php
namespace api\modules\wechat\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\WechatUser;

class WechatController extends Controller{
    //第三方发起第三方登录，获得code
    public function actionGetCode(){
        $appid = Yii::$app->params['appid'];//应用唯一标识
        $redirect_uri = urlencode("http://dev.365zhuawawa.com");//重定向地址，需要进行UrlEncode
        $state = "";//用于保持请求和回调的状态，授权请求后原样带回给第三方。该参数可用于防止csrf攻击（跨站请求伪造攻击），建议第三方带上该参数，可设置为简单的随机数加session进行校验
        $scope = "snsapi_userinfo";//应用授权作用域，拥有多个作用域用逗号（,）分隔，网页应用目前仅填写snsapi_login即可
        $request_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=STATE#wechat_redirect";
        //初始化一个curl会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        print_r($result);
    }
    //用户授权接口：获取access_token、openId等；获取并保存用户资料到数据库
    public function actionAccessToken($code)
    {
//        $request = Yii::$app->request;
//        $code = $request->post('code');
//        $state = $request->post('state');
        $appid = Yii::$app->params['appid'];
        $appsecret = Yii::$app->params['appsecret'];
        $request_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code";
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
        $time = date("Y-m-d H:i:s",time());
        //请求微信接口，获取用户信息
        $userInfo = $this->getUserInfo($access_token,$openid);

        $user_check = WechatUser::find()->where(['openid'=>$openid])->one();
        if ($user_check) {
            //更新用户资料
//            $user_check->unionid = $userInfo['unionid'];
//            $user_check->openid = $userInfo['openid'];
//            $user_check->nickname = $userInfo['nickname'];
//            $user_check->sex = $userInfo['sex'];
//            $user_check->headimgurl = $userInfo['headimgurl'];
//            $user_check->country = $userInfo['country'];
//            $user_check->province = $userInfo['province'];
//            $user_check->city = $userInfo['city'];
            $user_check->access_token = $access_token;
            $user_check->refresh_token = $refresh_token;
//            $user_check->created_at = $time;
            $user_check->update();
        } else {
            $inviteCode = rand(10000000, 99999999);
            //保存用户资料
            $user = new WechatUser();
            $user->unionid = $userInfo['unionid'];
            $user->openid = $userInfo['openid'];
            $user->nickname = $userInfo['nickname'];
            $user->sex = $userInfo['sex'];
            $user->headimgurl = $userInfo['headimgurl'];
            $user->country = $userInfo['country'];
            $user->province = $userInfo['province'];
            $user->city = $userInfo['city'];
            $user->access_token = $access_token;
            $user->refresh_token = $refresh_token;
            $user->created_at = $time;
            $user->invite_code = $inviteCode;
            $user->save();
        }
    }

    //从微信获取用户资料
    public function getUserInfo($access_token,$openid)
    {
        $request_url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
        //初始化一个curl会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        return $result;
    }

    private function response($text)
    {
        return json_decode($text, true);
    }

    //刷新access_token
    public function actionRefreshToken($refresh_token){
        $appid = Yii::$app->params['appid'];
        $request_url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=$appid&grant_type=refresh_token&refresh_token=$refresh_token";
        //初始化一个curl会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        $user = new WechatUser();
        $user->openid = $result['openid'];
        $user->access_token = $result['access_token'];
        $user->refresh_token = $result['refresh_token'];
        $user->update();
        return $result;
    }

    //验证access_token是否有效
    public function actionAuth($openId,$access_token){
        $request_url = "https://api.weixin.qq.com/sns/auth?access_token=$access_token&openid=$openId";
        //初始化一个curl会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        return $result;
    }
}