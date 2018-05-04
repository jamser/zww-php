<?php
namespace channel\controllers;

use common\models\ChargeOrder;
use common\models\DollCatchHistory;
use common\models\Member;
use common\models\User;
use Yii;
use yii\web\Controller;

class FloreaController extends Controller{
    public function actionIndex()
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

        return $this->render('index', [
            'rows' => $rows,
            'pages' => $pages,
            'count' => $count,
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
        $Info = ChargeOrder::find()->where(['id'=>$id])->asArray()->one();
        return $this->render('catch',[
            'Info' => $Info,
        ]);
    }

    public function actionCharge(){
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

        return $this->render('charge', [
            'rows' => $charger_rows,
            'pages' => $pages,
            'count' => $counts,
            'catch_count' => $catch_count,
            'sum' => $sum,
        ]);
    }
}