<?php

namespace backend\modules\doll\controllers;

use backend\modules\doll\models\Inform;
use common\models\DollAddress;
use common\services\doll\MessagesService;
use frontend\models\Doll;
use Yii;
use yii\web\Controller;
ini_set("display_errors", "on");

require_once "../aliyun-email/Smtp.class.php";
include_once '../aliyun-email/aliyun-php-sdk-core/Config.php';

use Dm\Request\V20151123 as Dm;
use common\services\doll\StatisticService;
use common\enums\StatisticTypeEnum;
use common\models\doll\Machine;

class AlarmController extends Controller{
    public $enableCsrfValidation = false;

    public function actionAlarm(){
        $service = new StatisticService();
        $startTime = time() - 1800;
        $endTime = time();
        $service->machineRate($startTime, $endTime, 1, StatisticTypeEnum::TYPE_HALF_HOURS);
        $db = Yii::$app->db_php;
        $sql = "select * from doll_machine_statistic WHERE start_time>='$startTime' AND start_time<='$endTime'";
        $machineData = $db->createCommand($sql)->queryAll();
        $machineIds = [];
        $machineId = '';
        foreach($machineData as $k=>$v){
            $rate = $v['play_count']>0 ? round(($v['grab_count']/$v['play_count'])*100,2):0;
            if($rate ==0 || $rate >5){
                array_push($machineIds,$v);
                $machineId .= "{$v['machine_device_name']} ({$v['machine_doll_name']});";
            }
        }

        $time = date('Y-m-d H:i:s',time());
        if($machineIds){
            $this->sendEmail();//发送邮件

            //发送微信提醒
            $data = array(
                'first' => array(
                    'value' => '机器概率异常报警',
                    'color' => '#FF0000'
                ),
                'keyword1' => array(
                    'value' => $time,
                    'color' => '#FF0000'
                ),
                'keyword2' => array(
                    'value' => '机器抓中概率',
                    'color' => '#FF0000'
                ),
                'remark' => array(
                    'value' => '机器名：'.$machineId.'请尽快检查机器'.'---测试信息',
                    'color' => '#FF0000'
                )
            );
            $service = new MessagesService();
            $access_token = $service->getAccessToken();
            $message_url = 'http://p-admin.365zhuawawa.com/doll/alarm/alarm-data';
            $userInfo = Inform::find()->asArray()->all();
            $users = [];
            foreach($userInfo as $k=>$v){
                $memberID = $v['memberID'];
                $memberID=sprintf("%08d", $memberID);
                $sql1 = "select weixin_id from t_member WHERE memberID=$memberID";
                $op_id = $db->createCommand($sql1)->queryAll();
                if(empty($op_id)){
                    continue;
                }else{
                    $op_id = $op_id[0]['weixin_id'];
                    $sql2 = "select union_id from member_wx WHERE open_id='$op_id'";
                    $un_id = $db->createCommand($sql2)->queryAll();
                    if(empty($un_id)){
                        continue;
                    }else{
                        $un_id = $un_id[0]['union_id'];
                        $sql3 = "select openid from member_add WHERE unionid='$un_id'";
                        $open_id = $db->createCommand($sql3)->queryAll();
                        if(empty($open_id)){
                            continue;
                        }else{
                            $open_id = $open_id[0]['openid'];
                            array_push($users,$open_id);
                        }
                    }
                }
            }
            foreach($users as $k=>$v){
                $touser = $v;
                $service->sendMessage($access_token,$message_url,$data,$touser);
            }
        }
    }

    public function actionAlarmData()
    {
        $db = Yii::$app->db_php;
        $db_p = Yii::$app->db;
        $startTime = time() - 1800;
        $endTime = time();
        $sql = "select * from doll_machine_statistic WHERE start_time>='$startTime' AND start_time<='$endTime'";
        $machineData = $db->createCommand($sql)->queryAll();
        $sqls = "select * from t_doll";
        $dollData = $db_p->createCommand($sqls)->queryAll();
        $machineIds = [];
        foreach ($machineData as $k => $v) {
            $rate = $v['play_count'] > 0 ? round(($v['grab_count'] / $v['play_count']) * 100, 2) : 0;
            if ($rate == 0 || $rate > 5) {
                array_push($machineIds, $v);
            }
        }

        $status = $addresses = [];
        foreach($machineData as $k=>$v){
            $doll_id = $v['machine_id'];
            $Data = Doll::find()->where(['id'=>$doll_id])->asArray()->one();
            $state = $Data['machine_status'];
            $status[$doll_id] = $state;
        }

        foreach($dollData as $k=>$v){
            $doll_id = $v['id'];
            $address_id = $v['doll_address_id'];
            $addressInfo = DollAddress::find()->where(['id'=>$address_id])->asArray()->one();
            $address = $addressInfo['county'];
            $addresses[$doll_id] = $address;
        }

        return $this->renderPartial('alarm', [
            'models' => $machineIds,
            'status' => $status,
            'address' => $addresses,
        ]);
    }

