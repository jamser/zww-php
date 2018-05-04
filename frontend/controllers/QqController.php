<?php
namespace api\controllers;

use Yii;
use yii\web\Controller;
use api\models\QqUser;

class QqController extends Controller{
//    public function actionGetCode(){
//        $client_id = $_GET['client_id'];
//        $redirect_uri = $_GET['redirect_uri'];
//        $state = $_GET['state'];
//        $request_url = 'https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id='.$client_id.'&redirect_uri='.$redirect_uri.'&state='.$state.'';
//        //初始化一个curl会话
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $request_url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $result = curl_exec($ch);
//        curl_close($ch);
//        $result = $this->response($result);
//        //获取Authorization Code 成功
//        $authorization_code = $result['code'];
//        return $authorization_code;
//    }
//
//    public function actionGetToken(){
//        $client_id = $_GET['client_id'];
//        $client_secret = $_GET['client_secret'];
//        $code = $_GET['code'];
//        $redirect_uri = $_GET['redirect_uri'];
//        $request_url = 'https://graph.z.qq.com/moc2/token?grant_type=authorization_code&client_id='.$client_id.'&client_secret='.$client_secret.'&code='.$code.'&redirect_uri='.$redirect_uri.'';
//        //初始化一个curl会话
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $request_url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $result = curl_exec($ch);
//        curl_close($ch);
//        $result = $this->response($result);
//        //获取access_token Code 成功
//        $access_token = $result['access_token'];
//        $expires_in = $result['expires_in'];
//        $refresh_token = $result['refresh_token'];
//        return $result;
//    }

    //WAP
    public function actionAccessToken(){
        $state = $_GET['state'];
        $client_id = $_GET['client_id'];
        $redirect_uri = $_GET['redirect_uri'];
        $request_url = 'https://graph.z.qq.com/moc2/authorize?response_type=token&client_id='.$client_id.'&redirect_uri='.$redirect_uri.'';
        //初始化一个curl会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        //获取access_token成功
        $access_token = $result['access_token'];
        $refresh_token = $result['refresh_token'];
        //获取open_id
        $open_id = $this->getOpenId($access_token);
        $open_id = $open_id['open_id'];
        //获取用户信息
        $userInfo = $this->getUserInfo($access_token,$client_id,$open_id);
        //存储用户信息
        $user_check = QqUser::find()->where(['open_id'=>$open_id])->one();
        if($user_check){
            //更新用户资料
            $user_check->nickname = $userInfo['nickname'];
            $user_check->figureurl = $userInfo['figureurl'];
            $user_check->gender = $userInfo['gender'];
            $user_check->is_yellow_vip = $userInfo['is_yellow_vip'];
            $user_check->vip = $userInfo['vip'];
            $user_check->yellow_vip_level = $userInfo['yellow_vip_level'];
            $user_check->level = $userInfo['level'];
            $user_check->is_yellow_year_vip = $userInfo['is_yellow_year_vip'];
            $user_check->open_id = $open_id;
            $user_check->access_token = $access_token;
            $user_check->refresh_token = $refresh_token;
            $user_check->update();
        } else {
            //保存用户资料
            $user = new QqUser();
            $user->nickname = $userInfo['nickname'];
            $user->figureurl = $userInfo['figureurl'];
            $user->gender = $userInfo['gender'];
            $user->is_yellow_vip = $userInfo['is_yellow_vip'];
            $user->vip = $userInfo['vip'];
            $user->yellow_vip_level = $userInfo['yellow_vip_level'];
            $user->level = $userInfo['level'];
            $user->is_yellow_year_vip = $userInfo['is_yellow_year_vip'];
            $user->access_token = $access_token;
            $user->refresh_token = $refresh_token;
            $user->open_id = $open_id;
            $user->save();
        }
        if ($open_id) {
            return $this->redirect($state.$open_id);
        } else {
            return $this->redirect($state);
        }
    }

    public function getOpenId($access_token){
        $request_url = 'https://graph.z.qq.com/moc2/me?access_token='.$access_token.'';
        //初始化一个curl会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        //返回client_id和open_id
        return $result;
    }

    public function getUserInfo($access_token,$oauth_consumer_key,$openid){
        $request_url = 'https://graph.qq.com/user/get_user_info?access_token='.$access_token.'&oauth_consumer_key='.$oauth_consumer_key.'&open_id='.$openid.'';
        //初始化一个curl会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        return $result;
    }

    //access_token过期重新获取
    public function actionGetToken(){
        $client_id = $_GET['client_id'];
        $client_secret = $_GET['client_secret'];
        $refresh_token = $_GET['refresh_token'];
        $request_url = 'https://graph.z.qq.com/moc2/token?grant_type=refresh_token&client_id='.$client_id.'&client_secret='.$client_secret.'&refresh_token='.$refresh_token.'';
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






































}