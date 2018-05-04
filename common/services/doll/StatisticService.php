<?php

namespace common\services\doll;

use frontend\models\Doll;
use Yii;
use common\models\doll\Statistic;
use common\enums\StatisticTypeEnum;
use common\models\doll\MachineStatistic;

class StatisticService extends \common\services\BaseService {
    
    /**
     * 运行统计
     * @param string $day
     * @return []
     * @throws \Exception
     */
    public function run($day=null, $insert=0) {
        if($day===null) {
            $day = date('Ymd', time() - 86400);
            $startTime = strtotime('yesterday');
        } else {
            $startTime = strtotime($day);
            if(!$startTime) {
                throw new \Exception('日期格式不正确（格式：2010-01-01 00:00:00）');
            }
            
        }
        $endTime = $startTime + 86400;
        
//      根据时间查用户新注册的数量
        $dateParams = [
            ':startDate'=>date('Y-m-d H:i:s', $startTime),
            ':endDate'=>date('Y-m-d H:i:s', $endTime),
        ];
        $timeParams = [
            ':startTime'=>$startTime,
            ':endTime'=>$endTime
        ];
        $db = Yii::$app->db;
        $dbRead = Yii::$app->dbRd;

        //练习房机器id
        $sql = "select * from t_doll WHERE machine_type=1";
        $data = $dbRead->createCommand($sql)->queryAll();
        $ids = '';
        foreach($data as $k=>$v){
            $id = $v['id'];
            $ids .= $id.",";
        }
        $ids = rtrim($ids,',');

//        #钻石房ID
//        $sql = "select * from t_doll WHERE machine_type=2";
//        $data = $dbRead->createCommand($sql)->queryAll();
//        $z_ids = '';
//        foreach($data as $k=>$v){
//            $id = $v['id'];
//            $z_ids .= $id.",";
//        }
//        $z_ids = rtrim($z_ids,',');
//
//        #普通房ID
//        $sql = "select * from t_doll WHERE machine_type=0";
//        $data = $dbRead->createCommand($sql)->queryAll();
//        $p_ids = '';
//        foreach($data as $k=>$v){
//            $id = $v['id'];
//            $p_ids .= $id.",";
//        }
//        $p_ids = rtrim($p_ids,',');

        $whereDate = "register_date >= :startDate and register_date < :endDate";
        
        #注册人数
        $sql = "select count(*) from t_member where {$whereDate}";
        //echo $sql."\n";
        //var_dump($dateParams);echo "\n";
        $registrationNum = $dbRead->createCommand($sql,$dateParams)->queryScalar();
        //echo $registrationNum."\n";
        
        #安卓人数 register_from = 'android' and 
        $sql2 = $sql." AND register_from = 'android'";
        //echo $sql2."\n";
        //var_dump($dateParams);echo "\n";
        $androidRegistrationNum = $dbRead->createCommand($sql2,$dateParams)->queryScalar();
        //echo $androidRegistrationNum."\n";
        #IOS 人数
        $iosRegistrationNum = $registrationNum - $androidRegistrationNum;
        //echo $iosRegistrationNum."\n";
        #支付数量
        $whereDate = "create_date >= :startDate and create_date < :endDate";
        $chargeNumSql = "SELECT count(*) FROM charge_order where {$whereDate} AND charge_state=1";
        $chargeNum = $dbRead->createCommand($chargeNumSql,$dateParams)->queryScalar();

        #支付的金额
        $chargeAmountSql = "SELECT sum(price) FROM charge_order where {$whereDate} AND charge_state=1";
        $chargeAmount = $dbRead->createCommand($chargeAmountSql,$dateParams)->queryScalar();
        if($chargeAmount===null) {
            $chargeAmount = 0;
        }

        #钻石房支付的金额
        $chargeAmountSql = "SELECT sum(price) FROM charge_order where {$whereDate} AND charge_state=1 AND charge_name LIKE '%钻石%'";
        $z_chargeAmount = $dbRead->createCommand($chargeAmountSql,$dateParams)->queryScalar();
        if($z_chargeAmount===null) {
            $z_chargeAmount = 0;
        }
        
        #抓取成功的次数 ,排除练习房,占卜房
        $whereDate = "catch_date >= :startDate and catch_date < :endDate";
        $grabCountSql = "SELECT count(*) FROM t_doll_catch_history where {$whereDate} AND catch_status='抓取成功' AND machine_type NOT IN (1,3)";
        $grabCount = $dbRead->createCommand($grabCountSql,$dateParams)->queryScalar();

        #钻石房抓取成功次数
        $whereDate = "catch_date >= :startDate and catch_date < :endDate";
        $grabCountSql = "SELECT count(*) FROM t_doll_catch_history where {$whereDate} AND catch_status='抓取成功' AND machine_type=2";
        $z_grabCount = $dbRead->createCommand($grabCountSql,$dateParams)->queryScalar();

        #练习房和占卜房抓取成功次数
        $whereDate = "catch_date >= :startDate and catch_date < :endDate";
        $grabCountSql = "SELECT count(*) FROM t_doll_catch_history where {$whereDate} AND catch_status='抓取成功' AND machine_type IN (1,3)";
        $l_grabCount = $dbRead->createCommand($grabCountSql,$dateParams)->queryScalar();

        #普通房抓取成功次数
        $whereDate = "catch_date >= :startDate and catch_date < :endDate";
        $grabCountSql = "SELECT count(*) FROM t_doll_catch_history where {$whereDate} AND catch_status='抓取成功' AND machine_type=0";
        $p_grabCount = $dbRead->createCommand($grabCountSql,$dateParams)->queryScalar();

        #充值用户抓取成功次数
//        $whereDate = "catch_date >= :startDate and catch_date < :endDate";
//        $grabCountSql = "SELECT count(*) FROM t_doll_catch_history d LEFT JOIN charge_order di ON d.member_id=di.member_id  where {$whereDate} AND catch_status='抓取成功' AND di.charge_state=1";
//        $z_grabCount = $dbRead->createCommand($grabCountSql,$dateParams)->queryScalar();
        
        #抓取总次数 
        $playCountSql = "SELECT count(*) FROM t_doll_catch_history where {$whereDate}";
        $playCount = $dbRead->createCommand($playCountSql,$dateParams)->queryScalar();

        #钻石抓取总次数
        $playCountSql = "SELECT count(*) FROM t_doll_catch_history where {$whereDate} AND machine_type=2";
        $z_playCount = $dbRead->createCommand($playCountSql,$dateParams)->queryScalar();

        #普通抓取总次数
        $playCountSql = "SELECT count(*) FROM t_doll_catch_history where {$whereDate} AND machine_type=0";
        $p_playCount = $dbRead->createCommand($playCountSql,$dateParams)->queryScalar();

        #练习抓取总次数
        $playCountSql = "SELECT count(*) FROM t_doll_catch_history where {$whereDate} AND machine_type IN (1,3)";
        $l_playCount = $dbRead->createCommand($playCountSql,$dateParams)->queryScalar();

        #失败次数 
        $failCount = $playCount - $grabCount;
        
        #总用户数
        $whereDate = "register_date < :endDate";
        $userNumSql = "SELECT count(*) FROM t_member where {$whereDate}";
        $userNum = $dbRead->createCommand($userNumSql,[':endDate'=>$dateParams[':endDate']])->queryScalar();
        
        #当日新用户充值比例
        $sql = "SELECT COUNT(*) as total_count,SUM(co.price) as sum_price,COUNT(DISTINCT co.member_id) as total_user_count
            FROM charge_order co LEFT JOIN t_member m ON co.member_id=m.id 
WHERE co.create_date>=:startDate AND co.create_date<:endDate
AND co.charge_state=1
AND m.register_date>=:startDate AND m.register_date<:endDate";
        $newUserChargeRow = $dbRead->createCommand($sql,$dateParams)->queryOne();
        
        $sql = "SELECT COUNT(*) as total_count,SUM(co.price) as sum_price,COUNT(DISTINCT co.member_id) as total_user_count
            FROM charge_order co LEFT JOIN t_member m ON co.member_id=m.id 
WHERE co.create_date>=:startDate AND co.create_date<:endDate
AND co.charge_state=1
AND m.register_date<:startDate";
        $oldUserChargeRow = $dbRead->createCommand($sql,$dateParams)->queryOne();
        
        #老用户充值比例
        
        #免费用户抓取次数
        
        #付费用户抓取次数
        
        $data = [
            'registrationNum'=>$registrationNum,
            'androidRegistrationNum'=>$androidRegistrationNum,
            'iosRegistrationNum'=>$iosRegistrationNum,
            'chargeNum'=>$chargeNum,
            'chargeAmount'=>$chargeAmount,
            'z_chargeAmount'=>$z_chargeAmount,
            'playCount'=>$playCount,
            'grabCount'=>$grabCount,
            'z_grabCount'=>$z_grabCount,
            'z_playCount'=>$z_playCount,
            'l_grabCount'=>$l_grabCount,
            'l_playCount'=>$l_playCount,
            'p_grabCount'=>$p_grabCount,
            'p_playCount'=>$p_playCount,
            'failCount'=>$failCount,
            'userNum'=>$userNum,
            'newUserChargeNum'=>(int)$newUserChargeRow['total_user_count'],
            'newUserChargeOrderNum'=>(int)$newUserChargeRow['total_count'],
            'newUserChargeAmount'=>round($newUserChargeRow['sum_price'],2),
            'oldUserChargeNum'=>(int)$oldUserChargeRow['total_user_count'],
            'oldUserChargeOrderNum'=>(int)$oldUserChargeRow['total_count'],
            'oldUserChargeAmount'=>round($oldUserChargeRow['sum_price'],2),
            'day'=>date('Y-m-d', $startTime),
        ];
        
        if($insert) {
            
            $table = Statistic::tableName();
            $dbPhp = Yii::$app->db_php;
            $delSql = "DELETE FROM {$table} WHERE day=".$startTime;
            $insertSql = "INSERT INTO {$table} (registration_num, android_registration_num,ios_registration_num, charge_num, "
            . "charge_amount, play_count, grab_count, day, user_num, new_user_charge_num, new_user_charge_order_num, "
                    . "new_user_charge_amount, old_user_charge_num, old_user_charge_order_num, old_user_charge_amount,z_grabCount,z_playCount,p_grabCount,p_playCount,l_grabCount,l_playCount,z_charge_amount ) "
            . "VALUES (:registrationNum, :androidRegistrationNum,:iosRegistrationNum, :chargeNum, :chargeAmount, :playCount, "
                    . ":grabCount, :day, :userNum, :newUserChargeNum, :newUserChargeOrderNum, "
                    . ":newUserChargeAmount, :oldUserChargeNum, :oldUserChargeOrderNum, :oldUserChargeAmount,:z_grabCount,:z_playCount,:p_grabCount,:p_playCount,:l_grabCount,:l_playCount,:z_chargeAmount)";
            $dbPhp->createCommand($delSql)->execute();
            $dbPhp->createCommand($insertSql,[
                ':registrationNum'=>$registrationNum,
                ':androidRegistrationNum'=>$androidRegistrationNum,
                ':iosRegistrationNum'=>$iosRegistrationNum,
                ':chargeNum'=>$chargeNum,
                ':chargeAmount'=>$chargeAmount,
                ':z_chargeAmount'=>$z_chargeAmount,
                ':playCount'=>$playCount,
                ':grabCount'=>$grabCount,
                ':z_grabCount'=>$z_grabCount,
                ':z_playCount'=>$z_playCount,
                ':l_grabCount'=>$l_grabCount,
                ':l_playCount'=>$l_playCount,
                ':p_grabCount'=>$p_grabCount,
                ':p_playCount'=>$p_playCount,
                ':userNum'=>$userNum,
                ':day'=>$startTime,
                ':newUserChargeNum'=>(int)$newUserChargeRow['total_user_count'],
                ':newUserChargeOrderNum'=>(int)$newUserChargeRow['total_count'],
                ':newUserChargeAmount'=>round($newUserChargeRow['sum_price'],2),
                ':oldUserChargeNum'=>(int)$oldUserChargeRow['total_user_count'],
                ':oldUserChargeOrderNum'=>(int)$oldUserChargeRow['total_count'],
                ':oldUserChargeAmount'=>round($oldUserChargeRow['sum_price'],2),
            ])->execute();
            
        }
        
        return $data;
    }
    