    //阿里SDK发送邮件
    public function sendEmail(){
        $startTime = date('Y-m-d H:i:s',time()-1800);
        $endTime = date('H:i:s',time());
        $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", "LTAIiRG3VWVjAIpU", "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX");
        $client = new \DefaultAcsClient($iClientProfile);
        $request = new Dm\SingleSendMailRequest();//发送单条邮件
        //$request = new Dm\BatchSendMailRequest();//批量发送邮件
        $request->setAccountName("notify@365zhuawawa.com");
        $request->setFromAlias("365抓娃娃");
        $request->setAddressType(1);
        $request->setTagName("控制台创建的标签");
        $request->setReplyToAddress("true");
        $request->setToAddress("vink@wanyiguo.com");
        $request->setSubject("机器概率异常报警(".$startTime."-".$endTime.")");
        $request->setHtmlBody($this->actionAlarmData());
        try {
            $response = $client->getAcsResponse($request);
            print_r($response);
        }
        catch (\ClientException  $e) {
            print_r($e->getErrorCode());
            print_r($e->getErrorMessage());
        }
        catch (\ServerException  $e) {
            print_r($e->getErrorCode());
            print_r($e->getErrorMessage());
        }
    }

    //机器下线报警
    public function actionMachineAlarm(){
        $redis = Yii::$app->redis;
        $time = '2018-04-03 13:25:00';
        $time=strtotime($time);
        $date = date('Y-m-d H:i:s',$time-360);
        $s_time = date('Y-m-d H:i:s',$time-600);
        $e_time = date('Y-m-d H:i:s',$time);
        $time = date('Y-m-d H:i:s', $time);
        $sql = "SELECT COUNT(*) as num,h.doll_id, d.machine_type,d.machine_url,d.name FROM t_doll_catch_history h LEFT JOIN t_doll d ON h.doll_id=d.id  WHERE h.catch_date>='".$date."' AND h.catch_status='抓取成功'"
            . " GROUP BY h.member_id";
        $db = Yii::$app->db;
        $rows = $db->createCommand($sql)->queryAll();
        foreach($rows as $row) {
            $dollId = $row['doll_id'];
            $catchNum = $row['num'];

            if ($dollId && ($catchNum >1) && ($row['machine_type'] == 0)) {
                $sql = "select * from t_doll_monitor WHERE dollId=$dollId AND created_date>='$s_time' AND created_date<='$e_time'";
                $result = $db->createCommand($sql)->execute();
                if ($result == 0) {
                    $this->send($dollId,$time);
                    $machine_status = 'room_'.$dollId.'_status';
                    $redis->set($machine_status,'维修中');
                    $reason = '连续抓中异常下线';
                    $this->insert($dollId,$reason);
                }
            }else if ($dollId && ($catchNum >5) && ($row['machine_type'] == 2)) {
                $sql = "select * from t_doll_monitor WHERE dollId=$dollId AND created_date>='$s_time' AND created_date<='$e_time'";
                $result = $db->createCommand($sql)->execute();
                if ($result == 0) {
                    $this->send($dollId,$time);
                    $machine_status = 'room_'.$dollId.'_status';
                    $redis->set($machine_status,'维修中');
                    $reason = '连续抓中异常下线';
                    $this->insert($dollId,$reason);
                }
            }
//            else if ($dollId && ($catchNum >= 6) && ($row['machine_type'] == 2)) {
//                $sql = "select * from t_doll_monitor WHERE dollId=$dollId AND created_date>='$s_time' AND created_date<='$e_time'";
//                $result = $db->createCommand($sql)->execute();
//                if ($result == 0) {
//                    $this->send($dollId,$time);
//                }
//            }
        }
    }

    public function send($dollId,$time){
        $db = Yii::$app->db;
        $sql = "select * from t_doll WHERE id=$dollId";
        $machineStatus = $db->createCommand($sql)->queryAll();
        $machine_url = $machineStatus[0]['machine_url'];
        $name = $machineStatus[0]['name'];
        $this->sendMachineEmail($dollId);//发送邮件
        //发送微信提醒
        $data = array(
            'first' => array(
                'value' => '机器下线报警',
                'color' => '#FF0000'
            ),
            'keyword1' => array(
                'value' => $time,
                'color' => '#FF0000'
            ),
            'keyword2' => array(
                'value' => '短时间内连续抓中机器下线',
                'color' => '#FF0000'
            ),
            'remark' => array(
                'value' => '机器名：' . $machine_url . ' ' . ($name) . '请尽快检查机器' . '---测试信息',
                'color' => '#FF0000'
            )
        );
        $service = new MessagesService();
        $access_token = $service->getAccessToken();
        $message_url = "http://p-admin.365zhuawawa.com/doll/alarm/alarm-machine1?dollId=$dollId";
        $userInfo = Inform::find()->asArray()->all();
        $users = ['opTs00ytey1f_xygGGxqvUoR9CSk'];
//        foreach ($userInfo as $k => $v) {
//            $memberID = $v['memberID'];
//            $memberID = sprintf("%08d", $memberID);
//            $sql1 = "select weixin_id from t_member WHERE memberID=$memberID";
//            $op_id = $db->createCommand($sql1)->queryAll();
//            $op_id = $op_id[0]['weixin_id'];
//            $sql2 = "select union_id from member_wx WHERE open_id='$op_id'";
//            $un_id = $db->createCommand($sql2)->queryAll();
//            $un_id = $un_id[0]['union_id'];
//            $sql3 = "select openid from member_add WHERE unionid='$un_id'";
//            $open_id = $db->createCommand($sql3)->queryAll();
//            $open_id = $open_id[0]['openid'];
//            array_push($users, $open_id);
//        }
        foreach ($users as $k => $v) {
            $touser = $v;
            $service->sendMessage($access_token, $message_url, $data, $touser);
        }
    }

