<?php

namespace backend\modules\doll\controllers;

use frontend\models\Doll;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;

use backend\models\PublishAssetForm;
use common\models\doll\Statistic;
use common\models\doll\MachineStatistic;
use common\services\doll\StatisticService;
use common\enums\StatisticTypeEnum;

/**
 * FileController implements the CRUD actions for File model.
 */
class StatisticController extends Controller {

    public $enableCsrfValidation = false;
    
    public function behaviors() {
        return [
//            'as access' => [
//                'class' => 'mdm\admin\components\AccessControl',
//                'allowActions' => [
//                    //'index',
//                    //'machine-rate',
//                ]
//            ],
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['logout', 'signup', 'login'],
                'rules' => [
//                    [
//                        'actions' => ['callback'],
//                        'allow' => true,
//                    ],
                    [
                        'actions' => ['index','machine-rate','chart','get-chart-data','week-rate','test1','machine-rate1'
                                
                            ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['overview','chart','export-pay-user','week-rate','machine-rate1'],
                        'allow' => true,
                        'roles' => ['超级管理员'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex($refresh=0) {
        $cache = Yii::$app->cache;
        $updateTodayTime = $cache->get('dollStatisticUpdateTime');
        if (!$updateTodayTime || ($updateTodayTime < (time() - 1800)) || $refresh) {
            $service = new StatisticService();
            $data = $service->run('today', 1);
            $cache->set('dollStatisticUpdateTime', time(), 3600);
        }
        $model = Statistic::find();
        $pages = new \yii\data\Pagination(['totalCount' => $model->count()]);
        $models = $model->offset($pages->offset)
                ->limit($pages->limit)
                ->orderBy('day DESC')
                ->all();
        return $this->render('index', [
                'models' => $models,
                'pages' => $pages,
                    
        ]);
    }
    
    public function actionOverview($refresh=0) {
        $cache = Yii::$app->cache;
        $updateTodayTime = $cache->get('dollStatisticUpdateTime');
        if (!$updateTodayTime || ($updateTodayTime < (time() - 1800)) || $refresh) {
            $service = new StatisticService();
            $data = $service->run('today', 1);
            $cache->set('dollStatisticUpdateTime', time(), 3600);
        }
        $model = Statistic::find();
        $pages = new \yii\data\Pagination(['totalCount' => $model->count()]);
        $models = $model->offset($pages->offset)
                ->limit($pages->limit)
                ->orderBy('day DESC')
                ->all();
        return $this->render('overview', [
                'models' => $models,
                'pages' => $pages,
        ]);
    }
    
    /**
     * 抓住概率
     */
    public function actionMachineRate($refresh=0) {
//        $db = Yii::$app->db_php;
        $db = Yii::$app->db;
        $start_time = Yii::$app->getRequest()->get('start_time',null);
        $machine_id = Yii::$app->getRequest()->get('machine_id',null);
        $machine_code = Yii::$app->getRequest()->get('machine_code',null);
        $grab_count = Yii::$app->getRequest()->get('grab_count',null);
        $play_count = Yii::$app->getRequest()->get('play_count',null);
        $play_num = Yii::$app->getRequest()->get('play_num',null);
        $machine_name = Yii::$app->getRequest()->get('machine_name',null);
        $code = Yii::$app->getRequest()->get('code',null);
        $name = Yii::$app->getRequest()->get('name',null);
        $status = Yii::$app->getRequest()->get('status',null);
        $price = Yii::$app->getRequest()->get('price',null);
        $machine_type = Yii::$app->getRequest()->get('machine_type',null);
        $machine_device_name = Yii::$app->getRequest()->get('machine_device_name',null);
        $machine_state = Yii::$app->getRequest()->get('machine_state',null);
        $s_time = strtotime($start_time);
        $end_time = strtotime($start_time .'23:59:59');
        $conditions = $params = [];

        if($start_time) {
            $conditions[] = 'd.`start_time`>=:start_time';
            $params[':start_time'] = trim($s_time);
        }

        if($start_time) {
            $conditions[] = 'd.`start_time`<=:end_time';
            $params[':end_time'] = trim($end_time);
        }

        if($machine_id) {
            $conditions[] = 'd.`machine_id`=:machine_id';
            $params[':machine_id'] = trim($machine_id);
        }

        if($machine_code) {
            $conditions[] = 'd.`machine_code`=:machine_code';
            $params[':machine_code'] = trim($machine_code);
        }

        if($name) {
            $conditions[] = 'di.`name` like :name';
            $params[':name'] = '%'.trim($name).'%';
        }

        if($machine_device_name) {
            $conditions[] = 'd.`machine_device_name`=:machine_device_name';
            $params[':machine_device_name'] = trim($machine_device_name);
        }

        if($machine_type && $machine_type!='种类') {
            if($machine_type == '普通'){
                $conditions[] = 'di.`machine_type`=:machine_type';
                $params[':machine_type'] = 0;
            }else{
                $conditions[] = 'di.`machine_type`=:machine_type';
                $params[':machine_type'] = trim($machine_type);
            }
        }

        if($machine_state && $machine_state != '状态'){
            if($machine_state == '在线'){
                $conditions[] = "di.`machine_status` in ('空闲中','游戏中')";
            }else{
                $conditions[] = 'di.`machine_status`=:machine_status';
                $params[':machine_status'] = trim($machine_state);
            }
        }

        if(1) {
            $conditions[] = 'd.`type`=:type';
            $params[':type'] = StatisticTypeEnum::TYPE_DAY;
        }

        if($play_count){
            $conditionss = ',d.`play_count` '.$play_count;
        }elseif($grab_count){
            $conditionss = ',d.`grab_count`/d.`play_count` '.$grab_count;
        }elseif($play_num){
            $conditionss = ',d.`grab_count` '.$play_num;
        }elseif($machine_name){
            $conditionss = ',d.`machine_doll_name` '.$machine_name;
        }elseif($code){
            $conditionss = ',d.`machine_code` '.$code;
        }elseif($status){
            $conditionss = ',di.`machine_status` '.$status;
        }elseif($price){
            $conditionss = ',di.`price` '.$price;
        }else{
            $conditionss = ',d.`grab_count`/d.`play_count` DESC';
        }

        $sql = 'SELECT COUNT(*) FROM doll_machine_statistic d '
            . ' LEFT JOIN t_doll di ON d.machine_id=di.id '
            .($conditions ? 'WHERE '.implode(' AND ', $conditions) : '');
        $count = $db->createCommand($sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);

        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        if ($refresh) {
            $service = new StatisticService();
            $startTime = strtotime('today');
            $endTime = $startTime + 86400 -1;
            $service->machineRate($startTime, $endTime, 1, StatisticTypeEnum::TYPE_DAY);
        }

        $sql = 'SELECT * FROM doll_machine_statistic d'
            . ' LEFT JOIN t_doll di ON d.machine_id=di.id  LEFT JOIN machine_status ti ON d.machine_device_name=ti.machine_name '
            .($conditions ? ' WHERE '.implode(' AND ', $conditions) : '')
            . "  ORDER BY d.start_time DESC $conditionss limit $offset,$size";
        $rows = $db->createCommand($sql, $params)->queryAll();

        $status = $prices = [];
        foreach($rows as $k=>$v){
            $doll_id = $v['machine_id'];
            $machineData = Doll::find()->where(['id'=>$doll_id])->asArray()->one();
            $state = $machineData['machine_status'];
            $price = $machineData['price'];
            $status[$doll_id] = $state;
            $prices[$doll_id] = $price;
        }

//        $model = MachineStatistic::find()
//                ->where('type='.StatisticTypeEnum::TYPE_DAY);
//        $pages = new Pagination(['totalCount' => $model->count()]);
//        $models = $model->offset($pages->offset)
//                ->limit($pages->limit)
//                ->orderBy('start_time DESC,play_count DESC')
//                ->all();
        return $this->render('machine-rate', [
                    'models' => $rows,
                    'pages' => $pages,
                    'status' => $status,
                    'prices' => $prices,
        ]);
    }

    public function actionWeekRate(){
        $db = Yii::$app->db;
        $start_time = Yii::$app->getRequest()->get('start_time',null);
        $endTime = Yii::$app->getRequest()->get('end_time',null);
        $s_time = strtotime($start_time.' 00:00:00');
        $e_time = strtotime($endTime.' 23:59:59');
        $conditions = $params = [];
        if($start_time && $endTime){
            $conditions[] = '`start_time`>=:start_time';
            $params[':start_time'] = trim($s_time);
        }

        if($start_time && $endTime){
            $conditions[] = '`start_time`<=:end_time';
            $params[':end_time'] = trim($e_time);
        }

        $sql = 'SELECT COUNT(*) FROM doll_machine_statistic d '
            . ' LEFT JOIN t_doll di ON d.machine_id=di.id '
            .($conditions ? 'WHERE '.implode(' AND ', $conditions) : '');
        $count = $db->createCommand($sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);

        $offset = $pages->getOffset();
        $size = $pages->getLimit();

        $sql = 'SELECT start_time,machine_id,machine_code,machine_doll_name,machine_doll_code,sum(play_count) AS play_count,sum(grab_count) AS grab_count FROM doll_machine_statistic'
            .($conditions ? ' WHERE '.implode(' AND ', $conditions) : '')
            . ' GROUP BY machine_id'
            . "  limit $offset,$size";
        $rows = $db->createCommand($sql, $params)->queryAll();
        return $this->render('week-rate', [
            'models' => $rows,
            'pages' => $pages,
            'startTime' =>$start_time,
            'endTime' => $endTime,
        ]);
    }
    
    public function actionChart() {
        $model = Statistic::find()->where(['type'=> StatisticTypeEnum::TYPE_DAY]);
        $pages = new \yii\data\Pagination([
            'totalCount' => $model->count(),
            'pageSize'=>20
        ]);
        $models = $model->offset($pages->offset)
                ->limit($pages->limit)
                ->orderBy('day DESC')
                ->all();
        $days = [];
        $datas = [
            'newUserNum'=>[],
            'userNum'=>[],
            'amount'=>[],
            'playTimes'=>[],
        ];
        $models = array_reverse($models);
        foreach($models as $model) {
            $day = date('Y-m-d', $model->day);
            $days[] = $day;
            #新用户数量 
            $datas['newUserNum'][] = $model->registration_num;
            
            #用户数量 
            $datas['userNum'][] = $model->user_num;
            
            #金额
            $datas['amount'][] = $model->charge_amount;
            
            #游戏次数 
            $datas['playTimes'][] = $model->play_count;
        }
        return $this->renderPartial('chart', [
            'models'=>$models,
            'days'=>$days,
            'userNum'=>$datas['userNum'],
            'newUserNum'=>$datas['newUserNum'],
            'amount'=>$datas['amount'],
            'playTimes'=>$datas['playTimes'],
        ]);
    }
    
    public function actionGetChartData() {
        $model = Statistic::find()->where(['type'=> StatisticTypeEnum::TYPE_DAY]);
        $pages = new \yii\data\Pagination([
            'totalCount' => $model->count(),
            'pageSize'=>20
        ]);
        $models = $model->offset($pages->offset)
                ->limit($pages->limit)
                ->orderBy('day ASC')
                ->all();
        $days = [];
        $datas = [
            'newUserNum'=>[],
            'userNum'=>[],
            'amount'=>[],
            'playTimes'=>[],
        ];
        foreach($models as $model) {
            $day = date('Y-m-d', $model->day);
            $days[] = $day;
            #新用户数量 
            $datas['newUserNum'][] = $model->registration_num;
            
            #用户数量 
            $datas['userNum'][] = $model->user_num;
            
            #金额
            $datas['amount'][] = $model->charge_amount;
            
            #游戏次数 
            $datas['playTimes'][] = $model->play_count;
        }
        return json_encode([
            'days'=>$days,
            'userNum'=>$datas['userNum'],
            'newUserNum'=>$datas['newUserNum'],
            'amount'=>$datas['amount'],
            'playTimes'=>$datas['playTimes'],
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
    
    public function actionExportPayUser() {
        $filename = "pay_users_".date("Y-m-d H:i:s");
        
        $sql = "SELECT m.id,m.weixin_id,m.mobile,m.memberID,m.register_channel,m.login_channel, m.`name`,m.gender, sum(price) as chargeAmount,
m.register_date,m.catch_number,m.phone_model
FROM charge_order co
LEFT JOIN t_member m ON co.member_id=m.id
WHERE co.charge_state=1
GROUP BY co.member_id";
        $db = Yii::$app->db;
        $rows = $db->createCommand($sql)->queryAll();
        
        $items = [];
        foreach($rows as $row) {
            $catchSuccessCount = $row['id'] ? $db->createCommand('SELECT COUNT(*) FROM t_doll_catch_history WHERE member_id='.$row['id'].' AND catch_status="抓取成功"')->queryScalar() : 0;
            $items[] = [
                '微信ID'=>$row['weixin_id'],
                '手机号'=>$row['mobile'],
                '会员ID'=>$row['memberID'],
                '昵称'=>$row['weixin_id'],
                '性别'=>$row['gender'],
                '充值金额'=>$row['chargeAmount'],
                '注册日期'=>$row['register_date'],
                '抓取次数'=>$row['catch_number'],
                '成功次数'=>$catchSuccessCount,
                '手机型号'=>$row['phone_model'],
                '注册渠道号'=>$row['register_channel'],
                '登录渠道号'=>$row['login_channel']
            ];
        }
        
        $this->_setcsvHeader("{$filename}.csv");
        echo $this->_array2csv($items);
        Yii::$app->end();
    }
    
    /**
     * 渠道日报
     */
    public function actionChannelDaily($refresh=0) {
        $cache = Yii::$app->cache;
        $updateTodayTime = $cache->get('channelDailyUpdateTime');
        if (!$updateTodayTime || ($updateTodayTime < (time() - 1800)) || $refresh) {
            $service = new StatisticService();
            $data = $service->channelDaily(date('Y-m-d'), 1);
            $cache->set('channelDailyUpdateTime', time(), 3600);
        }
        $model = \common\models\dollstatistic\ChannelDaily::find();
        $pages = new \yii\data\Pagination(['totalCount' => $model->count()]);
        $models = $model->offset($pages->offset)
                ->limit($pages->limit)
                ->orderBy('day DESC')
                ->all();
        return $this->render('channel-daily', [
                'models' => $models,
                'pages' => $pages,
        ]);
    }
    
    /**
     * 支付日报
     */
    public function actionPayDaily() {
        
    }
    
    /**
     * 支付次数日报
     */
    public function actionPayCountDaily() {
        
    }

    //测试
    public function actionTest1($day='2018-03-07', $insert = 1){
        $service = new \common\services\doll\StatisticService();
        $data = $service->run($day, $insert);
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function actionMachineRate1($day=null) {
        if($day===null) {
            $day = date('Y-m-d H:i:s', strtotime('yesterday'));
        }
        $startTime = strtotime($day);
        $endTime = $startTime + 86400 -1;
        $service = new \common\services\doll\StatisticService();
        $data = $service->machineRate($startTime, $endTime, 1, \common\enums\StatisticTypeEnum::TYPE_DAY);
        return 0;
    }

}