    /**
     * 机器抓中比率统计 
     * @param integer $startTime 开始时间戳
     * @param integer $endTime 结束时间戳
     * @param integer $insert 是否插入到表中 默认为否
     * @param integer $insertType 插入类型 默认为空
     * @return []
     */
    public function machineRate($startTime, $endTime, $insert=0, $insertType=NULL) {
        $db = Yii::$app->db;
//        $dbPhp = Yii::$app->db_php;
        $dbPhp = Yii::$app->db;
        $dbRead = Yii::$app->dbRd;
        $where = "where h.catch_date>='".date('Y-m-d H:i:s',$startTime)."' AND h.catch_date<='".date('Y-m-d H:i:s',$endTime)."' and d.machine_status!='未上线'";
        $successWhere = $where.' AND h.catch_status="抓取成功"';
        //总的抓取概率
        $sql = 'select count(*) as play_count,d.id as machine_id,d.machine_code,d.machine_url,d.name as doll_name,d.doll_ID as doll_id from t_doll_catch_history h left join t_doll d ON h.doll_id=d.id '.$where.' GROUP BY d.machine_code';
        $successSql = 'select count(*) as grab_count,d.id as machine_id,d.machine_code,d.machine_url,d.name as doll_name,d.doll_ID as doll_id from t_doll_catch_history h left join t_doll d ON h.doll_id=d.id '.$successWhere.' GROUP BY d.machine_code';

        $dollSql = "select * from t_doll WHERE machine_status != '未上线'";

        $historyRows = $dbRead->createCommand($sql)->queryAll();
        
        $grabRows = $dbRead->createCommand($successSql)->queryAll();

        $dollRows = $dbRead->createCommand($dollSql)->queryAll();

        $dollIds = $machineIds = [];
        foreach($dollRows as $k=>$v){
            $doll_id = $v['id'];
            array_push($dollIds,$doll_id);
        }
        foreach($historyRows as $k=>$v){
            $machine_id = $v['machine_id'];
            array_push($machineIds,$machine_id);
        }
        $ids = array_diff($dollIds,$machineIds);
        
        if($insert) {
            if($insertType===null) {
                if( ($endTime - $startTime)==86400 ) {
                    $insertType = StatisticTypeEnum::TYPE_DAY;
                } elseif( ($endTime - $startTime)==1800) {
                    $insertType = StatisticTypeEnum::TYPE_HALF_HOURS;
                }else{
                    $insertType = StatisticTypeEnum::TYPE_CUSTOM;
                }
            }
            
            $table = MachineStatistic::tableName();
            $formatGrabRows = \yii\helpers\ArrayHelper::index($grabRows, 'machine_code');
            $machine_codes = '';
            foreach($historyRows as $row){
                $machine_code = $row['machine_code'];
                $machine_codes .= $machine_code . ',';
            }
            $machine_codes = rtrim($machine_codes,',');
            $machine_codes = ltrim($machine_codes,',');
            $l_playCounts = $this->lPlayCount($machine_codes,$startTime,$endTime);
            $l_grabCounts = $this->lgrabCount($machine_codes,$startTime,$endTime);
            $s_playCounts = $this->sPlayCount($machine_codes,$startTime,$endTime);
            $s_grabCounts = $this->sgrabCount($machine_codes,$startTime,$endTime);

            foreach($historyRows as $row) {
                $deleteSql = "DELETE FROM {$table} WHERE machine_code=:machine_code "
                . "AND start_time=:start_time "
                . "AND end_time=:end_time "
                . "AND type=:type";
                
                
                $insertSql = "INSERT INTO {$table} (machine_id, machine_code, machine_device_name,play_count,"
                . "grab_count,start_time,end_time,type,machine_doll_name,machine_doll_code,no_playCount,no_grabCount,s_playCount,s_grabCount,l_playCount,l_grabCount) VALUES (:machine_id, :machine_code, :machine_device_name,"
                        . ":play_count,:grab_count,:start_time,:end_time,:type,:doll_name, :doll_code,:no_playCount,:no_grabCount,:s_playCount,:s_grabCount,:l_playCount,:l_grabCount)";
                
                $grabRow = isset($formatGrabRows[$row['machine_code']]) ? $formatGrabRows[$row['machine_code']] : null;
                $machine_id = $row['machine_id'];
                $playCount = $row['play_count'];
                $grabCount = $grabRow ? $grabRow['grab_count'] : 0;
                $s_playCount = isset($s_playCounts[$machine_id]) ? $s_playCounts[$machine_id] : 0;
                $s_grabCount = isset($s_grabCounts[$machine_id]) ? $s_grabCounts[$machine_id] : 0;
                $l_playCount = isset($l_playCounts[$machine_id]) ? $l_playCounts[$machine_id] : 0;
                $l_grabCount = isset($l_grabCounts[$machine_id]) ? $l_grabCounts[$machine_id] : 0;
                $no_playCount = $playCount-$s_playCount-$l_playCount;
                $no_grabCount = $grabCount-$s_grabCount-$l_grabCount;
                
                $dbPhp->createCommand($deleteSql, [
                    ':machine_code'=>$row['machine_code'],
                    ':start_time'=>$startTime,
                    ':end_time'=>$endTime,
                    ':type'=>$insertType,
                ])->execute();
                
                $dbPhp->createCommand($insertSql, [
                    ':machine_id'=> $row['machine_id'], 
                    ':machine_code'=>$row['machine_code'], 
                    ':machine_device_name'=>$row['machine_url'],
                    ':play_count'=>$row['play_count'],
                    ':no_playCount'=>$no_playCount,
                    ':s_playCount'=>$s_playCount,
                    ':l_playCount'=>$l_playCount,
                    ':grab_count'=>$grabRow ? $grabRow['grab_count'] : 0,
                    ':no_grabCount'=>$no_grabCount,
                    ':s_grabCount'=>$s_grabCount,
                    ':l_grabCount'=>$l_grabCount,
                    ':start_time'=>$startTime,
                    ':end_time'=>$endTime,
                    ':type'=>$insertType,
                    ':doll_name'=>$row['doll_name'],
                    ':doll_code'=>$row['doll_id'],
                ])->execute();
            }

//            $machine_codes = '';
//            foreach($ids as $k){
//                $id = $k;
//                if($id == NULL){
//                    continue;
//                }else{
//                    $failWhere = $where." AND d.id=$id";
//                    $failSql = 'select count(*) as play_count,d.id as machine_id,d.machine_code,d.machine_url,d.name as doll_name,d.doll_ID as doll_id from t_doll_catch_history h left join t_doll d ON h.doll_id=d.id '.$failWhere;
//                    $dollData = $db->createCommand($failSql)->queryAll();
//                    $machine_code = $dollData[0]['machine_code'];
//                    $machine_codes .= $machine_code . ',';
//                }
//            }
//            $machine_codes = rtrim($machine_codes,',');
//            $machine_codes = ltrim($machine_codes,',');
//            $l_playCounts = $this->lPlayCount($machine_codes,$startTime,$endTime);
//            $l_grabCounts = $this->lgrabCount($machine_codes,$startTime,$endTime);
//            $s_playCounts = $this->sPlayCount($machine_codes,$startTime,$endTime);
//            $s_grabCounts = $this->sgrabCount($machine_codes,$startTime,$endTime);

            foreach($ids as $k){
                $deSql = "DELETE FROM {$table} WHERE machine_code=:machine_code "
                    . "AND start_time=:start_time "
                    . "AND end_time=:end_time "
                    . "AND type=:type";

                $id = $k;
                if($id == NULL){
                    continue;
                }else{
                    $failWhere = $where." AND d.id=$id";
                    $failSql = 'select count(*) as play_count,d.id as machine_id,d.machine_code,d.machine_url,d.name as doll_name,d.doll_ID as doll_id from t_doll_catch_history h left join t_doll d ON h.doll_id=d.id '.$failWhere;
                    $dollData = $db->createCommand($failSql)->queryAll();
                    $machine_id = $dollData[0]['machine_id'];
                    $playCount = $dollData[0]['play_count'];
                    $grabCount = 0;
                    $s_playCount = isset($s_playCounts[$machine_id]) ? $s_playCounts[$machine_id] : 0;
                    $s_grabCount = isset($s_grabCounts[$machine_id]) ? $s_grabCounts[$machine_id] : 0;
                    $l_playCount = isset($l_playCounts[$machine_id]) ? $l_playCounts[$machine_id] : 0;
                    $l_grabCount = isset($l_grabCounts[$machine_id]) ? $l_grabCounts[$machine_id] : 0;
                    $no_playCount = $playCount-$s_playCount-$l_playCount;
                    $no_grabCount = $grabCount-$s_grabCount-$l_grabCount;
//                    $dollData = Doll::find()->where(['id'=>$id])->asArray()->one();
                    $insertSql = "INSERT INTO {$table} (machine_id, machine_code, machine_device_name,play_count,"
                        . "grab_count,start_time,end_time,type,machine_doll_name,machine_doll_code,no_playCount,no_grabCount,s_playCount,s_grabCount,l_playCount,l_grabCount) VALUES (:machine_id, :machine_code, :machine_device_name,"
                        . ":play_count,:grab_count,:start_time,:end_time,:type,:doll_name, :doll_code,:no_playCount,:no_grabCount,:s_playCount,:s_grabCount,:l_playCount,:l_grabCount)";

                    $dbPhp->createCommand($deSql, [
                        ':machine_code'=>$dollData[0]['machine_code'],
                        ':start_time'=>$startTime,
                        ':end_time'=>$endTime,
                        ':type'=>$insertType,
                    ])->execute();

                    $dbPhp->createCommand($insertSql, [
                        ':machine_id'=> $dollData[0]['machine_id'],
                        ':machine_code'=>$dollData[0]['machine_code'] ? $dollData[0]['machine_code'] : 0,
                        ':machine_device_name'=>$dollData[0]['machine_url'] ? $dollData[0]['machine_url'] : 0,
                        ':play_count'=>$dollData[0]['play_count'],
                        ':no_playCount'=>$no_playCount,
                        ':no_grabCount'=>$no_grabCount,
                        ':s_playCount'=>$s_playCount,
                        ':s_grabCount'=>$s_grabCount,
                        ':l_playCount'=>$l_playCount,
                        ':l_grabCount'=>$l_grabCount,
                        ':grab_count'=>0,
                        ':start_time'=>$startTime,
                        ':end_time'=>$endTime,
                        ':type'=>$insertType,
                        ':doll_name'=>$dollData[0]['doll_name'] ? $dollData[0]['doll_name'] : 0,
                        ':doll_code'=>$dollData[0]['doll_id'] ? $dollData[0]['doll_id'] : 0,
                    ])->execute();
                }
            }
        }

        return [
            'historyRows'=>$historyRows,
            'grabRows'=>$grabRows
        ];
    }