    public function actionAlarmMachine($dollId)
    {
        $db = Yii::$app->db;
        $sql = "select * from t_doll WHERE id=$dollId";
        $machineStatus = $db->createCommand($sql)->queryAll();

        return $this->renderPartial('machine', [
            'models' => $machineStatus,
        ]);
    }

    public function actionAlarmMachine1()
    {
        $request = Yii::$app->request;

        $dollId = $request->post('dollId') ? $request->post('dollId') : $request->get('dollId');
        $db = Yii::$app->db;
        $sql = "select * from t_doll WHERE id=$dollId";
        $machineStatus = $db->createCommand($sql)->queryAll();

        return $this->renderPartial('machine', [
            'models' => $machineStatus,
        ]);
    }

    public function sendMachineEmail($dollId){
        $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", "LTAIiRG3VWVjAIpU", "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX");
        $client = new \DefaultAcsClient($iClientProfile);
        $request = new Dm\SingleSendMailRequest();//发送单条邮件
        //$request = new Dm\BatchSendMailRequest();//批量发送邮件
        $request->setAccountName("notify@365zhuawawa.com");
        $request->setFromAlias("365抓娃娃");
        $request->setAddressType(1);
        $request->setTagName("控制台创建的标签");
        $request->setReplyToAddress("true");
        $request->setToAddress("yuxiuhong@wanyiguo.com");
        $request->setSubject("机器下线报警");
        $request->setHtmlBody($this->actionAlarmMachine($dollId));
        try {
            $response = $client->getAcsResponse($request);
            print_r($response);
        }
        catch (\ClientException  $e) {
            print_r($e->getErrorCode());
            print_r($e->getErrorMessage());
        }
        catch (\ServerException  $e) {
            print_r($e->getErrorCode());
            print_r($e->getErrorMessage());
        }
    }

    public function actionTest(){
        return $this->render('test');
    }

    public function actionIndex(){
        return $this->render('index');
    }

    //机器下线报警
    public function actionMachine(){
        $db = Yii::$app->db;
        $sql = "select * from machine_status WHERE machine_state='OFFLINE' OR machine_state='UNACTIVE'";
        $machineData = $db->createCommand($sql)->queryAll();
        $machineNames = [];
        foreach($machineData as $k=>$v){
            array_push($machineNames,$v['machine_name']);
        }
        if($machineNames){
            $this->sendMachine();
        }
    }

    public function actionMachineData(){
        $db = Yii::$app->db;
        $sql = "select * from machine_status WHERE machine_state='OFFLINE' OR machine_state='UNACTIVE'";
        $machineData = $db->createCommand($sql)->queryAll();

        return $this->render('online',[
           'models'=>$machineData,
        ]);
    }

    public function sendMachine(){
        $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", "LTAIiRG3VWVjAIpU", "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX");
        $client = new \DefaultAcsClient($iClientProfile);
        $request = new Dm\SingleSendMailRequest();//发送单条邮件
        //$request = new Dm\BatchSendMailRequest();//批量发送邮件
        $request->setAccountName("notify@365zhuawawa.com");
        $request->setFromAlias("365抓娃娃");
        $request->setAddressType(1);
        $request->setTagName("控制台创建的标签");
        $request->setReplyToAddress("true");
        $request->setToAddress("yuxiuhong@wanyiguo.com");
        $request->setSubject("机器离线报警");
        $request->setHtmlBody($this->actionMachineData());
        try {
            $response = $client->getAcsResponse($request);
            print_r($response);
        }
        catch (\ClientException  $e) {
            print_r($e->getErrorCode());
            print_r($e->getErrorMessage());
        }
        catch (\ServerException  $e) {
            print_r($e->getErrorCode());
            print_r($e->getErrorMessage());
        }
    }

