<?php
namespace backend\modules\doll\controllers;

use common\models\PayCountDaily;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\services\doll\StatisticService;
use common\models\doll\Statistic;
use common\enums\StatisticTypeEnum;

class PayController extends Controller{
    public $enableCsrfValidation = false;

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','pay-daily','channel-daily','charge','chart','charge-chart','charge-status','chart-n'

                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index','pay-daily','channel-daily','charge','chart','charge-chart','charge-status','chart-n'],
                        'allow' => true,
                        'roles' => ['超级管理员'],
                    ],
                ],
            ],
        ];
    }
    public function actionIndex(){
        $dbStatistic = Yii::$app->dbStatistic;
        $time = Yii::$app->getRequest()->get('day',null);
        $conditions = $params = [];
        if($time) {
            $conditions[] = '`day`=:day';
            $params[':day'] = trim($time);
        }
        $sql = 'SELECT COUNT(*) FROM pay_count_daily '.($conditions ? 'WHERE'.implode(' AND ', $conditions) : '');
        $count = $dbStatistic->createCommand($sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);

        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $sql = 'SELECT * FROM pay_count_daily'
            .($conditions ? ' WHERE '.implode(' AND ', $conditions) : '')
            . "  ORDER BY  day DESC limit $offset,$size";
        $rows = $dbStatistic->createCommand($sql, $params)->queryAll();
        return $this->render('index', [
            'models' => $rows,
            'pages' => $pages,
        ]);
    }

    //支付日报
    public function actionPayDaily(){
        $dbStatistic = Yii::$app->dbStatistic;
        $time = Yii::$app->getRequest()->get('day',null);
        $conditions = $params = [];
        if($time) {
            $conditions[] = '`day`=:day';
            $params[':day'] = trim($time);
        }
        $sql = 'SELECT COUNT(*) FROM pay_daily '.($conditions ? 'WHERE'.implode(' AND ', $conditions) : '');
        $count = $dbStatistic->createCommand($sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);

        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $sql = 'SELECT * FROM pay_daily'
            .($conditions ? ' WHERE '.implode(' AND ', $conditions) : '')
            . "  ORDER BY  day DESC limit $offset,$size";
        $rows = $dbStatistic->createCommand($sql, $params)->queryAll();
        return $this->render('pay_daily', [
            'models' => $rows,
            'pages' => $pages,
        ]);
    }

    public function actionChannelDaily(){
        $dbStatistic = Yii::$app->dbStatistic;
        $time = Yii::$app->getRequest()->get('day',null);
        $channel = Yii::$app->getRequest()->get('channel',null);
        $register_num = Yii::$app->getRequest()->get('register_num',null);
        $charge_num = Yii::$app->getRequest()->get('charge_num',null);
        $charge_rate = Yii::$app->getRequest()->get('charge_rate',null);
        $charge_amount = Yii::$app->getRequest()->get('charge_amoun',null);
        $charge_one = Yii::$app->getRequest()->get('charge_one',null);
        $register_one = Yii::$app->getRequest()->get('register_one',null);

        $conditions = $params = [];
        if($time) {
            $conditions[] = '`day`=:day';
            $params[':day'] = trim($time);
        }
        if($channel && $channel!='渠道') {
            $conditions[] = '`channel`=:channel';
            $params[':channel'] = trim($channel);
        }

        if($register_num){
            $conditionss = 'registration_num '.$register_num;
        }elseif($charge_num){
            $conditionss = 'charge_user_num '.$charge_num;
        }elseif($charge_rate){
            $conditionss = 'charge_user_num/registration_num '.$charge_rate;
        } elseif($charge_amount){
            $conditionss = 'charge_amount '.$charge_amount;
        }elseif($charge_one){
            $conditionss = 'charge_user_avg_amount '.$charge_one;
        } elseif($register_one){
            $conditionss = 'registration_user_avg_amount '.$register_one;
        } else{
            $conditionss = 'day DESC';
        }

        $sql = 'SELECT COUNT(*) FROM channel_daily '.($conditions ? 'WHERE'.implode(' AND ', $conditions) : '');
        $count = $dbStatistic->createCommand($sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);

        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $sql = 'SELECT * FROM channel_daily'
            .($conditions ? ' WHERE '.implode(' AND ', $conditions) : '')
            . "  ORDER BY  $conditionss limit $offset,$size";
        $rows = $dbStatistic->createCommand($sql, $params)->queryAll();

        return $this->render('channel_daily', [
            'models' => $rows,
            'pages'=>$pages
        ]);
    }

    public function actionCharge(){
        $db = Yii::$app->db_php;
        $time = Yii::$app->getRequest()->get('day',null);
        $conditions = $params = [];
        if($time) {
            $conditions[] = '`day`=:day';
            $params[':day'] = trim($time);
        }
        $sql = 'SELECT COUNT(*) FROM charge_info '.($conditions ? 'WHERE'.implode(' AND ', $conditions) : '');
        $count = $db->createCommand($sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);

        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $sql = 'select * from charge_info'
            .($conditions ? ' WHERE '.implode(' AND ', $conditions) : '')
            ." ORDER BY day DESC limit $offset,$size ";
        $rows = $db->createCommand($sql,$params)->queryAll();
        return $this->render('charge',[
            'models' => $rows,
            'pages' => $pages,
        ]);
    }

    public function actionChargeStatus(){
        $service = new \common\services\doll\ChargeService();
        $service->charge();
    }

    //充值情况折线图
    public function actionChargeChart(){
        $day = Yii::$app->getRequest()->get('day',null);
        if($day && $day != '周期'){
            $num = '-'.$day;
        }else{
            $num=-7;
        }
        $e_day = date('Y-m-d',strtotime("$num days"));
        $days = array();
        $new_rate = array();
        $old_rate = array();
        $sql = "SELECT * FROM pay_daily GROUP BY day";
        $data = Yii::$app->dbStatistic->createCommand($sql)->queryAll();
        foreach($data as $k=>$v){
            $day = $v['day'];
            if($day > $e_day){
                $new_r = round(($v['new_user_charge_num']/$v['registration_num'])*100,2);
                $old_r = round(($v['old_user_charge_num']/$v['registration_num'])*100,2);
                array_unshift($days,$day);
                array_unshift($new_rate,$new_r);
                array_unshift($old_rate,$old_r);
            }
        }
        $days = array_reverse($days);
        $new_rate = array_reverse($new_rate);
        $old_rate = array_reverse($old_rate);

        return $this->render('charge_chart',[
            'days'=>$days,
            'new_rate'=>$new_rate,
            'old_rate'=>$old_rate,
        ]);
    }

    //礼包折线图
    public function actionChart(){
        $day = Yii::$app->getRequest()->get('day',null);
        if($day && $day != '周期'){
            $d = '-'.$day;
        }else{
            $d=-7;
        }
        $day = $first_price = $h_price = $z_price = $m_price = $w_price = $y_price =
        $n_price = $t_price = $b_price = $a_price = $v_price = $nz_price = $hz_price = $zc_price = array();
        for($i=0;$i>$d;$i--){
            $time = strtotime("$i day");
            $times = date("Y-m-d",$time);
            $sql = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='首充包'";
            $price_f = Yii::$app->db_php->createCommand($sql)->queryAll();
            if(empty($price_f)){
                $price_f = 0;
            }else{
                $price_f = round(($price_f[0]['price']),2);
            }
            $sql_h = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='寒假包'";
            $price_h = Yii::$app->db_php->createCommand($sql_h)->queryAll();
            if(empty($price_h)){
                $price_h = 0;
            }else{
                $price_h = round(($price_h[0]['price']),2);
            }
            $sql_z = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='钻石礼包'";
            $price_z = Yii::$app->db_php->createCommand($sql_z)->queryAll();
            if(empty($price_z)){
                $price_z = 0;
            }else{
                $price_z = round(($price_z[0]['price']),2);
            }
            $sql_m = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='超多钻石'";
            $price_m = Yii::$app->db_php->createCommand($sql_m)->queryAll();
            if(empty($price_m)){
                $price_m = 0;
            }else{
                $price_m = round(($price_m[0]['price']),2);
            }
            $sql_w = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='周卡'";
            $price_w = Yii::$app->db_php->createCommand($sql_w)->queryAll();
            if(empty($price_w)){
                $price_w = 0;
            }else{
                $price_w = round(($price_w[0]['price']),2);
            }
            $sql_y = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='月卡'";
            $price_y = Yii::$app->db_php->createCommand($sql_y)->queryAll();
            if(empty($price_y)){
                $price_y = 0;
            }else{
                $price_y = round(($price_y[0]['price']),2);
            }
            $sql_n = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='新年礼包'";
            $price_n = Yii::$app->db_php->createCommand($sql_n)->queryAll();
            if(empty($price_n)){
                $price_n = 0;
            }else{
                $price_n = round(($price_n[0]['price']),2);
            }
            $sql_t = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='半糖礼包'";
            $price_t = Yii::$app->db_php->createCommand($sql_t)->queryAll();
            if(empty($price_t)){
                $price_t = 0;
            }else{
                $price_t = round(($price_t[0]['price']),2);
            }
            $sql_b = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='百合包'";
            $price_b = Yii::$app->db_php->createCommand($sql_b)->queryAll();
            if(empty($price_b)){
                $price_b = 0;
            }else{
                $price_b = round(($price_b[0]['price']),2);
            }
            $sql_a = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='爱久久'";
            $price_a = Yii::$app->db_php->createCommand($sql_a)->queryAll();
            if(empty($price_a)){
                $price_a = 0;
            }else{
                $price_a = round(($price_a[0]['price']),2);
            }
            $sql_v = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='土豪包'";
            $price_v = Yii::$app->db_php->createCommand($sql_v)->queryAll();
            if(empty($price_v)){
                $price_v = 0;
            }else{
                $price_v = round(($price_v[0]['price']),2);
            }
            $sql_nz = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='新年钻石包'";
            $price_nz = Yii::$app->db_php->createCommand($sql_nz)->queryAll();
            if(empty($price_nz)){
                $price_nz = 0;
            }else{
                $price_nz = round(($price_nz[0]['price']),2);
            }
            $sql_hz = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='豪华钻石包'";
            $price_hz = Yii::$app->db_php->createCommand($sql_hz)->queryAll();
            if(empty($price_hz)){
                $price_hz = 0;
            }else{
                $price_hz = round(($price_hz[0]['price']),2);
            }
            $sql_zc = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='招财大礼包'";
            $price_zc = Yii::$app->db_php->createCommand($sql_zc)->queryAll();
            if(empty($price_zc)){
                $price_zc = 0;
            }else{
                $price_zc = round(($price_zc[0]['price']),2);
            }
            array_unshift($day,$times);
            array_unshift($first_price,$price_f);
            array_unshift($h_price,$price_h);
            array_unshift($z_price,$price_z);
            array_unshift($m_price,$price_m);
            array_unshift($w_price,$price_w);
            array_unshift($y_price,$price_y);
            array_unshift($n_price,$price_n);
            array_unshift($t_price,$price_t);
            array_unshift($b_price,$price_b);
            array_unshift($a_price,$price_a);
            array_unshift($v_price,$price_v);
            array_unshift($nz_price,$price_nz);
            array_unshift($hz_price,$price_hz);
            array_unshift($zc_price,$price_zc);
        }

        return $this->render('chart',[
            'day'=>$day,
            'first_price'=>$first_price,
            'h_price'=>$h_price,
            'z_price'=>$z_price,
            'm_price'=>$m_price,
            'w_price'=>$w_price,
            'y_price'=>$y_price,
            'n_price'=>$n_price,
            't_price'=>$t_price,
            'b_price'=>$b_price,
            'a_price'=>$a_price,
            'v_price'=>$v_price,
            'nz_price'=>$nz_price,
            'hz_price'=>$hz_price,
            'zc_price'=>$zc_price,
        ]);
    }

    //礼包折线图
    public function actionChartN(){
        $day = Yii::$app->getRequest()->get('day',null);
        $name = Yii::$app->getRequest()->get('name',null);
        if($day && $day != '周期'){
            $d = '-'.$day;
        }else{
            $d=-7;
        }
        if(empty($name)){
            $name = '首充包';
        }
        $day = $prices = array();
        for($i=0;$i>$d;$i--){
            $time = strtotime("$i day");
            $times = date("Y-m-d",$time);
            $sql = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='$name'";
            $price = Yii::$app->db_php->createCommand($sql)->queryAll();
            if(empty($price)){
                $price = 0;
            }else{
                $price = round(($price[0]['price']),2);
            }
            array_unshift($day,$times);
            array_unshift($prices,$price);
        }
        $db = Yii::$app->db;
        $sql = "SELECT charge_name FROM t_charge_rules WHERE rules_status=1 ";
        $chargeNames = $db->createCommand($sql)->queryAll();

        return $this->render('chartN',[
            'day'=>$day,
            'name'=>$name,
            'prices'=>$prices,
            'chargeNames' => $chargeNames,
        ]);
    }
}