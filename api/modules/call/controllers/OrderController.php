<?php

namespace frontend\modules\call\controllers;

use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

use common\base\Response;
use common\base\ErrCode;
use common\models\call\Order;
use common\models\call\User;
use common\models\call\Caller;

use WechatSdk\pay\JsApiPay;
use WechatSdk\pay\Config;
use WechatSdk\pay\UnifiedOrder;
use WechatSdk\pay\Api;
use WechatSdk\pay\Notify;
use WechatSdk\pay\OrderQuery;

use frontend\modules\call\models\UserConfirm;

/**
 * OrderController implements the CRUD actions for PcallOrder model.
 */
class OrderController extends Controller
{
    public $layout = '/mobile';
    
    public $enableCsrfValidation = false;
    
    /**
     * 微信支付配置
     * @var Config
     */
    protected $wxPayConfig;
    
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
                        'actions' => ['pay-result', 'user-comment'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['create', 'user', 'view', 'update', 'delete', 'pay-params', 'update-pay-status'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index','update-status','dispatch', 'change-status'],
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
     * Lists all PcallOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PcallOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort([
            'attributes'=>[
                    'id'
                ],
                'defaultOrder' => [
                    'id' => 'DESC'
                ]
        ]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionUpdateStatus() {
        $id = (int)Yii::$app->getRequest()->post('id');
        $status = (int)Yii::$app->getRequest()->post('status');
        $remark = Yii::$app->getRequest()->post('remark');
        
        $order = PcallOrder::findOne($id);
        $ret = [];
        $adminUser = Yii::$app->user->getIdentity();
        if(!$order) {
            $ret = [
                'code'=>'ORDER_NOT_FOUND',
                'msg'=>"找不到对应订单"
            ];
        } else if($order->canChangeStatus($status)) {
            $order->changeStatus($status, $adminUser->id, $remark);
            $ret = [
                'code'=>'OK',
            ];
        } else {
            $ret = [
                'code'=>'CHANGE_STATUS_ERROR',
                'msg'=>"当前状态为 ".PcallOrder::$status_list[$order->status]." 不能修改为:".PcallOrder::$status_list[$status]
            ];
        }
        
        return json_encode($ret);
    }
    
    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionUser($type='all')
    {
        $query = PcallOrder::find()->where([
                'user_id'=>(int)Yii::$app->user->id,
            ])->orderBy('id DESC');
        switch ($type) {
            case 'unpay'://待支付
                $query->andWhere('status='.Order::STATUS_UNPAY);
                break;
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

        return $this->render('user', [
            'type'=>$type,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * 获取微信支付配置
     * @return Config
     */
    protected function getWxPayConfig() {
        if(!$this->wxPayConfig) {
            $wechatAppConfig = Mp::getDefaultMp();
            $this->wxPayConfig = new Config($wechatAppConfig->appId, $wechatAppConfig->appSecret, 
                    Yii::$app->params['wxpay']['mchId'], Yii::$app->params['wxpay']['payKey'], 
                    Yii::getAlias(Yii::$app->params['wxpay']['sslCert']), Yii::getAlias(Yii::$app->params['wxpay']['sslKey']));
        }
        return $this->wxPayConfig;
    }
    
    /**
     * 获取微信JSAPI参数
     * @param PcallOrder $order 订单
     * @return type
     * @throws \Exception
     */
    public function getJsApiParams($order) {
        Yii::$app->getRequest()->getHostInfo();
        $mp = Mp::getDefaultMp();
        
        $user = Yii::$app->user->getIdentity();
        $vendor = UserVendor::fetchByUserId($user->id, UserVendor::WECHAT_UNION);
        if(!$vendor) {
            throw new \Exception('找不到和你关联的微信号信息');
        }
        
        $unionId = $vendor->openId;
        $openIdRecord = UnionId::getAccount($unionId, $mp->app_id);
        if(!$openIdRecord) {
            throw new \Exception('找不到和你关联的微信号ID');
        }
        $openId = $openIdRecord->openId;
        
        //没有金额 直接返回支付成功
        if($order->fee<=0) {
            $order->changeStatus(PcallOrder::STATUS_PAY_CONFIRM, (int)Yii::$app->user->id, '没有金额,状态直接修改为支付检验通过');
            return $this->redirect(['view','id'=>$order->id]);
        }
        
        $wechatPayConfig = $this->getWxPayConfig();
        
        //①、获取用户openid
        //②、统一下单
        $input = new UnifiedOrder($wechatPayConfig);
        $input->SetBody($order->booking_date." 叫你起床");
        $input->SetAttach($order->id);
        $input->SetOut_trade_no($wechatPayConfig->getMchId().date("YmdHis", $order->created_at).$order->id);
        $input->SetTotal_fee($order->money_amount*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 15*60));
        //$input->SetGoods_tag("test");
        $input->SetNotify_url(Url::to(['/call/order/pay-result'], true));
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $wxOrder = Api::unifiedOrder($input);
        if($wxOrder['return_code']==='FAIL') {//SUCCESS/FAIL
            return Response::renderMsg('支付错误', $wxOrder['return_msg'], [
                [
                    'text'=>'重新支付',
                    'url'=>Url::current()
                ],
                [
                    'text'=>'返回订单',
                    'url'=>Url::to(['view', 'id'=>$order->id])
                ]
            ]);
        }

        //获取js api参数
        $jsapi = new JsApiPay($wechatPayConfig);
        $timestamp = time();
        $jsapi->SetTimeStamp("$timestamp");
        $jsapi->SetNonceStr(Api::getNonceStr());
        $jsapi->SetPackage("prepay_id=".$wxOrder['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign());
        $jsApiParams = json_encode($jsapi->GetValues());

        Yii::trace('支付参数:'.var_export($jsApiParams,1));
        
        return $jsApiParams;
    }
    
    public function actionPayResult() {
        $wechatPayConfig = $this->getWxPayConfig();   
        $notify = new Notify($wechatPayConfig, [$this, 'handleNotifyProcess']);
        return $notify->Handle(false);
    }

    /**
     * 处理微信发来的消息
     * @param type $data
     * @param type $msg
     */
    public function handleNotifyProcess($data, &$msg) {
        $wechatPayConfig = $this->getWxPayConfig();
        $orderId = isset($data['attach']) ? (int)$data['attach'] : null;
        if(!$orderId) {
            $msg = "订单参数不正确";
	    return false;
        } else if(! ($order=PcallOrder::findOne($orderId))) {
            $msg = "找不到对应的订单信息";
	    return false;
        }
        $dataInfo = json_encode($data, JSON_UNESCAPED_UNICODE);
        if(!in_array($order->status, [PcallOrder::STATUS_UNPAY, PcallOrder::STATUS_PAY_NOTIFY,  PcallOrder::STATUS_PAY_CONFIRM])) {
            SecurityLog::add(SecurityLog::TYPE_ORDER_PAY_NOTIFY, $order->user_id, '收到微信支付付款通知, 由于状态不在允许范围内中断,数据:'.$dataInfo);
            $msg = "订单当前状态不接受支付";
	    return false;
        } else if($order->status==PcallOrder::STATUS_UNPAY) {
            $order->changeStatus(Order::STATUS_PAY_NOTIFY, 0, '收到微信支付付款通知 , 更新订单状态.数据:'.$dataInfo);
        } else {
            SecurityLog::add(SecurityLog::TYPE_ORDER_PAY_NOTIFY, $order->user_id, '收到微信支付付款通知,由于状态已经是付款完成状态不需要更新,数据:'.$dataInfo);
        }
        return true;
    }

    /**
     * Displays a single PcallOrder model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id, $pay=0)
    {
        $model = $this->findModel($id);
        $jsApiParams = null;
        if($model->status==PcallOrder::STATUS_UNPAY && $pay) {
            //$jsApiParams = $this->getJsApiParams($model);
        }
        return $this->render('view', [
            'model' => $model,
            'pay'=>$pay,
            'jsApiParams'=>$jsApiParams,
        ]);
        
    }

    /**
     * Creates a new PcallOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PcallOrder();
        
        $user = Yii::$app->user->getIdentity();

        $call_user = PcallUser::getByUserId($user->id);
        
        if ($call_user->load(Yii::$app->request->post()) && $call_user->save() && 
            $model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->caller_id = 0;
            $model->user_id = (int)$user->id;
            $model->status = PcallOrder::STATUS_UNPAY;
            $model->money_amount = 0.01;
            
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'call_user'=>$call_user,
                'model' => $model,
            ]);
        }
    }
    
     
    /**
     * 支付订单
     * @param type $id
     * @return type
     */
    public function actionPay($id) {
        $order = $this->findModel($id);
        return $this->render('pay',[
            'order'=>$order,
            //'wxOrder'=>$wxOrder,
            'jsApiParams'=>$this->getJsApiParams($order),
        ]);
    }

    /**
     * Updates an existing PcallOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $user = Yii::$app->user->getIdentity();
        
        $model = $this->findModel($id);
        
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

    /**
     * Deletes an existing PcallOrder model.
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
     * Finds the PcallOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return PcallOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PcallOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * 分配订单
     */
    public function actionDispatch($id) {
        $this->layout = '/main';
        $order = $id ?  PcallOrder::findOne((int)$id) : null;
        if(!$order) {
            throw new \Exception('订单不存在');
        }
        /* @var $order PcallOrder */
        $model = new CallerDispatchForm();
        $model->order_id = $order->id;
        if($model->load(Yii::$app->getRequest()->post()) && $model->save((int)Yii::$app->user->id)) {
            return $this->redirect(['view','id'=>$order->id]);
        }
        
        $condition = '';
        $pagination = new \yii\data\Pagination([
            'totalCount'=>PcallCaller::find($condition)->count()
        ]);
        $callers = PcallCaller::find($condition)->offset($pagination->getOffset())->limit($pagination->getLimit())->all();
        
        return $this->render('dispatch', [
            'order'=>$order,
            'model'=>$model,
            'callers'=>$callers,
            'pagination'=>$pagination,
        ]);
    }
    
    public function actionDoDispatch() {
        $model = new CallerDispatchForm();
        if($model->load(Yii::$app->getRequest()->post(), '') && $model->save((int)Yii::$app->user->id)) {
            return [
                'code'=>'OK',
                'data'=>[]
            ];
        }
        $firstErrors = $model->getFirstErrors();
        return json_encode([
            'code'=>'FORM_VALIDATE_ERROR',
            'msg'=>$firstErrors ? array_shift($firstErrors) : '缺少请求参数'
        ]);
    }
    
    /**
     * 更新订单状态
     * @param int $id
     */
    public function actionUpdatePayStatus($id) {
        Yii::$app->getRequest()->getHostInfo();
        $order = $id>0 ? PcallOrder::findOne((int)$id) : null;
        if(!$order || ($order->user_id!=Yii::$app->user->id && !Yii::$app->getAuthManager()->checkAccess(Yii::$app->user->id, '超级管理员'))) {
            return Response::error(ErrCode::ACCESS_DEINED, '你无权限查看该页面');
        }
        /* @var $order PcallOrder */
        //没有金额 直接返回支付成功
        if($order->money_amount<=0) {
            return Response::success();
        }
        
        $wechatPayConfig = $this->getWxPayConfig();

        $input = new OrderQuery($wechatPayConfig);
        $input->SetOut_trade_no($order->getTradeNo($wechatPayConfig->getMchId()));
	$wxOrder = Api::orderQuery($input);
        if($wxOrder['return_code']==='FAIL') {//SUCCESS/FAIL
            return Response::error(ErrCode::ORDER_QUERY_ERROR, $wxOrder['return_msg']);
        }
        $tradeState = $wxOrder['trade_state'];
        
        /**
         * SUCCESS—支付成功
REFUND—转入退款
NOTPAY—未支付
CLOSED—已关闭
REVOKED—已撤销（刷卡支付）
USERPAYING--用户支付中
PAYERROR--支付失败(其他原因，如银行返回失败)
         */
        switch ($tradeState) {
            case 'SUCCESS'://支付成功
                if(in_array($order->status==  PcallOrder::STATUS_UNPAY,[PcallOrder::STATUS_UNPAY, PcallOrder::STATUS_PAY_NOTIFY]) ) {
                    $order->changeStatus(PcallOrder::STATUS_PAY_CONFIRM, Yii::$app->user->id, '系统获取微信订单， 状态为已支付');
                }
                break;
            case 'REFUND'://转入退款
                break;
            case 'NOTPAY'://未支付
                break;
            case 'CLOSED'://已关闭
                break;
            case 'REVOKED'://已撤销（刷卡支付）
                break;
            case 'PAYERROR':
                break;
            default:
                break;
        }
        $updateAttributes = [];
        if(!$order->pay_id) {
            $updateAttributes[] = 'pay_id';
            $order->pay_id = $wxOrder['transaction_id'];
        }
        if(!$order->pay_time) {
            $updateAttributes[] = 'pay_time';
            $order->pay_time = round($wxOrder['time_end']/1000);
        }
        if($updateAttributes) {
            $order->updateAttributes($updateAttributes);
        }
        
        Response::success(['tradeState'=>$tradeState]);
    }
    
    public function actionChangeStatus($id) {
        $id = (int)$id;
        $order = PcallOrder::findOne($id);
        $order->changeStatus($order->status, (int)Yii::$app->user->id, '请求改变订单状态');
        
    }
    
    /**
     * 买家确认
     * @param integer $id
     */
    public function actionUserConfirm($id) {
        $order = PcallOrder::findOne((int)$id);
        if(!$order) {
            throw new \Exception('找不到对应订单');
        }
        if($order->user_id!=Yii::$app->user->id) {
            throw new \Exception('无法确认订单权限', 403);
        }
        
        
        
        $order->user_confirm = 1;
        $order->updateAttributes(['user_confirm']);
        
    }
    
    /**
     * 服务者确认
     * @param integer $id 订单ID
     */
    public function actionCallerConfirm($id) {
        $order = PcallOrder::findOne((int)$id);
        if(!$order) {
            throw new \Exception('找不到对应订单');
        }
        if($order->caller_id!=Yii::$app->user->id) {
            throw new \Exception('无法确认订单权限', 403);
        }
        
        $order->caller_confirm = 1;
        $order->updateAttributes(['caller_confirm']);
    }
    
    public function actionUserComment() {
        $model = new UserConfirm;
        return $this->render('userComment', [
            'model'=>$model
        ]);
    }
}