    public function actionMessage(){
        $db = Yii::$app->db;
        $redis = Yii::$app->redis;
        $sql = "select d.*,di.name from machine_status d LEFT JOIN t_doll di ON d.machine_name=di.machine_url
              WHERE di.machine_status IN ('空闲中','游戏中') and delete_status=1 AND d.machine_state='OFFLINE'";
        $result = $db->createCommand($sql)->queryAll();

        $machineId='';
        foreach($result as $k=>$v) {
            $id = $v['machine_name'];
            $name = $v['name'];
            $r_id = $redis->get($id);
            if (empty($r_id)) {
                $machineId .= "{$id} ({$name})；";
                $redis->set($id, $name);
                $redis->expire($id, '3600');
            }

            $time = date('Y-m-d H:i:s', time());
            $s_time = date('Y-m-d 00:00:00', time());
            $e_time = date('Y-m-d 06:00:00', time());

            $data = array(
                'first' => array(
                    'value' => '机器离线报警',
                    'color' => '#FF0000'
                ),
                'keyword1' => array(
                    'value' => $time,
                    'color' => '#FF0000'
                ),
                'keyword2' => array(
                    'value' => '机器状态',
                    'color' => '#FF0000'
                ),
                'remark' => array(
                    'value' => '机器名：' . $machineId . '请尽快检查这些机器',
                    'color' => '#FF0000'
                )
            );
            if (empty($machineId)) {
                echo '没有机器离线';
            } else {
                $userInfo = Inform::find()->asArray()->all();
                $users = ['0' => 'opTs00ytey1f_xygGGxqvUoR9CSk'];
//            foreach($userInfo as $k=>$v){
//                $memberID = $v['memberID'];
//                $memberID=sprintf("%08d", $memberID);
//                $sql1 = "select weixin_id from t_member WHERE memberID=$memberID";
//                $op_id = $db->createCommand($sql1)->queryAll();
//                $op_id = $op_id[0]['weixin_id'];
//                $sql2 = "select union_id from member_wx WHERE open_id='$op_id'";
//                $un_id = $db->createCommand($sql2)->queryAll();
//                $un_id = $un_id[0]['union_id'];
//                $sql3 = "select openid from member_add WHERE unionid='$un_id'";
//                $open_id = $db->createCommand($sql3)->queryAll();
//                $open_id = $open_id[0]['openid'];
//                array_push($users,$open_id);
//            }
                $service = new MessagesService();
                $access_token = $service->getAccessToken();
                $message_url = 'http://p-admin.365zhuawawa.com/doll/alarm/machine-status';
                if ($time >= $s_time && $time <= $e_time) {
                    echo "改时间段内不发送报警";
                } else {
                    foreach ($users as $k => $v) {
                        $touser = $v;
                        $service->sendMessage($access_token, $message_url, $data, $touser);
                    }
                }
            }
        }
    }

    public function actionMessageR(){
        $db = Yii::$app->db;
        $sql = "select * from machine_status d LEFT JOIN t_doll di ON d.machine_name=di.machine_url
              WHERE di.machine_status IN ('空闲中','游戏中') and delete_status=1 AND d.machine_state='OFFLINE'";
        $result = $db->createCommand($sql)->queryAll();
        $redis = Yii::$app->redis;
        $machineId='';
        foreach($result as $k=>$v){
            $machine_name = $v['machine_name'];
            $machineName = 'machine_'.$machine_name;
            $name = $v['name'];
            $state = $v['machine_state'];
            $r_id = $redis->get($machineName);
            if(empty($r_id)){
                $machineId .= "{$machine_name} ({$name})；";
                $time = date('Y-m-d H:i:s',time());
                $s_time = date('Y-m-d 00:00:00',time());
                $e_time = date('Y-m-d 06:00:00',time());


                $data = array(
                    'first' => array(
                        'value' => '机器离线报警-R',
                        'color' => '#FF0000'
                    ),
                    'keyword1' => array(
                        'value' => $time,
                        'color' => '#FF0000'
                    ),
                    'keyword2' => array(
                        'value' => '机器状态',
                        'color' => '#FF0000'
                    ),
                    'remark' => array(
                        'value' => '机器名：'.$machineId.'请尽快检查这些机器',
                        'color' => '#FF0000'
                    )
                );
                if($state == 'OFFLINE'){
                    $redis->lpush($machine_name,$state);
                    $redis->expire($machine_name,60*4);
                    $len = $redis->llen($machine_name);
                    if($len>3){
                        $redis->rpop($machine_name);
                    }elseif($len == 3){
                        $userInfo = Inform::find()->asArray()->all();
                        $users = [];
                        foreach($userInfo as $k=>$v){
                            $memberID = $v['memberID'];
                            $memberID=sprintf("%08d", $memberID);
                            $sql1 = "select weixin_id from t_member WHERE memberID=$memberID";
                            $op_id = $db->createCommand($sql1)->queryAll();
                            if(empty($op_id)){
                                continue;
                            }else{
                                $op_id = $op_id[0]['weixin_id'];
                                $sql2 = "select union_id from member_wx WHERE open_id='$op_id'";
                                $un_id = $db->createCommand($sql2)->queryAll();
                                if(empty($un_id)){
                                    continue;
                                }else{
                                    $un_id = $un_id[0]['union_id'];
                                    $sql3 = "select openid from member_add WHERE unionid='$un_id'";
                                    $open_id = $db->createCommand($sql3)->queryAll();
                                    if(empty($open_id)){
                                        continue;
                                    }else{
                                        $open_id = $open_id[0]['openid'];
                                        array_push($users,$open_id);
                                    }
                                }
                            }
                        }
                        $service = new MessagesService();
                        $access_token = $service->getAccessToken();
                        $message_url = 'http://p-admin.365zhuawawa.com/doll/alarm/machine-status';
                        if($time >= $s_time && $time <= $e_time){
                            echo "该时间段内不发送报警";
                        }else{
                            foreach($users as $k=>$v){
                                $touser = $v;
                                $service->sendMessage($access_token,$message_url,$data,$touser);
                            }
                            $redis->set($machineName,$name);
                            $redis->expire($machineName,'3600');
                        }
                    }else{
                        echo '没有机器离线';
                    }
                }
            }
        }
    }

