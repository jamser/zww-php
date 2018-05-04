<?php
namespace backend\modules\doll\controllers;

use Yii;
use backend\modules\doll\models\User;
use backend\modules\doll\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class ChannelController extends Controller{
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
                        'actions' => ['index','create','delete','view', 'update','chart','chart-hour'],
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
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
     * Deletes an existing User model.
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
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionChart(){
        $channel = Yii::$app->getRequest()->get('channel',null);
        if(empty($channel)){
            $channel = 'vivo';
        }
        $day = Yii::$app->getRequest()->get('day',null);
        if($day && $day != '周期'){
            $d = '-'.$day;
        }else{
            $d=-7;
        }
        $days = $counts_d = array();
        for($i=0;$i>$d;$i--) {
            $time = strtotime("$i day");
            $times = date("Y-m-d", $time);
            $s_times = date("Y-m-d 00:00:00", $time);
            $e_times = date("Y-m-d 23:59:59", $time);
            $sql = "SELECT COUNT(*) FROM t_member WHERE register_date > '$s_times' AND register_date < '$e_times' AND register_channel = '$channel'";
            $d_count = Yii::$app->db->createCommand($sql)->queryAll();
            $d_count = $d_count[0]['COUNT(*)'];
            array_unshift($days,$times);
            array_unshift($counts_d,round($d_count));
        }

        return $this->render('chart_day',[
            'days' => $days,
            'counts_d' => $counts_d,
            'channel' => $channel,
        ]);
    }

    public function actionChartHour(){
        $channel = Yii::$app->getRequest()->get('channel',null);
        if(empty($channel)){
            $channel = 'vivo';
        }
        $date = Yii::$app->getRequest()->get('date',null);
        if($date){
            $beginTime = strtotime($date);
        }else{
            $date_m = date('Y-m-d',time());
            $beginTime = strtotime($date_m);
        }
        $hours = $counts_h = array();
        for($i = 0; $i < 24; $i++){
            $b = $beginTime + ($i * 3600);
            $e = $beginTime + (($i+1) * 3600)-1;
            $hour = date("H:00:00",$b);
            $s_hour = date("Y-m-d H:i:s",$b);
            $e_hour = date("Y-m-d H:i:s",$e);
            $sql = "SELECT COUNT(*) FROM t_member WHERE register_date > '$s_hour' AND register_date < '$e_hour' AND register_channel = '$channel'";
            $h_count = Yii::$app->db->createCommand($sql)->queryAll();
            $h_count = $h_count[0]['COUNT(*)'];
            array_unshift($hours,$hour);
            array_unshift($counts_h,round($h_count));
        }
        $hours = array_reverse($hours);
        $counts_h = array_reverse($counts_h);

        return $this->render('chart_hour',[
            'hours' => $hours,
            'counts_h' => $counts_h,
            'channel' => $channel,
        ]);
    }
}