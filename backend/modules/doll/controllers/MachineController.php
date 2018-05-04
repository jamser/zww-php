<?php

namespace backend\modules\doll\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * Machine controller for the `doll` module
 */
class MachineController extends Controller
{
    
      /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','reset','control','send-control-command', 'get-validate-key', 'machine-setting'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    
    /**
     * 娃娃房间重置
     */
    public function actionReset($id) {
        $sql = 'SELECT * FROM t_doll WHERE id='.$id;
        $row = Yii::$app->db->createCommand($sql)->queryOne();
        if(!$row || ($row['machine_status']==='维修中') || ($row['machine_status']==='未上线')) {
            echo "no in game";
            Yii::$app->end();
        }
        
        $redis = Yii::$app->redis;
        $id = (int)$id;
        $roomHostKey = "room_{$id}_host";
        $roomStatusKey = "room_{$id}_status";
        $redis = Yii::$app->redis;
        /* @var $redis \yii\redis\Connection */
        $redis->del($roomHostKey);
        $redis->del($roomStatusKey);
        
        $sql = "UPDATE t_doll SET `machine_status`='空闲中' WHERE id=".$id;
        Yii::$app->db->createCommand($sql)->execute();
    }
    
    /**
     * 控制机器
     */
    public function actionControl() {
        return $this->render('control');
    }

    /**
     * 发送控制指令
     */
    public function actionSendControlCommand($ip) {
        $host = 'dev.365zhawawa.com';
        $port = '2345';

        $get = $_GET;

        if(isset($get['content'])) {
                $message = $get['content'];
        } else {
                throw new \Exception('找不到参数 device  action');
        }

        error_reporting(E_ALL);

        echo "<h2>TCP/IP Connection</h2>\n";

        /* Get the port for the WWW service. */
        $service_port = 2345;//getservbyname('www', 'tcp');

        /* Get the IP address for the target host. */
        //$address = '106.15.156.126';////'101.132.166.121';//gethostbyname('dev.365zhawawa.com');
        $address = trim($ip);
        /* Create a TCP/IP socket. */
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
        } else {
            echo "OK.\n";
        }

        echo "Attempting to connect to '$address' on port '$service_port'...";
        $result = socket_connect($socket, $address, $service_port);
        if ($result === false) {
            echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
        } else {
            echo "OK.\n";
        }

        $in = "HEAD / HTTP/1.1\r\n";
        $in .= "Host: www.example.com\r\n";
        $in .= "Connection: Close\r\n\r\n";
        $out = '';

        echo "Sending HTTP HEAD request...";
        socket_write($socket, $message, strlen($message));
        echo "OK.\n";

        echo "Reading response:\n\n";
        $runCount = 0;
        if ($out = socket_read($socket, 1024)) {
            echo $out."<br/>\n\n";
           
        }
        echo date('H:i:s')."<br/>\n\n";
//        sleep(5);
//        
//        echo date('H:i:s')."\n\n";
//        
//        sleep(5);
//
//        echo date('H:i:s')."\n\n";
        echo "Closing socket...";
        socket_close($socket);
        echo "OK.\n\n";
    }
    
    public function actionGetValidateKey($device='') {
        if(!empty($device)) {
            $key = substr(md5("{$device}|machineMsg|".Yii::$app->params['machineValidateKey']), 0, 10);
            echo "{$device}|machineMsg|{$key}|";
            Yii::$app->end();
        }
        return $this->render('getValidateKey');
    }
    
    /**
     * 机器设置
     */
    public function actionMachineSetting() {
        $settingData = $_POST;
        
        if($settingData) {
            $strongVoltage = isset($settingData['strongVoltage']) ? (int)$settingData['strongVoltage'] : null;
            $weakOneVoltage = isset($settingData['weakOneVoltage']) ? (int)$settingData['weakOneVoltage'] : null;
            $weakTwoVoltage = isset($settingData['weakTwoVoltage']) ? (int)$settingData['weakTwoVoltage'] : null;
            $strongTime = isset($settingData['strongTime']) ? (int)$settingData['strongTime'] : null;
            $weakTime = isset($settingData['weakTime']) ? (int)$settingData['weakTime'] : null;
            $gameTime = isset($settingData['gameTime']) ? (int)$settingData['gameTime'] : 32;
            
        }
        return $this->render('machineSetting');
    }
}