    public function actionMachineStatus(){
        $db = Yii::$app->db;
        $sql = "select * from machine_status d LEFT JOIN t_doll di ON d.machine_name=di.machine_url
              WHERE di.machine_status IN ('空闲中','游戏中') and delete_status=1 AND d.machine_state='OFFLINE'";
        $result = $db->createCommand($sql)->queryAll();
        return $this->renderPartial('status',[
            'models'=>$result,
        ]);
    }

    //推流报警
    public function actionRtmp(){
        $db = Yii::$app->db;
        $sql = "select a.*,b.machine_url,b.name,b.id machine_id,b.machine_code,b.machine_status from machine_status a LEFT JOIN t_doll b ON a.machine_name=b.machine_url"
            . " WHERE b.machine_status  in('空闲中','游戏中') and delete_status=1 AND a.rtmp_state='断流'";
//        $sql = "select * from doll_rtmp WHERE machine_status ='空闲中' AND rtmp_status='断流'";
        $result = $db->createCommand($sql)->queryAll();
        $machineId='';
        foreach($result as $k=>$v){
            $id = $v['machine_url'];
            $name = $v['name'];
            $machineId .= "{$id} ({$name})；";
        }

        $time = date('Y-m-d H:i:s',time());

        $data = array(
            'first' => array(
                'value' => '机器视频无输入流报警',
                'color' => '#FF0000'
            ),
            'keyword1' => array(
                'value' => $time,
                'color' => '#FF0000'
            ),
            'keyword2' => array(
                'value' => '机器视频',
                'color' => '#FF0000'
            ),
            'remark' => array(
                'value' => '机器名：'.$machineId.'请尽快检查这些机器',
                'color' => '#FF0000'
            )
        );
        if(empty($result)){
            echo '没有机器视频错误';
        }else{
            $userInfo = Inform::find()->asArray()->all();
            $users = [];
            foreach($userInfo as $k=>$v){
                $memberID = $v['memberID'];
                $memberID=sprintf("%08d", $memberID);
                $sql1 = "select weixin_id from t_member WHERE memberID=$memberID";
                $op_id = $db->createCommand($sql1)->queryAll();
                if(empty($op_id)){
                    continue;
                }else{
                    $op_id = $op_id[0]['weixin_id'];
                    $sql2 = "select union_id from member_wx WHERE open_id='$op_id'";
                    $un_id = $db->createCommand($sql2)->queryAll();
                    if(empty($un_id)){
                        continue;
                    }else{
                        $un_id = $un_id[0]['union_id'];
                        $sql3 = "select openid from member_add WHERE unionid='$un_id'";
                        $open_id = $db->createCommand($sql3)->queryAll();
                        if(empty($open_id)){
                            continue;
                        }else{
                            $open_id = $open_id[0]['openid'];
                            array_push($users,$open_id);
                        }
                    }
                }
            }
            $service = new MessagesService();
            $access_token = $service->getAccessToken();
            $message_url = 'http://p-admin.365zhuawawa.com/doll/alarm/rtmp-data';
            foreach($users as $k=>$v){
                $touser = $v;
                $service->sendMessage($access_token,$message_url,$data,$touser);
            }
        }
    }

    public function actionRtmpData(){
        $db = Yii::$app->db;
        $sql = "select a.*,b.machine_url,b.name,b.id machine_id,b.machine_code,b.machine_status from machine_status a LEFT JOIN t_doll b ON a.machine_name=b.machine_url"
            . " WHERE b.machine_status  in('空闲中','游戏中') and delete_status=1 AND a.rtmp_state='断流'";
        $result = $db->createCommand($sql)->queryAll();
        return $this->renderPartial('rtmp',[
            'data'=>$result,
        ]);
    }

