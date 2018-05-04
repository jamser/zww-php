<?php

namespace console\modules\doll\controllers;

use Yii;
use common\models\doll\Machine;

/**
 * 监控控制
 */
class MonitorController extends \yii\console\Controller
{
    
    /**
     * 机器抓娃娃概率监控
     */
    public function actionMachineRate() {
        $date = date('Y-m-d H:i:s',time()-360);
        $table = Machine::tableName();
        $sql = "SELECT COUNT(*) as num,h.doll_id, d.machine_type FROM t_doll_catch_history h LEFT JOIN t_doll d ON h.doll_id=d.id  WHERE catch_date>='".$date."' AND catch_status='抓取成功'"
                . " GROUP BY member_id";
        $db = Yii::$app->db;
        $rows = $db->createCommand($sql)->queryAll();
        foreach($rows as $row) {
            $dollId = $row['doll_id'];
            $catchNum = $row['num'];
            
            $update = false;
            $catchLevel = 0;
            if($dollId && ($catchNum>1) && ($row['machine_type'] == 0)) {
                //加入报警 把房间设置了维修中
                $update = true;
                $catchLevel = 2;
            } else if($dollId && ($catchNum>5) && ($row['machine_type']==2)) {
                $update = true;
                $catchLevel = 6;
            }
//            else if($dollId && ($catchNum>=6) && ($row['machine_type']==2)){
//                $update = true;
//                $catchLevel = 6;
//            }
            
            if($update) {
                $sql = 'UPDATE '.$table.' SET machine_status="维修中" WHERE id='.$dollId;
                $db->createCommand($sql)->execute();
                
                $sql = 'INSERT INTO t_doll_monitor (dollId,alert_type,alert_number,description,created_date,created_by,modified_date,'
                        . 'modified_by) VALUES (:dollId,:alert_type,:alert_number,:description,:created_date,:created_by,:modified_date,'
                        . ':modified_by)';
                $db->createCommand($sql,[
                    ':dollId'=>$dollId,
                    ':alert_type'=>'系统自动监控',
                    ':alert_number'=>1,
                    ':description'=>'6分钟内同一用户抓取成功超过'.$catchLevel.'次',
                    ':created_date'=>date('Y-m-d H:i:s'),
                    ':created_by'=>0,
                    ':modified_date'=>date('Y-m-d H:i:s'),
                    ':modified_by'=>0,
                ])->execute();
            }
        }
    }

}