    //充值小于100
    public function sGrabCount($machine_codes,$startTime,$endTime){
        $db = Yii::$app->db;
        $dbRead = Yii::$app->dbRd;
        $nosql = "select member_id,sum(price) from charge_order WHERE charge_state=1 GROUP BY member_id HAVING SUM(price)<=100";
        $noData = $db->createCommand($nosql)->queryAll();
        $noIds = '';
        foreach($noData as $k=>$v){
            $no_id = $v['member_id'];
            $noIds .=$no_id.",";
        }
        $noIds = rtrim($noIds,',');
        $no_where = "where h.catch_date>='".date('Y-m-d H:i:s',$startTime)."' AND h.catch_date<='".date('Y-m-d H:i:s',$endTime)."' and h.member_id in ($noIds) and d.machine_code in ($machine_codes)";
        $no_successWhere = $no_where.' AND h.catch_status="抓取成功"';
        $no_successSql = 'select count(*) as s_grab_count,d.id as machine_id from t_doll_catch_history h left join t_doll d ON h.doll_id=d.id '.$no_successWhere.' GROUP BY d.machine_code';
        $grabRows = $dbRead->createCommand($no_successSql)->queryAll();
        $s_grabCount = [];
        foreach($grabRows as $k=>$v){
            $machine_id = $v['machine_id'];
            $s_grab_count = $v['s_grab_count'];
            $s_grabCount[$machine_id]=$s_grab_count;
        }
        return $s_grabCount;
    }
    public function sPlayCount($machine_codes,$startTime,$endTime){
        $db = Yii::$app->db;
        $dbRead = Yii::$app->dbRd;
        $nosql = "select member_id,sum(price) from charge_order WHERE charge_state=1 GROUP BY member_id HAVING SUM(price)<=100";
        $noData = $db->createCommand($nosql)->queryAll();
        $noIds = '';
        foreach($noData as $k=>$v){
            $no_id = $v['member_id'];
            $noIds .=$no_id.",";
        }
        $noIds = rtrim($noIds,',');
        $no_where = "where h.catch_date>='".date('Y-m-d H:i:s',$startTime)."' AND h.catch_date<='".date('Y-m-d H:i:s',$endTime)."' and h.member_id in ($noIds) and d.machine_code in ($machine_codes)";
        $no_sql = 'select count(*) as s_play_count,d.id as machine_id from t_doll_catch_history h left join t_doll d ON h.doll_id=d.id '.$no_where.' GROUP BY d.machine_code';
        $historyRows = $dbRead->createCommand($no_sql)->queryAll();
        $s_playCount = [];
        foreach($historyRows as $k=>$v){
            $machine_id = $v['machine_id'];
            $s_play_count = $v['s_play_count'];
            $s_playCount[$machine_id]=$s_play_count;
        }
        return $s_playCount;
    }