    //机器申诉次数过多下线和报警
    public function actionComplaint(){
        $db = Yii::$app->db;
        $time = date('Y-m-d 00:00:00',time());
        $sql = "SELECT doll_id,COUNT(*) num FROM member_complaint WHERE creat_date>='$time' AND check_state NOT IN (0,-1) GROUP BY doll_id ORDER BY COUNT(*) DESC";
        $rows = $db->createCommand($sql)->queryAll();
        foreach($rows as $k=>$v){
            $num = $v['num'];
            $doll_id = $v['doll_id'];
            if($num >= 20){
                //报警
                $sql = "UPDATE t_doll SET machine_status='维修中' WHERE id=$doll_id";
                $db->createCommand($sql)->execute();
                $machineData = Doll::find()->where(['id'=>$doll_id])->asArray()->one();
                $machine_name = $machineData['machine_url'];
                $name = $machineData['name'];
                $time = date('Y-m-d H:i:s',time());
                $data = array(
                    'first' => array(
                        'value' => '机器今天被申诉超过20次',
                        'color' => '#FF0000'
                    ),
                    'keyword1' => array(
                        'value' => $time,
                        'color' => '#FF0000'
                    ),
                    'keyword2' => array(
                        'value' => '机器故障',
                        'color' => '#FF0000'
                    ),
                    'remark' => array(
                        'value' => '机器名：' . $machine_name . ' ' . ($name) .'请尽快检查这些机器',
                        'color' => '#FF0000'
                    )
                );
                $userInfo = Inform::find()->asArray()->all();
                $users = [];
                foreach($userInfo as $k=>$v){
                    $memberID = $v['memberID'];
                    $memberID=sprintf("%08d", $memberID);
                    $sql1 = "select weixin_id from t_member WHERE memberID=$memberID";
                    $op_id = $db->createCommand($sql1)->queryAll();
                    if(empty($op_id)){
                        continue;
                    }else{
                        $op_id = $op_id[0]['weixin_id'];
                        $sql2 = "select union_id from member_wx WHERE open_id='$op_id'";
                        $un_id = $db->createCommand($sql2)->queryAll();
                        if(empty($un_id)){
                            continue;
                        }else{
                            $un_id = $un_id[0]['union_id'];
                            $sql3 = "select openid from member_add WHERE unionid='$un_id'";
                            $open_id = $db->createCommand($sql3)->queryAll();
                            if(empty($open_id)){
                                continue;
                            }else{
                                $open_id = $open_id[0]['openid'];
                                array_push($users,$open_id);
                            }
                        }
                    }
                }
                $service = new MessagesService();
                $access_token = $service->getAccessToken();
                $message_url = 'http://p-admin.365zhuawawa.com/doll/alarm/complaint-data ';
                foreach ($users as $k => $v) {
                    $touser = $v;
                    $service->sendMessage($access_token, $message_url, $data, $touser);
                }
            }else{
                echo '没有机器故障';
            }
        }
    }

    public function actionComplaintData(){
        $request = Yii::$app->request;
        $doll_id = $request->post('doll_id') ? $request->post('doll_id') : $request->get('doll_id');
        $time = date('Y-m-d 00:00:00',time());
        $db = Yii::$app->db;
        $sql = "select * from member_complaint WHERE doll_id=$doll_id AND creat_date>='$time'";
        $result = $db->createCommand($sql)->queryAll();
        return $this->renderPartial('complaint',[
            'models'=>$result,
        ]);
    }

