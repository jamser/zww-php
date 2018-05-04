<?php
namespace api\modules\socket\controllers;

use api\modules\socket\models\TMember;
use api\modules\socket\models\TMemberToken;
use Yii;
use yii\web\Controller;
use api\modules\socket\models\TDoll;

class SocketController extends Controller{
    //判断用户token是否有效
    private function token($token){
        $userResult = TMemberToken::find()->where(['token'=>$token])->asArray()->one();
        if($userResult){
            $userId = $userResult['member_id'];
            $userInfo = TMember::find()->where(['id'=>$userId])->asArray()->one();
            $arr = array('code'=>200,'data'=>$userInfo);
            return json_encode($arr);
        }else{
            return json_decode(array('msg'=>'token无效','code'=>403));
        }
    }

    //判断房间是否空闲
    private function dollStatus($dollid){
        $dollresult = TDoll::find()->where(['id'=>$dollid])->asArray()->one();
        $machine_status = $dollresult['machine_status'];
        switch($machine_status){
            case "空闲中":
                return json_encode(array('cdoe'=>200,'msg'=>'机器空闲中'));
                break;
            case "未上线":
                return json_encode(array('cdoe'=>403,'msg'=>'未上线'));
                break;
            case "游戏中":
                return json_encode(array('cdoe'=>402,'msg'=>'游戏中'));
                break;
            default;
        }
    }

    //排队，抢位

    //抢位开始建立socket连接
    public function actionGameStart($token,$dollid){
        $user = $this->token($token);
        $user = json_decode($user);
        $doll = $this->dollStatus($dollid);
        $doll = json_decode($doll);
        if($user['code']==200 && $doll['code']==200){
            //调用阿里云接口
        }
    }

    //控制机器（时间基准，60没收到操作信息调用结束游戏接口），和分析抓取结果记录
}