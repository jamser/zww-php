<?php
namespace frontend\controllers;

use yii\web\Controller;
use Yii;
require('../pili-sdk/lib/Pili_v2.php');

class QnController extends Controller{
    public $enableCsrfValidation = false;
    //创建房间
    public function actionCreateRoom(){
        $request = Yii::$app->request;
        $userId = $request->post('userId');
        $roomName = $request->post('roomName');
        $ak="GPUedk3L2R1hKBM8Mmq8WAkq0W5aAmmKUH2M4AyU";
        $sk="uuUGTT6JnlNwDk9F9dzLV-WYCjqp_1GAHDDvxveC";
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\RoomClient($mac);
        $resp = $client->createRoom($userId, $roomName);
        print_r(json_encode($resp));
    }

    //roomToken
    public function actionGetRoomToken(){
        $request = Yii::$app->request;
        $userId = $request->post('userId');
        $roomName = $request->post('roomName');
        $perm = $request->post('perm');
        $ak="GPUedk3L2R1hKBM8Mmq8WAkq0W5aAmmKUH2M4AyU";
        $sk="uuUGTT6JnlNwDk9F9dzLV-WYCjqp_1GAHDDvxveC";
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\RoomClient($mac);
        $resp = $client->roomToken($roomName,$userId,$perm,(time()+3600));
        return json_encode(['code'=>0,'data'=>['token'=>$resp],'msg'=>'success']);
//        print_r(json_encode($resp));
    }

    //获取房间
    public function actionGetRoom(){
        $request = Yii::$app->request;
        $roomName = $request->post('roomName');
        $ak="GPUedk3L2R1hKBM8Mmq8WAkq0W5aAmmKUH2M4AyU";
        $sk="uuUGTT6JnlNwDk9F9dzLV-WYCjqp_1GAHDDvxveC";
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\RoomClient($mac);
        $resp = $client->getRoom($roomName);
        print_r(json_encode($resp));
    }

    //删除房间
    public function actionDeleteRoom(){
        $request = Yii::$app->request;
        $roomName = $request->post('roomName');
        $ak="GPUedk3L2R1hKBM8Mmq8WAkq0W5aAmmKUH2M4AyU";
        $sk="uuUGTT6JnlNwDk9F9dzLV-WYCjqp_1GAHDDvxveC";
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\RoomClient($mac);
        $resp = $client->deleteRoom($roomName);
        print_r(json_encode($resp));
    }

    //获取房间用户数量
    public function actionGetRoomUserNum(){
        $request = Yii::$app->request;
        $roomName = $request->post('roomName');
        $ak="GPUedk3L2R1hKBM8Mmq8WAkq0W5aAmmKUH2M4AyU";
        $sk="uuUGTT6JnlNwDk9F9dzLV-WYCjqp_1GAHDDvxveC";
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\RoomClient($mac);
        $resp=$client->getRoomUserNum($roomName);
        print_r(json_encode($resp));
    }

    //剔除房间用户
    public function actionKickingPlayer(){
        $request = Yii::$app->request;
        $roomName = $request->post('roomName');
        $playerId = $request->post('playerId');
        $ak="GPUedk3L2R1hKBM8Mmq8WAkq0W5aAmmKUH2M4AyU";
        $sk="uuUGTT6JnlNwDk9F9dzLV-WYCjqp_1GAHDDvxveC";
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\RoomClient($mac);
        $resp=$client->kickingPlayer($roomName,$playerId);
        print_r(json_encode($resp));
    }

    /*直播接口部分*/
    //创建hub
    public function actionCreateHub(){
        $ak="GPUedk3L2R1hKBM8Mmq8WAkq0W5aAmmKUH2M4AyU";
        $sk="uuUGTT6JnlNwDk9F9dzLV-WYCjqp_1GAHDDvxveC";
        $hubName = "PiliSDKTest";
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\Client($mac);
        $hub = $client->hub($hubName);
        print_r($hub);
        //获取stream
        $streamKey = "php-sdk-test" . time();
        $stream = $hub->stream($streamKey);
        print_r(json_encode($stream));
    }

    //创建stream
    public function actionCreateStream(){
        $ak = "Ge_kRfuV_4JW0hOCOnRq5_kD1sX53bKVht8FNdd3";
        $sk = "0fU92CSrvgNJTVCXqbuRVqkntPFJLFERGa4akpko";
        $hubName = "PiliSDKTest";
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\Client($mac);
        $hub = $client->hub($hubName);
        $streamKey = "php-sdk-test" . time();
        $resp = $hub->create($streamKey);
        print_r(json_encode($resp));
    }


    /**
     * 获取推流地址
     * 如果不传key和过期时间，将返回不含防盗链的url
     * @param bizId 您在腾讯云分配到的bizid
     *        streamId 您用来区别不同推流地址的唯一id
     *        key 安全密钥
     *        time 过期时间 sample 2016-11-12 12:00:00
     * @return String url */
     public function actionGetPushUrl($key = null, $time = null){
         $request = Yii::$app->request;
         $bizId = $request->post('bizId');
         $streamId = $request->post('streamId');

        if($key && $time){
            $txTime = strtoupper(base_convert(strtotime($time),10,16));
            //txSecret = MD5( KEY + livecode + txTime )
            //livecode = bizid+"_"+stream_id  如 8888_test123456
            $livecode = $bizId."_".$streamId; //直播码
            $txSecret = md5($key.$livecode.$txTime);
            $ext_str = "?".http_build_query(array(
                    "bizid"=> $bizId,
                    "txSecret"=> $txSecret,
                    "txTime"=> $txTime
                ));
        }
        return "rtmp://".$bizId.".livepush.myqcloud.com/live/".$livecode.(isset($ext_str) ? $ext_str : "");
    }


    /**
     * 获取播放地址
     * @param bizId 您在腾讯云分配到的bizid
     *        streamId 您用来区别不同推流地址的唯一id
     * @return String url */
    public function actionGetPlayUrl($bizId, $streamId){
        $livecode = $bizId."_".$streamId; //直播码
        return array(
            "rtmp://".$bizId.".liveplay.myqcloud.com/live/".$livecode,
            "http://".$bizId.".liveplay.myqcloud.com/live/".$livecode.".flv",
            "http://".$bizId.".liveplay.myqcloud.com/live/".$livecode.".m3u8"
        );
    }































}