    //普通房间机器概率报警
    public function actionRateP(){
        $time = date('Y-m-d H:i:s',time());
        $ss_time = date('Y-m-d 00:00:00',time());
        $ee_time = date('Y-m-d 06:00:00',time());
        $startTime = date('Y-m-d 00:00:00',time());
        $endTime = date('Y-m-d 23:59:59',time());
        $offlineTime = date('Y-m-d H:i:s',time());
        $s_time = strtotime($startTime);
        $e_time = strtotime($endTime);
        $of_time = strtotime($offlineTime);
        $out_time = 86400 - ($of_time-$s_time);
        $db = Yii::$app->db;
        $redis = Yii::$app->redis;
        $sql = "select d.* from doll_machine_statistic d LEFT JOIN t_doll di ON d.machine_id=di.id WHERE d.start_time>='$s_time' AND d.start_time<='$e_time' AND di.machine_type=0 AND d.play_count>=10 AND d.machine_id=234";
        $machineData = $db->createCommand($sql)->queryAll();
        $machineId = $machineIds = '';
        foreach($machineData as $k=>$v){
            $machine_id = $v['machine_id'];
            $machine_name = 'rate'.$v['machine_device_name'];
            $machine_offline = 'offline'.$v['machine_device_name'];
            $r_id = $redis->get($machine_name);
            $o_id = $redis->get($machine_offline);
            if($r_id){
                if($o_id){
                    $sql1 = "select d.grab_count,d.play_count from doll_machine_statistic d LEFT JOIN t_doll di ON d.machine_id=di.id WHERE d.start_time>='$o_id' AND d.start_time<='$e_time' AND di.machine_type=0 AND d.play_count>=10 AND d.machine_id=$machine_id";
                    $machineData1 = $db->createCommand($sql1)->queryAll();
                    $rate = $machineData1[0]['play_count']>0 ? round(($machineData1[0]['grab_count']/$machineData1[0]['play_count'])*100,2):0;
                }else{
                    $rate = $v['play_count']>0 ? round(($v['grab_count']/$v['play_count'])*100,2):0;
                }
                if($rate >7){
                    $sql2 = "UPDATE t_doll SET machine_status='维修中' WHERE id=$machine_id";
                    $db->createCommand($sql2)->execute();
                    $machineIds .= $machine_id .',';
                    $machineId .= "{$v['machine_device_name']} ({$v['machine_doll_name']});";
                    $redis->set($machine_name,$machine_id);
                    $redis->expire($machine_name,'21600');
                    $redis->set($machine_offline,$offlineTime);
                    $redis->expire($machine_offline,$out_time);
                }
            }
        }

        if($machineIds){
            $this->sendEmail();//发送邮件

            //发送微信提醒
            $data = array(
                'first' => array(
                    'value' => '普通房机器概率异常下线报警',
                    'color' => '#FF0000'
                ),
                'keyword1' => array(
                    'value' => $time,
                    'color' => '#FF0000'
                ),
                'keyword2' => array(
                    'value' => '机器抓中概率',
                    'color' => '#FF0000'
                ),
                'remark' => array(
                    'value' => '机器名：'.$machineId.'请尽快调整机器概率重新上线',
                    'color' => '#FF0000'
                )
            );
            $service = new MessagesService();
            $access_token = $service->getAccessToken();
            $message_url = "http://p-admin.365zhuawawa.com/doll/alarm/data-p?machineId=$machineIds";
            $userInfo = Inform::find()->asArray()->all();
            $users = ['opTs00ytey1f_xygGGxqvUoR9CSk'];
//            foreach($userInfo as $k=>$v){
//                $memberID = $v['memberID'];
//                $memberID=sprintf("%08d", $memberID);
//                $sql1 = "select weixin_id from t_member WHERE memberID=$memberID";
//                $op_id = $db->createCommand($sql1)->queryAll();
//                if(empty($op_id)){
//                    continue;
//                }else{
//                    $op_id = $op_id[0]['weixin_id'];
//                    $sql2 = "select union_id from member_wx WHERE open_id='$op_id'";
//                    $un_id = $db->createCommand($sql2)->queryAll();
//                    if(empty($un_id)){
//                        continue;
//                    }else{
//                        $un_id = $un_id[0]['union_id'];
//                        $sql3 = "select openid from member_add WHERE unionid='$un_id'";
//                        $open_id = $db->createCommand($sql3)->queryAll();
//                        if(empty($open_id)){
//                            continue;
//                        }else{
//                            $open_id = $open_id[0]['openid'];
//                            array_push($users,$open_id);
//                        }
//                    }
//                }
//            }
            if($time >= $ss_time && $time <= $ee_time){
                echo "该时间段内不发送报警";
            }else{
                foreach($users as $k=>$v){
                    $touser = $v;
                    $service->sendMessage($access_token,$message_url,$data,$touser);
                }
            }
        }
    }

