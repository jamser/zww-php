<?php
namespace frontend\controllers;

use common\helpers\MyFunction;
use common\models\InviteNum;
use Yii;
use yii\web\Controller;
use frontend\models\Member;
use frontend\models\MemberToken;
use yii\web\Request;

// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:GET');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');

class ShareController extends Controller{
    public $enableCsrfValidation = false;
    public function actionShare(){
        $request = Yii::$app->request;

        $memberId = $request->post('memberId') ? $request->post('memberId') : $request->get('memberId');
        $token = $request->post('token') ? $request->post('token') : $request->get('token');

        if(!$token) {
            Yii::error("member Id : {$memberId}; token {$token} 没有找到授权");
            return json_encode(array('code'=>403,'message'=>'授权已过期'));
        }
        $redis = Yii::$app->redis;
        $type = $redis->TYPE($token);
        if($type!='string') {
            Yii::error("member Id : {$memberId}; token {$token} 授权非string");
            return json_encode(array('code'=>403,'message'=>'授权已过期'));
        }
        $cacheUserId = $redis->GET($token);
        if(!$cacheUserId || !($user=Member::find()->where(['id'=>(int)$cacheUserId])->asArray()->one())) {
            Yii::error("member Id : {$memberId}; token {$token} 分享参数不正确， 找不到ID用户");
            return json_encode(array('code'=>403,'message'=>'分享参数不正确'));
        }
        if($user['memberID']!==trim($memberId)) {
            Yii::error("member Id : {$memberId}; token {$token} 授权memberId不一致");
            return json_encode(array('code'=>403,'message'=>'授权已过期'));
        }

        return json_encode(array('code'=>200,'message'=>'right token','url'=>"http://p.365zhuawawa.com/?r=share/view&memberId=$memberId"));

//        $result = MemberToken::find()->where(['token'=>$token])->asArray()->one();
//        $userid = $result['member_id'];
//        $result1 = Member::find()->where(['id'=>$userid])->asArray()->one();
//        $mid = $result1['memberID'];
//        if($memberId == $mid){
//            return json_encode(array('code'=>200,'message'=>'right token','url'=>"http://p.365zhuawawa.com/?r=share/view&memberId=$memberId"));
//        }else{
//            return json_encode(array('code'=>403,'message'=>'error token'));
//        }
    }

    //不验证token分享
    public function actionShareTest(){
        $request = Yii::$app->request;

        $memberId = $request->post('memberId') ? $request->post('memberId') : $request->get('memberId');
        $token = $request->post('token') ? $request->post('token') : $request->get('token');

//        if(!$token) {
//            Yii::error("member Id : {$memberId}; token {$token} 没有找到授权");
//            return json_encode(array('code'=>403,'message'=>'授权已过期'));
//        }
//        $redis = Yii::$app->redis;
//        $type = $redis->TYPE($token);
//        if($type!='string') {
//            Yii::error("member Id : {$memberId}; token {$token} 授权非string");
//            return json_encode(array('code'=>403,'message'=>'授权已过期'));
//        }
//        $cacheUserId = $redis->GET($token);
//        if(!$cacheUserId || !($user=Member::find()->where(['id'=>(int)$cacheUserId])->asArray()->one())) {
//            Yii::error("member Id : {$memberId}; token {$token} 分享参数不正确， 找不到ID用户");
//            return json_encode(array('code'=>403,'message'=>'分享参数不正确'));
//        }
//        if($user['memberID']!==trim($memberId)) {
//            Yii::error("member Id : {$memberId}; token {$token} 授权memberId不一致");
//            return json_encode(array('code'=>403,'message'=>'授权已过期'));
//        }

        return json_encode(array('code'=>200,'message'=>'right token','url'=>"http://p.365zhuawawa.com/?r=share/view&memberId=$memberId"));
    }
    public function actionView(){
        $memberId = isset($_GET['memberId']) ? $_GET['memberId'] : '';
        $inviteInfo = InviteNum::find()->where(['invite_code'=>$memberId])->asArray()->one();
        if(empty($inviteInfo)){
            $myfunction = new MyFunction();
            $myfunction->addNum($memberId);
        }else{
            $num = $inviteInfo['invite_num'];
            $num = $num + 1;
            $myfunction = new MyFunction();
            $myfunction->updateNum($memberId,$num);
        }
        $deviceType = 'unknow';
        $downloadUrl = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365';
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
            $deviceType = 'ios';
            $downloadUrl = 'https://itunes.apple.com/cn/app/365%E6%8A%93%E5%A8%83%E5%A8%83/id1314921684?mt=8';
        }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
            $deviceType = 'android';
        }
        
        
        return $this->render('shareShow',[
            'inviteCode' => $memberId,
            'deviceType' => $deviceType,
            'downloadUrl' => $downloadUrl
        ]);
    }
    public function actionRedio(){
        return $this->render('redio');
    }
    public function actionDownload(){
        $deviceType = null;
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
            $deviceType = 'ios';
        }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
            $deviceType = 'android';
