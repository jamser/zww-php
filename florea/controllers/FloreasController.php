<?php
namespace florea\controllers;

use common\models\ChargeOrder;
use common\models\DollCatchHistory;
use common\models\Member;
use common\models\User;
use Yii;
use yii\web\Controller;

class FloreasController extends Controller{
    //小蜜蜂后台
    public function actionFlorea()
    {
//        $id = Yii::$app->user->getId();
//        $userInfo = User::find()->where(['id'=>$id])->asArray()->one();
//        $identity = $userInfo['identity'];
//        if(Yii::$app->user->isGuest){
//            return $this->goHome();
//        }
        //邀请人id
        $invite_id = '32298460';

        $db = Yii::$app->db;
        $startTime = Yii::$app->getRequest()->get('startTime', null);
        $endTime = Yii::$app->getRequest()->get('endTime', null);
        $memberID = Yii::$app->getRequest()->get('memberID',null);
        $name = Yii::$app->getRequest()->get('name',null);
        $register = Yii::$app->getRequest()->get('last_login_from',null);
        $export = Yii::$app->getRequest()->get('export', null);
        $channel = Yii::$app->getRequest()->get('register_channel',null);
        $mobile = Yii::$app->getRequest()->get('mobile',null);
        $conditions = $params = [];

        if ($startTime) {
            $conditions[] = '`register_date`>=:startTime';
            $params[':startTime'] = trim($startTime);
        }

        if ($endTime) {
            $conditions[] = '`register_date`<=:endTime';
            $params[':endTime'] = trim($endTime);
        }

        if ($memberID) {
            $conditions[] = '`memberID`=:memberID';
            $params[':memberID'] = trim($memberID);
        }

        if ($name) {
            $conditions[] = '`name`=:name';
            $params[':name'] = trim($name);
        }

        if ($mobile) {
            $conditions[] = '`mobile`=:mobile';
            $params[':mobile'] = trim($mobile);
        }

        if ($channel && $channel!='渠道') {
            $conditions[] = '`register_channel`=:register_channel';
            $params[':register_channel'] = trim($channel);
        }

        if ($register && $register!='设备') {
            if ($register == 'android'){
                $conditions[] = '`last_login_from`=:last_login_from';
                $params[':last_login_from'] = trim($register);
            }else{
                $conditions[] = '`last_login_from` is null';
            }
        }

//        if ($identity) {
//            $conditions[] = 'd.`register_channel`=:register_channel';
//            $params[':register_channel'] = trim($identity);
//        }

        if ($export !== 'on') {
            $export = false;
        }

        $sql = "select invited_member_id from floreas WHERE invite_member_id=$invite_id";
        $rows = $db->createCommand($sql)->queryAll();
        $ids = [];
        foreach($rows as $k=>$v){
            $id = $v['invited_member_id'];
            array_push($ids,$id);
        }

        if($invite_id){
            $conditions[] = '`memberID` in ('.implode(',',$ids).')';
        }

        $sql = 'SELECT COUNT(*) FROM t_member d'
            . ($conditions ? ' WHERE ' . implode(' AND ', $conditions) : '');
        $count = $db->createCommand($sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount' => $count
        ]);

        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $sql = 'SELECT d.* FROM t_member d'
            . ($conditions ? ' WHERE ' . implode(' AND ', $conditions) : '')
            . "  ORDER BY id DESC" . ($export ? '' : " limit $offset,$size ");
        $rows = $db->createCommand($sql, $params)->queryAll();

        if ($export) {
            $filename = date("Y-m-d H:i:s");
            foreach ($rows as $row) {
                $items[] = [
                    '登录渠道号' => $row['login_channel'],
                    '注册渠道号' => $row['register_channel'],
                    '用户ID' => $row['memberID'],
                    '昵称' => $row['name'],
                    '电话' => $row['mobile'],
                    '性别' => $row['gender'],
                    '机型' => $row['phone_model'],
                    '注册时间' => $row['register_date'],
                    '最近登录时间' => $row['last_login_date']
                ];
            }
            $this->_setcsvHeader("{$filename}.csv");
            echo $this->_array2csv($items);
            Yii::$app->end();
        }