    //钻石房，练习房 房间机器概率报警
    public function actionRateZ(){
        $time = date('Y-m-d H:i:s',time());
        $ss_time = date('Y-m-d 00:00:00',time());
        $ee_time = date('Y-m-d 06:00:00',time());
        $startTime = date('Y-m-d 00:00:00',time());
        $endTime = date('Y-m-d 23:59:59',time());
        $offlineTime = date('Y-m-d H:i:s',time());
        $s_time = strtotime($startTime);
        $e_time = strtotime($endTime);
        $of_time = strtotime($offlineTime);
        $out_time = 86400 - ($of_time-$s_time);
        $db = Yii::$app->db;
        $redis = Yii::$app->redis;
        $sql = "select d.* from doll_machine_statistic d LEFT JOIN t_doll di ON d.machine_id=di.id WHERE d.start_time>='$s_time' AND d.start_time<='$e_time' AND di.machine_type IN (1,2) AND play_count>=10";
        $machineData = $db->createCommand($sql)->queryAll();
        $machineId = $machineIds = '';
        foreach($machineData as $k=>$v){
            $machine_id = $v['machine_id'];
            $machine_name = 'rate'.$v['machine_device_name'];
            $machine_offline = 'offline'.$v['machine_device_name'];
            $r_id = $redis->get($machine_name);
            $o_id = $redis->get($machine_offline);
            if(empty($r_id)){
                if($o_id){
                    $sql1 = "select d.grab_count,d.play_count from doll_machine_statistic d LEFT JOIN t_doll di ON d.machine_id=di.id WHERE d.start_time>='$o_id' AND d.start_time<='$e_time' AND di.machine_type=0 AND d.play_count>=10 AND d.machine_id=$machine_id";
                    $machineData1 = $db->createCommand($sql1)->queryAll();
                    $rate = $machineData1[0]['play_count']>0 ? round(($machineData1[0]['grab_count']/$machineData1[0]['play_count'])*100,2):0;
                }else{
                    $rate = $v['play_count']>0 ? round(($v['grab_count']/$v['play_count'])*100,2):0;
                }
                if($rate >20){
                    $sql2 = "UPDATE t_doll SET machine_status='维修中' WHERE id=$machine_id";
                    $db->createCommand($sql2)->execute();
                    $machineIds .= $machine_id .',';
                    $machineId .= "{$v['machine_device_name']} ({$v['machine_doll_name']});";
                    $redis->set($machine_name,$machine_id);
                    $redis->expire($machine_name,'21600');
                    $redis->set($machine_offline,$offlineTime);
                    $redis->expire($machine_offline,$out_time);
                }
            }
        }

        if($machineIds){
            $this->sendEmail();//发送邮件

            //发送微信提醒
            $data = array(
                'first' => array(
                    'value' => '钻石房 练习房 占卜房 机器概率异常下线报警',
                    'color' => '#FF0000'
                ),
                'keyword1' => array(
                    'value' => $time,
                    'color' => '#FF0000'
                ),
                'keyword2' => array(
                    'value' => '机器抓中概率',
                    'color' => '#FF0000'
                ),
                'remark' => array(
                    'value' => '机器名：'.$machineId.'请尽快调整机器概率重新上线',
                    'color' => '#FF0000'
                )
            );
            $service = new MessagesService();
            $access_token = $service->getAccessToken();
            $message_url = "http://p-admin.365zhuawawa.com/doll/alarm/data-p?machineId=$machineIds";
            $userInfo = Inform::find()->asArray()->all();
            $users = [];
            foreach($userInfo as $k=>$v){
                $memberID = $v['memberID'];
                $memberID=sprintf("%08d", $memberID);
                $sql1 = "select weixin_id from t_member WHERE memberID=$memberID";
                $op_id = $db->createCommand($sql1)->queryAll();
                if(empty($op_id)){
                    continue;
                }else{
                    $op_id = $op_id[0]['weixin_id'];
                    $sql2 = "select union_id from member_wx WHERE open_id='$op_id'";
                    $un_id = $db->createCommand($sql2)->queryAll();
                    if(empty($un_id)){
                        continue;
                    }else{
                        $un_id = $un_id[0]['union_id'];
                        $sql3 = "select openid from member_add WHERE unionid='$un_id'";
                        $open_id = $db->createCommand($sql3)->queryAll();
                        if(empty($open_id)){
                            continue;
                        }else{
                            $open_id = $open_id[0]['openid'];
                            array_push($users,$open_id);
                        }
                    }
                }
            }
            if($time >= $ss_time && $time <= $ee_time){
                echo "该时间段内不发送报警";
            }else{
                foreach($users as $k=>$v){
                    $touser = $v;
                    $service->sendMessage($access_token,$message_url,$data,$touser);
                }
            }
        }
    }

    public function actionDataP(){
        $db = Yii::$app->db;
        $request = Yii::$app->request;
        $machineId = $request->post('machineId') ? $request->post('machineId') : $request->get('machineId');
        $machineId = rtrim($machineId, ',');
        $startTime = date('Y-m-d 00:00:00',time());
        $endTime = date('Y-m-d 23:59:59',time());
        $s_time = strtotime($startTime);
        $e_time = strtotime($endTime);
        $sql = "select * from doll_machine_statistic WHERE machine_id IN ($machineId) AND start_time>='$s_time' AND start_time<='$e_time'";
        $machineData = $db->createCommand($sql)->queryAll();
        return $this->renderPartial('data-p',[
            'models'=>$machineData,
        ]);
    }

    public function insert($dollId,$reason){
        $db = Yii::$app->db;
        $sql2 = "UPDATE t_doll SET machine_status='维修中' WHERE id=$dollId";
        $db->createCommand($sql2)->execute();
        $sql = 'INSERT INTO t_doll_monitor (dollId,alert_type,alert_number,description,created_date,created_by,modified_date,'
            . 'modified_by) VALUES (:dollId,:alert_type,:alert_number,:description,:created_date,:created_by,:modified_date,'
            . ':modified_by)';
        $db->createCommand($sql,[
            ':dollId'=>$dollId,
            ':alert_type'=>'机器监控下线',
            ':alert_number'=>1,
            ':description'=>$reason,
            ':created_date'=>date('Y-m-d H:i:s'),
            ':created_by'=>0,
            ':modified_date'=>date('Y-m-d H:i:s'),
            ':modified_by'=>0,
        ])->execute();
    }
































}