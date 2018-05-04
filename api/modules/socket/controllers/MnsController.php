<?php
namespace api\modules\socket\controllers;

use Yii;
use yii\web\Controller;
include_once '../modules/socket/sdks/mns_sdk/mns-autoloader.php';
use AliyunMNS\Client;
use AliyunMNS\Requests\SendMessageRequest;
use AliyunMNS\Requests\CreateQueueRequest;
use AliyunMNS\Exception\MnsException;

class MnsController extends Controller{
    public $enableCsrfValidation = false;
    //接收消息
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

    private  function object_array($array) {
        if(is_object($array)) {
            $array = (array)$array;
        } if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }

    //删除消息
    public function actionDeleteMessage($receiptHandle,$queueName){
//        $request = Yii::$app->request;
//        $receiptHandle = $request->get('messageId');
//        $queueName = $request->get('queueName');
        $accessId = Yii::$app->params['accessKeyId'];
        $accessKey = Yii::$app->params['accessSecret'];
        $endPoint = Yii::$app->params['endPoint'];
        $client = new Client($endPoint, $accessId, $accessKey);
        $queue = $client->getQueueRef($queueName);

        try
        {
            $res = $queue->deleteMessage($receiptHandle);
            print_r($res);
        }
        catch (MnsException $e)
        {
            echo "DeleteMessage Failed: " . $e . "\n";
            echo "MNSErrorCode: " . $e->getMnsErrorCode() . "\n";
            return;
        }
    }

    //发送消息
    public function actionSend($messageBody,$queueName){
//        $request = Yii::$app->request;
//        $messageBody = $request->get('control');
//        $queueName = $request->get('queueName');
        $accessId = Yii::$app->params['accessKeyId'];
        $accessKey = Yii::$app->params['accessSecret'];
        $endPoint = Yii::$app->params['endPoint'];
        $client = new Client($endPoint, $accessId, $accessKey);
        $queue = $client->getQueueRef($queueName);
        $request = new SendMessageRequest($messageBody);
        try
        {
            $res = $queue->sendMessage($request);
            print_r($res);
        }
        catch (MnsException $e)
        {
            echo "SendMessage Failed: " . $e . "\n";
            echo "MNSErrorCode: " . $e->getMnsErrorCode() . "\n";
            return;
        }
    }

    //创建队列
    public function actionCreateQueue($queueName){
//        $request = Yii::$app->request;
//        $queueName = $request->get('queueName');
        $accessId = Yii::$app->params['accessKeyId'];
        $accessKey = Yii::$app->params['accessSecret'];
        $endPoint = Yii::$app->params['endPoint'];
        $client = new Client($endPoint, $accessId, $accessKey);
        $request = new CreateQueueRequest($queueName);
        try
        {
            $res = $client->createQueue($request);
            print_r($res);
        }
        catch (MnsException $e)
        {
            echo "CreateQueueFailed: " . $e . "\n";
            echo "MNSErrorCode: " . $e->getMnsErrorCode() . "\n";
            return;
        }
    }

    //删除队列
    public function actionDeleteQueue($queueName){
//        $request = Yii::$app->request;
//        $queueName = $request->get('queueName');
        $accessId = Yii::$app->params['accessKeyId'];
        $accessKey = Yii::$app->params['accessSecret'];
        $endPoint = Yii::$app->params['endPoint'];
        $client = new Client($endPoint, $accessId, $accessKey);

        try {
            $client->deleteQueue($queueName);
            echo "DeleteQueue Succeed! \n";
        } catch (MnsException $e) {
            echo "DeleteQueue Failed: " . $e;
            return;
        }
    }
}