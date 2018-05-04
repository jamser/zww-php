<?php

namespace console\controllers;

use Yii;

use common\models\gift\Gift;
use common\models\gift\SendRecord;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GiftController implements the CRUD actions for Gift model.
 */
class CountController extends \yii\console\Controller
{
    public  function  __construct(){
        $t = time();
        $starTtime = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));  //当天开始时间
        $endTime = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t)); //当天结束时间

        $starTtime = date("Y-m-d H:i:s",$starTtime);
        $endTime = date("Y-m-d H:i:s",$endTime);
//      根据时间查用户新注册的数量
        $sql = "select count(*) as counts from t_member where register_date > '$starTtime' and register_date < '$endTime'";
        $data = Yii::$app->db->createCommand($sql)->queryScalar();
//        print_r($data);die;

//        查询日期
        $sql1 = "select * from t_member where register_date > '$starTtime' and register_date < '$endTime'";
        $data1 = Yii::$app->db->createCommand($sql1)->queryScalar();
//         print_r($data1);die;

//        注册手机类型为安卓的人数
        $sql2 = "select count(*) as people from t_member where register_from = 'android' and register_date > '$starTtime' and register_date < '$endTime'";
        $data2 = Yii::$app->db->createCommand($sql2)->queryScalar();
//          print_r($data2);die;

        //        注册手机类型为苹果的人数
        $sql3 = "select count(*) as people from t_member where register_from = 'ios' and register_date > '$starTtime' and register_date < '$endTime'";
        $data3 = Yii::$app->db->createCommand($sql3)->queryScalar();
//    print_r($data3);die;

//      根据用户查询订单数量
        $order = "SELECT count(*) as orderNumber FROM charge_order LEFT JOIN t_member ON charge_order.member_id=t_member.id where register_date > '$starTtime' and register_date < '$endTime'";
        $data4 = Yii::$app->db->createCommand($order)->queryScalar();
//    print_r($data4);die;
//      支付的金额
        $money = "SELECT sum(price) as prices FROM charge_order LEFT JOIN t_member ON charge_order.member_id=t_member.id  where register_date > '$starTtime' and register_date < '$endTime'";
        $data5 = Yii::$app->db->createCommand($money)->queryAll();
//        print_r($data5);die;
        $price_num=$data5[0]['prices'];

        //入库操作
        $array = array(
            'register_people_count'  => $data,    //注册人数
            'android_people'    => $data2,
            'ios_people'    => $data3,
            'order_number'    => $data4,
            'price'    =>  $price_num ,
            'today_date'    =>  date("Y-m-d H:i:s",time())
        );
        $result = Yii::$app->db->createCommand()->insert('t_statistic',$array)->execute();
        if($result){
            echo '<script>alert("入库成功");location.href="?r=statistic/index"</script>';
        }else{
            echo '<script>alert("入库失败");location.href="?r=count"</script>';
        }
    }

}