    //充值大于100
    public function lGrabCount($machine_codes,$startTime,$endTime){
        $db = Yii::$app->db;
        $dbRead = Yii::$app->dbRd;
        $nosql = "select member_id,sum(price) from charge_order WHERE charge_state=1 GROUP BY member_id HAVING SUM(price)>100";
        $noData = $db->createCommand($nosql)->queryAll();
        $noIds = '';
        foreach($noData as $k=>$v){
            $no_id = $v['member_id'];
            $noIds .=$no_id.",";
        }
        $noIds = rtrim($noIds,',');
        $no_where = "where h.catch_date>='".date('Y-m-d H:i:s',$startTime)."' AND h.catch_date<='".date('Y-m-d H:i:s',$endTime)."' and h.member_id in ($noIds) and d.machine_code in ($machine_codes)";
        $no_successWhere = $no_where.' AND h.catch_status="抓取成功"';
        $no_successSql = 'select count(*) as l_grab_count,d.id as machine_id from t_doll_catch_history h left join t_doll d ON h.doll_id=d.id '.$no_successWhere.' GROUP BY d.machine_code';
        $grabRows = $dbRead->createCommand($no_successSql)->queryAll();
        $l_grabCount = [];
        foreach($grabRows as $k=>$v){
            $machine_id = $v['machine_id'];
            $l_grab_count = $v['l_grab_count'];
            $l_grabCount[$machine_id]=$l_grab_count;
        }
        return $l_grabCount;
    }
    public function lPlayCount($machine_codes,$startTime,$endTime){
        $db = Yii::$app->db;
        $dbRead = Yii::$app->dbRd;
        $nosql = "select member_id,sum(price) from charge_order WHERE charge_state=1 GROUP BY member_id HAVING SUM(price)>100";
        $noData = $db->createCommand($nosql)->queryAll();
        $noIds = '';
        foreach($noData as $k=>$v){
            $no_id = $v['member_id'];
            $noIds .=$no_id.",";
        }
        $noIds = rtrim($noIds,',');
        $no_where = "where h.catch_date>='".date('Y-m-d H:i:s',$startTime)."' AND h.catch_date<='".date('Y-m-d H:i:s',$endTime)."' and h.member_id in ($noIds) and d.machine_code in ($machine_codes)";
        $no_sql = 'select count(*) as l_play_count,d.id as machine_id from t_doll_catch_history h left join t_doll d ON h.doll_id=d.id '.$no_where. 'group by d.machine_code';
        $historyRows = $dbRead->createCommand($no_sql)->queryAll();
        $l_playCount = [];
        foreach($historyRows as $k=>$v){
            $machine_id = $v['machine_id'];
            $l_play_count = $v['l_play_count'];
            $l_playCount[$machine_id]=$l_play_count;
        }
        return $l_playCount;
    }
    /**
     * 渠道日报
     */
    public function channelDaily($day, $insert=0, $insertType=NULL) {
        $db = Yii::$app->db;
        $dbPhp = Yii::$app->db_php;
        $dbRead = Yii::$app->dbRd;
        $dbStatistic = Yii::$app->dbStatistic;
        
        #注册数据 
        $sql = "SELECT register_channel as channel, COUNT(*) as register_num, FROM_UNIXTIME(UNIX_TIMESTAMP(register_date),'%Y-%m-%d') as register_day
FROM t_member 
WHERE register_date>= '{$day} 00:00:00' AND register_date<='{$day} 23:59:59' "
. "GROUP BY register_channel";
        
        $channelRegisterData = $dbRead->createCommand($sql)->queryAll();

        #充值数据 
        $sql = "SELECT SUM(price) as charge_sum, COUNT(DISTINCT charge_order.member_id) as charge_user_num, count(*) as charge_order_num, 
	FROM_UNIXTIME(UNIX_TIMESTAMP(charge_order.create_date),'%Y-%m-%d') as day,
	register_channel as channel
  FROM charge_order 
	LEFT JOIN t_member ON charge_order.member_id=t_member.id
	WHERE charge_order.charge_state=1 AND charge_order.create_date>='{$day} 00:00:00' AND charge_order.create_date<='{$day} 23:59:59'
  GROUP BY register_channel";
        
        $channelChargeData = $dbRead->createCommand($sql)->queryAll();
        
        //删除数据
        $sql = "DELETE FROM channel_daily WHERE day='{$day}'";
        $dbStatistic->createCommand($sql)->execute();
        //插入新数据
        $chargeMap = [];
        foreach($channelChargeData as $data) {
            if(!$data['channel']) {
                $data['channel'] = '无渠道';
            }
            $chargeMap[$data['channel'].'_'.$day] = $data;
        }
        $sql = "INSERT INTO channel_daily (channel, day, registration_num, charge_amount, charge_order_num,"
                . "charge_user_num,charge_user_avg_amount,registration_user_avg_amount)"
                . " VALUES (:channel, :day, :registration_num, :charge_amount, :charge_order_num,"
                . ":charge_user_num,:charge_user_avg_amount,:registration_user_avg_amount)";
        foreach($channelRegisterData as $data) {
            if(!$data['channel']) { 
                $data['channel'] = '无渠道';
            }
            $chargeData = isset($chargeMap[$data['channel'].'_'.$day]) ? $chargeMap[$data['channel'].'_'.$day]:0;
            $dbStatistic->createCommand($sql,[
                ':channel'=>$data['channel'], 
                ':day'=>$day, 
                ':registration_num'=>$data['register_num'],
                ':charge_amount'=>isset($chargeData['charge_sum']) ? $chargeData['charge_sum'] : 0, 
                ':charge_order_num'=>isset($chargeData['charge_order_num']) ? $chargeData['charge_order_num'] : 0, 
                ':charge_user_num'=>isset($chargeData['charge_user_num']) ? $chargeData['charge_user_num'] : 0, 
                ':charge_user_avg_amount'=>$chargeData && $chargeData['charge_user_num']>0 ? $chargeData['charge_sum']/$chargeData['charge_user_num'] : 0,
                ':registration_user_avg_amount'=>$chargeData && $data['register_num'] ? $chargeData['charge_sum']/$data['register_num'] : 0
            ])->execute();
        }
        
    }
    
