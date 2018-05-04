<?php

namespace backend\modules\doll\controllers;

use common\models\doll\Machine;
use Yii;
use common\models\doll\Monitor;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\doll\CatchHistory;

/**
 * MonitorController implements the CRUD actions for Monitor model.
 */
class MonitorController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','catch-history','machine-rate','rtmp'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Monitor models.
     * @return mixed
     */
    public function actionIndex()
    {
        $machine_id = Yii::$app->getRequest()->get('machine_id',null);
        $machine_code = Yii::$app->getRequest()->get('machine_code',null);
        $machine_name = Yii::$app->getRequest()->get('machine_name',null);
        $alert_type = Yii::$app->getRequest()->get('alert_type',null);
        $conditions = $params = [];
        if($machine_id) {
            $conditions[] = 't.`dollId`=:dollId';
            $params[':dollId'] = trim($machine_id);
        }
        if($machine_code) {
            $conditions[] = 'mt.`machine_code`=:machine_code';
            $params[':machine_code'] = trim($machine_code);
        }
        if($machine_name) {
            $conditions[] = 'mt.`name`=:name';
            $params[':name'] = trim($machine_name);
        }
        if($alert_type) {
            $conditions[] = 't.`alert_type`=:alert_type';
            $params[':alert_type'] = trim($alert_type);
        }
        $pagination = new \yii\data\Pagination([
            'totalCount'=>Monitor::find()->count()
        ]);
        
        $db = Monitor::getDb();
        $table = Monitor::tableName();
        $machineTable = \common\models\doll\Machine::tableName();
        $sql = "SELECT t.*,mt.name,mt.machine_code FROM $table t LEFT JOIN $machineTable mt ON t.dollId=mt.id"
            . ($conditions ? ' WHERE '.implode(' AND ', $conditions) : '')
            . "  ORDER BY t.created_date DESC LIMIT {$pagination->getOffset()},{$pagination->getLimit()}";
        $rows = $db->createCommand($sql, $params)->queryAll();
        
        return $this->render('index', [
            'pages' => $pagination,
            'rows'=>$rows
        ]);
    }
    
     /**
     * Lists all Monitor models.
     * @return mixed
     */
    public function actionCatchHistory()
    {
        $status = Yii::$app->getRequest()->get('status',null);
        $startTime = Yii::$app->getRequest()->get('startTime',null);
        $endTime = Yii::$app->getRequest()->get('endTime',null);
        $memberId = Yii::$app->getRequest()->get('memberId',null);
        $memberCode = Yii::$app->getRequest()->get('memberCode',null);
        $machineId = Yii::$app->getRequest()->get('machineId',null);
        $machineCode = Yii::$app->getRequest()->get('machineCode',null);
        $conditions = $params = [];
        if($status && $status!='全部') {
            $conditions[] = '`catch_status`=:status';
            $params[':status'] = trim($status);
        }
        
        if($startTime) {
            $conditions[] = '`catch_date`>=:startTime';
            $params[':startTime'] = trim($startTime);
        }
        
        if($endTime) {
            $conditions[] = '`catch_date`<=:endTime';
            $params[':endTime'] = trim($endTime);
        }
        
        if($memberCode) {
            $member = \common\models\Member::find()->where('memberID=:memberId',[':memberId'=>$memberCode])->one();
            if($member) {
                $memberId = $member->id;
            }
        }
        
        if($memberId) {
            $conditions[] = '`member_id`=:member_id';
            $params[':member_id'] = (int)$memberId;
        }
        
        if($machineCode) {
//            $machine = \common\models\doll\Machine::find()->where('machine_code=:machineCode and machine_status!=:machineStatus',[':machineCode'=>$machineCode,':machineStatus'=>'未上线'])->one();
            $machine = \common\models\doll\Machine::find()->where('machine_code=:machineCode',[':machineCode'=>$machineCode])->one();
            if($machine) {
                $machineId = $machine->id;
            }else{
                throw new \Exception('未找到相关信息');
            }
        }
        
        if($machineId) {
            $conditions[] = 't.`doll_id`=:machineId';
            $params[':machineId'] = (int)$machineId;
        }
        
        
        $db = CatchHistory::getDb();
        $table = CatchHistory::tableName();
        $machineTable = \common\models\doll\Machine::tableName();
        
        $count = $db->createCommand("SELECT count(*) FROM $table t LEFT JOIN $machineTable mt"
                . " ON t.doll_id=mt.id "
                . ($conditions ? " WHERE ". implode(' AND ', $conditions) : ""), $params)->queryScalar();
        
        $pagination = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);

        
        $rows = $db->createCommand("SELECT t.*,mt.name AS dollName,mt.machine_code,d.memberID,d.name AS username FROM $table t LEFT JOIN $machineTable mt"
                . " ON t.doll_id=mt.id  left join t_member d on t.member_id=d.id"
                . ($conditions ? " WHERE ". implode(' AND ', $conditions) : "")
                . " ORDER BY t.id DESC LIMIT {$pagination->getOffset()},{$pagination->getLimit()}", $params)->queryAll();
        
        return $this->render('catch-history', [
            'pages' => $pagination,
            'rows'=>$rows
        ]);
    }

    /**
     * Displays a single Monitor model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Monitor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Monitor();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Monitor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Monitor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Monitor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Monitor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Monitor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 机器抓娃娃概率监控测试
     */
    public function actionMachineRate() {
        $time_s = '2018-03-03 10:54:02';
        $time = strtotime($time_s);
        $date = date('Y-m-d H:i:s',$time-360);
        $table = Machine::tableName();
        $sql = "SELECT COUNT(*) as num,h.doll_id, d.machine_type,d.name FROM t_doll_catch_history h LEFT JOIN t_doll d ON h.doll_id=d.id  WHERE catch_date>='".$date."' and catch_date<='".$time_s."' AND catch_status='抓取成功'"
            . " GROUP BY doll_id";
        $db = Yii::$app->db;
        $rows = $db->createCommand($sql)->queryAll();
        print_r($rows);die;
        foreach($rows as $row) {
            $dollId = $row['doll_id'];
            $catchNum = $row['num'];

            $update = false;
            $catchLevel = 0;
            if($dollId && ($catchNum>=3) && ($row['machine_type']!=1) && ($row['machine_type']!=2) && ($row['machine_type']!=3)) {
                //加入报警 把房间设置了维修中
                $update = true;
                $catchLevel = 3;
            } else if($dollId && ($catchNum>=6) && ($row['machine_type']==1)) {
                $update = true;
                $catchLevel = 6;
            } else if($dollId && ($catchNum>=6) && ($row['machine_type']==2)){
                $update = true;
                $catchLevel = 6;
            }

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
                    ':description'=>'6分钟内抓取成功超过'.$catchLevel.'次',
                    ':created_date'=>date('Y-m-d H:i:s'),
                    ':created_by'=>0,
                    ':modified_date'=>date('Y-m-d H:i:s'),
                    ':modified_by'=>0,
                ])->execute();
            }
        }
    }

    public function actionRtmp(){
        $service = new \common\services\doll\RtmpService();
        $service->machineRtmp();
    }
}
