<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\models\PublishAssetForm;

include_once Yii::getAlias('@nextrip').'/aliyun-sdk/aliyun-php-sdk-core/Config.php';
use Iot\Request\V20170420 as Iot;

class DollMachineController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout = false;

    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => Gift::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
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