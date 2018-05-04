<?php

namespace backend\modules\erp\controllers;

use backend\modules\doll\models\DollOrderGoodsDetain;
use backend\modules\erp\models\DollInfo;
use backend\modules\erp\models\DollOrder;
use common\models\Member;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\models\PublishAssetForm;
use common\helpers\MyFunction;
use backend\modules\erp\models\DollOrderGoods;
use OSS\OssClient;
use yii\web\UploadedFile;
require_once("../Classes/PHPExcel.php");

class OrderController extends Controller
{
    public $enableCsrfValidation = false;

    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','add','delivery','export','export-delivery','export-test','export-unshipped','search', 'search1', 'search2','update','un-charge','info','cost','test-add','detain','detain-order','order','detain-update','add-detain'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    public function actions()
    {
        return [
            'error' => ['class' => 'yii\web\ErrorAction'],
        ];
    }

//    展示出所有订单数据
    public function actionIndex()
    {
        $db = Yii::$app->db;
        /*
        $data = Yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
    select e.allDoll from (
        select order_id,group_concat((c.dollNames)) allDoll from (
        select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id ")->queryAll();
        $count = count($data);
        
        

        $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';')
                dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,
                g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d .`status`,d.order_date
                from t_doll_order d 
                left join t_member_addr ma ON (d.order_by=ma.member_id AND ma.default_flg=1)
                limit $offset,$size ";
        $datas = $db->createCommand($sql)->queryAll();
//                print_r($datas);die;
         * 
         * $sql = "SELECT d.*,
ma.receiver_name,
ma.receiver_phone,
ma.province,ma.city,ma.county,ma.street
d.`status`,
d.order_date,
di.dollName
from t_doll_order d 
LEFT JOIN t_member_addr ma ON (d.order_by=ma.member_id AND ma.default_flg=1)
LEFT JOIN t_doll_order_item doi ON d.id=doi.order_id
LEFT JOIN doll_info di ON di.dollCode=doi.doll_id
limit $offset,$size";
        */
        $status = Yii::$app->getRequest()->get('status',null);
        $startTime = Yii::$app->getRequest()->get('startTime',null);
        $endTime = Yii::$app->getRequest()->get('endTime',null);
        $phone = Yii::$app->getRequest()->get('phone',null);
        $receiveUser = Yii::$app->getRequest()->get('receiveUser',null);
        $orderNo = Yii::$app->getRequest()->get('orderNo',null);
        $deliverNo = Yii::$app->getRequest()->get('deliverNo',null);
        $userId = Yii::$app->getRequest()->get('userId',null);
        $export = Yii::$app->getRequest()->get('export',null);
        $exportMerge = Yii::$app->getRequest()->get('exportMerge',null);
        $fkcheck = Yii::$app->getRequest()->get('id',null);
        $merge = Yii::$app->getRequest()->get('merge',null);
        $allmerge = Yii::$app->getRequest()->get('allmerge',null);
        $split = Yii::$app->getRequest()->get('split', null);
        $agency = Yii::$app->getRequest()->get('agency', null);
        $detain = Yii::$app->getRequest()->get('detain', null);
        $last = Yii::$app->getRequest()->get('last', null);
        $conditions = $params = [];
        if($status && $status!='全部') {
            $conditions[] = '`status`=:status';
            $params[':status'] = trim($status);
        }
        
        if($startTime) {
            $conditions[] = '`order_date`>=:startTime';
            $params[':startTime'] = trim($startTime);
        }

        if($last){
            $conditions[] = '`order_date`>=:startTime';
            $params[':startTime'] = date('Y-m-d 00:00:00',time()-86400*8);
            $conditions[] = '`order_date`<=:endTime';
            $params[':endTime'] = date('Y-m-d 23:59:59',time()-86400);
        }
        
        if($endTime) {
            $conditions[] = '`order_date`<=:endTime';
            $params[':endTime'] = trim($endTime);
        }
        
        if($receiveUser) {
            $conditions[] = '`receiver_name`=:receiver_name';
            $params[':receiver_name'] = trim($receiveUser);
        }
        
        if($phone) {
            $conditions[] = '`receiver_phone`=:phone';
            $params[':phone'] = trim($phone);
        }
        
        if($orderNo) {
            $conditions[] = '`order_number`=:order_number';
            $params[':order_number'] = 'goods_'.trim($orderNo);
        }
        if($deliverNo) {
            $conditions[] = '`deliver_number`=:deliver_number';
            $params[':deliver_number'] = trim($deliverNo);
        }

        if($userId){
            $member = \common\models\Member::find()->where('memberID=:memberId',[':memberId'=>$userId])->one();
            if($member) {
                $member_id = $member->id;
                $conditions[] = '`member_id`=:member_id';
                $params[':member_id'] = trim($member_id);
            }
        }
        if($export!=='on') {
            $export = false;
        }
        if($exportMerge!=='on') {
            $exportMerge = false;
        }
        if($merge!=='on'){
            $merge = false;
        }
        if($allmerge!=='on'){
            $allmerge = false;
        }
        if($allmerge) {
            if(!$startTime || !$endTime) {
                throw new \Exception('必须要输入开始和结束时间才能合并订单');
            }
        }

        if($detain) {
            if(!$startTime || !$endTime) {
                throw new \Exception('必须要输入开始和结束时间才能扣留订单');
            }
        }

        if($split!=='on'){
            $split = false;
        }

        if($agency!=='on'){
            $agency = false;
        }
        
        if($fkcheck){
            $ids = explode(',', trim($fkcheck));
            foreach ($ids as $key=>$id) {
                $ids[$key] = (int)$id;
            }
            $conditions[] = '`id` in ('.implode(',',$ids).')';
        }
        if($split) {
            if(!$startTime || !$endTime) {
                throw new \Exception('必须要输入开始和结束时间才能分割订单');
            } 
        }

        if($agency) {
            if(!$startTime || !$endTime) {
                throw new \Exception('必须要输入开始和结束时间才能合并订单');
            }
        }
        
        $sql = 'SELECT COUNT(*) FROM doll_order_goods '.($conditions ? 'WHERE'.implode(' AND ', $conditions) : '');
        $count = $db->createCommand($sql, $params)->queryScalar();
        
        $pages = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);
        
        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $white_sql = "select user_id from member_white_list";
        $whiteData = $db->createCommand($white_sql)->queryAll();
        $white_id = '';
        foreach($whiteData as $k=>$v){
            $id = $v['user_id'];
            $white_id .=$id.',';
        }
        $white_id = rtrim($white_id,',');
        if($export){
            $conditions[] = '`member_id` not in ('.$white_id.')';
        }

        if($allmerge){
            $sql = 'SELECT d.*,di.memberID FROM doll_order_goods d'
                . ' LEFT JOIN t_member di ON d.member_id=di.id '
                .($conditions ? 'WHERE d.'.implode(' AND ', $conditions) : '');
            $rows = $db->createCommand($sql, $params)->queryAll();
        }elseif($export){
            $sql = 'SELECT DISTINCT(d.id),d.*,di.memberID,ti.agency FROM doll_order_goods d'
                . ' LEFT JOIN t_member di ON d.member_id=di.id '
                . " LEFT JOIN doll_info ti ON d.dolls_info LIKE concat('%',ti.dollCode,'%')"
                .($conditions ? 'WHERE d.'.implode(' AND ', $conditions) : '')
                . "  ORDER BY id DESC".( ($export || $split) ?  '' : " limit $offset,$size ");
            $rows = $db->createCommand($sql, $params)->queryAll();
        }else{
            $sql = 'SELECT d.*,di.memberID FROM doll_order_goods d'
                . ' LEFT JOIN t_member di ON d.member_id=di.id '
                .($conditions ? 'WHERE d.'.implode(' AND ', $conditions) : '')
                . "  ORDER BY id DESC".( ($export || $split) ?  '' : " limit $offset,$size ");
            $rows = $db->createCommand($sql, $params)->queryAll();
        }
        
        $myfunction = new MyFunction();
        
        #分拆订单
        if($split) {
            $sqls = 'SELECT d.*,di.memberID FROM doll_order_goods d'
                . ' LEFT JOIN t_member di ON d.member_id=di.id '
                .($conditions ? 'WHERE d.'.implode(' AND ', $conditions) : '')
                . "  ORDER BY id DESC".( ($export || $split) ?  '' : " limit $offset,$size ");
            $rowss = $db->createCommand($sqls, $params)->queryAll();
            $this->splitOrders($rowss);
            $redirectParams = ['index'] + $_GET;
            unset($redirectParams['split']);
            return $this->redirect($redirectParams);
        }

        #按经销商合并订单
        if($agency){
            $sqla = 'SELECT DISTINCT(d.id),d.*,di.memberID,ti.agency FROM doll_order_goods d'
                . ' LEFT JOIN t_member di ON d.member_id=di.id '
                . " LEFT JOIN doll_info ti ON d.dolls_info LIKE concat('%',ti.dollCode,'%')"
                .($conditions ? 'WHERE d.'.implode(' AND ', $conditions) : '')
                . "  ORDER BY id DESC";
            $rowsa = $db->createCommand($sqla, $params)->queryAll();
            $this->agencyOrders($rowsa);
            $redirectParams = ['index'] + $_GET;
            unset($redirectParams['agency']);
            return $this->redirect($redirectParams);
        }

        //一键扣留订单
        if($detain){
            $sql = 'SELECT d.*,di.memberID FROM doll_order_goods d'
                . ' LEFT JOIN t_member di ON d.member_id=di.id '
                .($conditions ? 'WHERE d.'.implode(' AND ', $conditions) : '');
            $rows = $db->createCommand($sql, $params)->queryAll();
            $unMembers = $this->unusualMember();
            $detain_reason = '账号有多个id';
            foreach($rows as $k=>$v){
                $phone = $v['receiver_phone'];
                $order_id = $v['id'];
                $detain = $v['detain'];
                if(isset($unMembers[$phone]) && $detain != 1){
                    $this->actionDetain($order_id,$detain_reason);
                }else{
                    continue;
                }
            }
        }

