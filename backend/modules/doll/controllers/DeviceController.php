<?php
namespace backend\modules\doll\controllers;

require_once '../aliyun-util/ClientUtil.php';
require_once '../aliyun-util/ServiceUtil.php';
include_once '../aliyun-iot/aliyun-php-sdk-core/Config.php';

use common\services\doll\RtmpService;
use frontend\models\Doll;
use Iot\Request\V20170420 as Iot;
use yii\filters\AccessControl;
use Yii;
use yii\web\Controller;

class DeviceController extends Controller{
    public $enableCsrfValidation = false;

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['device','device-state','device-states','device-statess','small-device-state','complaint','device-state-m','device-states-m','device-statess-m','small-device-state-m'

                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['device','device-state','device-states','device-statess','small-device-state','complaint','device-state-m','device-states-m','device-statess-m','small-device-state-m'],
                        'allow' => true,
                        'roles' => ['超级管理员'],
                    ],
                ],
            ],
        ];
    }
//    private $client;
//
//    public function init()
//    {
//        $this->client = \ClientUtil::createClient();
//    }

    public function actionDeviceState(){
        $request = new Iot\BatchGetDeviceStateRequest();
        $request->setProductKey('gbHwjqaIekS');
        $machineInfo = Doll::find()->where(['or',"machine_status='空闲中'","machine_status='游戏中'"])->asArray()->all();
        $service = new RtmpService();
        $machineNames = [];
        foreach($machineInfo as $k=>$v){
            $machineName = $v['machine_url'];
            if($machineName == 12){
                continue;
            }else{
                array_push($machineNames,$machineName);
            }
        }
        $machineNames = array_slice($machineNames,0,50);
        $request->setDeviceNames($machineNames);
        $client = \ClientUtil::createClient();
        $response = $client->getAcsResponse($request);
        $deviceStatusList = $response->DeviceStatusList;
        $deviceStatus = $deviceStatusList->DeviceStatus;
        $status = [];
        foreach($deviceStatus as $v)
        {
            $status[$v->DeviceName] = $v->Status;
        }
        foreach($status as $k=>$v){
            $checkSql = "select * from machine_status WHERE machine_name='$k'";
            $result = Yii::$app->db->createCommand($checkSql)->execute();
            if($result == 0){
                $rtmp_status = $service->rtmpStatus($k);
                if($rtmp_status == 1){
                    $rtmp_status = "开启";
                }elseif($rtmp_status == 3){
                    $rtmp_status = "关闭";
                }elseif($rtmp_status == 0){
                    $rtmp_status = "断流";
                }else{
                    $rtmp_status = '未找到流';
                }
                $sql = "insert into machine_status(machine_name,machine_state,rtmp_state) VALUES ('$k','$v','$rtmp_status')";
                Yii::$app->db->createCommand($sql)->execute();
            }else{
                $rtmp_status = $service->rtmpStatus($k);
                if($rtmp_status == 1){
                    $rtmp_status = "开启";
                }elseif($rtmp_status == 3){
                    $rtmp_status = "关闭";
                }elseif($rtmp_status == 0){
                    $rtmp_status = "断流";
                }else{
                    $rtmp_status = '未找到流';
                }
                $updateSql = "update machine_status set machine_state = '$v',rtmp_state = '$rtmp_status' WHERE machine_name='$k'";
                Yii::$app->db->createCommand($updateSql)->execute();
            }
        }
    }

    public function actionDeviceStates(){
        $service = new RtmpService();
        $request = new Iot\BatchGetDeviceStateRequest();
        $request->setProductKey('gbHwjqaIekS');
        $machineInfo = Doll::find()->asArray()->all();
        $machineNames = [];
        foreach($machineInfo as $k=>$v){
            $machineName = $v['machine_url'];
            if($machineName == 12){
                continue;
            }else{
                array_push($machineNames,$machineName);
            }
        }
        $machineNames = array_slice($machineNames,50,50);
        $request->setDeviceNames($machineNames);
        $client = \ClientUtil::createClient();
        $response = $client->getAcsResponse($request);
        $deviceStatusList = $response->DeviceStatusList;
        $deviceStatus = $deviceStatusList->DeviceStatus;
        $status = [];
        foreach($deviceStatus as $v)
        {
            $status[$v->DeviceName] = $v->Status;
        }
        foreach($status as $k=>$v){
            $checkSql = "select * from machine_status WHERE machine_name='$k'";
            $result = Yii::$app->db->createCommand($checkSql)->execute();
            if($result == 0){
                $rtmp_status = $service->rtmpStatus($k);
                if($rtmp_status == 1){
                    $rtmp_status = "开启";
                }elseif($rtmp_status == 3){
                    $rtmp_status = "关闭";
                }elseif($rtmp_status == 0){
                    $rtmp_status = "断流";
                }else{
                    $rtmp_status = '未找到流';
                }
                $sql = "insert into machine_status(machine_name,machine_state,rtmp_state) VALUES ('$k','$v','$rtmp_status')";
                Yii::$app->db->createCommand($sql)->execute();
            }else{
                $rtmp_status = $service->rtmpStatus($k);
                if($rtmp_status == 1){
                    $rtmp_status = "开启";
                }elseif($rtmp_status == 3){
                    $rtmp_status = "关闭";
                }elseif($rtmp_status == 0){
                    $rtmp_status = "断流";
                }else{
                    $rtmp_status = '未找到流';
                }
                $updateSql = "update machine_status set machine_state = '$v',rtmp_state = '$rtmp_status' WHERE machine_name='$k'";
                Yii::$app->db->createCommand($updateSql)->execute();
            }
        }
    }

    public function actionDeviceStatess(){
        $service = new RtmpService();
        $request = new Iot\BatchGetDeviceStateRequest();
        $request->setProductKey('gbHwjqaIekS');
        $machineInfo = Doll::find()->asArray()->all();
        $machineNames = [];
        foreach($machineInfo as $k=>$v){
            $machineName = $v['machine_url'];
            if($machineName == 12){
                continue;
            }else{
                array_push($machineNames,$machineName);
            }
        }
        $count = count($machineNames);
        $num = $count - 100;
        $machineNames = array_slice($machineNames,100,$num);
        $request->setDeviceNames($machineNames);
        $client = \ClientUtil::createClient();
        $response = $client->getAcsResponse($request);
        $deviceStatusList = $response->DeviceStatusList;
        $deviceStatus = $deviceStatusList->DeviceStatus;
        $status = [];
        foreach($deviceStatus as $v)
        {
            $status[$v->DeviceName] = $v->Status;
        }
        foreach($status as $k=>$v){
            $checkSql = "select * from machine_status WHERE machine_name='$k'";
            $result = Yii::$app->db->createCommand($checkSql)->execute();
            if($result == 0){
                $rtmp_status = $service->rtmpStatus($k);
                if($rtmp_status == 1){
                    $rtmp_status = "开启";
                }elseif($rtmp_status == 3){
                    $rtmp_status = "关闭";
                }elseif($rtmp_status == 0){
                    $rtmp_status = "断流";
                }else{
                    $rtmp_status = '未找到流';
                }
                $sql = "insert into machine_status(machine_name,machine_state,rtmp_state) VALUES ('$k','$v','$rtmp_status')";
                Yii::$app->db->createCommand($sql)->execute();
            }else{
                $rtmp_status = $service->rtmpStatus($k);
                if($rtmp_status == 1){
                    $rtmp_status = "开启";
                }elseif($rtmp_status == 3){
                    $rtmp_status = "关闭";
                }elseif($rtmp_status == 0){
                    $rtmp_status = "断流";
                }else{
                    $rtmp_status = '未找到流';
                }
                $updateSql = "update machine_status set machine_state = '$v',rtmp_state = '$rtmp_status' WHERE machine_name='$k'";
                Yii::$app->db->createCommand($updateSql)->execute();
            }
        }
    }

    //小机器在线状态
    public function actionSmallDeviceState(){
        $redis = Yii::$app->redis;
        $service = new RtmpService();
        $machineInfo = Doll::find()->asArray()->all();
        foreach($machineInfo as $k=>$v){
//            $machine_url = $v['machine_url'];
            $machine_url = 'devicea_2001';
            $checkSql = "select * from machine_status WHERE machine_name='$machine_url'";
            $result = Yii::$app->db->createCommand($checkSql)->execute();
            if($result == 0){
                $machine_name = "machine_".$machine_url."_heartbeat";
                $machine_state = $redis->get($machine_name);
                if(empty($machine_state)){
                    $updateSql = "update machine_status set machine_state = '$machine_state',rtmp_state = '断流' WHERE machine_name='$machine_url'";
                    Yii::$app->db->createCommand($updateSql)->execute();
                    continue;
                }else {
                    if ($machine_state == 'idle' || $machine_state == 'running') {
                        $machine_state = 'ONLINE';
                    } elseif ($machine_state == 'maintain' || $machine_state == 'fault') {
                        $machine_state = 'OFFLINE';
                    }
                    $rtmp_status = $service->rtmpStatus($machine_url);
                    if ($rtmp_status == 1) {
                        $rtmp_status = "开启";
                    } elseif ($rtmp_status == 3) {
                        $rtmp_status = "关闭";
                    } elseif ($rtmp_status == 0) {
                        $rtmp_status = "断流";
                    } else {
                        $rtmp_status = '未找到流';
                    }
                    $sql = "insert into machine_status(machine_name,machine_state,rtmp_state) VALUES ('$machine_url','$machine_state','$rtmp_status')";
                    Yii::$app->db->createCommand($sql)->execute();
                }
            }else{
                $machine_name = "machine_".$machine_url."_heartbeat";
                $machine_state = $redis->get($machine_name);
                if(empty($machine_state)){
                    $updateSql = "update machine_status set machine_state = '$machine_state',rtmp_state = '断流' WHERE machine_name='$machine_url'";
                    Yii::$app->db->createCommand($updateSql)->execute();
                    continue;
                }else {
                    if ($machine_state == 'idle' || $machine_state == 'running') {
                        $machine_state = 'ONLINE';
                    } elseif ($machine_state == 'maintain' || $machine_state == 'fault') {
                        $machine_state = 'OFFLINE';
                    }
                    $rtmp_status = $service->rtmpStatus($machine_url);
                    if ($rtmp_status == 1) {
                        $rtmp_status = "开启";
                    } elseif ($rtmp_status == 3) {
                        $rtmp_status = "关闭";
                    } elseif ($rtmp_status == 0) {
                        $rtmp_status = "断流";
                    } else {
                        $rtmp_status = '未找到流';
                    }
                    $updateSql = "update machine_status set machine_state = '$machine_state',rtmp_state = '$rtmp_status' WHERE machine_name='$machine_url'";
                    Yii::$app->db->createCommand($updateSql)->execute();
                }
            }
        }
    }

    //马甲环境机器监控
    public function actionDeviceStateM(){
        $request = new Iot\BatchGetDeviceStateRequest();
        $request->setProductKey('gbHwjqaIekS');
        $machineInfo = Doll::find()->where(['or',"machine_status='空闲中'","machine_status='游戏中'"])->asArray()->all();
        $service = new RtmpService();
        $machineNames = [];
        foreach($machineInfo as $k=>$v){
            $machineName = $v['machine_url'];
            if($machineName == 12){
                continue;
            }else{
                array_push($machineNames,$machineName);
            }
        }
        $machineNames = array_slice($machineNames,0,50);
        $request->setDeviceNames($machineNames);
        $client = \ClientUtil::createClient();
        $response = $client->getAcsResponse($request);
        $deviceStatusList = $response->DeviceStatusList;
        $deviceStatus = $deviceStatusList->DeviceStatus;
        $status = [];
        foreach($deviceStatus as $v)
        {
            $status[$v->DeviceName] = $v->Status;
        }
        foreach($status as $k=>$v){
            $checkSql = "select * from machine_status WHERE machine_name='$k'";
            $result = Yii::$app->dbMajia->createCommand($checkSql)->execute();
            if($result == 0){
                $rtmp_status = $service->rtmpStatus($k);
                if($rtmp_status == 1){
                    $rtmp_status = "开启";
                }elseif($rtmp_status == 3){
                    $rtmp_status = "关闭";
                }elseif($rtmp_status == 0){
                    $rtmp_status = "断流";
                }else{
                    $rtmp_status = '未找到流';
                }
                $sql = "insert into machine_status(machine_name,machine_state,rtmp_state) VALUES ('$k','$v','$rtmp_status')";
                Yii::$app->dbMajia->createCommand($sql)->execute();
            }else{
                $rtmp_status = $service->rtmpStatus($k);
                if($rtmp_status == 1){
                    $rtmp_status = "开启";
                }elseif($rtmp_status == 3){
                    $rtmp_status = "关闭";
                }elseif($rtmp_status == 0){
                    $rtmp_status = "断流";
                }else{
                    $rtmp_status = '未找到流';
                }
                $updateSql = "update machine_status set machine_state = '$v',rtmp_state = '$rtmp_status' WHERE machine_name='$k'";
                Yii::$app->dbMajia->createCommand($updateSql)->execute();
            }
        }
    }

    public function actionDeviceStatesM(){
        $service = new RtmpService();
        $request = new Iot\BatchGetDeviceStateRequest();
        $request->setProductKey('gbHwjqaIekS');
        $machineInfo = Doll::find()->asArray()->all();
        $machineNames = [];
        foreach($machineInfo as $k=>$v){
            $machineName = $v['machine_url'];
            if($machineName == 12){
                continue;
            }else{
                array_push($machineNames,$machineName);
            }
        }
        $machineNames = array_slice($machineNames,50,50);
        $request->setDeviceNames($machineNames);
        $client = \ClientUtil::createClient();
        $response = $client->getAcsResponse($request);
        $deviceStatusList = $response->DeviceStatusList;
        $deviceStatus = $deviceStatusList->DeviceStatus;
        $status = [];
        foreach($deviceStatus as $v)
        {
            $status[$v->DeviceName] = $v->Status;
        }
        foreach($status as $k=>$v){
            $checkSql = "select * from machine_status WHERE machine_name='$k'";
            $result = Yii::$app->dbMajia->createCommand($checkSql)->execute();
            if($result == 0){
                $rtmp_status = $service->rtmpStatus($k);
                if($rtmp_status == 1){
                    $rtmp_status = "开启";
                }elseif($rtmp_status == 3){
                    $rtmp_status = "关闭";
                }elseif($rtmp_status == 0){
                    $rtmp_status = "断流";
                }else{
                    $rtmp_status = '未找到流';
                }
                $sql = "insert into machine_status(machine_name,machine_state,rtmp_state) VALUES ('$k','$v','$rtmp_status')";
                Yii::$app->dbMajia->createCommand($sql)->execute();
            }else{
                $rtmp_status = $service->rtmpStatus($k);
                if($rtmp_status == 1){
                    $rtmp_status = "开启";
                }elseif($rtmp_status == 3){
                    $rtmp_status = "关闭";
                }elseif($rtmp_status == 0){
                    $rtmp_status = "断流";
                }else{
                    $rtmp_status = '未找到流';
                }
                $updateSql = "update machine_status set machine_state = '$v',rtmp_state = '$rtmp_status' WHERE machine_name='$k'";
                Yii::$app->dbMajia->createCommand($updateSql)->execute();
            }
        }
    }

    public function actionDeviceStatessM(){
        $service = new RtmpService();
        $request = new Iot\BatchGetDeviceStateRequest();
        $request->setProductKey('gbHwjqaIekS');
        $machineInfo = Doll::find()->asArray()->all();
        $machineNames = [];
        foreach($machineInfo as $k=>$v){
            $machineName = $v['machine_url'];
            if($machineName == 12){
                continue;
            }else{
                array_push($machineNames,$machineName);
            }
        }
        $count = count($machineNames);
        $num = $count - 100;
        $machineNames = array_slice($machineNames,100,$num);
        $request->setDeviceNames($machineNames);
        $client = \ClientUtil::createClient();
        $response = $client->getAcsResponse($request);
        $deviceStatusList = $response->DeviceStatusList;
        $deviceStatus = $deviceStatusList->DeviceStatus;
        $status = [];
        foreach($deviceStatus as $v)
        {
            $status[$v->DeviceName] = $v->Status;
        }
        foreach($status as $k=>$v){
            $checkSql = "select * from machine_status WHERE machine_name='$k'";
            $result = Yii::$app->dbMajia->createCommand($checkSql)->execute();
            if($result == 0){
                $rtmp_status = $service->rtmpStatus($k);
                if($rtmp_status == 1){
                    $rtmp_status = "开启";
                }elseif($rtmp_status == 3){
                    $rtmp_status = "关闭";
                }elseif($rtmp_status == 0){
                    $rtmp_status = "断流";
                }else{
                    $rtmp_status = '未找到流';
                }
                $sql = "insert into machine_status(machine_name,machine_state,rtmp_state) VALUES ('$k','$v','$rtmp_status')";
                Yii::$app->dbMajia->createCommand($sql)->execute();
            }else{
                $rtmp_status = $service->rtmpStatus($k);
                if($rtmp_status == 1){
                    $rtmp_status = "开启";
                }elseif($rtmp_status == 3){
                    $rtmp_status = "关闭";
                }elseif($rtmp_status == 0){
                    $rtmp_status = "断流";
                }else{
                    $rtmp_status = '未找到流';
                }
                $updateSql = "update machine_status set machine_state = '$v',rtmp_state = '$rtmp_status' WHERE machine_name='$k'";
                Yii::$app->dbMajia->createCommand($updateSql)->execute();
            }
        }
    }

    //小机器在线状态
    public function actionSmallDeviceStateM(){
        $redis = Yii::$app->redis;
        $service = new RtmpService();
        $machineInfo = Doll::find()->asArray()->all();
        foreach($machineInfo as $k=>$v){
//            $machine_url = $v['machine_url'];
            $machine_url = 'devicea_2001';
            $checkSql = "select * from machine_status WHERE machine_name='$machine_url'";
            $result = Yii::$app->dbMajia->createCommand($checkSql)->execute();
            if($result == 0){
                $machine_name = "machine_".$machine_url."_heartbeat";
                $machine_state = $redis->get($machine_name);
                if(empty($machine_state)){
                    $updateSql = "update machine_status set machine_state = '$machine_state',rtmp_state = '断流' WHERE machine_name='$machine_url'";
                    Yii::$app->dbMajia->createCommand($updateSql)->execute();
                    continue;
                }else {
                    if ($machine_state == 'idle' || $machine_state == 'running') {
                        $machine_state = 'ONLINE';
                    } elseif ($machine_state == 'maintain' || $machine_state == 'fault') {
                        $machine_state = 'OFFLINE';
                    }
                    $rtmp_status = $service->rtmpStatus($machine_url);
                    if ($rtmp_status == 1) {
                        $rtmp_status = "开启";
                    } elseif ($rtmp_status == 3) {
                        $rtmp_status = "关闭";
                    } elseif ($rtmp_status == 0) {
                        $rtmp_status = "断流";
                    } else {
                        $rtmp_status = '未找到流';
                    }
                    $sql = "insert into machine_status(machine_name,machine_state,rtmp_state) VALUES ('$machine_url','$machine_state','$rtmp_status')";
                    Yii::$app->dbMajia->createCommand($sql)->execute();
                }
            }else{
                $machine_name = "machine_".$machine_url."_heartbeat";
                $machine_state = $redis->get($machine_name);
                if(empty($machine_state)){
                    $updateSql = "update machine_status set machine_state = '$machine_state',rtmp_state = '断流' WHERE machine_name='$machine_url'";
                    Yii::$app->dbMajia->createCommand($updateSql)->execute();
                    continue;
                }else {
                    if ($machine_state == 'idle' || $machine_state == 'running') {
                        $machine_state = 'ONLINE';
                    } elseif ($machine_state == 'maintain' || $machine_state == 'fault') {
                        $machine_state = 'OFFLINE';
                    }
                    $rtmp_status = $service->rtmpStatus($machine_url);
                    if ($rtmp_status == 1) {
                        $rtmp_status = "开启";
                    } elseif ($rtmp_status == 3) {
                        $rtmp_status = "关闭";
                    } elseif ($rtmp_status == 0) {
                        $rtmp_status = "断流";
                    } else {
                        $rtmp_status = '未找到流';
                    }
                    $updateSql = "update machine_status set machine_state = '$machine_state',rtmp_state = '$rtmp_status' WHERE machine_name='$machine_url'";
                    Yii::$app->dbMajia->createCommand($updateSql)->execute();
                }
            }
        }
    }

    public function actionDevice(){
        $db = Yii::$app->db;
        $dbPhp = Yii::$app->db_php;
//        $status = Yii::$app->getRequest()->get('status',null);
        $machine_status = Yii::$app->getRequest()->get('machine_state',null);
        $name = Yii::$app->getRequest()->get('machine_name',null);
        $rtmp_status = Yii::$app->getRequest()->get('rtmp_status',null);
        $conditions = $params = [];
//        if($status) {
//            $conditionss = 'machine_state '.$status;
//        }else{
//            $conditionss = 'machine_state DESC';
//        }
        if($name) {
            $conditions[] = "machine_name like '%".trim($name).'%'."'";
        }
        if($machine_status && $machine_status != '状态') {
            $conditions[] = 'machine_state=:machine_state';
            $params[':machine_state'] = trim($machine_status);
        }
        if($rtmp_status && $rtmp_status!='全部') {
            if($rtmp_status == '直播中'){
                $conditions[] = '`rtmp_state`=:rtmp_state';
                $params[':rtmp_state'] = '开启';
            }else{
                $conditions[] = '`rtmp_state`=:rtmp_state';
                $params[':rtmp_state'] = '断流';
            }
        }
        $sql = 'SELECT COUNT(*) FROM machine_status'
            .($conditions ? ' WHERE '.implode(' AND ', $conditions) : '');
        $count = $db->createCommand($sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);

        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $sql = 'SELECT * FROM machine_status'
            .($conditions ? ' WHERE '.implode(' AND ', $conditions) : '')
            . "  limit $offset,$size";
        $rows = $db->createCommand($sql, $params)->queryAll();

        return $this->render('index', [
            'models' => $rows,
            'pages' => $pages,
        ]);
    }

}