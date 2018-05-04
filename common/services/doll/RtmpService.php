<?php
namespace common\services\doll;

use common\models\DollRtmp;
use frontend\models\Doll;
use Yii;

class RtmpService extends \common\services\BaseService{
    //直播流状态统计入库
    public function machineRtmp(){
        $dbPhp = Yii::$app->db_php;
        $table = DollRtmp::tableName();
        $sql = "select * from t_doll d LEFT join doll_h5 di on d.id=di.id WHERE di.rtmpUrlH5 is null";
        $dollData = Yii::$app->db->createCommand($sql)->queryAll();
//        $dollData =Doll::find()->asArray()->all();
        foreach($dollData as $k=>$v){
            $machine_id = $v['id'];
            $machine_code = $v['machine_code'];
            $name = $v['name'];
            $time = date('Y-m-d H:i:s',time());
            $machine_url = $v['machine_url'];
            $machine_status = $v['machine_status'];
            $status = $this->rtmpStatus($machine_url);
            if($status == 1){
                $status = "开启";
            }elseif($status == 3){
                $status = "关闭";
            }elseif($status == 0){
                $status = "断流";
            }else{
                $status = '未找到流';
            }
            $deleteSql = "DELETE FROM {$table} WHERE machine_code=:machine_code ";

            $insertSql = "INSERT INTO {$table} (machine_id, machine_code, name,create_date,rtmp_status,machine_url,machine_status) VALUES (:machine_id, :machine_code, :name,:create_date,:rtmp_status,:machine_url,:machine_status)";

            $dbPhp->createCommand($deleteSql, [
                ':machine_code'=>$machine_code ? $machine_code : 0,
            ])->execute();

            $dbPhp->createCommand($insertSql, [
                ':machine_id'=> $machine_id ? $machine_id : 0,
                ':machine_code'=>$machine_code ? $machine_code : 0,
                ':name'=>$name ? $name : 0,
                ':create_date'=>$time,
                ':rtmp_status'=>$status,
                ':machine_url'=>$machine_url,
                ':machine_status'=>$machine_status,
            ])->execute();
        }
    }

    //单个机器查询推流状况
    public function oneRtmp($machine_code){
        $dbPhp = Yii::$app->db_php;
        $table = DollRtmp::tableName();
        $dollData =Doll::find()->where(['machine_code'=>$machine_code])->asArray()->one();
        if(empty($dollData)){
            throw new \Exception('找不到该编码，请输入正确的机器编码');
        }else{
            $machine_id = $dollData['id'];
            $machine_code = $dollData['machine_code'];
            $name = $dollData['name'];
            $time = date('Y-m-d H:i:s',time());
            $machine_url = $dollData['machine_url'];
            $machine_status = $dollData['machine_status'];
            $status = $this->rtmpStatus($machine_url);
            if($status == 1){
                $status = "开启";
            }elseif($status == 3){
                $status = "关闭";
            }elseif($status == 0){
                $status = "断流";
            }else{
                $status = '未找到流';
            }
            $deleteSql = "DELETE FROM {$table} WHERE machine_code=:machine_code ";

            $insertSql = "INSERT INTO {$table} (machine_id, machine_code, name,create_date,rtmp_status,machine_url,machine_status) VALUES (:machine_id, :machine_code, :name,:create_date,:rtmp_status,:machine_url,:machine_status)";

            $dbPhp->createCommand($deleteSql, [
                ':machine_code'=>$machine_code ? $machine_code : 0,
            ])->execute();

            $dbPhp->createCommand($insertSql, [
                ':machine_id'=> $machine_id ? $machine_id : 0,
                ':machine_code'=>$machine_code ? $machine_code : 0,
                ':name'=>$name ? $name : 0,
                ':create_date'=>$time,
                ':rtmp_status'=>$status,
                ':machine_url'=>$machine_url,
                ':machine_status'=>$machine_status,
            ])->execute();
        }
    }

    //判断房间直播流状态
    function rtmpStatus($id){
        $appid = "1255545077";
        $interface = "Live_Channel_GetStatus";
        $time = strtotime("+1 day");
        $key = "5675547bf825cf58dd74bdaafc1e372d";
        $sign = md5($key.$time);
        $channel_id = "16787_"."$id";
        //$request_url = "http://fcgi.video.qcloud.com/common_access?appid=$appid&interface=$interface&Param.s.channel_id=$channel_id&t=$time&sign=$sign";
        $request_url = "http://fcgi.video.qcloud.com/common_access?appid=$appid&interface=$interface&Param.s.channel_id=$channel_id&t=$time&sign=$sign";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        if(empty($result['output'])){
            $status = '未找到流';
        }else{
            $status = $result['output'][0]['status'];
        }
        return $status;
    }

    private function response($text)
    {
        return json_decode($text, true);
    }

}