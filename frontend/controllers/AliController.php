<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
include_once '../aliyun-sdk/aliyun-php-sdk-core/Config.php';
use \Iot\Request\V20170420 as Iot;
include_once '../mns_sdk/mns-autoloader.php';
use AliyunMNS\Client;
use AliyunMNS\Requests\SendMessageRequest;
use AliyunMNS\Requests\CreateQueueRequest;
use AliyunMNS\Exception\MnsException;

class AliController extends Controller{
    //get方式请求数据
    //发布消息到Topic
    public function actionPubCoin(){
//        $data数据格式为{'control':'claw'}
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\PubRequest();
        $request->setProductKey("gbHwjqaIekS");
        $request->setTopicFullName("/gbHwjqaIekS/device_001/control"); //消息发送到的Topic全名.
        $request->setMessageContent(base64_encode('{"control":"coin"}')); // Base64 String.
        $request->setQos(0);
        $response = $client->getAcsResponse($request);
        return json_encode($response);
    }

    public function actionPubLeft(){
//        $data数据格式为{'control':'claw'}
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\PubRequest();
        $request->setProductKey("gbHwjqaIekS");
        $request->setTopicFullName("/gbHwjqaIekS/device_001/control"); //消息发送到的Topic全名.
        $request->setMessageContent(base64_encode('{"control":"left"}')); // Base64 String.
        $request->setQos(0);
        $response = $client->getAcsResponse($request);
        return json_encode($response);
    }

    public function actionPubRight(){
//        $data数据格式为{'control':'claw'}
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\PubRequest();
        $request->setProductKey("gbHwjqaIekS");
        $request->setTopicFullName("/gbHwjqaIekS/device_001/control"); //消息发送到的Topic全名.
        $request->setMessageContent(base64_encode('{"control":"right"}')); // Base64 String.
        $request->setQos(0);
        $response = $client->getAcsResponse($request);
        return json_encode($response);
    }

    public function actionPubBackward(){
//        $data数据格式为{'control':'claw'}
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\PubRequest();
        $request->setProductKey("gbHwjqaIekS");
        $request->setTopicFullName("/gbHwjqaIekS/device_001/control"); //消息发送到的Topic全名.
        $request->setMessageContent(base64_encode('{"control":"backward"}')); // Base64 String.
        $request->setQos(0);
        $response = $client->getAcsResponse($request);
        return json_encode($response);
    }

    public function actionPubClaw(){
//        $data数据格式为{'control':'claw'}
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\PubRequest();
        $request->setProductKey("gbHwjqaIekS");
        $request->setTopicFullName("/gbHwjqaIekS/device_001/control"); //消息发送到的Topic全名.
        $request->setMessageContent(base64_encode('{"control":"claw"}')); // Base64 String.
        $request->setQos(0);
        $response = $client->getAcsResponse($request);
        return json_encode($response);
    }

    public function actionPubForward(){
//        $data数据格式为{'control':'claw'}
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\PubRequest();
        $request->setProductKey("gbHwjqaIekS");
        $request->setTopicFullName("/gbHwjqaIekS/device_001/control"); //消息发送到的Topic全名.
        $request->setMessageContent(base64_encode('{"control":"forward"}')); // Base64 String.
        $request->setQos(0);
        $response = $client->getAcsResponse($request);
        return json_encode($response);
    }

    public function actionPubQuery(){
//        $data数据格式为{'control':'claw'}
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\PubRequest();
        $request->setProductKey("gbHwjqaIekS");
        $request->setTopicFullName("/gbHwjqaIekS/device_001/control"); //消息发送到的Topic全名.
        $request->setMessageContent(base64_encode('{"control":"query"}')); // Base64 String.
        $request->setQos(0);
        $response = $client->getAcsResponse($request);
        return json_encode($response);
    }

    public function actionPubStop(){
//        $data数据格式为{'control':'claw'}
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\PubRequest();
        $request->setProductKey("gbHwjqaIekS");
        $request->setTopicFullName("/gbHwjqaIekS/device_001/control"); //消息发送到的Topic全名.
        $request->setMessageContent(base64_encode('{"control":"stop"}')); // Base64 String.
        $request->setQos(0);
        $response = $client->getAcsResponse($request);
        return json_encode($response);
    }