    /**
     * 用户购买次数统计
     * @param string $day 日期
     * @param integer $count 最少次数
     * @param integer $cache 是否缓存 
     * @return integer
     */
    protected function payCount($day, $count, $judge='=', $cache=0) {
        $cache = Yii::$app->cache;
        
        
        $db = Yii::$app->db;
        $dbPhp = Yii::$app->db_php;
        $dbRead = Yii::$app->dbRd;
        $dbStatistic = Yii::$app->dbStatistic;
        
        $sql = "select count(*) from ("
                . "select count(*) as count,member_id, max(create_date) as last_date"
                . "  from charge_order WHERE charge_state=1 AND create_date<='{$day} 23:59:59' group by member_id having count{$judge}{$count}"
                . ") t where last_date>='{$day} 00:00:00' AND last_date<='{$day} 23:59:59'";
        
        if($cache) {
            $result = $dbRead->cache(function ($dbRead) use($sql) {
                return $dbRead->createCommand($sql)->queryScalar();
            },600);
        } else {
            $result = $dbRead->createCommand($sql)->queryScalar();
        }
        return $result;
    }
    
    public function payCountDaily($day) {
        $db = Yii::$app->db;
        $dbPhp = Yii::$app->db_php;
        $dbRead = Yii::$app->dbRd;
        $dbStatistic = Yii::$app->dbStatistic;
        
        //删除数据
        $sql = "DELETE FROM pay_count_daily WHERE day='{$day}'";
        $dbStatistic->createCommand($sql)->execute();
        
        #注册数据 
        $sql = "SELECT COUNT(*) as register_num
FROM t_member 
WHERE register_date>= '{$day} 00:00:00' AND register_date<='{$day} 23:59:59' ";
        
        $registrationNum = $dbRead->createCommand($sql)->queryScalar();
        
        #用户数量
        $sql = "SELECT COUNT(*)
FROM t_member 
WHERE  register_date<='{$day} 23:59:59' ";
        $userNum = $dbRead->createCommand($sql)->queryScalar();
        
        $sql = "SELECT COUNT(DISTINCT member_id) FROM charge_order c LEFT JOIN t_member m ON c.member_id=m.id"
                . " WHERE c.charge_state=1 AND c.create_date>='{$day} 00:00:00' AND c.create_date<='{$day} 23:59:59'";
        $payUserNum = $dbRead->createCommand($sql)->queryScalar();
        
        $sql = "INSERT INTO pay_count_daily (day, user_num, registration_num, pay_user_num, pay_1, pay_2, pay_3, pay_4,"
                . "pay_5, pay_6, pay_7, pay_8, pay_gt_8)"
                . " VALUES (:day, :user_num, :registration_num, :pay_user_num, :pay_1, :pay_2, :pay_3, :pay_4,"
                . ":pay_5, :pay_6, :pay_7, :pay_8, :pay_gt_8)";
        
        $dbStatistic->createCommand($sql,[
            ':day'=>$day, 
            ':user_num'=>$userNum, 
            ':registration_num'=>$registrationNum,
            ':pay_user_num'=>$payUserNum, 
            ':pay_1'=>$this->payCount($day, 1),
            ':pay_2'=>$this->payCount($day, 2), 
            ':pay_3'=>$this->payCount($day, 3), 
            ':pay_4'=>$this->payCount($day, 4), 
            ':pay_5'=>$this->payCount($day, 5),
            ':pay_6'=>$this->payCount($day, 6), 
            ':pay_7'=>$this->payCount($day, 7), 
            ':pay_8'=>$this->payCount($day, 8),
            ':pay_gt_8'=>$this->payCount($day, 8, '>'),
        ])->execute();
    }
    
