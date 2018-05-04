<?php
namespace api\modules\lianmai\controllers;

use yii\web\Controller;
use Yii;
include_once '../modules/lianmai/sdks/pili-sdk/lib/Pili_v2.php';

class QnController extends Controller{
    public $enableCsrfValidation = false;
    //创建房间
    public function actionCreateRoom($userId,$roomName){
        $ak=Yii::$app->params['qiniukey'];
        $sk=Yii::$app->params['qiniusecret'];
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\RoomClient($mac);
        $resp = $client->createRoom($userId, $roomName);
        print_r($resp);
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
    public function actionGetRoom($roomName){
        $ak=Yii::$app->params['qiniukey'];
        $sk=Yii::$app->params['qiniusecret'];
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\RoomClient($mac);
        $resp = $client->getRoom($roomName);
        print_r($resp);
    }

    //删除房间
    public function actionDeleteRoom($roomName){
        $ak=Yii::$app->params['qiniukey'];
        $sk=Yii::$app->params['qiniusecret'];
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\RoomClient($mac);
        $resp = $client->deleteRoom($roomName);
        print_r($resp);
    }

    //获取房间用户数量
    public function actionGetRoomUserNum($roomName){
        $ak=Yii::$app->params['qiniukey'];
        $sk=Yii::$app->params['qiniusecret'];
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\RoomClient($mac);
        $resp=$client->getRoomUserNum($roomName);
        print_r($resp);
    }

    //剔除房间用户
    public function actionKickingPlayer($roomName,$playerId){
        $ak=Yii::$app->params['qiniukey'];
        $sk=Yii::$app->params['qiniusecret'];
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\RoomClient($mac);
        $resp=$client->kickingPlayer($roomName,$playerId);
        print_r($resp);
    }

    /*直播接口部分*/
    //创建hub
    public function actionCreateHub($hubName){
        $ak=Yii::$app->params['qiniukey'];
        $sk=Yii::$app->params['qiniusecret'];
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\Client($mac);
        $hub = $client->hub($hubName);
        print_r($hub);
        //获取stream
        $streamKey = "php-sdk-test" . time();
        $stream = $hub->stream($streamKey);
        print_r($stream);
    }

    //创建stream
    public function actionCreateStream($hubName){
        $ak=Yii::$app->params['qiniukey'];
        $sk=Yii::$app->params['qiniusecret'];
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\Client($mac);
        $hub = $client->hub($hubName);
        $streamKey = "php-sdk-test" . time();
        $resp = $hub->create($streamKey);
        print_r($resp);
    }




























}