        #发货时间剩余一天将订单日期改成当前
        if($last){
            $sql_num = 'SELECT d.*,di.memberID FROM doll_order_goods d'
                . ' LEFT JOIN t_member di ON d.member_id=di.id '
                .($conditions ? 'WHERE d.'.implode(' AND ', $conditions) : '');
            $rows_num = $db->createCommand($sql_num, $params)->queryAll();
            foreach($rows_num as $k=>$v){
                //发货天数计算
                $order_date = $v['order_date'];
                $userid = $v['member_id'];
                $order_time = strtotime($order_date);
                $last_dayNum = (time()-$order_time)/86400;//申请发货时间距今是多少天
                $user_level = $this->level($userid);
                $redis = Yii::$app->redis;
                $old_level = $redis->get($userid);
                $old_dayNum = $this->dayNum($old_level);
                $new_dayNum = $this->dayNum($user_level);
                if($old_level && $old_level != $user_level){
                    $day_num = ($old_dayNum-$last_dayNum)-$old_dayNum+$new_dayNum;
                    $day_num = ceil($day_num);
                    if($day_num < 0){
                        $day_num = 0;
                    }
                }else{
                    $redis->set($userid,$user_level);
                    $day_num = $new_dayNum-$last_dayNum;
                    $day_num = ceil($day_num);
                    if($day_num < 0){
                        $day_num = 0;
                    }
                }

                if($day_num == 0){
                    $new_order_date = date('Y-m-d H:i:s',time()-86400);
                    $orderid = $v['id'];
                    $sql_d = "update doll_order_goods set order_date='$new_order_date' WHERE id=$orderid";
                    Yii::$app->db->createCommand($sql_d)->execute();
                }else{
                    continue;
                }

            }
        }

        $receiverMap = [];
        foreach($rows as $key=>$row) {
            $dollItemIds = trim($row['dollitemids'],',');
            if($dollItemIds) {
                $dollItems = $db->createCommand('SELECT d.*,di.dollName FROM t_doll_order_item d'
                    . ' LEFT JOIN doll_info di ON d.doll_code=di.dollCode '
                    . ' where d.id in ('. $dollItemIds.')')->queryAll();
                $rows[$key]['dollItems'] = $row['dollItems'] = $dollItems;
            } else {
                $rows[$key]['dollItems'] = $row['dollItems'] = [];
            }

            $address = $row['province'].$row['city'].$row['county'].$row['street'];
            $rows[$key]['receiver_address'] = $row['receiver_address'] = $address;

            $receiverKey = md5("{$row['receiver_name']}:{$row['receiver_phone']}:{$address}:{$row['status']}");
            $receiverMap[$receiverKey][] = $row;
        }
        
        $sender = '365抓娃娃';
        $senderPhone = '17601323004';
        $senderAddress = '上海市金山区365抓娃娃';

        if($merge){
            foreach($rows as $key=>$v){
                $id = $v['id'];
                $date = $v['created_date'];
                $json = json_encode($v, JSON_UNESCAPED_UNICODE);
                $myfunction->mergeOrder($id,$date,$json);
            }

            foreach($receiverMap as $key=>$receiverRows) {
                $orderNos = $dollInfos = $dollitemids = [];
                $dollitemid = '';
                foreach ($receiverRows as $row) {
                    $dollitemid .= $row['dollitemids'];
                    $orderNos = substr($row['order_number'], 6);
                    $dollInfo = '';
                    $dollInfo_y = [];
                    $dollInfo_c = [];
                    foreach ($row['dollItems'] as $dollItem) {
                        $dollInfo .= "{$dollItem['dollName']}({$dollItem['doll_code']}) * {$dollItem['quantity']}；";
                        $dollInfo_y[] = "{$dollItem['dollName']}({$dollItem['doll_code']})";
                        $dollInfo_c[] = "{$dollItem['doll_code']}";
                    }
                    $dollInfos[] = $dollInfo;
                }
                $dollitemids[] = $dollitemid;

                $dolls = array_count_values($dollInfo_y);
                foreach($dolls as $k=>$v){
                    $data = $k."*".$v;
                }

                $dollCodes = array_count_values($dollInfo_c);
                foreach($dollCodes as $k=>$v){
                    $sql_c = "select dollCoins,deliverCoins from doll_info WHERE dollCode='$k'";
                    $coinData = Yii::$app->db->createCommand($sql_c)->queryAll();
                    if(empty($coinData)){
                        $dollCoins = 0;
                        $deliverCoins = 0;
                    }else{
                        $dollCoins = $coinData[0]['dollCoins'];
                        $deliverCoins = $coinData[0]['deliverCoins'];
                    }
                    $dollCost = $v*($dollCoins+$deliverCoins);
                }

                $infos = implode(' ', $dollInfos);

                $updated = false;
                foreach ($receiverRows as $row) {
                    if(!$updated) {
                        //update
                        $myfunction->updateDoll($dollitemids[0], $infos,$row['id']);
                        $updated = true;
                    } else {
                        //delete
                        $myfunction->deleteDoll($row['id']);
                    }
                }
            }
        }

        if($allmerge){
            foreach($rows as $key=>$v){
                $id = $v['id'];
                $date = $v['created_date'];
                $json = json_encode($v, JSON_UNESCAPED_UNICODE);
                $myfunction = new MyFunction();
                $myfunction->mergeOrder($id,$date,$json);
            }

            foreach($receiverMap as $key=>$receiverRows) {
                $orderNos = $dollInfos = $dollitemids = [];
                $dollitemid = '';
                foreach ($receiverRows as $row) {
                    $dollitemid .= $row['dollitemids'];
                    $orderNos = substr($row['order_number'], 6);
                    $dollInfo = '';
                    $dollInfo_y = [];
                    $dollInfo_c = [];
                    foreach ($row['dollItems'] as $dollItem) {
                        $dollInfo .= "{$dollItem['dollName']}({$dollItem['doll_code']}) * {$dollItem['quantity']}；";
                        $dollInfo_y[] = "{$dollItem['dollName']}({$dollItem['doll_code']})";
                        $dollInfo_c[] = "{$dollItem['doll_code']}";
                    }
                    $dolls = array_count_values($dollInfo_y);
                    foreach($dolls as $k=>$v){
                        $data = $k."*".$v;
                    }

                    $dollCodes = array_count_values($dollInfo_c);
                    foreach($dollCodes as $k=>$v){
                        $sql_c = "select dollCoins,deliverCoins from doll_info WHERE dollCode='$k'";
                        $coinData = Yii::$app->db->createCommand($sql_c)->queryAll();
                        if(empty($coinData)){
                            $dollCoins = 0;
                            $deliverCoins = 0;
                        }else{
                            $dollCoins = $coinData[0]['dollCoins'];
                            $deliverCoins = $coinData[0]['deliverCoins'];
                        }
                        $dollCost = $v*($dollCoins+$deliverCoins);
                    }
                    $dollInfos[] = $dollInfo;
                }
                $dollitemids[] = $dollitemid;

                $infos = implode(' ', $dollInfos);

                $updated = false;
                if(count($receiverRows)>1){
                    foreach ($receiverRows as $row) {
                        if(!$updated) {
                            //update
                            try {
                                $myfunction = new MyFunction();
                                $myfunction->updateDoll($dollitemids[0], $infos,$row['id']);
                            } catch (\Exception $e) {
                                throw $e;
//                            throw new BadRequestHttpException($e->getMessage());
                            }
                            $updated = true;
                        } else {
                            //delete
                            try {
                                $myfunction = new MyFunction();
                                $myfunction->deleteDoll($row['id']);
                            } catch (\Exception $e) {
//                            throw $e;
                                throw new \Exception();
//                            throw new BadRequestHttpException($e->getMessage());
                            }

                        }
                    }
                }
            }
        }
        
