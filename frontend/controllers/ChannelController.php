<?php
namespace frontend\controllers;

use common\helpers\MyFunction;
use common\models\CatchSum;
use common\models\ChargeOrder;
use common\models\ChargeSum;
use common\models\CostSum;
use common\models\InviteNum;
use common\models\Member;
use common\models\MemberToken;
use common\models\ShareInvite;
use frontend\models\Doll;
use Yii;
use yii\web\Controller;
use yii\web\Cookie;
use yii\data\Pagination;

// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:GET');
header('Access-Control-Allow-Methods:POST');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');


class ChannelController extends Controller{
    public function actionGetCk($channel){
//        $cookie = new Cookie();
//        $cookie->name = 'channel';
//        $cookie->expire = time()+86400;
//        $cookie->httpOnly = true;
//        $cookie->value = $channel;
//        Yii::$app->response->getCookies()->add($cookie);
        echo 'http://p.365zhuawawa.com'. \yii\helpers\Url::to(['download-app','channel'=>$channel,'ck'=>md5('365channel:'.$channel)]);
    }
    
    public function actionGetChannelUrl() {
        return $this->render('get-channel-url');
    }
    
    
    
    public function getIosHttpUserAgentKey() {
        // (KHTML, like Gecko)
        $ip = getIp();
        $httpUserAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        $info = substr($httpUserAgent, 0, strpos($httpUserAgent, '(KHTML, like Gecko)'));
        return md5("{$ip}_{$info}_365zww_channelkey");
    }
    
    /**
     * 下载APP
     */
    public function actionDownloadApp($channel,$ck) {
        $ip = getIp();
        $httpUserAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        //$lan = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : null;
        if($ip && $httpUserAgent && ($ck===md5('365channel:'.$channel))) {
            $key = $this->getIosHttpUserAgentKey();
            $redis = Yii::$app->redis;
            $redis->SET($key, $channel);
            $redis->EXPIRE($key, 1200);
            
            $cookie = new Cookie();
            $cookie->name = 'channel';
            $cookie->expire = time()+1200;
            $cookie->httpOnly = true;
            $cookie->value = $channel;
            Yii::$app->response->getCookies()->add($cookie);
        }
        
        switch ($channel) {
            case 'WangHongZhiBo':
            //case 'Wanghongzhibo':
                $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365&ckey=CK1383910899061';
                break;
            
            case 'WangHongZhiBo1':
                $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365&ckey=CK1386874852441';
                break;
            case 'WangHongZhiBo2':
                $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365&ckey=CK1386875709939';
                break;
            case 'WangHongZhiBo3':
                $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365&ckey=CK1386875777367';
                break;
            case 'WangHongZhiBo4':
                $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365&ckey=CK1386875777366';
                break;
            //case 'Qulingaoxiao':
            case 'QuLinGaoXiao':
                $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365&ckey=CK1383910425297';
                break;
            //case 'Yunlianchuanmei':
            case 'YunLianChuanMei':
                $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365&ckey=CK1384191423996';
                break;
            case 'QQGroup001';
                $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365&ckey=CK1385747378378';
                break;
            case 'QQGroup002';
                $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365&ckey=CK1385747390895';
                break;
            case 'QQGroup003';
                $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365&ckey=CK1385747548841';
                break;
            case 'QQGroup004';
                $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365&ckey=CK1385747548842';
                break;
            case 'QQGroup005';
                $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365&ckey=CK1385747893678';
                break;
            case 'QQGroup006';
                $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365&ckey=CK1385748498073';
                break;
            case 'TencentZone':
                $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365&ckey=CK1382708241281';
                break;
            default:
                $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365';
                break;
            
        }

        return $this->redirect($url);
//        return $this->redirect('https://fir.im/mdyb');
    }