//            微信
        }else if(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $deviceType = 'weixin';
        } else{
            $deviceType = 'unknow';
        }
        return $this->renderPartial('download', [
            'deviceType'=>$deviceType
        ]);
//        return $this->renderPartial('download');
    }
    /***
    判断是否是苹果还是安卓
     */
    public function actionIsIosAndroid(){
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
            echo '2';
        }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
            echo '1';
        }else{
            echo '0';
        }
    }
//    分享
//    public function actionShareShow(){
//        $deviceType = null;
//        if (strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') === false) {
//            // 非微信浏览器禁止浏览
//            $deviceType = 'unknow';
//        } else{
//            // 微信浏览器，允许访问
//            $deviceType = 'weixin';
//        }
//        return $this->renderPartial('shareShow',['deviceType'=>$deviceType]);
//    }
//    qq分享
     public function actionQqShow(){
         return $this->renderPartial('qqShare');
     }

    public function actionShareNew(){
        $request = Yii::$app->request;

        $memberId = $request->post('memberId') ? $request->post('memberId') : $request->get('memberId');
        $token = $request->post('token') ? $request->post('token') : $request->get('token');

        if(!$token) {
            Yii::error("member Id : {$memberId}; token {$token} 没有找到授权");
            return json_encode(array('code'=>403,'message'=>'授权已过期'));
        }
        $redis = Yii::$app->redis;
        $type = $redis->TYPE($token);
        if($type!='string') {
            Yii::error("member Id : {$memberId}; token {$token} 授权非string");
            return json_encode(array('code'=>403,'message'=>'授权已过期'));
        }
        $cacheUserId = $redis->GET($token);
        if(!$cacheUserId || !($user=Member::find()->where(['id'=>(int)$cacheUserId])->asArray()->one())) {
            Yii::error("member Id : {$memberId}; token {$token} 分享参数不正确， 找不到ID用户");
            return json_encode(array('code'=>403,'message'=>'分享参数不正确'));
        }
        if($user['memberID']!==trim($memberId)) {
            Yii::error("member Id : {$memberId}; token {$token} 授权memberId不一致");
            return json_encode(array('code'=>403,'message'=>'授权已过期'));
        }

        return json_encode(array('code'=>200,'message'=>'right token','url'=>"http://p.365zhuawawa.com/?r=share/invite&memberId=$memberId"));
    }

    public function actionInvite(){
        $invite_code = isset($_GET['memberId']) ? $_GET['memberId'] : '';
        $invite_num = "select count(*) from share_invite WHERE invite_code = $invite_code";
        $invite_num = Yii::$app->db->createCommand($invite_num)->execute();
        return $this->renderPartial('invite',[
            'invite_num' => $invite_num,
            'invite_code' => $invite_code,
        ]);
    }

    public function actionInvite1(){
        $invite_code = '27873661';
        $invite_num = "select count(*) from share_invite WHERE invite_code = $invite_code";
        $invite_num = Yii::$app->db->createCommand($invite_num)->execute();
        return $this->renderPartial('invite',[
            'invite_num' => $invite_num,
            'invite_code' => $invite_code,
        ]);
    }

    public function actionTest(){
        return $this->render('share');
    }
}