<?php
namespace backend\modules\doll\controllers;

use Yii;
use yii\web\Controller;

class RecordController extends Controller{
    public function actionRecord(){
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
        $sql = "SELECT COUNT(*) num FROM t_doll_catch_history d LEFT JOIN t_doll di ON d.doll_id=di.id WHERE d.catch_status='抓取成功' AND di.machine_type!=1";
        $count_catch = $db->createCommand($sql)->queryAll();
        $count_catch = $count_catch[0]['num'];

        #充值用户的抓中次数
        $sql = "SELECT COUNT(*) num FROM t_doll_catch_history d LEFT JOIN t_doll di ON d.doll_id=di.id WHERE catch_status='抓取成功' AND di.machine_type!=1 AND d.member_id IN( SELECT member_id FROM charge_order WHERE charge_state=1)";
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

    public function actionIndex(){
        $db = Yii::$app->db;
        $day = Yii::$app->getRequest()->get('day',null);
        $conditions = $params = [];
        if($day){
            $conditions[] = '`day`=:day';
            $params[':day'] = trim($day).' 00:00:00';
        }
        $sql = 'SELECT COUNT(*) FROM record '
            .($conditions ? 'WHERE '.implode(' AND ', $conditions) : '');
        $count = $db->createCommand($sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);

        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $sql = 'SELECT * FROM record'
            .($conditions ? ' WHERE '.implode(' AND ', $conditions) : '')
            . " order by day desc limit $offset,$size";
        $rows = $db->createCommand($sql, $params)->queryAll();

        return $this->render('record', [
            'models' => $rows,
            'pages' => $pages,
        ]);
    }

    //金币 钻石 统计
    public function actionCoins(){
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
        $sql = "select d.doll_id,COUNT(d.doll_id) num from t_doll_catch_history d LEFT JOIN t_doll di ON d.doll_id=di.id WHERE di.machine_type IN (0,1) AND catch_date>'$time' GROUP BY d.doll_id";
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

    ////金币 钻石 展示
    public function actionCoinsData(){
        $db = Yii::$app->db;
        $day = Yii::$app->getRequest()->get('day',null);
        $conditions = $params = [];
        if($day){
            $conditions[] = '`day`=:day';
            $params[':day'] = trim($day).' 00:00:00';
        }
        $sql = 'SELECT COUNT(*) FROM trade '
            .($conditions ? 'WHERE '.implode(' AND ', $conditions) : '');
        $count = $db->createCommand($sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);

        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $sql = 'SELECT * FROM trade'
            .($conditions ? ' WHERE '.implode(' AND ', $conditions) : '')
            . " order by day desc limit $offset,$size";
        $rows = $db->createCommand($sql, $params)->queryAll();

        return $this->render('trade', [
            'models' => $rows,
            'pages' => $pages,
        ]);
    }
}