    public function actionGet(){
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
            if($user['register_channel']) {
                Yii::error("token {$token} 用户已经有注册渠道");
                return json_encode(array('code'=>403,'message'=>'用户已经有注册渠道'));
            }
            
            $cookie = Yii::$app->request->cookies;
            $myfunction = new MyFunction();
            if($cookie->has('channel')){
                $channel = $cookie->getValue('channel');
                $myfunction->updateChannel($cacheUserId,$channel);
                Yii::error("token {$token} 添加成功");
                return json_encode(array('code'=>200,'message'=>'channel:'.$channel));
            }else{
                $ip = getIp();
                $httpUserAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
                if($ip && $httpUserAgent) {
                    $key = $this->getIosHttpUserAgentKey();
                    $channel = $redis->GET($key);
                    $myfunction->updateChannel($user['id'],$channel);
                    Yii::error("token {$token} 添加成功");
                    return json_encode(array('code'=>200,'message'=>'channel:'.$channel));
                }
                Yii::error("token {$token} 渠道号未找到");
                return json_encode(array('code'=>403,'message'=>'渠道号未找到'));
            }
        }
    }

    //分享页面的浏览人数和邀请下载人数
    public function actionNum($invite_code='40497798'){
        $inviteNum = InviteNum::find()->where(['invite_code'=>$invite_code])->asArray()->one();
        $inviteNum = $inviteNum['invite_num'];
        $shareNum = ShareInvite::find()->where(['invite_code'=>$invite_code])->count();
        return $this->render('shareNum',[
            'inviteNum' => $inviteNum,
            'shareNum' => $shareNum,
        ]);
    }

    //双旦活动
    public function actionCharge(){
        $export = Yii::$app->getRequest()->get('export', null);

        if ($export !== 'on') {
            $export = false;
        }

        $db = Yii::$app->db;

        $se_sql = "select * from charge_order where charge_name='月卡'";
        $userInfo = $db->createCommand($se_sql)->queryAll();
        $yue_ids = [];
        foreach($userInfo as $k=>$v){
            $id = $v['member_id'];
            if(!in_array($id,$yue_ids)){
                array_unshift($yue_ids,$id);
            }
        }

        $sql = "SELECT COUNT(*) FROM charge_order WHERE create_date>'2017-12-21 12:00:00' AND create_date<'2018-1-4 00:00:00' AND charge_state=1 AND charge_name!='双蛋惊喜礼包'";
        $count = $db->createCommand($sql)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);
        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $sql = "SELECT sum(price),member_id,member_name FROM charge_order WHERE create_date>'2017-12-21 12:00:00' AND create_date<'2018-1-4 00:00:00' AND charge_state=1 AND charge_name!='双蛋惊喜礼包' GROUP BY member_id";
        $rows = $db->createCommand($sql)->queryAll();
        foreach($rows as $k=>$v){
            $id = $v['member_id'];
            $price = $v['sum(price)'];
            $memberInfo = Member::find()->where(['id'=>$id])->asArray()->one();
            $user_id = $memberInfo['memberID'];
            if(in_array($id,$yue_ids)){
                $member_name = $v['member_name']."__月卡用户";
            }else{
                $member_name = $v['member_name'];
            }
            $insert_sql = "insert into charge_sum(price,member_id,member_name) VALUE ('$price','$user_id',:member_name)";
            Yii::$app->db->createCommand($insert_sql,[
                ':member_name'=>$member_name,
            ])->execute();
        }