        return $this->render('florea_index', [
            'rows' => $rows,
            'pages' => $pages,
            'count' => $count,
        ]);
    }

    //分销后台
    public function actionIndex()
    {
        $id = Yii::$app->user->getId();
        $userInfo = User::find()->where(['id'=>$id])->asArray()->one();
        $identity = $userInfo['identity'];
        if(Yii::$app->user->isGuest){
            return $this->goHome();
        }
        $db = Yii::$app->db;
        $startTime = Yii::$app->getRequest()->get('startTime', null);
        $endTime = Yii::$app->getRequest()->get('endTime', null);
        $memberID = Yii::$app->getRequest()->get('memberID',null);
        $name = Yii::$app->getRequest()->get('name',null);
        $register = Yii::$app->getRequest()->get('last_login_from',null);
        $export = Yii::$app->getRequest()->get('export', null);
        $channel = Yii::$app->getRequest()->get('register_channel',null);
        $mobile = Yii::$app->getRequest()->get('mobile',null);
        $conditions = $params = [];

        if ($startTime) {
            $conditions[] = 'd.`register_date`>=:startTime';
            $params[':startTime'] = trim($startTime);
        }

        if ($endTime) {
            $conditions[] = 'd.`register_date`<=:endTime';
            $params[':endTime'] = trim($endTime);
        }

        if ($memberID) {
            $conditions[] = 'd.`memberID`=:memberID';
            $params[':memberID'] = trim($memberID);
        }

        if ($name) {
            $conditions[] = 'd.`name`=:name';
            $params[':name'] = trim($name);
        }

        if ($mobile) {
            $conditions[] = 'd.`mobile`=:mobile';
            $params[':mobile'] = trim($mobile);
        }

        if ($channel && $channel!='渠道') {
            $conditions[] = 'd.`register_channel`=:register_channel';
            $params[':register_channel'] = trim($channel);
        }

        if ($register && $register!='设备') {
            if ($register == 'android'){
                $conditions[] = 'd.`last_login_from`=:last_login_from';
                $params[':last_login_from'] = trim($register);
            }else{
                $conditions[] = 'd.`last_login_from` is null';
            }
        }

        if ($identity) {
            $conditions[] = 'd.`register_channel`=:register_channel';
            $params[':register_channel'] = trim($identity);
        }

        if ($export !== 'on') {
            $export = false;
        }

        $sql = 'SELECT COUNT(*) FROM t_member d'
            . ' LEFT JOIN member_channel_deduct di ON d.id!=di.user_id'
            . ' LEFT JOIN member_registe_channel ti ON d.id=ti.member_id'
            . ($conditions ? ' WHERE ' . implode(' AND ', $conditions) : '');
        $count = $db->createCommand($sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount' => $count
        ]);

        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $sql = 'SELECT d.*,ti.rank,ti.inviter FROM t_member d'
            . ' LEFT JOIN member_channel_deduct di ON d.id!=di.user_id'
            . ' LEFT JOIN member_registe_channel ti ON d.id=ti.member_id'
            . ($conditions ? ' WHERE ' . implode(' AND ', $conditions) : '')
            . "  ORDER BY id DESC" . ($export ? '' : " limit $offset,$size ");
        $rows = $db->createCommand($sql, $params)->queryAll();

        $charge = [];
        foreach($rows as $k=>$v){
            $user_id = $v['id'];
            $member_id = $v['memberID'];
            $sql2 = "select sum(price) price from charge_order WHERE member_id=$user_id AND charge_state=1";
            $data2 = Yii::$app->db->createCommand($sql2)->queryAll();
            if(empty($data2)){
                continue;
            }else{
                $price = $data2[0]['price'];
                $charge[$member_id] = $price;
            }
        }