    /**
     * 支付日报
     */
    public function payDaily($day) {
        $db = Yii::$app->db;
        $dbPhp = Yii::$app->db_php;
        $dbRead = Yii::$app->dbRd;
        $dbStatistic = Yii::$app->dbStatistic;
        
        #注册数据 
        $sql = "SELECT COUNT(*) as register_num
FROM t_member 
WHERE register_date>= '{$day} 00:00:00' AND register_date<='{$day} 23:59:59' ";
        
        $registrationNum = $dbRead->createCommand($sql)->queryScalar();

        #充值数据 
        $sql = "SELECT SUM(price) as charge_amount, COUNT(DISTINCT charge_order.member_id) as charge_user_num, count(*) as charge_order_num, 
	FROM_UNIXTIME(UNIX_TIMESTAMP(charge_order.create_date),'%Y-%m-%d') as day
  FROM charge_order 
	LEFT JOIN t_member ON charge_order.member_id=t_member.id
	WHERE  charge_state=1 AND  charge_order.create_date>='{$day} 00:00:00' AND charge_order.create_date<='{$day} 23:59:59'";
        
        $chargeData = $dbRead->createCommand($sql)->queryOne();
        
        #首充人数 
        $firstChargeUserNum = $this->payCount($day, 1, '>=');

        #新用户充值人数 新用户充值订单数 新用户充值额 新用户人均充值 新用户人均订单 老用户充值人数 老用户充值订单 老用户充值额 老用户人均充值  老用户人均订单数
        
        #新用户充值数据 
        $sql = "SELECT SUM(price) as charge_amount, COUNT(DISTINCT charge_order.member_id) as charge_user_num, count(*) as charge_order_num, 
	FROM_UNIXTIME(UNIX_TIMESTAMP(charge_order.create_date),'%Y-%m-%d') as day
  FROM charge_order 
	LEFT JOIN t_member ON charge_order.member_id=t_member.id
	WHERE charge_state=1 AND  charge_order.create_date>='{$day} 00:00:00' AND charge_order.create_date<='{$day} 23:59:59'"
        . " AND t_member.register_date>='{$day} 00:00:00' AND t_member.register_date<='{$day} 23:59:59'";
        
        $newUserChargeData = $dbRead->createCommand($sql)->queryOne();
        
        #老用户充值数据 
        $sql = "SELECT SUM(price) as charge_amount, COUNT(DISTINCT charge_order.member_id) as charge_user_num, count(*) as charge_order_num, 
	FROM_UNIXTIME(UNIX_TIMESTAMP(charge_order.create_date),'%Y-%m-%d') as day
  FROM charge_order 
	LEFT JOIN t_member ON charge_order.member_id=t_member.id
	WHERE charge_state=1 AND charge_order.create_date>='{$day} 00:00:00' AND charge_order.create_date<='{$day} 23:59:59'"
        . " AND t_member.register_date<'{$day} 00:00:00'";
        
        $oldUserChargeData = $dbRead->createCommand($sql)->queryOne();
        
        
        //删除数据
        $sql = "DELETE FROM pay_daily WHERE day='{$day}'";
        $dbStatistic->createCommand($sql)->execute();
        
        $sql = "INSERT INTO pay_daily (day, registration_num, charge_amount, charge_order_num,"
                . "charge_user_num, new_user_charge_num,new_user_charge_order_num, new_user_charge_amount,"
                . "old_user_charge_num, old_user_charge_order_num, old_user_charge_amount, first_charge_user_num)"
                . " VALUES (:day, :registration_num, :charge_amount, :charge_order_num,"
                . ":charge_user_num, :new_user_charge_num, :new_user_charge_order_num, :new_user_charge_amount,"
                . ":old_user_charge_num, :old_user_charge_order_num, :old_user_charge_amount, :first_charge_user_num)";
        
        $dbStatistic->createCommand($sql,[
            ':day'=>$day, 
            ':registration_num'=>$registrationNum, 
            ':charge_amount'=>round($chargeData['charge_amount'],2), 
            ':charge_order_num'=>(int)$chargeData['charge_order_num'],
            ':charge_user_num'=>(int)$chargeData['charge_user_num'], 
            ':new_user_charge_num'=>(int)$newUserChargeData['charge_user_num'], 
            ':new_user_charge_order_num'=>(int)$newUserChargeData['charge_order_num'], 
            ':new_user_charge_amount'=>round($newUserChargeData['charge_amount'],2),
            ':old_user_charge_num'=>(int)$oldUserChargeData['charge_user_num'], 
            ':old_user_charge_order_num'=>(int)$oldUserChargeData['charge_order_num'], 
            ':old_user_charge_amount'=>round($oldUserChargeData['charge_amount'],2),
            ':first_charge_user_num'=>$firstChargeUserNum
        ])->execute();
    }

