<?php
namespace frontend\controllers;

use common\models\ChargeOrder;
use common\models\DollRtmp;
use common\models\Member;
use Yii;
use yii\web\Controller;

// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:GET');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');

class RankController extends Controller{
    public $enableCsrfValidation = false;
    public function actionIndex(){
        return $this->render('index');
    }

    public function actionMember(){
        $db = Yii::$app->db;
        $request = Yii::$app->request;
        $token = $request->post('token') ? $request->post('token') : $request->get('token');

        if(!$token) {
            Yii::error("token {$token} 没有找到授权");
            return json_encode(array('code'=>403,'message'=>'授权已过期'));
        }
        $redis = Yii::$app->redis;
        $type = $redis->TYPE($token);
        if($type!='string') {
            Yii::error("token {$token} 授权非string");
            return json_encode(array('code'=>403,'message'=>'授权已过期'));
        }
        $cacheUserId = $redis->GET($token);
        if(!$cacheUserId || !($user=Member::find()->where(['id'=>(int)$cacheUserId])->asArray()->one())) {
            Yii::error("token {$token} 参数不正确， 找不到ID用户");
            return json_encode(array('code'=>403,'message'=>'参数不正确'));
        }else{
            $chargeSql = "select sum(price) from charge_order WHERE member_id=$cacheUserId AND charge_type=1";
            $chargeData = $db->createCommand($chargeSql)->queryAll();
            $chargeNum = $chargeData[0]['sum(price)'];
            $inviteSql = "select count(*) from share_invite WHERE invite_member_id=$cacheUserId AND create_date>='2018-03-11 00:00:00' AND create_date <= '2018-03-18 00:00:00'";
            $inviteData = $db->createCommand($inviteSql)->queryAll();
            $inviteNum = $inviteData[0]['count(*)'];
            $catchSql = "select count(*) from t_doll_catch_history WHERE member_id=$cacheUserId AND catch_status='抓取成功' and catch_date >='2018-03-11 00:00:00' AND catch_date <= '2018-03-18 00:00:00' AND machine_type NOT IN (1,3)";
            $catchData = $db->createCommand($catchSql)->queryAll();
            $catchNum = $catchData[0]['count(*)'];
            $img_url = $user['icon_real_path'];
            $name = $user['name'];
            $invite_code = $user['memberID'];
            $memberData = json_encode(array('img_url'=>$img_url,'name'=>$name,'invite_code'=>$invite_code,'chargeNum'=>$chargeNum,'inviteNum'=>$inviteNum,'catchNum'=>$catchNum));
            return json_encode(array('code'=>200,'resultData'=>$memberData));
        }
    }

    public function actionTest($cacheUserId=164){
        $db = Yii::$app->db;
        $user=Member::find()->where(['id'=>(int)$cacheUserId])->asArray()->one();
        $chargeSql = "select sum(price) from charge_order WHERE member_id=$cacheUserId AND charge_type=1";
        $chargeData = $db->createCommand($chargeSql)->queryAll();
        $chargeNum = $chargeData[0]['sum(price)'];
        $inviteSql = "select count(*) from share_invite WHERE invite_member_id=$cacheUserId";
        $inviteData = $db->createCommand($inviteSql)->queryAll();
        $inviteNum = $inviteData[0]['count(*)'];
        $catchSql = "select count(*) from t_doll_catch_history WHERE member_id=$cacheUserId AND catch_status='抓取成功'";
        $catchData = $db->createCommand($catchSql)->queryAll();
        $catchNum = $catchData[0]['count(*)'];
        $img_url = $user['icon_real_path'];
        $name = $user['name'];
        $invite_code = $user['memberID'];
        $memberData = json_encode(array('img_url'=>$img_url,'name'=>$name,'invite_code'=>$invite_code,'chargeNum'=>$chargeNum,'inviteNum'=>$inviteNum,'catchNum'=>$catchNum),JSON_UNESCAPED_SLASHES);
        $data = str_replace("\\/",'/',$memberData);
        return json_encode(array('code'=>200,'resultData'=>$data));
    }