//        foreach($rows as $k=>$v){
//            $inviter_id = $v['inviter'];
//            if(empty($inviter_id)){
//                continue;
//            }else{
//                $sql1 = "select member_id from member_registe_channel WHERE inviter=$inviter_id";
//                $data1 = Yii::$app->db->createCommand($sql1)->queryAll();
//                $prices = $price_one = $money = [];
//                foreach($data1 as $k=>$v){
//                    $member_id = $v['member_id'];
//                    $sql2 = "select sum(price) price from charge_order WHERE charge_state=1 AND member_id=$member_id";
//                    $data2 = Yii::$app->db->createCommand($sql2)->queryAll();
//                    if(empty($data2)){
//                        continue;
//                    }else{
//                        $price=$data2[0]['price'];
//                        if($price < 50){
//                            $price = $price * 0.15;
//                        }elseif($price >= 50 && $price <100){
//                            $price = $price * 0.18;
//                        }else{
//                            $price = $price * 0.2;
//                        }
//                        array_push($prices,$price);
//                    }
//                }
//                $prices_sum = array_sum($prices);
//                $money[$inviter_id] = $prices_sum;
//            }
//        }

        if ($export) {
            $filename = "channel_" . date("Y-m-d H:i:s");
            foreach ($rows as $row) {
                $items[] = [
                    '登录渠道号' => $row['login_channel'],
                    '注册渠道号' => $row['register_channel'],
                    '用户ID' => $row['memberID'],
                    '昵称' => $row['name'],
                    '电话' => $row['mobile'],
                    '性别' => $row['gender'],
                    '机型' => $row['phone_model'],
                    '注册时间' => $row['register_date'],
                    '最近登录时间' => $row['last_login_date']
                ];
            }
            $this->_setcsvHeader("{$filename}.csv");
            echo $this->_array2csv($items);
            Yii::$app->end();
        }

        return $this->render('index', [
            'rows' => $rows,
            'pages' => $pages,
            'count' => $count,
            'charge' => $charge,
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

    public function actionInfo($id){
        $Info = DollCatchHistory::find()->where(['id'=>$id])->asArray()->one();
        return $this->render('info',[
            'Info' => $Info,
        ]);
    }

    public function actionCatch($id){
        $Info = ChargeOrder::find()->where("member_id=:member_id and charge_state=:charge_state", [
            ':member_id' => $id,
            ':charge_state' => 1
        ])  ->asArray()->all();
        return $this->render('catch',[
            'Info' => $Info,
        ]);
    }

    public function actionFloreaCharge(){
//        $id = Yii::$app->user->getId();
//        $userInfo = User::find()->where(['id'=>$id])->asArray()->one();
//        $identity = $userInfo['identity'];
//        if(Yii::$app->user->isGuest){
//            return $this->goHome();
//        }
//        if($identity == 'xiaomi' || $identity == 'meizu'){
//            return $this->render('out');
//        }
        $invite_id = '32298460';
        $db = Yii::$app->db;
        $startTime = Yii::$app->getRequest()->get('startTime', null);
        $endTime = Yii::$app->getRequest()->get('endTime', null);
        $memberID = Yii::$app->getRequest()->get('member_id',null);
        $name = Yii::$app->getRequest()->get('member_name',null);
        $export = Yii::$app->getRequest()->get('export', null);
        $charge_name = Yii::$app->getRequest()->get('charge_name',null);
        $charge_state = Yii::$app->getRequest()->get('charge_state',null);
        $conditions = $params = [];
        $conditionss = $paramss = [];

        if ($startTime) {
            $conditions[] = '`create_date`>=:startTime';
            $params[':startTime'] = trim($startTime);
        }

        if ($endTime) {
            $conditions[] = '`create_date`<=:endTime';
            $params[':endTime'] = trim($endTime);
        }

        if ($memberID) {
            $conditions[] = '`member_id`=:member_id';
            $params[':member_id'] = trim($memberID);
        }

        if ($name) {
            $conditions[] = '`member_name`=:member_name';
            $params[':member_name'] = trim($name);
        }

        if ($charge_name && $charge_name!='充值规则') {
            $conditions[] = '`charge_name`=:charge_name';
            $params[':charge_name'] = trim($charge_name);
        }

        if ($charge_state && $charge_state!='订单状态') {
            $conditions[] = '`charge_state`=:charge_state';
            $params[':charge_state'] = trim($charge_state);
        }

        $sql = "select invited_member_id from floreas WHERE invite_member_id=$invite_id";
        $rows = $db->createCommand($sql)->queryAll();
        $ids = [];
        foreach($rows as $k=>$v){
            $id = $v['invited_member_id'];
            $userInfo = Member::find()->where(['memberID'=>$id])->asArray()->one();
            $user_id = $userInfo['id'];
            array_push($ids,$user_id);
        }

        if($invite_id){
            $conditions[] = '`member_id` in ('.implode(',',$ids).')';
        }

        if($invite_id){
            $conditionss[] = '`member_id` in ('.implode(',',$ids).')';
        }

        if ($invite_id) {
            $conditionss[] = '`charge_state`=:charge_state';
            $paramss[':charge_state'] = 1;
        }

        if ($export !== 'on') {
            $export = false;
        }

        $charge_sql = 'SELECT COUNT(*) FROM charge_order'
            . ($conditions ? ' WHERE ' . implode(' AND ', $conditions) : '');
        $counts = $db->createCommand($charge_sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount' => $counts
        ]);
        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $charge_sql = 'SELECT * FROM charge_order'
            . ($conditions ? ' WHERE ' . implode(' AND ', $conditions) : '')
            . "  ORDER BY id DESC" . ($export ? '' : " limit $offset,$size ");
        $charger_rows = $db->createCommand($charge_sql, $params)->queryAll();

        $catch_sql = 'SELECT * FROM charge_order'
            . ($conditionss ? ' WHERE ' . implode(' AND ', $conditionss) : '');
        $catch_rows = $db->createCommand($catch_sql, $paramss)->queryAll();

        $pay = [];
        foreach($catch_rows as $key=>$v){
            $price = $v['price'];
            array_unshift($pay,$price);
        }
        $catch_count = count($pay);
        $sum = array_sum($pay);


        if ($export) {
            $filename = date("Y-m-d H:i:s");
            foreach ($charger_rows as $row) {
                if($row['charge_state'] ==0){
                    $state = "未完成";
                }else{
                    $state = "已完成";
                }
                $items[] = [
                    'id' => $row['id'],
                    '订单编号' => $row['order_no'],
                    '充值规则' => $row['charge_name'],
                    '充值金额' => $row['price'],
                    '用户id' => $row['member_id'],
                    '用户名' => $row['member_name'],
                    '订单状态' => $state,
                    '充值前' => $row['coins_before'],
                    '充值' => $row['coins_after'],
                    '赠送' => $row['coins_charge'],
                    '充值时间' => $row['create_date']
                ];
            }
            $this->_setcsvHeader("{$filename}.csv");
            echo $this->_array2csv($items);
            Yii::$app->end();
        }

        return $this->render('florea_charge', [
            'rows' => $charger_rows,
            'pages' => $pages,
            'count' => $counts,
            'catch_count' => $catch_count,
            'sum' => $sum,
        ]);
    }

    public function actionCharge(){
        $id = Yii::$app->user->getId();
        $userInfo = User::find()->where(['id'=>$id])->asArray()->one();
        $identity = $userInfo['identity'];
        if(Yii::$app->user->isGuest){
            return $this->goHome();
        }
        $db = Yii::$app->db;
        $startTime = Yii::$app->getRequest()->get('startTime', null);
        $endTime = Yii::$app->getRequest()->get('endTime', null);
        $memberID = Yii::$app->getRequest()->get('member_id',null);
        $name = Yii::$app->getRequest()->get('member_name',null);
        $export = Yii::$app->getRequest()->get('export', null);
        $charge_name = Yii::$app->getRequest()->get('charge_name',null);
        $charge_state = Yii::$app->getRequest()->get('charge_state',null);
        $inviter = Yii::$app->getRequest()->get('inviter',null);
        $conditions = $params = [];
        $conditionss = $paramss = [];

        if ($startTime) {
            $conditions[] = '`create_date`>=:startTime';
            $params[':startTime'] = trim($startTime);
        }

        if ($endTime) {
            $conditions[] = 'd.`create_date`<=:endTime';
            $params[':endTime'] = trim($endTime);
        }

        if ($memberID) {
            $conditions[] = 'di.`memberID`=:memberID';
            $params[':memberID'] = trim($memberID);
        }

        if ($name) {
            $conditions[] = 'd.`member_name`=:member_name';
            $params[':member_name'] = trim($name);
        }

        if ($charge_name && $charge_name!='充值规则') {
            $conditions[] = 'd.`charge_name`=:charge_name';
            $params[':charge_name'] = trim($charge_name);
        }

        if ($charge_state && $charge_state!='订单状态') {
            $conditions[] = 'd.`charge_state`=:charge_state';
            $params[':charge_state'] = trim($charge_state);
        }

        if ($identity) {
            $conditions[] = 'di.`register_channel`=:register_channel';
            $params[':register_channel'] = trim($identity);
        }

        if ($identity) {
            $conditionss[] = 'di.`register_channel`=:register_channel';
            $paramss[':register_channel'] = trim($identity);
        }

        if ($identity) {
            $conditionss[] = 'd.`charge_state`=:charge_state';
            $paramss[':charge_state'] = 1;
        }

        if($inviter) {
            $m_sql = "select id from t_member WHERE memberID ='$inviter'";
            $m_data = Yii::$app->db->createCommand($m_sql)->queryAll();
            if(empty($m_data)){

            }else{
                $in_memberID = $m_data[0]['id'];
                $sql = "select member_id from member_registe_channel WHERE inviter='$in_memberID'";
                $data = Yii::$app->db->createCommand($sql)->queryAll();
                $ids = '';
                foreach($data as $k=>$v){
                    $member_id = $v['member_id'];
                    $ids .= $member_id.',';
                }
                $ids = rtrim($ids,',');
                if(empty($ids)){

                }else{
                    $conditions[] = "di.`id` in ($ids)";
                    $conditionss[] = "di.`id` in ($ids)";
                }
            }
        }

        if ($export !== 'on') {
            $export = false;
        }

        $charge_sql = 'SELECT COUNT(*) FROM charge_order d'
            . ' LEFT JOIN t_member di ON d.member_id=di.id'
            . ($conditions ? ' WHERE ' . implode(' AND ', $conditions) : '');
        $counts = $db->createCommand($charge_sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount' => $counts
        ]);
        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $charge_sql = 'SELECT d.*,ti.inviter,ti.rank,di.memberID FROM charge_order d'
            . ' LEFT JOIN t_member di ON d.member_id=di.id'
            . ' LEFT JOIN member_registe_channel ti ON d.member_id=ti.member_id'
            . ($conditions ? ' WHERE ' . implode(' AND ', $conditions) : '')
            . "  ORDER BY d.id DESC" . ($export ? '' : " limit $offset,$size ");
        $charger_rows = $db->createCommand($charge_sql, $params)->queryAll();

        $catch_sql = 'SELECT d.*,ti.inviter,ti.rank FROM charge_order d'
            . ' LEFT JOIN t_member di ON d.member_id=di.id'
            . ' LEFT JOIN member_registe_channel ti ON d.member_id=ti.member_id'
            . ($conditionss ? ' WHERE ' . implode(' AND ', $conditionss) : '');
        $catch_rows = $db->createCommand($catch_sql, $paramss)->queryAll();

        $pay = [];
        foreach($catch_rows as $key=>$v){
            $price = $v['price'];
            array_unshift($pay,$price);
        }
        $catch_count = count($pay);
        $sum = array_sum($pay);


        if ($export) {
            $filename = "charge_" . date("Y-m-d H:i:s");
            foreach ($charger_rows as $row) {
                if($row['charge_state'] ==0){
                    $state = "未完成";
                }else{
                    $state = "已完成";
                }
                $items[] = [
                    'id' => $row['id'],
                    '订单编号' => $row['order_no'],
                    '充值规则' => $row['charge_name'],
                    '充值金额' => $row['price'],
                    '用户id' => $row['member_id'],
                    '用户名' => $row['member_name'],
                    '订单状态' => $state,
                    '充值前' => $row['coins_before'],
                    '充值' => $row['coins_after'],
                    '赠送' => $row['coins_charge'],
                    '充值时间' => $row['create_date']
                ];
            }
            $this->_setcsvHeader("{$filename}.csv");
            echo $this->_array2csv($items);
            Yii::$app->end();
        }
        $sql = "SELECT charge_name FROM t_charge_rules WHERE rules_status=1 ";
        $chargeNames = $db->createCommand($sql)->queryAll();

        return $this->render('charge', [
            'rows' => $charger_rows,
            'pages' => $pages,
            'count' => $counts,
            'catch_count' => $catch_count,
            'sum' => $sum,
            'chargeNames' => $chargeNames,
        ]);
    }
}