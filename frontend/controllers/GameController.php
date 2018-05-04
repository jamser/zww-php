<?php
namespace frontend\controllers;

use common\models\WechatUnionid;
use Yii;
use yii\web\Controller;
use frontend\models\Doll;
use frontend\models\Member;
use frontend\models\MemberToken;
use frontend\models\MemberAddr;
use frontend\models\SwVideo;
use common\helpers\MyFunction;
use yii\web\Session;

class GameController extends Controller{
    public $enableCsrfValidation = false;
    public $layout = false;

    //游戏房间列表
    public function actionIndex($code,$channel=null)
    {
        $code = $_GET['code'];
        print_r($code);die;
        Yii::$app->session->open();
        $mp = \nextrip\wechat\models\Mp::findAcModel('defaultMp');
        $wechatMpAuth = new \WechatSdk\mp\Auth($mp->app_id, $mp->app_secret);
        //$code = $_GET['code'];
//        $accessToken = $wechatMpAuth->getAccessPermission($code);
//        $openId = $accessToken['openid'];
//        $accesstoken = $accessToken['access_token'];
//        //获取微信用户信息
//        $userInfo = $wechatMpAuth->getUser($openId,$accesstoken);
//        $userInfo = $this->object2array($userInfo);
//        $unionid= $userInfo['unionid'];
//        $user_check = WechatUnionid::find()->where(['union_id'=>$unionid])->one();
//        if(empty($user_check)){
//            $userid = '';
//        }else{
//            $userid = $user_check['id'];
//        }
//        $token = $this->getToken();
//        $token = 'wx_'.$token;
////        $session->set('token',$token);
//        //存储用户信息
//        $myfunction = new MyFunction();
//        $myfunction->addUser($userInfo,$token);
//        //房间信息
//        $rooms = Doll::find()->asArray()->all();
//        print_r($code);die;

        return $this->redirect("http://h5.365zhuawawa.com/H5/index.html?code=$code".($channel?"&channel={$channel}":''));
//        $session = Yii::$app->session;
//        $result = $session->get('token');
//        if($result){
//            $rooms = Doll::find()->asArray()->all();
//
//            return $this->render('index',[
//                'token' => $result,
//                'rooms' => $rooms,
//            ]);
//        }else{
//            Yii::$app->session->open();
//            $mp = \nextrip\wechat\models\Mp::findAcModel('defaultMp');
//            $wechatMpAuth = new \WechatSdk\mp\Auth($mp->app_id, $mp->app_secret);
//            $code = $_GET['code'];
//            $accessToken = $wechatMpAuth->getAccessPermission($code);
//            $openId = $accessToken['openid'];
//            $accesstoken = $accessToken['access_token'];
//            //获取微信用户信息
//            $userInfo = $wechatMpAuth->getUser($openId,$accesstoken);
//            $userInfo = $this->object2array($userInfo);
//            $token = $this->getToken();
//            $session->set('token',$token);
//            //存储用户信息
//            $myfunction = new MyFunction();
//            $myfunction->addUser($userInfo,$token);
//            //房间信息
//            $rooms = Doll::find()->asArray()->all();
//
//            return $this->redirect("http://jgm.nat300.top?token=$token");

//            return $this->render('index',[
//                'token' => $token,
//                'rooms' => $rooms,
//            ]);
//        }
    }

    public function actionTest(){
        $token = "12345";
        return $this->redirect("jgm.nat300.top?token=$token");
    }

    //对象数据转化为数组数据
    function object2array($object) {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        }
        else {
            $array = $object;
        }
        return $array;
    }

    //用户token
    private function getToken(){
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()+-';
        $random = $chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)];//Random 5 times
        $content = uniqid().$random;   // 类似  5443e09c27bf4aB4uT
        return sha1($content);
    }

    //游戏房间,测试用
    public function actionGame(){
        $id = $_GET['id'];
        $token = $_GET['token'];
        $result = Doll::find()->where(['id'=>$id])->asArray()->one();
        $userId = MemberToken::find()->where(['token'=>$token])->asArray()->one();
        $userId = $userId['member_id'];
        return $this->render('game',[
            'machineData' => $result,
            'token' => $token,
            'userId' => $userId,
        ]);
    }

    //socket游戏房间
    public function actionGameClient($dollId,$token){
        $doll = Doll::findOne($dollId);
        $userId = MemberToken::find()->where(['token'=>$token])->asArray()->one();
        $userId = $userId['member_id'];
        $validateKey = Yii::$app->params['wmValidateKey'];
        $swData = SwVideo::find()->where(['roomId'=>$dollId])->asArray()->one();
        return $this->render('game_client',[
            'machineData' => $doll,
            'roomId'=>$doll->machine_code,
            'machineId'=>$doll->id,
            'machineCode'=>$doll->machine_code,
            'key'=>md5("{$doll->id}_{$doll->machine_code}_{$userId}_{$validateKey}"),
            'userId' => $userId,
            'swData' => $swData
        ]);
    }

    //判断用户金币是否充足
    public function actionCoin($userId,$roomId){
        $roomInfo = Doll::find()->where(['machine_code'=>$roomId])->asArray()->one();
        $doll_coin = $roomInfo['price'];
        $userInfo = Member::find()->where(['id'=>$userId])->asArray()->one();
        $user_coin = $userInfo['coins'];
        $coins = $user_coin-$doll_coin;
        if($user_coin >= $doll_coin){
            $myfunction = new MyFunction();
            $myfunction->reduceCoin($coins,$userId);
            return json_encode(array('code'=>200,'msg'=>'coins enough'));
        }else{
            return json_encode(array('code'=>403,'msg'=>'coins not enough'));
        }
    }

    //修改数据库机器状态
    public function actionStatus($dollId,$status){
        $myfunction = new MyFunction();
        $myfunction->updateStatus($dollId,$status);
    }

    //订单入库
    public function actionOrder($userId,$dollId){
        $addrInfo = MemberAddr::find()->where(['member_id'=>$userId])->asArray()->one();
        $address_id = $addrInfo['id'];
        $myfunction = new MyFunction();
        $myfunction->addOrder($address_id,$dollId);
    }

    //用户个人信息
    public function actionUserInfo(){
//        $token = $_GET['token'];
        $token = "5480c84e0d07e7b9413ccd6ae0bfddfce065784b";
        $userId = MemberToken::find()->where(['token'=>$token])->asArray()->one();
        if($userId){
            $userId = $userId['member_id'];
            $userInfo = Member::find()->where(['id'=>$userId])->asArray()->one();
            return $this->render('user',[
                'userInfo' => $userInfo,
            ]);
        }else{
            return json_encode(array('code'=>403,'msg'=>'error token'));
        }
    }
}