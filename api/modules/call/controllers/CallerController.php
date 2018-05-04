<?php

namespace frontend\modules\call\controllers;

use Yii;
use common\models\PcallCaller;
use common\models\search\PcallCallerSearch;
use common\models\PcallUser;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use common\models\PcallOrder;
use yii\data\ActiveDataProvider;

/**
 * Caller
 */
class CallerController extends Controller
{
    public $layout = '/mobileSimple';
    
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
                        'actions' => ['create', 'index', 'view', 'update', 'confirm-order', 'task', 'task-list'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['超级管理员'],
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
     * Lists all PcallCaller models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PcallCallerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 审核申请
     * @param integer $id 申请ID
     * @param string $status 状态值
     * @return type
     */
    public function actionReview($id, $status) {
        $model = $this->findModel($id);
        if($status==='pass') {
            $model->changeStatus(PcallCaller::STATUS_REVIEW_PASS);
        } else if($status==='rejected') {
            $model->changeStatus(PcallCaller::STATUS_REVIEW_REJECTED);
        } else {
            throw new \Exception('错误的状态');
        }
        return [
             
        ];
    }
    
    /**
     * Displays a single PcallCaller model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PcallCaller model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $user = Yii::$app->user;
        if( ($caller = PcallCaller::findOne((int)$user->id)) ) {
            return $this->redirect(['/call/caller/view','id'=>$caller->id]);
        }
        
        $model = new PcallCaller();
        $call_user = PcallUser::getByUserId($user->id);

        $model->user_id = (int)Yii::$app->user->id;
        
        if ($call_user->load(Yii::$app->request->post()) && $call_user->save() 
                && $model->load(Yii::$app->request->post()) && ($model->cover_files = UploadedFile::getInstances($model, 'cover_files')) && $model->validate() && $model->upload() && $model->save(false)) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'call_user'=>$call_user,
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PcallCaller model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $user = Yii::$app->user->getIdentity();
        
        $model = PcallCaller::findOne(['user_id'=>$user->id]);
        if(!$model) {
            return $this->redirect(['create']);
        }
        
        $call_user = PcallUser::getByUserId($user->id);

        if ($call_user->load(Yii::$app->request->post()) && $call_user->save() && 
                $model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'call_user'=>$call_user,
                'model' => $model,
            ]);
        }
    }
    
    public function actionAdminUpdate($id) {

    }

    /**
     * Deletes an existing PcallCaller model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PcallCaller model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return PcallCaller the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PcallCaller::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * 确认订单
     */
    public function actionConfirmOrder($id) {
        $order = PcallOrder::findOne((int)$id);
        if(!$order || $order->caller_id!=Yii::$app->user->id) {
            throw new \Exception('不能对该订单进行确认, 请反馈给主页君吧');
        }
        if($order->canChangeStatus(PcallOrder::STATUS_WAIT_FOR_SERVICE)) {
            $order->changeStatus(PcallOrder::STATUS_WAIT_FOR_SERVICE, (int)Yii::$app->user->id, "起床达人主动确认订单有效");
        }
        return $this->redirect(['task','id'=>$id]);
        
    }
    
    /**
     * 查看任务
     */
    public function actionTask($id) {
        $order = PcallOrder::findOne((int)$id);
        if(!$order || $order->caller_id!=Yii::$app->user->id) {
            throw new \Exception('无权限查看任务');
        }
        return $this->render('task',['model'=>$order]);
    }
    
    /**
     * 查看任务列表
     */
    public function actionTaskList($type='all') {
        $query = PcallOrder::find()->where([
                'caller_id'=>(int)Yii::$app->user->id,
            ])->orderBy('id DESC');
        switch ($type) {
            case 'confirmed'://已确认 暂时不需要商家确认 , 把支付待确认的订单也归于这里
                $status = [
                    PcallOrder::STATUS_PAY_NOTIFY,
                    PcallOrder::STATUS_PAY_CONFIRM,
                    PcallOrder::STATUS_PAY_CONFIRMED,
                ];
                $query->andWhere('status in ('.  implode(',', $status).')');
                break;
            case 'review'://支付已经成功 待评价
                $query->andWhere('status = '.Order::STATUS_REVIEW);
                break;
            default://所有订单
                $type = 'all';
                break;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('task-list', [
            'type'=>$type,
            'dataProvider' => $dataProvider,
        ]);
    }
}