    public function actionApplyDeviceWithNames(){
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
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
    public function actionBatchGetDeviceState(){
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\BatchGetDeviceStateRequest();
        $request->setProductKey("gbHwjqaIekS");
        //$request->setDeviceName("");
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

//创建产品
    public function actionCreateProduct(){
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
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
    public function actionGetDeviceShadow(){
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\GetDeviceShadowRequest();
        $request->setProductKey("gbHwjqaIekS");
        $request->setDeviceName("device_001");
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

//发布广播消息
    public function actionPubBroadcast(){
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\PubBroadcastRequest();
        $request->setProductKey("gbHwjqaIekS");
        $request->setTopicFullName("/broadcast/gbHwjqaIekS/yxh");
        $request->setMessageContent(base64_encode("{'control':'left'}"));
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

//查询批量设备的申请状态
    public function actionQueryApplyStatus(){
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\QueryApplyStatusRequest();
        $request->setApplyId("");
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

//查询产品的设备列表
    public function actionQueryDevice(){
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\QueryDeviceRequest();
        $request->setProductKey("gbHwjqaIekS");
        $request->setCurrentPage(1);
        $request->setPageSize(10);
        $response = $client->getAcsResponse($request);
        print_r("\r\n");
        print_r(json_encode($response));
    }

//根据设备名称查询设备信息
    public function actionQueryDeviceByName(){
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\QueryDeviceByNameRequest();
        $request->setProductKey("gbHwjqaIekS");
        $request->setDeviceName("device_001");
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

    public function actionQueryPageByApplyId(){
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
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
    public function actionRegistDevice(){
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\RegistDeviceRequest();
        $request->setProductKey("gbHwjqaIekS");
        $response = $client->getAcsResponse($request);
        print_r("\r\n");
        print_r(json_encode($response));
    }

//发消息给设备并同步返回响应
    public function actionRRpc(){
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\RRpcRequest();
        $request->setProductKey("gbHwjqaIekS");
        $request->setDeviceName("device_001");
        $request->setRequestBase64Byte(base64_encode("{'control':'coin'}"));
        $request->setTimeout(5000);
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

//更新设备影子
    public function actionUpdateDeviceShadow(){
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\UpdateDeviceShadowRequest();
        $request->setProductKey("gbHwjqaIekS");
        $request->setDeviceName("device_001");
        $request->setShadowMessage("");
        $response = $client->getAcsResponse($request);
        print_r(json_encode($response));
    }

//修改产品信息
    public function actionUpdateProduct(){
        $accessKeyId = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = \DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\UpdateProductRequest();
        $request->setProductKey("gbHwjqaIekS");
        $request->setProductName("TestProductNew");
        $response = $client->getAcsResponse($request);
        print_r("\r\n");
        print_r(json_encode($response));
    }

    //队列信息处理
    public function actionReceive($queueName="aliyun-iot-gbHwjqaIekS"){
//        $request = Yii::$app->request;
//        $queueName = $request->post('queueName');
        set_time_limit(1800);
        $accessId = Yii::$app->params['accessKeyId'];
        $accessKey = Yii::$app->params['accessSecret'];
        $endPoint = Yii::$app->params['endPoint'];
        $client = new Client($endPoint, $accessId, $accessKey);
        $queue = $client->getQueueRef($queueName);
        while(1) {
            try {
                $res = $queue->receiveMessage(30);
                $body = $res->getMessageBody();
                $body = json_decode($body);
                $messageType = $body['messageType'];
                if($messageType == 'status'){//status为数据是套件的通知数据，upload为设备发布到Topic中的原始数据
                    $payload = $body['payload'];
                    $paybody = base64_decode($payload);//套件返回的处理信息
                }
                Yii::$app->db->createCommand("insert into t_mns_data(`data`) VALUES(:data)",[':data'=>$body])->execute();
                $receiptHandle = $res->getReceiptHandle();
                $queue->deleteMessage($receiptHandle);
            } catch (MnsException $e) {
                echo "ReceiveMessage Failed: " . $e . "\n";
                echo "MNSErrorCode: " . $e->getMnsErrorCode() . "\n";
                return;
            }
        }
    }




















}