    public function actionLove(){
        $db = Yii::$app->db_php;
        $request = Yii::$app->request;
        $loved_id = $request->post('loved_id') ? $request->post('loved_id') : $request->get('loved_id');
        $love_id = $request->post('love_id') ? $request->post('love_id') : $request->get('love_id');
        $lSql = "select * from love_num WHERE loved_id=$loved_id AND love_id=$love_id";
        $ldata = $db->createCommand($lSql)->execute();
        $hSql = "select * from hate_num WHERE hated_id=$loved_id AND hate_id=$love_id";
        $hdata = $db->createCommand($hSql)->execute();
        if($ldata == 0 and $hdata == 0){
            $sql = "insert into love_num(loved_id,love_id) VALUES ('$loved_id','$love_id')";
            $db->createCommand($sql)->execute();
            $result = array('code'=>200,'message'=>'点赞成功');
            return json_encode($result);
        }else{
            return json_encode(array('code'=>403,'message'=>'你已经赞/踩过了','action'=>'love'));
        }
    }

    public function actionLoveNum(){
        $db = Yii::$app->db_php;
        $sql = "select loved_id,count(loved_id) as num from love_num GROUP BY loved_id";
        $data = $db->createCommand($sql)->queryAll();
        $loveData = [];
        foreach($data as $k=>$v){
            $loveData[$v['loved_id']] = $v['num'];
        }
        print_r(json_encode($loveData));
    }

    public function actionHate(){
        $db = Yii::$app->db_php;
        $request = Yii::$app->request;
        $hated_id = $request->post('loved_id') ? $request->post('loved_id') : $request->get('loved_id');
        $hate_id = $request->post('love_id') ? $request->post('love_id') : $request->get('love_id');
        $lSql = "select * from hate_num WHERE hated_id=$hated_id AND hate_id=$hate_id";
        $ldata = $db->createCommand($lSql)->execute();
        $hSql = "select * from love_num WHERE loved_id=$hated_id AND love_id=$hate_id";
        $hdata = $db->createCommand($hSql)->execute();
        if($ldata == 0 and $hdata == 0){
            $sql = "insert into hate_num(hated_id,hate_id) VALUES ('$hated_id','$hate_id')";
            $db->createCommand($sql)->execute();
            $result = array('code'=>200,'message'=>'踩成功');
            return json_encode($result);
        }else{
            return json_encode(array('code'=>403,'message'=>'你已经踩/赞过了','action'=>'hate'));
        }
    }

    public function actionHateNum(){
        $db = Yii::$app->db_php;
        $sql = "select hated_id,count(hated_id) as num from hate_num GROUP BY hated_id";
        $data = $db->createCommand($sql)->queryAll();
        $hateData = [];
        foreach($data as $k=>$v){
            $hateData[$v['hated_id']] = $v['num'];
        }
        print_r(json_encode($hateData));
    }

    public function actionIs(){
        $db = Yii::$app->db_php;
        $request = Yii::$app->request;
        $memberd_id = $request->post('loved_id') ? $request->post('loved_id') : $request->get('loved_id');
        $member_id = $request->post('love_id') ? $request->post('love_id') : $request->get('love_id');
        $hSql = "select * from hate_num WHERE hated_id=$memberd_id AND hate_id=$member_id";
        $hdata = $db->createCommand($hSql)->execute();
        $lSql = "select * from love_num WHERE loved_id=$memberd_id AND love_id=$member_id";
        $ldata = $db->createCommand($lSql)->execute();
        if($ldata == 0 && $hdata !=0){
            return json_encode(array('code'=>403,'message'=>'你已经踩过了','action'=>'hate'));
        }elseif($ldata !=0 && $hdata ==0){
            return json_encode(array('code'=>403,'message'=>'你已经赞过了','action'=>'love'));
        }else{
            return json_encode(array('code'=>200,'message'=>'你还没赞/踩'));
        }
    }

//    public function actionDe(){
//        $db = Yii::$app->db_php;
//        $sql = "delete from hate_num";
//        $db->createCommand($sql)->execute();
//    }
}