    //用户数据统计
    public function record(){
        #手机号注册用户量
        $db = Yii::$app->db;
        $time = date('Y-m-d',time());
        $sql="SELECT COUNT(*) num FROM t_member WHERE weixin_id IS NULL";
        $count_mobile = $db->createCommand($sql)->queryAll();
        $count_mobile = $count_mobile[0]['num'];

        #手机号注册的用户充值量
        $sql = "SELECT COUNT(*) num FROM t_member d LEFT JOIN charge_order di ON d.id=di.member_id
                WHERE d.weixin_id IS NULL AND di.charge_state=1";
        $charge_mobile = $db->createCommand($sql)->queryAll();
        $charge_mobile = $charge_mobile[0]['num'];

        #手机注册的充值金额量
        $sql = "SELECT sum(di.price) num FROM t_member d LEFT JOIN charge_order di ON d.id=di.member_id
                WHERE d.weixin_id IS NULL AND di.charge_state=1";
        $price_mobile = $db->createCommand($sql)->queryAll();
        $price_mobile = $price_mobile[0]['num'];

        #微信注册用户量
        $sql="SELECT COUNT(*) num FROM t_member WHERE weixin_id IS NOT NULL";
        $count_wechat = $db->createCommand($sql)->queryAll();
        $count_wechat = $count_wechat[0]['num'];

        #微信注册的用户充值量
        $sql = "SELECT COUNT(*) num FROM t_member d LEFT JOIN charge_order di ON d.id=di.member_id
                WHERE d.weixin_id IS NOT NULL AND di.charge_state=1";
        $charge_wechat = $db->createCommand($sql)->queryAll();
        $charge_wechat = $charge_wechat[0]['num'];

        #微信注册的充值金额量
        $sql = "SELECT sum(di.price) num FROM t_member d LEFT JOIN charge_order di ON d.id=di.member_id
                WHERE d.weixin_id IS NOT NULL AND di.charge_state=1";
        $price_wechat = $db->createCommand($sql)->queryAll();
        $price_wechat = $price_wechat[0]['num'];

        #所有用户的抓中次数
        $sql = "SELECT COUNT(*) num FROM t_doll_catch_history d LEFT JOIN t_doll di ON d.doll_id=di.id WHERE d.catch_status='抓取成功' AND di.machine_type NOT IN (1,3)";
        $count_catch = $db->createCommand($sql)->queryAll();
        $count_catch = $count_catch[0]['num'];

        #充值用户的抓中次数
        $sql = "SELECT COUNT(*) num FROM t_doll_catch_history d LEFT JOIN t_doll di ON d.doll_id=di.id WHERE catch_status='抓取成功' AND di.machine_type NOT IN (1,3) AND d.member_id IN( SELECT member_id FROM charge_order WHERE charge_state=1)";
        $charge_catch = $db->createCommand($sql)->queryAll();
        $charge_catch = $charge_catch[0]['num'];

        #充值用户的发货人数
//        $sql = "SELECT COUNT(*) num FROM doll_order_goods WHERE member_id IN (SELECT member_id FROM charge_order WHERE charge_state=1)";
//        $charge_order = $db->createCommand($sql)->queryAll();
//        $charge_order = $charge_order[0]['num'];

        $sql = "SELECT COUNT(*) num FROM t_doll_order WHERE status IN ('已发货','申请发货') AND order_by IN (SELECT member_id FROM charge_order WHERE charge_state=1)";
        $charge_order = $db->createCommand($sql)->queryAll();
        $charge_order = $charge_order[0]['num'];

        #充值用户的发货量
        $sql = "SELECT COUNT(*) num FROM t_doll_order WHERE `status`='已发货' AND order_by IN (SELECT member_id FROM charge_order WHERE charge_state=1)";
        $count_order = $db->createCommand($sql)->queryAll();
        $count_order = $count_order[0]['num'];

        #数据入库
        $deleteSql = "DELETE FROM record WHERE day=:day";
        $insert_sql = "insert into record(mobile_register,mobile_charge,mobile_price,wehcat_register,wechat_charge,wechat_price,catch_num,charge_num,
                       charge_order,order_num,day) VALUES(:mobile_register,:mobile_charge,:mobile_price,:wechat_register,:wechat_charge,:wechat_price,:catch_num,
                        :charge_num,:charge_order,:order_num,:day)";
        $db->createCommand($deleteSql, [
            ':day'=>$time,
        ])->execute();
        $db->createCommand($insert_sql,[
            ':mobile_register'=>$count_mobile,
            ':mobile_charge'=>$charge_mobile,
            ':mobile_price'=>$price_mobile,
            ':wechat_register'=>$count_wechat,
            ':wechat_charge'=>$charge_wechat,
            ':wechat_price'=>$price_wechat,
            ':catch_num'=>$count_catch,
            ':charge_num'=>$charge_catch,
            ':charge_order'=>$charge_order,
            ':order_num'=>$count_order,
            ':day'=>$time,
        ])->execute();
    }

    //金币 钻石 统计
    public function coins(){
        $db = Yii::$app->db;
        $time = date('Y-m-d 00:00:00',time());
        #金币数量
        $sql = "select sum(coins) num from account";
        $rows = $db->createCommand($sql)->queryAll();
        $coins = $rows[0]['num'];

        #钻石数量
        $sql = "select sum(superTicket) num from account";
        $rows = $db->createCommand($sql)->queryAll();
        $superTickets = $rows[0]['num'];

        #金币购买量
        $sql = "select sum(coins_charge) num from charge_order WHERE create_date>'$time'";
        $rows = $db->createCommand($sql)->queryAll();
        $coins_charge = $rows[0]['num'];

        #钻石购买量
        $sql = "select sum(superTicket_charge) num from charge_order WHERE create_date>'$time'";
        $rows = $db->createCommand($sql)->queryAll();
        $superTickets_charge = $rows[0]['num'];

        #金币消耗量
        $sql = "select d.doll_id,COUNT(d.doll_id) num from t_doll_catch_history d LEFT JOIN t_doll di ON d.doll_id=di.id WHERE di.machine_type IN (0,1,3) AND catch_date>'$time' GROUP BY d.doll_id";
        $rows = $db->createCommand($sql)->queryAll();
        $coins_cost = [];
        foreach($rows as $k=>$v){
            $doll_id = $v['doll_id'];
            $num = $v['num'];
            $sql = "select price from t_doll WHERE id=$doll_id";
            $rows = $db->createCommand($sql)->queryAll();
            $coin_cost = $rows[0]['price'] * $num;
            array_push($coins_cost,$coin_cost);
        }
        $coins_cost = array_sum($coins_cost);

        #钻石消耗量
        $sql = "select d.doll_id,COUNT(d.doll_id) num from t_doll_catch_history d LEFT JOIN t_doll di ON d.doll_id=di.id WHERE di.machine_type=2 AND catch_date>'$time' GROUP BY d.doll_id";
        $rows = $db->createCommand($sql)->queryAll();
        $superTickets_cost = [];
        foreach($rows as $k=>$v){
            $doll_id = $v['doll_id'];
            $num = $v['num'];
            $sql = "select price from t_doll WHERE id=$doll_id";
            $rows = $db->createCommand($sql)->queryAll();
            $superTicket_cost = $rows[0]['price'] * $num;
            array_push($superTickets_cost,$superTicket_cost);
        }
        $superTickets_cost = array_sum($superTickets_cost);

        #数据入库
        $deleteSql = "DELETE FROM trade WHERE day=:day";
        $insert_sql = "insert into trade(coins,superTickets,coins_charge,superTickets_charge,coins_cost,superTickets_cost,day) VALUES(:coins,:superTickets,:coins_charge,:superTickets_charge,:coins_cost,:superTickets_cost,:day)";
        $db->createCommand($deleteSql, [
            ':day'=>$time,
        ])->execute();
        $db->createCommand($insert_sql,[
            ':coins'=>$coins,
            ':superTickets'=>$superTickets,
            ':coins_charge'=>$coins_charge,
            ':superTickets_charge'=>$superTickets_charge,
            ':coins_cost'=>$coins_cost,
            ':superTickets_cost'=>$superTickets_cost,
            ':day'=>$time,
        ])->execute();
    }
}