        if($export) {
            $filename = "orders_".date("Y-m-d H:i:s");
            
            $items = [];
            if($exportMerge) {
                $filename = "orders_".date("Y-m-d H:i:s");
                foreach($receiverMap as $key=>$receiverRows) {
                    $orderNos = $dollInfos = [];
                    $dollitemid = '';
                    foreach ($receiverRows as $row) {
                        $dollitemid .=$row['dollitemids'];
                        $orderNos[] = substr($row['order_number'],6);
                        $dollInfo = '';
                        $dollInfo_y = [];
                        $dollInfo_c = [];
                        foreach($row['dollItems'] as $dollItem) {
                            $dollInfo .= "{$dollItem['dollName']}({$dollItem['doll_code']}) * {$dollItem['quantity']}；";
                            $dollInfo_y[] = "{$dollItem['dollName']}({$dollItem['doll_code']})";
                            $dollInfo_c[] = "{$dollItem['doll_code']}";
                        }
                        $dolls = array_count_values($dollInfo_y);
                        foreach($dolls as $k=>$v){
                            $data = $k."*".$v;
                        }
                        $dollCodes = array_count_values($dollInfo_c);
                        foreach($dollCodes as $k=>$v){
                            $sql_c = "select dollCoins,deliverCoins from doll_info WHERE dollCode='$k'";
                            $coinData = Yii::$app->db->createCommand($sql_c)->queryAll();
                            if(empty($coinData)){
                                $dollCoins = 0;
                                $deliverCoins = 0;
                            }else{
                                $dollCoins = $coinData[0]['dollCoins'];
                                $deliverCoins = $coinData[0]['deliverCoins'];
                            }
                            $dollCost = $v*($dollCoins+$deliverCoins);
                        }
                        $dollInfos[] = $dollInfo;
                    }


                    $items[] = [
                        '订单号'=> implode('；', $orderNos),
                        '发货地'=>$row['agency'],
                        '发件人姓名'=>$sender,
                        '发件人手机'=>$senderPhone,
                        '发件人详细地址'=>$senderAddress,
//                        '自定义区域1*（商品编码、娃娃名称、数量）'=> implode(' ', $dollInfos),
                        '自定义区域1*（商品编码、娃娃名称、数量）'=> $data,
                        '收件人姓名'=>$row['receiver_name'],
                        '收件人手机'=>$row['receiver_phone'],
                        '收件人详细地址'=>$row['receiver_address'],
                        '自定义区域'=>'',
                        '日期'=>$row['order_date']
                    ];
                }
            } else {
                $member_ids = '';
                foreach($rows as $k=>$v){
                    $user_id = $v['member_id'];
                    $member_ids .= $user_id .',';
                }
                $member_ids =rtrim($member_ids, ',');
                $unData = $this->unusualData($member_ids);
                $unMembers = $this->unusualMember();
                $unRate = $this->unRate($member_ids);
                $unRate1 = $this->unRate1($member_ids);

                foreach ($rows as $row) {
                    $dollInfo = '';
                    $dollInfo_y = [];
                    $dollInfo_c = [];
                    foreach($row['dollItems'] as $dollItem) {
                        $dollInfo .= "{$dollItem['dollName']}({$dollItem['doll_code']}) * {$dollItem['quantity']}；";
                        $dollInfo_y[] = "{$dollItem['dollName']}({$dollItem['doll_code']})";
                        $dollInfo_c[] = "{$dollItem['doll_code']}";
                    }

                    $dolls = array_count_values($dollInfo_y);
                    $dolls_num = count($dolls);
                    if($dolls_num>1){
                        $data = '';
                        foreach($dolls as $k=>$v){
                            $data .= $k."*".$v.";";
                        }
                    }else{
                        foreach($dolls as $k=>$v){
                            $data = $k."*".$v;
                        }
                    }

                    $dollCodes = array_count_values($dollInfo_c);
                    $Costs = [];
                    foreach($dollCodes as $k=>$v){
                        $sql_c = "select dollCoins,deliverCoins from doll_info WHERE dollCode='$k'";
                        $coinData = Yii::$app->db->createCommand($sql_c)->queryAll();
                        if(empty($coinData)){
                            $dollCoins = 0;
                            $deliverCoins = 0;
                        }else{
                            $dollCoins = $coinData[0]['dollCoins'];
                            $deliverCoins = $coinData[0]['deliverCoins'];
                        }
                        $dollCost = $v*($dollCoins+$deliverCoins);
                        array_push($Costs,$dollCost);
                    }
                    $dollCost = array_sum($Costs);

                    //id异常，一个手机对应多个id
                    $phone = $row['receiver_phone'];
                    if(isset($unMembers[$phone])){
                        $num = $unMembers[$phone];
                        $id_message = $num;
                    }else{
                        $id_message = ' ';
                    }

                    //寄存箱娃娃数量异常
                    $id = $row['memberID'];
                    if(isset($unData[$id]) && $unData[$id] > 5){
                        $message1 = $unData[$id];
                    }else{
                        $message1 = ' ';
                    }

                    //抓中概率异常
                    if(isset($unRate[$id]) && isset($unRate1[$id])){
                        $rate = round(($unRate[$id]/$unRate1[$id])*100,2);
                        $message2 = $rate;
                    }else{
                        $message2 = ' ';
                    }

                    $mobile =$row['receiver_phone'];
                    $charge_num = $this->unCharge($mobile);

                    $userid = $row['member_id'];
                    $allCost = $this->cost($userid);

                    $Cost = $allCost+$dollCost;
                    $userCharge = $this->charge($userid);
                    $userCharge = $userCharge/2;
                    if($Cost>$userCharge){
                        $message3 = '异常';
                    }else{
                        $message3 = ' ';
                    }

                    //发货天数计算
                    $order_date = $row['order_date'];
                    $order_time = strtotime($order_date);
                    $last_dayNum = (time()-$order_time)/86400;//申请发货时间距今是多少天
                    $user_level = $this->level($userid);
                    $redis = Yii::$app->redis;
                    $old_level = $redis->get($userid);
                    $old_dayNum = $this->dayNum($old_level);
                    $new_dayNum = $this->dayNum($user_level);
                    if($old_level && $old_level != $user_level){
                        $day_num = ($old_dayNum-$last_dayNum)-$old_dayNum+$new_dayNum;
                        $day_num = ceil($day_num);
                        if($day_num < 0){
                            $day_num = 0;
                        }
                    }else{
                        $redis->set($userid,$user_level);
                        $day_num = $new_dayNum-$last_dayNum;
                        $day_num = ceil($day_num);
                        if($day_num < 0){
                            $day_num = 0;
                        }
                    }

//                    if($day_num == 0){
//                        $new_order_date = date('Y-m-d H:i:s',time());
//                        $orderid = $row['id'];
//                        $sql_d = "update doll_order_goods set order_date='$new_order_date' WHERE id=$orderid";
//                        Yii::$app->db->createCommand($sql_d);
//                    }

                    $items[] = [
                        '订单号'=>substr($row['order_number'],6),
                        '发货地'=>$row['agency'],
                        '发件人姓名'=>$sender,
                        '发件人手机'=>$senderPhone,
                        '发件人详细地址'=>$senderAddress,
                        '自定义区域1*（商品编码、娃娃名称、数量）'=> $data,
                        '收件人姓名'=>$row['receiver_name'],
                        '收件人手机'=>$row['receiver_phone'],
                        '收件人详细地址'=>$row['receiver_address'],
                        '用户备注'=>$row['note'],
                        '日期'=>$row['order_date'],
                        '手机号对应多个id'=>$id_message,
                        '手机号对应的多个id总充值'=>$charge_num,
                        '寄存箱娃娃异常'=>$message1,
                        '抓中概率异常'=>$message2,
                        '当前申请发货成本'=>$dollCost,
                        '已发货成本'=>$allCost,
                        '成本异常'=>$message3,
                        '剩余发货天数'=>$day_num,
                        '用户ID'=>$row['memberID'],
                    ];

//                    if($day_num == 0){
//                        $items[] = [
//                            '订单号'=>substr($row['order_number'],6),
//                            '发货地'=>$row['agency'],
//                            '发件人姓名'=>$sender,
//                            '发件人手机'=>$senderPhone,
//                            '发件人详细地址'=>$senderAddress,
//                            '自定义区域1*（商品编码、娃娃名称、数量）'=> $data,
//                            '收件人姓名'=>$row['receiver_name'],
//                            '收件人手机'=>$row['receiver_phone'],
//                            '收件人详细地址'=>$row['receiver_address'],
//                            '用户备注'=>$row['note'],
//                            '日期'=>$row['order_date'],
//                            '手机号对应多个id'=>$id_message,
//                            '手机号对应的多个id总充值'=>$charge_num,
//                            '寄存箱娃娃异常'=>$message1,
//                            '抓中概率异常'=>$message2,
//                            '当前申请发货成本'=>$dollCost,
//                            '已发货成本'=>$allCost,
//                            '成本异常'=>$message3,
//                            '剩余发货天数'=>$day_num,
//                            '用户ID'=>$row['memberID'],
//                        ];
//                    }else{
//                        continue;
//                    }

                }
            }
            $this->_setcsvHeader("{$filename}.csv");
            echo $this->_array2csv($items);
            Yii::$app->end();
        }else{
            //用户充值和抓取
            $charges = $catchs = $s_catchs = $j_data =[];
            $member_ids = $memberIDs = '';
            foreach($rows as $k=>$v){
                $user_id = $v['member_id'];
                $memberID = $v['memberID'];
                $member_ids .= $user_id .',';
                $memberIDs .= $memberID .',';
            }
            $member_ids =rtrim($member_ids, ',');
            if(empty($member_ids)){
                throw new \Exception('未找到相关用户信息');
            }else{
                $sql = "select di.memberID,sum(d.price) price from charge_order d LEFT JOIN t_member di ON d.member_id=di.id  WHERE d.member_id IN ($member_ids)  AND d.charge_state=1 GROUP BY d.member_id";
                $charge = $db->createCommand($sql)->queryAll();
                foreach($charge as $k=>$v){
                    $id = $v['memberID'];
                    $price = $v['price'];
                    $charges[$id] = $price;
                }
                $c_sql = "select t.memberID,count(d.member_id) num from t_doll_catch_history d LEFT JOIN t_doll di ON d.doll_id=di.id LEFT JOIN t_member t ON d.member_id=t.id
                    WHERE d.member_id IN ($member_ids) AND d.catch_status='抓取成功' AND di.machine_type NOT IN (1,3) GROUP BY d.member_id";
                $catch = $db->createCommand($c_sql)->queryAll();
                foreach($catch as $k=>$v){
                    $id = $v['memberID'];
                    $num = $v['num'];
                    $catchs[$id] = $num;
                }

                $unusualData = $this->unusualMember();

                $s_sql = "select t.memberID,count(d.member_id) num from t_doll_catch_history d LEFT JOIN t_doll di ON d.doll_id=di.id LEFT JOIN t_member t ON d.member_id=t.id
                    WHERE d.member_id IN ($member_ids) AND di.machine_type NOT IN (1,3) GROUP BY d.member_id";
                $s_catch = $db->createCommand($s_sql)->queryAll();
                foreach($s_catch as $k=>$v){
                    $id = $v['memberID'];
                    $num = $v['num'];
                    $s_catchs[$id] = $num;
                }

                $j_sql = "select di.memberID,count(di.id) num from t_doll_order d LEFT JOIN t_member di ON d.order_by=di.id WHERE d.order_by IN ($member_ids) AND d.status IN ('寄存中','申请发货') GROUP BY di.id";
                $j_data = $db->createCommand($j_sql)->queryAll();
                foreach($j_data as $k=>$v){
                    $id = $v['memberID'];
                    $num = $v['num'];
                    $j_data[$id] = $num;
                }
            }

//            $charges = $catchs = [];
//            foreach($rows as $k=>$v){
//                $user_id = $v['member_id'];
//                $memberID = $v['memberID'];
//                $sql = "select sum(price) price from charge_order WHERE member_id=$user_id AND charge_state=1";
//                $charge = $db->createCommand($sql)->queryAll();
//                $charge = $charge[0]['price'];
//                $charges[$memberID] = $charge;
//                $c_sql = "select count(*) num from t_doll_catch_history d LEFT JOIN t_doll di ON d.doll_id=di.id WHERE d.member_id=$user_id AND d.catch_status='抓取成功' AND di.machine_type !=1";
//                $catch = $db->createCommand($c_sql)->queryAll();
//                $catch = $catch[0]['num'];
//                $catchs[$memberID] = $catch;
//            }
        }
        
