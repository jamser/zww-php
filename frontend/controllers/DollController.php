<?php
namespace frontend\controllers;

use frontend\models\Member;
use frontend\models\WechatUser;
use Yii;
use yii\web\Controller;
use Illuminate\Support\Facades\Input;
use common\helpers\MyFunction;
use frontend\models\Doll;
use frontend\models\MemberToken;

include_once Yii::getAlias('@nextrip').'/aliyun-sdk/aliyun-php-sdk-core/Config.php';
use Iot\Request\V20170420 as Iot;

class DollController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout = false;

    public function actionDoll()
    {
//         主房间页面
        Yii::$app->session->open();
        $mp = \nextrip\wechat\models\Mp::findAcModel('defaultMp');
        $wechatMpAuth = new \WechatSdk\mp\Auth($mp->app_id, $mp->app_secret);
        $code = $_GET['code'];
        $accessToken = $wechatMpAuth->getAccessPermission($code);
        $openId = $accessToken['openid'];
        $accesstoken = $accessToken['access_token'];
        //获取微信用户信息
        $userInfo = $wechatMpAuth->getUser($openId,$accesstoken);
        $userInfo = $this->object2array($userInfo);
        $token = $this->getToken();
        //存储用户信息
        $myfunction = new MyFunction();
        $myfunction->addUser($userInfo,$token);

        return $this->render('index',[
            'token' => $token,

        ]);
    }
    //对象转化为数组
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
    //  个人信息
    public function actionInfo()
    {
        $token = $_GET['token'];
        $tokenInfo = MemberToken::find()->where(['token'=>$token])->asArray()->one();
        if($tokenInfo){
            $userId = $tokenInfo['member_id'];
            $userInfo = Member::find()->where(['id'=>$userId])->asArray()->one();
            return $this->render('info',[
                'userInfo' => $userInfo,
            ]);
        }else{
            //token无效，退出重新登录
            exit;
        }
    }
//    单独房间
    public function actionRoom()
    {
        $id = $_GET['id'];
        $result = Doll::find()->where(['id'=>$id])->asArray()->one();
        return $this->render('room',[
            'machineData' => $result,
        ]);
    }
//    房间列表的接口
    public function actionGetDollRoom()
    {
//       获取所有房间
        $sql = "select * from t_doll";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
//       print_r($data);die;
        if ($data) {
            $result['resultData'] = $data;
            $result['success'] = true;
            $result['statusCode '] = 200;
            $result['message '] = '操作成功';
        } else {
            $result['resultData'] = '';
            $result['success'] = false;
            $result['statusCode '] = 403;
            $result['message '] = '操作失败';
        }
        return json_encode($result);
    }
    public function actionTest(){
        return $this->render('test');
    }
    //ajax方法测试
    public function actionAjax(){
        $name = $_POST['name'];
        $city = $_POST['city'];
        return json_encode(array('name'=>$name,'city'=>$city));
    }
//    常见问题
    public function actionQuestion(){
        return $this->render('question');
    }
    //    常见问题
    public function actionProblem(){
        return $this->render('problem');
    }
    
    /**
     * 注册阿里云IOT设备
     */
    public function actionRegisterIot($start, $num) {
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);
        
        $start = (int)$start;
        $max = $start+(int)$num;
        $deivceNames = [];
        while($start < $max) {
            $request = new Iot\RegistDeviceRequest();
            $request->setProductKey('gbHwjqaIekS');
            $deviceName = 'device_'. str_repeat('0', (3-strlen($start))).$start;
            $request->setDeviceName($deviceName);
            $response = $client->getAcsResponse($request);
            echo "{$deviceName}\t{$response->DeviceSecret}<br/>";
            $start++;
        }

        //"DeviceName.1=device_a&DeviceName.3=device_c&DeviceName.2=device_b&DeviceName.4=device01"
        //echo implode('&', $deivceNames);exit;
        //stdClass Object ( [DeviceId] => VSbqFE0ctCpDdLUb99t0 [DeviceName] => device_032 [DeviceSecret] => Q9HUYeDQRmSHLpkJj6ST4jwQnuKq9noN [RequestId] => BCCEC736-1BA2-41FD-BD0B-ABDE9939E6ED [Success] => 1 )
        
        //print_r("\r\n");
        //print_r($response);
    }
    
    /**
     * 显示设备名称 
     */
    public function actionShowDevice($start, $end) {
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);
        
        $request = new Iot\RegistDeviceRequest();
        $request->setProductKey('gbHwjqaIekS');
        
        $start = (int)$start;
        $end = (int)$end;
        
        $rows = [];
        while($start<=$end) {
            $request = new Iot\QueryDeviceByNameRequest();
            $request->setProductKey('gbHwjqaIekS');
            $request->setDeviceName('device_'. str_repeat('0', (3-strlen($start))).$start);
            $response = $client->getAcsResponse($request);
            //object(stdClass)#1279 (3) { ["RequestId"]=> string(36) "0720A8C2-C272-4F8D-824C-CE68C4529DFC" ["DeviceInfo"]=> object(stdClass)#1280 (5) { ["DeviceId"]=> string(20) "7GLT9sZqbwkYrC1C8AzV" ["DeviceName"]=> string(10) "device_001" ["ProductKey"]=> string(11) "gbHwjqaIekS" ["DeviceSecret"]=> string(32) "U3UtDOYt54sXbq631E9dhRdU8uEEVwVQ" ["GmtCreate"]=> string(29) "Thu, 09-Nov-2017 12:51:05 GMT" } ["Success"]=> bool(true) }
            $rows[] = $response;
            $start++;
        }
        
        foreach($rows as $row) {
            echo "{$row->DeviceInfo->DeviceName}<br/>";
        }
        echo "<br/>";
        echo "<br/>";
        foreach($rows as $row) {
            echo "{$row->DeviceInfo->DeviceSecret}<br/>";
        }
    }

}