//        $ids = [];
//        foreach($rows as $k=>$v){
//            $id = $v['member_id'];
////            if(!in_array($id,$ids)){
////                array_unshift($ids,$id);
////            }
//            array_unshift($ids,$id);
//        }
//        $member_ids=[];
//        $cha = ChargeSum::find()->asArray()->all();
//        foreach($cha as $k=>$v){
//            $member_id=$v['member_id'];
//            array_unshift($member_ids,$member_id);
//        }
//        $result = array_diff($ids,$member_ids);
//        print_r($result);die;
//        foreach($result as $id){
//            $sql = "select price,charge_name,member_id,member_name,create_date from charge_order WHERE member_id=$id";
//            $rows = $db->createCommand($sql)->queryAll();
//            $memberInfo = Member::find()->where(['id'=>$id])->asArray()->one();
//            $user_id = $memberInfo['memberID'];
//            if($rows){
//                $price = $rows[0]['sum(price)'];
//                if(!in_array($id,$yue_ids)){
//                    $member_name = $rows[0]['member_name']."__月卡用户";
//                }
//                $insert_sql = "insert into charge_sum(price,member_id,member_name) VALUE ('$price','$user_id',:member_name)";
//                Yii::$app->db->createCommand($insert_sql,[
//                    ':member_name'=>$member_name,
//                ])->execute();
//            }else{
//                continue;
//            }
//        }

    }

    public function actionChargeNum(){
        $export = Yii::$app->getRequest()->get('export', null);

        if ($export !== 'on') {
            $export = false;
        }

        $db = Yii::$app->db;

        $sql = "SELECT COUNT(*) FROM charge_sum WHERE price>'98'";
        $count = $db->createCommand($sql)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);
        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $sql = "SELECT * FROM charge_sum WHERE price>'98'"
            . "  ORDER BY price ASC" . ($export ? '' : " limit $offset,$size ");
        $rows = $db->createCommand($sql)->queryAll();

        if ($export) {
            $filename = "charge_" . date("Y-m-d H:i:s");
            foreach ($rows as $row) {
                $items[] = [
                    'id' => $row['id'],
                    '充值金额' => $row['price'],
                    '用户id' => $row['member_id'],
                    '用户名' => $row['member_name'],
                ];
            }
            $this->_setcsvHeader("{$filename}.csv");
            echo $this->_array2csv($items);
            Yii::$app->end();
        }

        return $this->render('charge',[
            'rows' => $rows,
            'pages' => $pages,
        ]);
    }

    protected function _setcsvHeader($filename) {
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-type: application/vnd.ms-excel; charset=utf8");
        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
        //设置utf-8 + bom ，处理汉字显示的乱码
        print(chr(0xEF) . chr(0xBB) . chr(0xBF));
    }

    protected function _array2csv(array &$array){
        if (count($array) == 0) {
            return  null;
        }
        set_time_limit(0);//响应时间改为60秒
        ini_set('memory_limit', '512M');
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }

    public function actionDe(){
        CostSum::deleteAll();
    }

    public function actionCatch(){
        $db = Yii::$app->db;
        $sql = "SELECT member_id,COUNT(*) FROM t_doll_catch_history WHERE catch_date>'2017-12-21 12:00:00' AND catch_date<'2018-1-4 00:00:00' AND catch_status='抓取成功' GROUP BY member_id ORDER BY COUNT(*) DESC LIMIT 20";
        $rows = $db->createCommand($sql)->queryAll();
        foreach($rows as $k=>$v) {
            $id = $v['member_id'];
            $num = $v['COUNT(*)'];
            $memberInfo = Member::find()->where(['id' => $id])->asArray()->one();
            $user_id = $memberInfo['memberID'];
            $name = $memberInfo['name'];
            $insert_sql = "insert into catch_sum(member_id,member_name,catch_num) VALUE ('$user_id','$name','$num')";
            Yii::$app->db->createCommand($insert_sql)->execute();
        }
    }

    public function actionCatchNum(){
        $data = CatchSum::find()->asArray()->all();
        return $this->render('catch',[
            'data' => $data,
        ]);
    }

    public function actionCast(){
        $buy_sql = "SELECT COUNT(*),member_id,member_name,charge_name FROM charge_order WHERE create_date>'2017-12-21 12:00:00' AND create_date<'2018-1-4 00:00:00' AND charge_state=1 AND charge_name='双蛋惊喜礼包' GROUP BY member_id";
        $users = Yii::$app->db->createCommand($buy_sql)->queryAll();
        foreach($users as $k=>$v){
            $member_id = $v['member_id'];
            $member_name = $v['member_name'];
            $buy_num = $v['COUNT(*)'];
            $catch_sql = "select * from t_doll_catch_history WHERE member_id=$member_id AND catch_status='抓取成功' AND catch_date>'2017-12-21 12:00:00' AND catch_date<'2018-1-4 00:00:00' ";
            $count = Yii::$app->db->createCommand($catch_sql)->execute();
            $catch = "select doll_id from t_doll_catch_history WHERE catch_date>'2017-12-21 12:00:00' AND catch_date<'2018-1-4 00:00:00' AND member_id=$member_id";
            $dolls = Yii::$app->db->createCommand($catch)->queryAll();
            $coins = [];
            foreach($dolls as $k=>$v){
                $doll_id = $v['doll_id'];
                $data = Doll::find()->where(['id'=>$doll_id])->asArray()->one();
                $coin = $data['price'];
                array_unshift($coins,$coin);
            }
            $cost = array_sum($coins);
            $insert_sql = "insert into cost_sum(member_id,member_name,buy_num,catch_num,coins) VALUE ('$member_id','$member_name','$buy_num','$count','$cost')";
            Yii::$app->db->createCommand($insert_sql,[
                ':member_name'=>$member_name,
            ])->execute();
        }
    }

    public function actionCastNum(){
        $data = CostSum::find()->asArray()->all();
        foreach($data as $k=>$v){
            $member_id = $v['member_id'];
            $buy_coin = $v['buy_num']*130;
            $cost_coin = $v['coins'];
            $catch_num = $v['catch_num'];
            if($cost_coin > $buy_coin){
                $cost = $buy_coin;
            }else{
                $cost = $cost_coin;
            }
            $get = $cost - $catch_num*119;
            if($get < 0){
                $num = 0;
                $update_sql = "update cost_sum set get_num =$num  WHERE member_id =$member_id";
                Yii::$app->db->createCommand($update_sql)->execute();
            }else{
                $get_num = $get/119;
                $num = intval($get_num);
                $update_sql = "update cost_sum set get_num =$num  WHERE member_id =$member_id";
                Yii::$app->db->createCommand($update_sql)->execute();
            }
        }
    }

    public function actionGetNum(){
        $data = CostSum::find()->where(['>','get_num',0])->orderBy(['get_num' => SORT_DESC])->asArray()->all();
        return $this->render('cost',[
            'data' => $data,
        ]);
    }

    public function actionUpId(){
        $data = CostSum::find()->asArray()->all();
        foreach($data as $k=>$v){
            $member_id = $v['member_id'];
            $userInfo = Member::find()->where(['id'=>$member_id])->asArray()->one();
            $user_id = $userInfo['memberID'];
            $update_sql = "update cost_sum set member_id =$user_id  WHERE member_id =$member_id";
            Yii::$app->db->createCommand($update_sql)->execute();
        }
    }

    /**
     * 兑换code
     */
    public function actionExchangeCode($code) {
        
        $code = trim($code);
        $codes = [];
        if(in_array($code, $codes)) {
            //查找用户是否加过币， 没有的话加币
            
        } else {
            //非正常邀请码 
            return json_encode([
                'code'=>400,
                'message'=>'没有找到该兑换码'
            ]);
        }
        
        
    }
}