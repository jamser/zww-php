<?php

namespace frontend\modules\call\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;


use common\models\call\Caller;
use frontend\models\CallerApplyForm;
use frontend\models\CallerUpdateForm;
use common\services\call\CallerSortService;

//use common\models\search\PcallCallerSearch;
//use common\models\call;
use common\models\call\Order;

use yii\web\UploadedFile;

/**
 * Caller
 */
class CallerController extends Controller
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
                        'actions' => ['apply', 'index', 'view', 'update', 'confirm-order', 'task', 'task-list',
                            'book', 'explore'],
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
        $user = Yii::$app->user->identity;
        $caller = Caller::findAcModel((int)$user->id);
        if($caller->status == Caller::STATUS_WAITING_FOR_REVIEW) {
            return $this->render('//base/weuiInfo', [
                'title'=>'申请审核中',
                'msg'=>'已经收到你的申请，请耐心等待审核'
            ]);
        }
        return $this->render('index', [
            'caller'=>$caller,
            'user'=>$user
        ]);
    }
    
    public function actionExplore($page=1) {
        $page = $page>0 ? (int)$page : 1;
        $user = Yii::$app->user->identity;
        $limit = 15;
        $service = new CallerSortService();
        $data = $service->getPass(($page-1)*$limit, $limit);
        return $this->render('explore', $data + [
            'prePage'=>$page>1 ? $page-1 : 1,
            'nextPage'=>$page+1,
            'page'=>$page,
            'pageCount'=> ceil($data['count']/$limit)
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
     * 查看Caller
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $caller = Caller::findAcModel((int)$id);
        if(!$caller) {
            throw new \yii\base\UserException('找不到对应的数据', 404);
        }
        return $this->render('view', [
            'model' => $caller,
            'user'=>$caller->user
        ]);
    }

    /**
     * Creates a new PcallCaller model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionApply()
    {
        $user = Yii::$app->user->identity;
        if( ($caller = Caller::findAcModel((int)$user->id)) ) {
            return $this->redirect(['index']);
        }
        $caller = new Caller([
            'user_id'=> (int)$user->id
        ]);
        $callerApplyForm = new CallerApplyForm($user, $caller);
        
        if ($callerApplyForm->load(Yii::$app->request->post(), 'data') && $callerApplyForm->save() ) {
            return $this->redirect(['index']);
        } else {
            return $this->render('apply', [
                'form'=>$callerApplyForm,
                'user' => $user,
                'caller' => $caller,
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
        $user = Yii::$app->user->identity;
        if( !($caller = Caller::findAcModel((int)$user->id)) ) {
            return $this->redirect(['apply']);
        }

        $callerUpdateForm = new CallerUpdateForm($user, $caller);
        
        if ($callerUpdateForm->load(Yii::$app->request->post(), 'data') && $callerUpdateForm->save() ) {
            return $this->redirect(['view','id'=>$user->id]);
        } else {
            return $this->render('update', [
                'form'=>$callerUpdateForm,
                'user' => $user,
                'caller' => $caller,
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
        if (($model = Caller::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * 确认订单
     */
    public function actionConfirmOrder($id) {
        $order = Order::findOne((int)$id);
        if(!$order || $order->caller_id!=Yii::$app->user->id) {
            throw new \Exception('不能对该订单进行确认, 请反馈给主页君吧');
        }
        if($order->canChangeStatus(Order::STATUS_WAIT_FOR_SERVICE)) {
            $order->changeStatus(Order::STATUS_WAIT_FOR_SERVICE, (int)Yii::$app->user->id, "起床达人主动确认订单有效");
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
    
    /**
     * 预约
     * @param integer $id 预约用户ID
     */
    public function actionBook($id) {
        $id = (int)$id;
        $caller = Caller::findAcModel($id);
        if(!$caller) {
            throw new \yii\base\UserException('找不到对应的数据',404);
        }
        
        $canSelectDays = Caller::getCanBookDayTimes();
        $user = Yii::$app->user->identity;
        /* @var $user User */
        return $this->render('book', [
            'user'=>$user,
            'caller'=>$caller,
            'canSelectDays'=>$canSelectDays,
            'startTimes'=>Caller::getCanBookStartTimes(),
            'endTimes'=>Caller::getCanBookEndTimes(),
            'unitPrice'=>round($caller->price,2),
            'totalPrice'=>round($caller->price,2),
        ]);
    }
}
