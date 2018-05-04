<?php
namespace common\services\doll;

use Yii;

class ChargeService extends \common\services\BaseService{
    public function charge(){
        $db = Yii::$app->db;
        $dbPhp = Yii::$app->db_php;
        $time = date('Y-m-d',time());
        $times = date('Y-m-d 00:00:00',time());
        $sql = "SELECT SUM(price) as price,charge_name as name,count(*) as num,COUNT(DISTINCT member_id) as number, count(*)/count(DISTINCT member_id) as one FROM charge_order WHERE charge_state=1 AND create_date>='$time'  GROUP BY charge_name ";
        $rows = $db->createCommand($sql)->queryAll();
        foreach($rows as $k=>$v){
            $deleteSql = "DELETE FROM charge_info WHERE day=:day AND charge_name=:charge_name";
            $insert_sql = "insert into charge_info(price,charge_name,charge_num,buy_num,buy_one,day) VALUES(:price,:charge_name,:charge_num,:buy_num,:buy_one,:day)";
            $dbPhp->createCommand($deleteSql, [
                ':day'=>$time,
                ':charge_name'=>$v['name'],
            ])->execute();
            $dbPhp->createCommand($insert_sql,[
                ':price'=>$v['price'],
                ':charge_name'=>$v['name'],
                ':charge_num'=>$v['num'],
                ':buy_num'=>$v['number'],
                ':buy_one'=>$v['one'],
                ':day'=>$time,
            ])->execute();
        }
    }
}