        return $this->render('index', [
            'rows' => $rows,
            'pages'=>$pages,
            'sender'=>$sender,
            'senderPhone'=>$senderPhone,
            'senderAddress'=>$senderAddress,
            'charges'=>$charges,
            'catchs'=>$catchs,
            'unusualData'=>$unusualData,
            's_catchs'=>$s_catchs,
            'j_data'=>$j_data,
        ]);
    }
    
    protected function splitOrders($rows) {
        $db = Yii::$app->db;
        foreach($rows as $key=>$row){
            $dollItemIds = trim($row['dollitemids'],',');
            if($dollItemIds) {
                $dollItems = $db->createCommand('SELECT d.*,di.dollName FROM t_doll_order_item d'
                        . ' LEFT JOIN doll_info di ON d.doll_code=di.dollCode '
                        . ' where d.id in ('. $dollItemIds.')')->queryAll();
            } else {
                $dollItems = [];
            }

            if(count($dollItems)>1) {
                //超过一个订单 拆分成两个订单
                $diffDolls = [];
                foreach($dollItems as $dollItem) {
                    $diffDolls[$dollItem['doll_code']][] = $dollItem['id'];
                }

                if(count($diffDolls) > 1) {
                    //备份数据 
                    $id = $row['id'];
                    $date = $row['created_date'];
                    $json = json_encode($row, JSON_UNESCAPED_UNICODE);
                    $myfunction = new MyFunction();
                    $myfunction->mergeOrder($id,$date,$json);

                    //插入数据
                    $trans = $db->beginTransaction();

                    try {
                        $updated = false;
                        foreach($diffDolls as $dollCode=>$diffDollRows) {
                            $dollitemids = implode(',', $diffDollRows).',';
                            $dollInfos = "{$dollCode}*".count($diffDollRows);

                            //插入到数据库
                            if(!$updated) {
                                $updateSql = "update doll_order_goods set dollitemids = '$dollitemids',"
                                        . "dolls_info ='$dollInfos' WHERE id =".$row['id'];
                                $db->createCommand($updateSql)->execute();
                                Yii::error("Insert sql : {$updateSql}");
                                //echo $updateSql."<br/>\n";
                                $updated = true;
                            } else {
                                $params = [];
                                foreach($row as $k=>$v) {
                                    if(in_array($k, ['id','memberID'])) {
                                        continue;
                                    }
                                    $params[":{$k}"] = $v;
                                }
                                $params[':dollitemids'] = $dollitemids;
                                $params[':dolls_info'] = $dollInfos;
                                $params[':order_number'] = 'goods_'.strtotime($row['order_date']).rand(1000, 9999);
                                $params[':note'] = $row['note'];
                                $params[':detain'] = $row['detain'];
                                $insertSql = "INSERT INTO `doll_order_goods` (`order_number`, `order_date`, `member_id`, `status`, `stock_valid_date`, `deliver_date`, `deliver_method`, `deliver_number`, `deliver_amount`, `deliver_coins`, `dollitemids`, `dolls_info`, `receiver_name`, `receiver_phone`, `province`, `city`, `county`, `street`, `comment`, `created_date`, `modified_date`, `modified_by`, `note`, `detain`) VALUES
                                    (:order_number, :order_date, :member_id, :status, :stock_valid_date, :deliver_date, :deliver_method, :deliver_number, :deliver_amount, :deliver_coins, :dollitemids, :dolls_info, :receiver_name, :receiver_phone, :province, :city, :county, :street, :comment, :created_date, :modified_date, :modified_by, :note,:detain)";
                                Yii::error("插入参数为：".var_export($params,1)."; 插入SQL为 {$insertSql}");
                                $db->createCommand($insertSql,$params)->execute();
                                //echo $insertSql."<br/>\n";
                                //var_dump($params);
                            }
                        }
                        $trans->commit();
                    } catch (\Exception $ex) {
                        $trans->rollback();
                        throw $ex;
                    }
                }
                //echo "end<br/>\n";
            }

        }
    }

    //经销商合并
    protected function agencyOrders($rows){
        $db = Yii::$app->db;
        $key = [];
        foreach($rows as $k=>$v){
            $address = $v['province'].$v['city'].$v['county'].$v['street'];
            $receiverKey = md5("{$v['receiver_name']}:{$v['receiver_phone']}:{$address}:{$v['status']}:{$v['agency']}").$v['agency'];
            if(array_key_exists($receiverKey,$key)){
                //备份数据
                $id = $v['id'];
                $date = $v['created_date'];
                $json = json_encode($v, JSON_UNESCAPED_UNICODE);
                $myfunction = new MyFunction();
                $myfunction->mergeOrder($id,$date,$json);

                //插入数据
                $old_id =  $key[$receiverKey];
                $dollitemids = $dolls_info = '';
                $sql_old = "select dollitemids,dolls_info from doll_order_goods WHERE id=$old_id";
                $olddata = $db->createCommand($sql_old)->queryAll();
                if(empty($olddata)){
                    $dollitemid_old = '';
                    $doll_info_old='';
                }else{
                    $dollitemid_old =$olddata[0]['dollitemids'];
                    $doll_info_old=$olddata[0]['dolls_info'];
                }
                $dollitemids .=$dollitemid_old;
                $dolls_info .=$doll_info_old;
                $sql_new = "select dollitemids,dolls_info from doll_order_goods WHERE id=$id";
                $newdata = $db->createCommand($sql_new)->queryAll();
                if(empty($newdata)){
                    $dollitemid_new = '';
                    $doll_info_new='';
                }else{
                    $dollitemid_new =$newdata[0]['dollitemids'];
                    $doll_info_new=$newdata[0]['dolls_info'];
                }
                $dollitemids .=$dollitemid_new;
                $dolls_info .=$doll_info_new;

                //合并数据
                $trans = $db->beginTransaction();
                try{
                    $updateSql = "update doll_order_goods set dollitemids = '$dollitemids',dolls_info ='$dolls_info' WHERE id =$old_id";
                    $num = $db->createCommand($updateSql)->execute();
                    if($num){
                        $deleteSql = "delete from doll_order_goods WHERE id=$id";
                        $db->createCommand($deleteSql)->execute();
                    }
                    $trans->commit();
                } catch (\Exception $ex) {
                    $trans->rollback();
                    throw $ex;
                }
            }else{
                $key[$receiverKey] = $v['id'];
            }
        }
    }

    //同一个手机号对应多个id
    public function unusualMember(){
        $sql = "select COUNT(member_id) num,receiver_phone,member_id from doll_order_goods GROUP BY receiver_phone HAVING num>=3";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $phones = [];
        $mobiles = '';
        foreach($data as $k=>$v){
            $phone = $v['receiver_phone'];
            $mobiles .= $phone .',';
        }
        $mobiles = rtrim($mobiles,',');
        $sql = "select d.member_id,d.receiver_phone from doll_order_goods d left JOIN t_member di ON d.member_id=di.id WHERE receiver_phone IN ($mobiles) AND di.active_flg=1";
        $rows = Yii::$app->db->createCommand($sql)->queryAll();
        $members = $receiver_phones = [];
        foreach($rows as $k=>$v){
            $id = $v['member_id'];
            $receiver_phone = $v['receiver_phone'];
            if(in_array($id,$members)){
                continue;
            }else{
                array_push($members,$id);
                array_push($receiver_phones,$receiver_phone);
            }
        }
        $data = array_count_values($receiver_phones);
        foreach($data as $k=>$v){
            if($v>=3){
                $phones[$k] = $v;
            }else{
                continue;
            }
        }
        return $phones;
    }

    //同一个手机号对应多个id的所有充值情况
    public function unCharge($mobile){
        $sql = "select d.member_id from doll_order_goods d LEFT JOIN t_member di ON d.member_id=di.id WHERE receiver_phone = '$mobile' AND di.active_flg=1";
        $rows = Yii::$app->db->createCommand($sql)->queryAll();
        $charges = $members = [];
        foreach($rows as $k=>$v){
            $id = $v['member_id'];
            if(in_array($id,$members)){
                continue;
            }else{
                array_push($members,$id);
                $c_sql = "SELECT SUM(price) num FROM charge_order WHERE charge_state = 1 AND member_id='$id'";
                $c_rows = Yii::$app->db->createCommand($c_sql)->queryAll();
                if(empty($c_rows)){
                    $charge = 0;
                }else{
                    $charge = $c_rows[0]['num'];
                }
                array_push($charges,$charge);
            }
        }
        $num = array_sum($charges);
        return $num;
    }

    //寄存箱娃娃数量异常
    public function unusualData($member_ids){
        $data = [];
        $db = Yii::$app->db;
        $j_sql = "select di.memberID,count(di.id) num from t_doll_order d LEFT JOIN t_member di ON d.order_by=di.id WHERE d.order_by IN ($member_ids) AND d.status IN ('寄存中','申请发货') AND di.active_flg=1 GROUP BY di.id";
        $j_data = $db->createCommand($j_sql)->queryAll();
        foreach($j_data as $k=>$v){
            $id = $v['memberID'];
            $num = $v['num'];
            $data[$id] = $num;
        }
        return $data;
    }

    //用户抓中概率异常
    public function unRate($member_ids){
        $db = Yii::$app->db;
        $catchs = [];
        $c_sql = "select t.memberID,count(d.member_id) num from t_doll_catch_history d LEFT JOIN t_doll di ON d.doll_id=di.id LEFT JOIN t_member t ON d.member_id=t.id
                    WHERE d.member_id IN ($member_ids) AND d.catch_status='抓取成功' AND di.machine_type NOT IN (1,3) GROUP BY d.member_id";
        $catch = $db->createCommand($c_sql)->queryAll();
        foreach($catch as $k=>$v){
            $id = $v['memberID'];
            $num = $v['num'];
            $catchs[$id] = $num;
        }
        return $catchs;
    }

    //用户已发货成本
    public function cost($user_id){
        $sql = "select dollitemids from doll_order_goods WHERE member_id='$user_id' AND status = '已发货'";
        $rows = Yii::$app->db->createCommand($sql)->queryAll();
        $dollItemIds = '';
        foreach($rows as $k=>$v){
            $itemId = $v['dollitemids'];
            $dollItemIds .= $itemId;
        }
        $dollItemIds = rtrim($dollItemIds,',');
        if(empty($dollItemIds)){
            $allCost = 0;
        }else{
            $sql1 = "SELECT d.doll_code FROM t_doll_order_item d LEFT JOIN doll_info di ON d.doll_code=di.dollCode where d.id in ($dollItemIds)";
            $data = Yii::$app->db->createCommand($sql1)->queryAll();
            $costs = [];
            foreach($data as $k=>$v){
                $doll_code = $v['doll_code'];
                $sql_c = "select dollCoins,deliverCoins from doll_info WHERE dollCode='$doll_code'";
                $coinData = Yii::$app->db->createCommand($sql_c)->queryAll();
                if(empty($coinData)){
                    $dollCoins = 0;
                    $deliverCoins = 0;
                }else{
                    $dollCoins = $coinData[0]['dollCoins'];
                    $deliverCoins = $coinData[0]['deliverCoins'];
                }
                $cost = $dollCoins+$deliverCoins;
                array_push($costs,$cost);
            }
            $allCost = array_sum($costs);
        }
        return $allCost;
    }

    //单个用户充值
    public function charge($user_id){
        $db = Yii::$app->db;
        $sql = "select sum(price) price from charge_order WHERE member_id='$user_id' AND charge_state=1";
        $data = $db->createCommand($sql)->queryAll();
        if(empty($data)){
            $charge = 0;
        }else{
            $charge = $data[0]['price'];
        }
        return $charge;
    }

    public function unRate1($member_ids){
        $db = Yii::$app->db;
        $s_catchs = [];
        $s_sql = "select t.memberID,count(d.member_id) num from t_doll_catch_history d LEFT JOIN t_doll di ON d.doll_id=di.id LEFT JOIN t_member t ON d.member_id=t.id
                    WHERE d.member_id IN ($member_ids) AND di.machine_type NOT IN (1,3) GROUP BY d.member_id";
        $s_catch = $db->createCommand($s_sql)->queryAll();
        foreach($s_catch as $k=>$v){
            $id = $v['memberID'];
            $num = $v['num'];
            $s_catchs[$id] = $num;
        }
        return $s_catchs;
    }

    //计算用户等级、、这个值是我懒写死的
    public function level($member_id){
        $db = Yii::$app->db;
        $sql = "select growth_value from account WHERE id=$member_id";
        $account = $db->createCommand($sql)->queryAll();
        $growth_value = $account[0]['growth_value'];
        if($growth_value>=0 && $growth_value<50){
            $user_level = 0;
        }elseif($growth_value>=50 && $growth_value<100){
            $user_level = 1;
        }elseif($growth_value>=100 && $growth_value<500){
            $user_level = 2;
        }else{
            $user_level = 3;
        }
        return $user_level;
    }

    //不同等级的发货天数计算
    public function dayNum($level){
        if($level == 0){
            $dayNum = 7;
        }elseif($level == 1){
            $dayNum = 5;
        }elseif($level == 2){
            $dayNum = 3;
        }else{
            $dayNum = 0;
        }
        return $dayNum;
    }

    //导入物流单号
    public function actionAdd(){
        $model = new DollOrderGoods();
        if ($model->load(Yii::$app->request->post())) {
//            $accessKeyId = "LTAIiRG3VWVjAIpU";
//            $accessKeySecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
//            $endpoint = "http://oss-cn-shanghai.aliyuncs.com/";
//            $bucket = "zww-file";
//            $ossClient = new OssClient($accessKeyId,$accessKeySecret,$endpoint);
//            $object = "excels/".date('HiiHsHis').'.xlsx';
            $model->deliver_number = UploadedFile::getInstance($model, 'deliver_number');
            $excelInfo = $this->object2array($model->deliver_number);
            $excel_url = $excelInfo['tempName'];
//            $content = file_get_contents($excel_url);
//            print_r($content);die;
//            $ossClient->putObject($bucket, $object, $content);

//            $file_url = "http://zww-file.oss-cn-shanghai.aliyuncs.com/".$object;
//            $destination_folder = 'excels/';
//            $newfname = $destination_folder . basename($file_url);
//            $file = fopen ($file_url, "rb");
//            if ($file) {
//                $newf = fopen ($newfname, "wb");
//                if ($newf)
//                    while(!feof($file)) {
//                        fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
//                    }
//            }
//            $file_name = $object;
            //文件名为文件路径和文件名的拼接字符串
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');//创建读取实例
            /*
             * log()//方法参数
             * $file_name excal文件的保存路径
             */
            $objPHPExcel = $objReader->load($excel_url);//加载文件
            $sheet = $objPHPExcel->getSheet(0);//取得sheet(0)表
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            for($i=2;$i<=$highestRow;$i++)
            {
                $order_number = $sheet->getCell("B".$i)->getValue();
                $deliver_method = $sheet->getCell("C".$i)->getValue();
                $deliver_number= $sheet->getCell("D".$i)->getValue();
//                $orderData = DollOrder::find()->where(['order_number'=>$order_number])->asArray()->one();
                $order_number=sprintf("%08d", $order_number);
                $number = 'goods_'.$order_number;
                $orderData = DollOrderGoods::find()->where(['order_number'=>$number])->asArray()->one();//带goods的订单号
                if(empty($orderData)){
                    $db = Yii::$app->db_php;
                    $time = date('Y-m-d H:i:s',time());
                    $sql = "insert into no_order_number(no_order_number,no_deliver_method,no_deliver_number,create_date) VALUES ('$order_number','$deliver_method','$deliver_number','$time')";
                    $db->createCommand($sql)->execute();
                    continue;
                }else{
                    $myfunction =new MyFunction();
                    echo 1;
                $myfunction->orderStatus($order_number,$deliver_method,$deliver_number);//带goods的订单号
//                    $myfunction->updateOrder($order_number,$deliver_method,$deliver_number);//不是goods的订单号
                }
            }
            return $this->redirect('/erp/order/index');
        } else {
            return $this->render('add',[
                'model' => $model,
            ]);
        }
    }

    //导入扣留订单
    public function actionAddDetain(){
        $model = new DollOrderGoods();
        if ($model->load(Yii::$app->request->post())) {
            $model->deliver_number = UploadedFile::getInstance($model, 'deliver_number');
            $excelInfo = $this->object2array($model->deliver_number);
            $excel_url = $excelInfo['tempName'];
            //文件名为文件路径和文件名的拼接字符串
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');//创建读取实例
            /*
             * log()//方法参数
             * $file_name excal文件的保存路径
             */
            $objPHPExcel = $objReader->load($excel_url);//加载文件
            $sheet = $objPHPExcel->getSheet(0);//取得sheet(0)表
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            for($i=2;$i<=$highestRow;$i++)
            {
                $order_number = $sheet->getCell("B".$i)->getValue();
                $detain_reason= $sheet->getCell("C".$i)->getValue();
                $order_number=sprintf("%08d", $order_number);
                $number = 'goods_'.$order_number;
                $orderData = DollOrderGoods::find()->where(['order_number'=>$number])->asArray()->one();//带goods的订单号
                if(empty($orderData)){
                    continue;
                }else{
                    $order_id = $orderData['id'];
                    $this->actionDetain($order_id,$detain_reason);
                }
            }
            return $this->redirect('/erp/order/detain-order');
        } else {
            return $this->render('detain-add',[
                'model' => $model,
            ]);
        }
    }

    function object2array($object) {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        }
        else {
            $array = $object;
        }
        return $array;
    }

    public function actionInfo($id){
        $model = new DollOrderGoods();
        $orderInfo = DollOrderGoods::find()->where(['id'=>$id])->asArray()->one();
        $address = $orderInfo['province'].$orderInfo['city'].$orderInfo['county'].$orderInfo['street'];
        return $this->render('info',[
            'orderInfo' => $orderInfo,
            'address' => $address,
            'model' => $model,
        ]);
    }

    public function actionUpdate($id){
        $model = $this->findModel($id);
//        $model = new DollOrderGoods();
        $orderInfo = DollOrderGoods::find()->where(['id'=>$id])->asArray()->one();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('/erp/order/index');
        }else{
            return $this->render('update',[
                'orderInfo' => $orderInfo,
                'model' => $model,
            ]);
        }
    }

    //扣留表修改
    public function actionDetainUpdate($id){
        $model = $this->dfindModel($id);
        $orderInfo = DollOrderGoodsDetain::find()->where(['id'=>$id])->asArray()->one();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('/erp/order/detain-order');
        }else{
            return $this->render('detain_update',[
                'orderInfo' => $orderInfo,
                'model' => $model,
            ]);
        }
    }

    //扣留订单
    public function actionDetain($id,$detain_reason=''){
        $db = Yii::$app->db;
        $sql = "select * from doll_order_goods WHERE id=$id";
        $row = $db->createCommand($sql)->queryAll();
        $insert_sql = "insert into doll_order_goods_detain(id,order_number,order_date,member_id,status,stock_valid_date,deliver_date,deliver_method,deliver_number,deliver_amount,
                       deliver_coins,dollitemids,dolls_info,receiver_name,receiver_phone,province,city,county,street,comment,created_date,
                       modified_date,modified_by,note,detain_date,detain_reason) VALUES(:id,:order_number,:order_date,:member_id,:status,:stock_valid_date,:deliver_date,:deliver_method,:deliver_number,:deliver_amount,
                       :deliver_coins,:dollitemids,:dolls_info,:receiver_name,:receiver_phone,:province,:city,:county,:street,:comment,:created_date,
                       :modified_date,:modified_by,:note,:detain_date,:detain_reason)";
        $delete_sql = "delete from doll_order_goods WHERE id=:id";
        $db->createCommand($insert_sql,[
            ':id'=>$row[0]['id'],
            ':order_number'=>$row[0]['order_number'],
            ':order_date'=>$row[0]['order_date'],
            ':member_id'=>$row[0]['member_id'],
            ':status'=>$row[0]['status'],
            ':stock_valid_date'=>$row[0]['stock_valid_date'],
            ':deliver_date'=>$row[0]['deliver_date'],
            ':deliver_method'=>$row[0]['deliver_method'],
            ':deliver_number'=>$row[0]['deliver_number'],
            ':deliver_amount'=>$row[0]['deliver_amount'],
            ':deliver_coins'=>$row[0]['deliver_coins'],
            ':dollitemids'=>$row[0]['dollitemids'],
            ':dolls_info'=>$row[0]['dolls_info'],
            ':receiver_name'=>$row[0]['receiver_name'],
            ':receiver_phone'=>$row[0]['receiver_phone'],
            ':province'=>$row[0]['province'],
            ':city'=>$row[0]['city'],
            ':county'=>$row[0]['county'],
            ':street'=>$row[0]['street'],
            ':comment'=>$row[0]['comment'],
            ':created_date'=>$row[0]['created_date'],
            ':modified_date'=>$row[0]['modified_date'],
            ':modified_by'=>$row[0]['modified_by'],
            ':note'=>$row[0]['note'],
            ':detain_date'=>date('Y-m-d H:i:s',time()),
            ':detain_reason'=>$detain_reason
        ])->execute();
        $db->createCommand($delete_sql, [
            ':id'=>$id,
        ])->execute();
        return $this->redirect('/erp/order/index');
    }

    //扣留订单列表
    public function actionDetainOrder(){
        $db = Yii::$app->db;
        $phone = Yii::$app->getRequest()->get('phone',null);
        $receiveUser = Yii::$app->getRequest()->get('receiveUser',null);
        $orderNo = Yii::$app->getRequest()->get('orderNo',null);
        $userId = Yii::$app->getRequest()->get('userId',null);
        $conditions = $params = [];

        if($receiveUser) {
            $conditions[] = '`receiver_name`=:receiver_name';
            $params[':receiver_name'] = trim($receiveUser);
        }

        if($phone) {
            $conditions[] = '`receiver_phone`=:phone';
            $params[':phone'] = trim($phone);
        }

        if($orderNo) {
            $conditions[] = '`order_number`=:order_number';
            $params[':order_number'] = 'goods_'.trim($orderNo);
        }

        if($userId){
            $member = \common\models\Member::find()->where('memberID=:memberId',[':memberId'=>$userId])->one();
            if($member) {
                $member_id = $member->id;
                $conditions[] = '`member_id`=:member_id';
                $params[':member_id'] = trim($member_id);
            }
        }
        $sql = 'SELECT COUNT(*) FROM doll_order_goods_detain '.($conditions ? 'WHERE'.implode(' AND ', $conditions) : '');
        $count = $db->createCommand($sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);

        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $sql = 'SELECT d.*,di.memberID FROM doll_order_goods_detain d'
            . ' LEFT JOIN t_member di ON d.member_id=di.id '
            .($conditions ? 'WHERE d.'.implode(' AND ', $conditions) : '')
            . "  ORDER BY id DESC limit $offset,$size ";
        $rows = $db->createCommand($sql, $params)->queryAll();

        $receiverMap = [];
        foreach($rows as $key=>$row) {
            $dollItemIds = trim($row['dollitemids'],',');
            if($dollItemIds) {
                $dollItems = $db->createCommand('SELECT d.*,di.dollName FROM t_doll_order_item d'
                    . ' LEFT JOIN doll_info di ON d.doll_code=di.dollCode '
                    . ' where d.id in ('. $dollItemIds.')')->queryAll();
                $rows[$key]['dollItems'] = $row['dollItems'] = $dollItems;
            } else {
                $rows[$key]['dollItems'] = $row['dollItems'] = [];
            }

            $address = $row['province'].$row['city'].$row['county'].$row['street'];
            $rows[$key]['receiver_address'] = $row['receiver_address'] = $address;

            $receiverKey = md5("{$row['receiver_name']}:{$row['receiver_phone']}:{$address}:{$row['status']}");
            $receiverMap[$receiverKey][] = $row;
        }

        return $this->render('detain',[
            'rows'=>$rows,
            'pages'=>$pages,
        ]);
    }

    //恢复扣留订单
    public function actionOrder($id){
        $db = Yii::$app->db;
        $sql = "select * from doll_order_goods_detain WHERE id=$id";
        $row = $db->createCommand($sql)->queryAll();
        $insert_sql = "insert into doll_order_goods(id,order_number,order_date,member_id,status,stock_valid_date,deliver_date,deliver_method,deliver_number,deliver_amount,
                       deliver_coins,dollitemids,dolls_info,receiver_name,receiver_phone,province,city,county,street,comment,created_date,
                       modified_date,modified_by,note,detain) VALUES(:id,:order_number,:order_date,:member_id,:status,:stock_valid_date,:deliver_date,:deliver_method,:deliver_number,:deliver_amount,
                       :deliver_coins,:dollitemids,:dolls_info,:receiver_name,:receiver_phone,:province,:city,:county,:street,:comment,:created_date,
                       :modified_date,:modified_by,:note,:detain)";
        $delete_sql = "delete from doll_order_goods_detain WHERE id=:id";
        $db->createCommand($insert_sql,[
            ':id'=>$row[0]['id'],
            ':order_number'=>$row[0]['order_number'],
            ':order_date'=>date('Y-m-d H:i:s',time()),
            ':member_id'=>$row[0]['member_id'],
            ':status'=>$row[0]['status'],
            ':stock_valid_date'=>$row[0]['stock_valid_date'],
            ':deliver_date'=>$row[0]['deliver_date'],
            ':deliver_method'=>$row[0]['deliver_method'],
            ':deliver_number'=>$row[0]['deliver_number'],
            ':deliver_amount'=>$row[0]['deliver_amount'],
            ':deliver_coins'=>$row[0]['deliver_coins'],
            ':dollitemids'=>$row[0]['dollitemids'],
            ':dolls_info'=>$row[0]['dolls_info'],
            ':receiver_name'=>$row[0]['receiver_name'],
            ':receiver_phone'=>$row[0]['receiver_phone'],
            ':province'=>$row[0]['province'],
            ':city'=>$row[0]['city'],
            ':county'=>$row[0]['county'],
            ':street'=>$row[0]['street'],
            ':comment'=>$row[0]['comment'],
            ':created_date'=>$row[0]['created_date'],
            ':modified_date'=>$row[0]['modified_date'],
            ':modified_by'=>$row[0]['modified_by'],
            ':note'=>$row[0]['note'],
            ':detain'=>1,
        ])->execute();
        $db->createCommand($delete_sql, [
            ':id'=>$id,
        ])->execute();
        return $this->redirect('/erp/order/detain-order');
    }

    protected function findModel($id)
    {
        if (($model = DollOrderGoods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function dfindModel($id)
    {
        if (($model = DollOrderGoodsDetain::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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

//    导出订单
    public function actionExport()
    {
//        echo 1111;die;
        $startTime = $_REQUEST['startTime'];
        $endTime = $_REQUEST['endTime'];
//        echo $dateTime;die;
        if($startTime=='' || $endTime==''){
            //        查询表中的数据
            $list = yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id")->queryAll();
            //        获取时间
            $filename = time("Y-m-d") . "t_doll_order";
            header("Content-type:applicationndnd/vnd.ms-excel");
            //        数据格式
            header("Content-Disposition:filename=" . $filename . ".xls");
//        导出的数据
            $strexport = "订单号\t发件人姓名\t发件人手机\t发件人详细地址\t自定义区域1*（商品编码、娃娃名称、数量）\t收件人姓名\t收件人手机\t收件人详细地址\t自定义区域\t日期\r";
            //        循环出来
            foreach ($list as $row) {
                $strexport .= $row['order_number'] . "\t";
                $strexport .= $row['addrPerson'] . "\t";
                $strexport .= $row['phone'] . "\t";
                $strexport .= $row['faddress'] . "\t";
                $strexport .= $row['dollinfos'] . "\t";
                $strexport .= $row['receiver_name'] . "\t";
                $strexport .= $row['receiver_phone'] . "\t";
                $strexport .= $row['taddress'] . "\t";
                $strexport .= '' . "\t";
                $strexport .= $row['order_date'] . "\r";
            }
            //        转码
            $strexport = iconv('UTF-8', "GB2312//IGNORE", $strexport);
            $this->redirect(["export/show"]);
            exit($strexport);
            //        渲染页面
        }else{
            //        查询表中的数据
            $list = yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id    where order_date between '$startTime' and '$endTime'")->queryAll();
            //        获取时间
            $filename = time("Y-m-d") . "t_doll_order";
            header("Content-type:applicationndnd/vnd.ms-excel");
            //        数据格式
            header("Content-Disposition:filename=" . $filename . ".xls");
//        导出的数据
            $strexport = "订单号\t发件人姓名\t发件人手机\t发件人详细地址\t自定义区域1*（商品编码、娃娃名称、数量）\t收件人姓名\t收件人手机\t收件人详细地址\t自定义区域\t日期\r";
            //        循环出来
            foreach ($list as $row) {
                $strexport .= $row['order_number'] . "\t";
                $strexport .= $row['addrPerson'] . "\t";
                $strexport .= $row['phone'] . "\t";
                $strexport .= $row['faddress'] . "\t";
                $strexport .= $row['dollinfos'] . "\t";
                $strexport .= $row['receiver_name'] . "\t";
                $strexport .= $row['receiver_phone'] . "\t";
                $strexport .= $row['taddress'] . "\t";
                $strexport .= '' . "\t";
                $strexport .= $row['order_date'] . "\r";
            }
            //        转码
            $strexport = iconv('UTF-8', "GB2312//IGNORE", $strexport);
            $this->redirect(["export/show"]);
            exit($strexport);
            //        渲染页面
        }

  }

//  搜索
    public function actionSearch(){
        $startTime = $_REQUEST['startTime'];
        $endTime = $_REQUEST['endTime'];
        $name = $_REQUEST['name'];
//          echo $name;die;
        if($startTime=='' || $endTime==''){
////              echo '<script>alert("值不能为空");location.href="?r=export/show"</script>';
//              echo 1;
            $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id  where receiver_phone='$name' or receiver_name like '%$name%' or order_number='$name'";
            $data = Yii::$app->db->createCommand($sql)->queryAll();
            return json_encode($data);
        }else{
            $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id  where  order_date between '$startTime' and '$endTime'";
            $data = Yii::$app->db->createCommand($sql)->queryAll();
//          print_r($data);die;
            return json_encode($data);
        }
    }

//  展示已发货的列表

      public function actionDelivery(){

          $page = isset($_GET['page'])?$_GET['page']:1;
          $size=10;
          $data = Yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
        select e.allDoll from (
        select order_id,group_concat((c.dollNames)) allDoll from (
        select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id where status='已发货'")->queryAll();
          $count = count($data);
//        print_r($count);die;
          $total_page=ceil($count/$size);
//        echo $total_page;die;
          $last_page=$page-1<1?1:$page-1;

          $next_page=$page+1>$total_page?$total_page:$page+1;

          $offset=($page-1)*$size;
//        echo $offset;die;


          $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id where status='已发货' limit $offset,$size";
          $datas = Yii::$app->db->createCommand($sql)->queryAll();
//                print_r($datas);die;
          return $this->render('deliveryShow', ['data' => $datas,'page'=>$page,'last_page'=>$last_page,'next_page'=>$next_page,'total_page'=>$total_page]);
      }


//      导出已发货的订单
        public function actionExportDelivery(){

            $startTime = $_REQUEST['startTime'];
            $endTime = $_REQUEST['endTime'];
//        echo $dateTime;die;
            if($startTime=='' || $endTime==''){
                //        查询表中的数据
                $list = yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id where status='已发货'")->queryAll();
                //        获取时间
                $filename = time("Y-m-d") . "t_doll_order";
                header("Content-type:applicationndnd/vnd.ms-excel");
                //        数据格式
                header("Content-Disposition:filename=" . $filename . ".xls");
//        导出的数据
                $strexport = "订单号\t发件人姓名\t发件人手机\t发件人详细地址\t自定义区域1*（商品编码、娃娃名称、数量）\t收件人姓名\t收件人手机\t收件人详细地址\t自定义区域\t日期\r";
                //        循环出来
                foreach ($list as $row) {
                    $strexport .= $row['order_number'] . "\t";
                    $strexport .= $row['addrPerson'] . "\t";
                    $strexport .= $row['phone'] . "\t";
                    $strexport .= $row['faddress'] . "\t";
                    $strexport .= $row['dollinfos'] . "\t";
                    $strexport .= $row['receiver_name'] . "\t";
                    $strexport .= $row['receiver_phone'] . "\t";
                    $strexport .= $row['taddress'] . "\t";
                    $strexport .= '' . "\t";
                    $strexport .= $row['order_date'] . "\r";
                }
                //        转码
                $strexport = iconv('UTF-8', "GB2312//IGNORE", $strexport);
                $this->redirect(["export/show"]);
                exit($strexport);
                //        渲染页面
            }else{
                //        查询表中的数据
                $list = yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id    where status='已发货'and order_date between '$startTime' and '$endTime'")->queryAll();
                //        获取时间
                $filename = time("Y-m-d") . "t_doll_order";
                header("Content-type:applicationndnd/vnd.ms-excel");
                //        数据格式
                header("Content-Disposition:filename=" . $filename . ".xls");
//        导出的数据
                $strexport = "订单号\t发件人姓名\t发件人手机\t发件人详细地址\t自定义区域1*（商品编码、娃娃名称、数量）\t收件人姓名\t收件人手机\t收件人详细地址\t自定义区域\t日期\r";
                //        循环出来
                foreach ($list as $row) {
                    $strexport .= $row['order_number'] . "\t";
                    $strexport .= $row['addrPerson'] . "\t";
                    $strexport .= $row['phone'] . "\t";
                    $strexport .= $row['faddress'] . "\t";
                    $strexport .= $row['dollinfos'] . "\t";
                    $strexport .= $row['receiver_name'] . "\t";
                    $strexport .= $row['receiver_phone'] . "\t";
                    $strexport .= $row['taddress'] . "\t";
                    $strexport .= '' . "\t";
                    $strexport .= $row['order_date'] . "\r";
                }
                //        转码
                $strexport = iconv('UTF-8', "GB2312//IGNORE", $strexport);
                $this->redirect(["export/show"]);
                exit($strexport);
                //        渲染页面
            }
        }

//  搜索以发货
    public function actionSearch1(){
        $startTime = $_REQUEST['startTime'];
        $endTime = $_REQUEST['endTime'];
        $name = $_REQUEST['name'];
//          echo $name;die;
        if($startTime=='' || $endTime==''){
////              echo '<script>alert("值不能为空");location.href="?r=export/show"</script>';
//              echo 1;
            $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id  where status='已发货' and receiver_phone='$name' or receiver_name like '%$name%' or order_number='$name'";
            $data = Yii::$app->db->createCommand($sql)->queryAll();
            return json_encode($data);
        }else{
            $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id  where status='已发货' and order_date between '$startTime' and '$endTime'";
            $data = Yii::$app->db->createCommand($sql)->queryAll();
//          print_r($data);die;
            return json_encode($data);
        }
    }




//    未发货的列表
    public function actionUnshipped(){

        $page = isset($_GET['page'])?$_GET['page']:1;
        $size=10;
        $data = Yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
    select e.allDoll from (
        select order_id,group_concat((c.dollNames)) allDoll from (
        select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id where status='已兑换' or status='寄存中' or status='申请发货'")->queryAll();
        $count = count($data);
//        print_r($count);die;
        $total_page=ceil($count/$size);
//        echo $total_page;die;
        $last_page=$page-1<1?1:$page-1;

        $next_page=$page+1>$total_page?$total_page:$page+1;

        $offset=($page-1)*$size;
//        echo $offset;die;


        $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id where status='已兑换' or status='寄存中' or status='申请发货' limit $offset,$size";
        $datas = Yii::$app->db->createCommand($sql)->queryAll();
//                print_r($datas);die;
        return $this->render('unshippedShow', ['data' => $datas,'page'=>$page,'last_page'=>$last_page,'next_page'=>$next_page,'total_page'=>$total_page]);
    }


//    导出未发货的订单
       public function actionExportUnshipped(){
           $startTime = $_REQUEST['startTime'];
           $endTime = $_REQUEST['endTime'];
           $name = $_REQUEST['name'];
//        echo $dateTime;die;
           if($startTime=='' || $endTime==''){
               //        查询表中的数据
               $list = yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id where status='已兑换' or status='寄存中' or status='申请发货'")->queryAll();
               //        获取时间
               $filename = time("Y-m-d") . "t_doll_order";
               header("Content-type:applicationndnd/vnd.ms-excel");
               //        数据格式
               header("Content-Disposition:filename=" . $filename . ".xls");
//        导出的数据
               $strexport = "订单号\t发件人姓名\t发件人手机\t发件人详细地址\t自定义区域1*（商品编码、娃娃名称、数量）\t收件人姓名\t收件人手机\t收件人详细地址\t自定义区域\t日期\r";
               //        循环出来
               foreach ($list as $row) {
                   $strexport .= $row['order_number'] . "\t";
                   $strexport .= $row['addrPerson'] . "\t";
                   $strexport .= $row['phone'] . "\t";
                   $strexport .= $row['faddress'] . "\t";
                   $strexport .= $row['dollinfos'] . "\t";
                   $strexport .= $row['receiver_name'] . "\t";
                   $strexport .= $row['receiver_phone'] . "\t";
                   $strexport .= $row['taddress'] . "\t";
                   $strexport .= '' . "\t";
                   $strexport .= $row['order_date'] . "\r";
               }
               //        转码
               $strexport = iconv('UTF-8', "GB2312//IGNORE", $strexport);
               $this->redirect(["export/show"]);
               exit($strexport);
               //        渲染页面
           }else{
               //        查询表中的数据
               $list = yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id   where  (status='已兑换' or status='寄存中' or status='申请发货') AND order_date between '$startTime' and '$endTime'")->queryAll();
               //        获取时间
               $filename = time("Y-m-d") . "t_doll_order";
               header("Content-type:applicationndnd/vnd.ms-excel");
               //        数据格式
               header("Content-Disposition:filename=" . $filename . ".xls");
//        导出的数据
               $strexport = "订单号\t发件人姓名\t发件人手机\t发件人详细地址\t自定义区域1*（商品编码、娃娃名称、数量）\t收件人姓名\t收件人手机\t收件人详细地址\t自定义区域\t日期\r";
               //        循环出来
               foreach ($list as $row) {
                   $strexport .= $row['order_number'] . "\t";
                   $strexport .= $row['addrPerson'] . "\t";
                   $strexport .= $row['phone'] . "\t";
                   $strexport .= $row['faddress'] . "\t";
                   $strexport .= $row['dollinfos'] . "\t";
                   $strexport .= $row['receiver_name'] . "\t";
                   $strexport .= $row['receiver_phone'] . "\t";
                   $strexport .= $row['taddress'] . "\t";
                   $strexport .= '' . "\t";
                   $strexport .= $row['order_date'] . "\r";
               }
               //        转码
               $strexport = iconv('UTF-8', "GB2312//IGNORE", $strexport);
               $this->redirect(["export/show"]);
               exit($strexport);
               //        渲染页面
           }
       }
//     搜索未发货
      public function actionSearch2(){
          $startTime = $_REQUEST['startTime'];
          $endTime = $_REQUEST['endTime'];
          $name = $_REQUEST['name'];
//          echo $name;die;
          if($startTime=='' || $endTime==''){
////              echo '<script>alert("值不能为空");location.href="?r=export/show"</script>';
//              echo 1;
              $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id  where (status='已兑换' or status='寄存中' or status='申请发货') and receiver_phone='$name' or receiver_name like '%$name%' or order_number='$name'";
              $data = Yii::$app->db->createCommand($sql)->queryAll();
              return json_encode($data);
          }else{
              $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id  where (status='已兑换' or status='寄存中' or status='申请发货') and order_date between '$startTime' and '$endTime'";
              $data = Yii::$app->db->createCommand($sql)->queryAll();
//          print_r($data);die;
              return json_encode($data);
          }
      }

    public function actionNewIndex()
    {
        $db = Yii::$app->db;
        $status = Yii::$app->getRequest()->get('status',null);
        $startTime = Yii::$app->getRequest()->get('startTime',null);
        $endTime = Yii::$app->getRequest()->get('endTime',null);
        $phone = Yii::$app->getRequest()->get('phone',null);
        $receiveUser = Yii::$app->getRequest()->get('receiveUser',null);
        $orderNo = Yii::$app->getRequest()->get('orderNo',null);
        $deliverNo = Yii::$app->getRequest()->get('deliverNo',null);
        $userId = Yii::$app->getRequest()->get('userId',null);
        $export = Yii::$app->getRequest()->get('export',null);
        $exportMerge = Yii::$app->getRequest()->get('exportMerge',null);
        $fkcheck = Yii::$app->getRequest()->get('id',null);
        $merge = Yii::$app->getRequest()->get('merge',null);
        $allmerge = false;// Yii::$app->getRequest()->get('allmerge',null);
        $split = Yii::$app->getRequest()->get('split', null);
        $conditions = $params = [];
        $conditionss = $paramss = [];
        if($status && $status!='全部') {
            $conditions[] = '`status`=:status';
            $params[':status'] = trim($status);
        }

        if($startTime) {
            $conditions[] = '`order_date`>=:startTime';
            $params[':startTime'] = trim($startTime);
        }

        if($endTime) {
            $conditions[] = '`order_date`<=:endTime';
            $params[':endTime'] = trim($endTime);
        }

        if($startTime && $export=='on'){
            $conditionss[] = 'tdo.`modified_date`>=:startTime';
            $paramss[':startTime'] = trim($startTime);
        }


        if($startTime && $export=='on'){
            $conditionss[] = 'tdo.`modified_date`<=:endTime';
            $paramss[':endTime'] = trim($endTime);
        }

        if($status && $export=='on'){
            $conditionss[] = 'tdo.`status`=:status';
            $paramss[':status'] = trim($status);
        }

        if($receiveUser) {
            $conditions[] = '`receiver_name`=:receiver_name';
            $params[':receiver_name'] = trim($receiveUser);
        }

        if($phone) {
            $conditions[] = '`receiver_phone`=:phone';
            $params[':phone'] = trim($phone);
        }

        if($orderNo) {
            $conditions[] = '`order_number`=:order_number';
            $params[':order_number'] = trim($orderNo);
        }
        if($deliverNo) {
            $conditions[] = '`deliver_number`=:deliver_number';
            $params[':deliver_number'] = trim($deliverNo);
        }

        if($userId){
            $member = \common\models\Member::find()->where('memberID=:memberId',[':memberId'=>$userId])->one();
            if($member) {
                $member_id = $member->id;
                $conditions[] = '`member_id`=:member_id';
                $params[':member_id'] = trim($member_id);
            }
        }
        if($export!=='on') {
            $export = false;
        }
        if($exportMerge!=='on') {
            $exportMerge = false;
        }
        if($merge!=='on'){
            $merge = false;
        }
        if($allmerge!=='on'){
            $allmerge = false;
        }
        if($split!=='on'){
            $split = false;
        }

        if($fkcheck){
            $ids = explode(',', trim($fkcheck));
            foreach ($ids as $key=>$id) {
                $ids[$key] = (int)$id;
            }
            $conditions[] = '`id` in ('.implode(',',$ids).')';
        }
        if($split) {
            if(!$startTime || !$endTime) {
                throw new \Exception('必须要输入开始和结束时间才能分割订单');
            }
        }

        $sql = 'SELECT COUNT(*) FROM doll_order_goods '.($conditions ? 'WHERE'.implode(' AND ', $conditions) : '');
        $count = $db->createCommand($sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);

        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        if($allmerge){
            $sql = 'SELECT d.*,di.memberID FROM doll_order_goods d'
                . ' LEFT JOIN t_member di ON d.member_id=di.id '
                .($conditions ? 'WHERE d.'.implode(' AND ', $conditions) : '');
            $rows = $db->createCommand($sql, $params)->queryAll();
        }else{
            $sql = 'SELECT d.*,di.memberID FROM doll_order_goods d'
                . ' LEFT JOIN t_member di ON d.member_id=di.id '
                .($conditions ? 'WHERE d.'.implode(' AND ', $conditions) : '')
                . "  ORDER BY id DESC".( ($export || $split) ?  '' : " limit $offset,$size ");
            $rows = $db->createCommand($sql, $params)->queryAll();
        }

        $myfunction = new MyFunction();

        #分拆订单
        if($split) {
            $this->splitOrders($rows);
            return $this->redirect(['index']);
        }

        $receiverMap = [];
        foreach($rows as $key=>$row) {
            $dollItemIds = trim($row['dollitemids'],',');
            if($dollItemIds) {
                $dollItems = $db->createCommand('SELECT d.*,di.dollName FROM t_doll_order_item d'
                    . ' LEFT JOIN doll_info di ON d.doll_code=di.dollCode '
                    . ' where d.id in ('. $dollItemIds.')')->queryAll();
                $rows[$key]['dollItems'] = $row['dollItems'] = $dollItems;
            } else {
                $rows[$key]['dollItems'] = $row['dollItems'] = [];
            }

            $address = $row['province'].$row['city'].$row['county'].$row['street'];
            $rows[$key]['receiver_address'] = $row['receiver_address'] = $address;

            $receiverKey = md5("{$row['receiver_name']}:{$row['receiver_phone']}:{$address}:{$row['status']}");
            $receiverMap[$receiverKey][] = $row;
        }

        $sender = '365抓娃娃';
        $senderPhone = '17601323004';
        $senderAddress = '上海市金山区365抓娃娃';

        if($merge){
            foreach($rows as $key=>$v){
                $id = $v['id'];
                $date = $v['created_date'];
                $json = json_encode($v, JSON_UNESCAPED_UNICODE);
                $myfunction->mergeOrder($id,$date,$json);
            }

            foreach($receiverMap as $key=>$receiverRows) {
                $orderNos = $dollInfos = $dollitemids = [];
                $dollitemid = '';
                foreach ($receiverRows as $row) {
                    $dollitemid .= $row['dollitemids'];
                    $orderNos = substr($row['order_number'], 6);
                    $dollInfo = '';
                    foreach ($row['dollItems'] as $dollItem) {
                        $dollInfo .= "{$dollItem['dollName']}({$dollItem['doll_code']}) * {$dollItem['quantity']}；";
                    }
                    $dollInfos[] = $dollInfo;
                }
                $dollitemids[] = $dollitemid;

                $infos = implode(' ', $dollInfos);

                $updated = false;
                foreach ($receiverRows as $row) {
                    if(!$updated) {
                        //update
                        $myfunction->updateDoll($dollitemids[0], $infos,$row['id']);
                        $updated = true;
                    } else {
                        //delete
                        $myfunction->deleteDoll($row['id']);
                    }
                }
            }
        }

        if($allmerge){
            foreach($rows as $key=>$v){
                $id = $v['id'];
                $date = $v['created_date'];
                $json = json_encode($v, JSON_UNESCAPED_UNICODE);
                $myfunction = new MyFunction();
                $myfunction->mergeOrder($id,$date,$json);
            }

            foreach($receiverMap as $key=>$receiverRows) {
                $orderNos = $dollInfos = $dollitemids = [];
                $dollitemid = '';
                foreach ($receiverRows as $row) {
                    $dollitemid .= $row['dollitemids'];
                    $orderNos = substr($row['order_number'], 6);
                    $dollInfo = '';
                    foreach ($row['dollItems'] as $dollItem) {
                        $dollInfo .= "{$dollItem['dollName']}({$dollItem['doll_code']}) * {$dollItem['quantity']}；";
                    }
                    $dollInfos[] = $dollInfo;
                }
                $dollitemids[] = $dollitemid;
                $infos = implode(' ', $dollInfos);

                $updated = false;
                if(count($receiverRows)>1){
                    foreach ($receiverRows as $row) {
                        if(!$updated) {
                            //update
                            try {
                                $myfunction = new MyFunction();
                                $myfunction->updateDoll($dollitemids[0], $infos,$row['id']);
                            } catch (\Exception $e) {
                                throw $e;
//                            throw new BadRequestHttpException($e->getMessage());
                            }
                            $updated = true;
                        } else {
                            //delete
                            try {
                                $myfunction = new MyFunction();
                                $myfunction->deleteDoll($row['id']);
                            } catch (\Exception $e) {
//                            throw $e;
                                throw new \Exception();
//                            throw new BadRequestHttpException($e->getMessage());
                            }

                        }
                    }
                }
            }
        }
        $sqls = 'SELECT tdo.order_number,di.dollName,tdoi.doll_code,COUNT(tdo.id) num,tma.receiver_name,tma.receiver_phone,
                CONCAT(tma.province,tma.city,tma.county,tma.street) address,
                tdo.modified_date,
                tdo.order_date,
                tdo.status
                FROM t_doll_order tdo'
            . ' LEFT JOIN t_member_addr tma ON tma.id = tdo.address_id LEFT JOIN t_doll_order_item tdoi ON tdoi.order_id = tdo.id
                  LEFT JOIN doll_info di ON di.dollCode = tdoi.doll_code'
            .($conditionss ? ' WHERE '.implode(' AND ', $conditionss) : '')
            . ' GROUP BY tdo.address_id,tdoi.doll_code';
        $rowss = $db->createCommand($sqls, $params)->queryAll();

        if($export) {
            $filename = "orders_".date("Y-m-d H:i:s");

            $items = [];
            if($exportMerge) {
                $filename = "orders_".date("Y-m-d H:i:s");
                foreach($rowss as $key=>$v) {
                    $items[] = [
                        '订单号'=> $v['order_number'],
                        '发件人姓名'=>$sender,
                        '发件人手机'=>$senderPhone,
                        '发件人详细地址'=>$senderAddress,
                        '自定义区域1*（商品编码、娃娃名称、数量）'=> $v['dollName'].($v['doll_code']).'*'.$v['num'],
                        '收件人姓名'=>$v['receiver_name'],
                        '收件人手机'=>$v['receiver_phone'],
                        '收件人详细地址'=>$v['address'],
                        '自定义区域'=>'',
                        '抓取日期'=>$v['order_date'],
                        '申请发货日期'=>$v['modified_date']
                    ];
                }
            } else {
                foreach ($rowss as $key=>$v) {
                    $items[] = [
                        '订单号'=> $v['order_number'],
                        '发件人姓名'=>$sender,
                        '发件人手机'=>$senderPhone,
                        '发件人详细地址'=>$senderAddress,
                        '自定义区域1*（商品编码、娃娃名称、数量）'=> $v['dollName'].($v['doll_code']).'*'.$v['num'],
                        '收件人姓名'=>$v['receiver_name'],
                        '收件人手机'=>$v['receiver_phone'],
                        '收件人详细地址'=>$v['address'],
                        '自定义区域'=>'',
                        '抓取日期'=>$v['order_date'],
                        '申请发货日期'=>$v['modified_date']
                    ];
                }
            }
            $this->_setcsvHeader("{$filename}.csv");
            echo $this->_array2csv($items);
            Yii::$app->end();
        }

        return $this->render('index_new', [
            'rows' => $rows,
            'pages'=>$pages,
            'sender'=>$sender,
            'senderPhone'=>$senderPhone,
            'senderAddress'=>$senderAddress
        ]);
    }

    public function actionExportTest(){
        $db = Yii::$app->db;
        $sql = "SELECT
         tdo.order_number ,
          di.dollName ,
          tdoi.doll_code ,
          Count(tdo.id) num ,
          tma.receiver_name ,
          tma.receiver_phone ,
          CONCAT(
            tma.province,
            tma.city,
            tma.county,
            tma.street
          ) address,
          tdo.modified_date ,
          tdo.order_date
        FROM
          t_doll_order tdo
        LEFT JOIN t_member_addr tma ON tma.id = tdo.address_id
        LEFT JOIN t_doll_order_item tdoi ON tdoi.order_id = tdo.id
        LEFT JOIN doll_info di ON di.dollCode = tdoi.doll_code
        WHERE
          tdo. STATUS = '申请发货'
        AND tdo.modified_date >= '2018-1-4 14:00:00'
        AND tdo.modified_date < '2018-1-5 14:00:00'
        GROUP BY
          tdo.address_id,
          tdoi.doll_code";
        $rows = $db->createCommand($sql)->queryAll();
        $count = count($rows);
        $datas = $item =[];
        for($i=0;$i<$count;$i++){
            $data = $rows[$i];
            if($rows[$i]['doll_code'] == $rows[$i-1]['doll_code'] && $rows[$i]['receiver_name'] == $rows[$i-1]['receiver_name'] && $rows[$i]['receiver_phone'] == $rows[$i-1]['receiver_phone'] && $rows[$i]['address'] == $rows[$i-1]['address']){
                //数据备份
                array_push($item,$data[$i]);
            }else{

                array_push($datas,$data);
            }
            print_r($data);die;
        }
    }

    //通过表上传数据
    public function actionTestAdd(){
        $model = new DollInfo();
        if ($model->load(Yii::$app->request->post())) {
            $model->dollCode = UploadedFile::getInstance($model, 'dollCode');
            $excelInfo = $this->object2array($model->dollCode);
            $excel_url = $excelInfo['tempName'];
            //文件名为文件路径和文件名的拼接字符串
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');//创建读取实例
            /*
             * log()//方法参数
             * $file_name excal文件的保存路径
             */
            $objPHPExcel = $objReader->load($excel_url);//加载文件
            $sheet = $objPHPExcel->getSheet(0);//取得sheet(0)表
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            for($i=2;$i<=$highestRow;$i++)
            {
                $doll_code = $sheet->getCell("B".$i)->getValue();
                $dollCoins = $sheet->getCell("D".$i)->getValue();
                $deliverCoins= $sheet->getCell("C".$i)->getValue();
                $sql = "update doll_info set dollCoins='$dollCoins',deliverCoins='$deliverCoins' WHERE dollCode='$doll_code'";
                Yii::$app->db->createCommand($sql)->execute();
            }
        } else {
            return $this->render('testadd',[
                'model' => $model,
            ]);
        }
    }
}
//(addDate = @addDate or @addDate is null) and (name = @name or @name = '')