<?php
namespace api\modules\socket\controllers;

use Yii;
use yii\web\Controller;
include_once '../modules/socket/sdks/aliyun-sdk/aliyun-php-sdk-core/Config.php';
use \Iot\Request\V20170420 as Iot;

class AliController extends Controller{
    public $enableCsrfValidation = false;
    //发布消息到Topic
    public function actionPub(){
        $request = Yii::$app->request;
        $control = $request->post('control');
        $productKey = $request->post('productKey');
        $deviceName = $request->post('deviceName');
        $accessKeyId = Yii::$app->params['accessKeyId'];
        $accessSecret = Yii::$app->params['accessSecret'];
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\PubRequest();
        $request->setProductKey($productKey);
        $request->setTopicFullName("/$productKey/$deviceName/control"); //消息发送到的Topic全名.
        $request->setMessageContent(base64_encode($control)); // Base64 String.
        $request->setQos(0);
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

    //批量申请设备
    public function actionApplyDeviceWithNames(){
        $accessKeyId = Yii::$app->params['accessKeyId'];
        $accessSecret = Yii::$app->params['accessSecret'];
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\ApplyDeviceWithNamesRequest();
        $request->setProductKey(1122226666);
        $request->setDeviceName("DeviceName.1=device_a&DeviceName.3=device_c&DeviceName.2=device_b&DeviceName.4=device01");
        $response = $client->getAcsResponse($request);
        print_r("\r\n");
        print_r(json_encode($response));
    }

    //查询设备影子
    public function actionBatchGetDeviceState($productKey){
        $accessKeyId = Yii::$app->params['accessKeyId'];
        $accessSecret = Yii::$app->params['accessSecret'];
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\BatchGetDeviceStateRequest();
        $request->setProductKey($productKey);
        //$request->setDeviceName("");
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

//创建产品
    public function actionCreateProduct(){
        $accessKeyId = Yii::$app->params['accessKeyId'];
        $accessSecret = Yii::$app->params['accessSecret'];
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\CreateProductRequest();
        $request->setCatId(10000);
        $request->setDesc("Create Product test");
        $request->setName("TestProduct");
        $response = $client->getAcsResponse($request);
        print_r("\r\n");
        print_r(json_encode($response));
    }

//查询设备影子
    public function actionGetDeviceShadow($productKey,$deviceName){
        $accessKeyId = Yii::$app->params['accessKeyId'];
        $accessSecret = Yii::$app->params['accessSecret'];
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\GetDeviceShadowRequest();
        $request->setProductKey($productKey);
        $request->setDeviceName($deviceName);
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

//发布广播消息
    public function actionPubBroadcast($productKey,$control){
        $accessKeyId = Yii::$app->params['accessKeyId'];
        $accessSecret = Yii::$app->params['accessSecret'];
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\PubBroadcastRequest();
        $request->setProductKey($productKey);
        $request->setTopicFullName("/broadcast/$productKey/yxh");
        $request->setMessageContent(base64_encode($control));
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

//查询批量设备的申请状态
    public function actionQueryApplyStatus(){
        $accessKeyId = Yii::$app->params['accessKeyId'];
        $accessSecret = Yii::$app->params['accessSecret'];
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\QueryApplyStatusRequest();
        $request->setApplyId("");
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

//查询产品的设备列表
    public function actionQueryDevice($productKey){
        $accessKeyId = Yii::$app->params['accessKeyId'];
        $accessSecret = Yii::$app->params['accessSecret'];
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\QueryDeviceRequest();
        $request->setProductKey($productKey);
        $request->setCurrentPage(1);
        $request->setPageSize(10);
        $response = $client->getAcsResponse($request);
        print_r("\r\n");
        print_r(json_encode($response));
    }

//根据设备名称查询设备信息
    public function actionQueryDeviceByName($productKey,$deviceName){
        $accessKeyId = Yii::$app->params['accessKeyId'];
        $accessSecret = Yii::$app->params['accessSecret'];
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\QueryDeviceByNameRequest();
        $request->setProductKey($productKey);
        $request->setDeviceName($deviceName);
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

    public function actionQueryPageByApplyId(){
        $accessKeyId = Yii::$app->params['accessKeyId'];
        $accessSecret = Yii::$app->params['accessSecret'];
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\QueryPageByApplyIdRequest();
        $request->setApplyId("");
        $request->setCurrentPage("");
        $request->setPageSize("");
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

//设备注册
    public function actionRegistDevice($productKey){
        $accessKeyId = Yii::$app->params['accessKeyId'];
        $accessSecret = Yii::$app->params['accessSecret'];
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\RegistDeviceRequest();
        $request->setProductKey($productKey);
        $response = $client->getAcsResponse($request);
        print_r("\r\n");
        print_r(json_encode($response));
    }

//发消息给设备并同步返回响应
    public function actionRRpc($productKey,$deviceName){
        $accessKeyId = Yii::$app->params['accessKeyId'];
        $accessSecret = Yii::$app->params['accessSecret'];
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\RRpcRequest();
        $request->setProductKey($productKey);
        $request->setDeviceName($deviceName);
        $request->setRequestBase64Byte(base64_encode("{'control':'coin'}"));
        $request->setTimeout(5000);
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

//更新设备影子
    public function actionUpdateDeviceShadow($productKey,$deviceName,$shadowMessage){
        $accessKeyId = Yii::$app->params['accessKeyId'];
        $accessSecret = Yii::$app->params['accessSecret'];
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\UpdateDeviceShadowRequest();
        $request->setProductKey($productKey);
        $request->setDeviceName($deviceName);
        $request->setShadowMessage($shadowMessage);
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

//修改产品信息
    public function actionUpdateProduct($productKey,$deviceNewname){
        $accessKeyId = Yii::$app->params['accessKeyId'];
        $accessSecret = Yii::$app->params['accessSecret'];
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\UpdateProductRequest();
        $request->setProductKey($productKey);
        $request->setProductName($deviceNewname);
        $response = $client->getAcsResponse($request);
        print_r("\r\n");
        print_r(json_encode($response));
    }



















}