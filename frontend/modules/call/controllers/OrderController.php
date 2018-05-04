<?php

namespace frontend\modules\call\controllers;

use Yii;

use Exception;

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
use common\models\user\Account;
use common\models\order\Pay;

use nextrip\wechat\models\Mp;
use nextrip\wechat\models\UnionId;
use WechatSdk\pay\JsApiPay;
use WechatSdk\pay\Config;
use WechatSdk\pay\UnifiedOrder;
use WechatSdk\pay\Api;
use WechatSdk\pay\Notify;
use WechatSdk\pay\OrderQuery;

use frontend\modules\call\models\UserConfirm;

/**
 * OrderController implements the CRUD actions for Order model.
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
                        'actions' => ['create', 'user', 'view', 'update', 'pay-params', 'update-pay-status', 'pay'],
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
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'caller.apply_time' => SORT_DESC,
                ],
                'attributes' => [
                    'caller.id', 'caller.user_id', 'caller.apply_time',
                ],
            ],
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
        
        $order = Order::findOne($id);
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
                'msg'=>"当前状态为 ".Order::$status_list[$order->status]." 不能修改为:".Order::$status_list[$status]
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
        $query = Order::find()->where([
                'user_id'=>(int)Yii::$app->user->id,
            ])->orderBy('id DESC');
        switch ($type) {
            case 'unpay'://待支付
                $query->andWhere('status='.Order::STATUS_UNPAY);
                break;
            case 'confirmed'://已确认 暂时不需要商家确认 , 把支付待确认的订单也归于这里
                $status = [
                    Order::STATUS_PAY_NOTIFY,
                    Order::STATUS_PAY_CONFIRM,
                    Order::STATUS_PAY_CONFIRMED,
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
     * @param string $mpKey 公众号KEY
     * @return Config
     */
    protected function getWxPayConfig($mpKey='defaultMp') {
        if(!$this->wxPayConfig) {
            $wechatAppConfig = Mp::findAcModel($mpKey);
            $this->wxPayConfig = new Config($wechatAppConfig->appId, $wechatAppConfig->appSecret, 
                    $wechatAppConfig->mch_id, $wechatAppConfig->pay_key, 
                    Yii::getAlias($wechatAppConfig->ssl_cert), 
                    Yii::getAlias($wechatAppConfig->ssl_key));
        }
        return $this->wxPayConfig;
    }
    
    /**
     * 获取微信JSAPI参数
     * @param Pay $pay 支付类
     * @param string $payTitle 支付标题
     * @return type
     * @throws \Exception
     */
    public function getJsApiParams($pay, $payTitle) {
        Yii::$app->getRequest()->getHostInfo();
        $mp = Mp::findAcModel('defaultMp');
        
        $user = Yii::$app->user->getIdentity();
        $account = Account::getUserAccount($user->id, Account::TYPE_WECHAT);
        if(!$account) {
            throw new \Exception('找不到和你关联的微信号信息');
        }
        
        $openIdRecord = UnionId::getAccount($account->value, $mp->app_id);
        if(!$openIdRecord) {
            throw new \Exception('找不到和你关联的微信号ID');
        }
        $openId = $openIdRecord->openId;
        if($pay->money_amount<=0) {
            throw new \Exception('支付金额不合法');
        }
        
        $wechatPayConfig = $this->getWxPayConfig();
        
        //①、获取用户openid
        //②、统一下单
        $input = new UnifiedOrder($wechatPayConfig);
        $input->SetBody($payTitle);
        $input->SetAttach($order->id);
        $input->SetOut_trade_no($wechatPayConfig->getMchId().date("YmdHis", $pay->created_at).$pay->id);
        $input->SetTotal_fee($order->money_amount*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + Pay::EXPIRE_TIME));
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
                    'url'=>Url::to(['index'])
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
        } else if(! ($order=Order::findOne($orderId))) {
            $msg = "找不到对应的订单信息";
	    return false;
        }
        $dataInfo = json_encode($data, JSON_UNESCAPED_UNICODE);
        if(!in_array($order->status, [Order::STATUS_UNPAY, Order::STATUS_PAY_NOTIFY,  Order::STATUS_PAY_CONFIRM])) {
            SecurityLog::add(SecurityLog::TYPE_ORDER_PAY_NOTIFY, $order->user_id, '收到微信支付付款通知, 由于状态不在允许范围内中断,数据:'.$dataInfo);
            $msg = "订单当前状态不接受支付";
	    return false;
        } else if($order->status==Order::STATUS_UNPAY) {
            $order->changeStatus(Order::STATUS_PAY_NOTIFY, 0, '收到微信支付付款通知 , 更新订单状态.数据:'.$dataInfo);
        } else {
            SecurityLog::add(SecurityLog::TYPE_ORDER_PAY_NOTIFY, $order->user_id, '收到微信支付付款通知,由于状态已经是付款完成状态不需要更新,数据:'.$dataInfo);
        }
        return true;
    }

    /**
     * Displays a single Order model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id, $pay=0)
    {
        $model = $this->findModel($id);
        $jsApiParams = null;
        if($model->status==Order::STATUS_UNPAY && $pay) {
            //$jsApiParams = $this->getJsApiParams($model);
        }
        return $this->render('view', [
            'model' => $model,
            'pay'=>$pay,
            'jsApiParams'=>$jsApiParams,
        ]);
        
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Order();
        
        $user = Yii::$app->user->getIdentity();

        $call_user = PcallUser::getByUserId($user->id);
        
        if ($call_user->load(Yii::$app->request->post()) && $call_user->save() && 
            $model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->caller_id = 0;
            $model->user_id = (int)$user->id;
            $model->status = Order::STATUS_UNPAY;
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
        $user = Yii::$app->user->identity;
        if(!$order || $order->user_id!=$user->id) {
            throw new \Exception('无效订单');
        } else if(!$order->canPay()) {
            throw new \Exception('该订单暂时不能支付');
        }
        $payTitle = date('m-d',$order->booking_date).' 起床服务预订';
        $pay = Pay::addCallServiceOrder([$order], $user);
        return $this->render('pay',[
            'pay'=>$pay,
            //'wxOrder'=>$wxOrder,
            'jsApiParams'=>$this->getJsApiParams($pay, $payTitle),
        ]);
    }
    
    public function actionMultiPay($ids) {
        $idArr = explode(',', $ids);
        $formatIds = [];
        foreach($idArr as $id) {
            $id = (int)$id;
            if(!isset($formatIds[$id])) {
                $formatIds[$id] = $id;
            }
        }
        if(!$formatIds) {
            throw new \Exception('无效的支付订单');
        }
        $orders = Order::findAllAcModels($formatIds);
        $errorMessages = $dates = [];
        /* @var $orders Order */
        foreach($orders as $id=>$order) {
            if(!$order || $order->user_id!=Yii::$app->user->id) {
                $errorMessages[] = "无效订单 {$id}";
            } else if(!$order->canPay()) {
                $errorMessages[] = "不能支付订单 {$id}";
            }
            $dates[] = date('m-d',$order->booking_date);
        }
        if($errorMessages) {
            throw new Exception(implode(";", $errorMessages));
        }
        $user = Yii::$app->user->identity;
        $pay = Pay::addCallServiceOrder($orders, $user);
        
        return $this->render('pay',[
            'pay'=>$pay,
            //'wxOrder'=>$wxOrder,
            'jsApiParams'=>$this->getJsApiParams($pay, implode(',', $dates).' 起床服务预订'),
        ]);
        
    }

    /**
     * Updates an existing Order model.
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
     * Deletes an existing Order model.
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
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * 支付订单
     * @param type $id
     * @return type
     */
    public function actionPayParams($id) {
        Yii::$app->getRequest()->getHostInfo();
        $wechatPayConfig = $this->getWxPayConfig();
        $order = $id>0 ? Order::findOne((int)$id) : null;
        if(!$order || $order->user_id!=Yii::$app->user->id) {
            return Response::error(ErrCode::ACCESS_DEINED, '你无权限查看该页面');
        }
        /* @var $order Order */
        $user = Yii::$app->user->getIdentity();
        $account = Account::getUserAccount($user->id, Account::TYPE_WECHAT);
        $mp = Mp::findAcModel(1);
        $openIdRecord = UnionId::getAccount($account->value, $mp->key);
        if(!$openIdRecord) {
            return Response::error(ErrCode::WECHAT_ACCOUNT_NOT_FOUND, '找不到和你关联的微信号信息');
        }
        
        $openId = $openIdRecord->openId;
        
        //没有金额 直接返回支付成功
        if($order->money_amount<=0) {
            $order->changeStatus(Order::STATUS_PAY_CONFIRM, (int)Yii::$app->user->id, '没有金额,状态直接修改为支付检验通过');
            return Response::error(ErrCode::ORDER_PAYED, '订单已经支付过了');
        }
        
        if(YII_DEBUG && Yii::$app->getAuthManager()->checkAccess($order->user_id, '超级管理员')) {
            $order->money_amount = 0.01;
        }
        
        /* @var $order Order */
        
        //①、获取用户openid

        //②、统一下单
        $input = new UnifiedOrder($wechatPayConfig);
        $input->SetBody($order->booking_date.'日叫我起床');
        $input->SetAttach($order->id);
        $input->SetOut_trade_no($order->getTradeNo($wechatPayConfig->getMchId()));
        $input->SetTotal_fee($order->money_amount*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 15*60));
        //$input->SetGoods_tag("test");
        $input->SetNotify_url(Url::to(['pay-result'], true));
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $wxOrder = Api::unifiedOrder($input);
        /**
         * Array
(
    [appid] => wxd1bf431ddd800374
    [err_code] => INVALID_REQUEST
    [err_code_des] => 201 商户订单号重复
    [mch_id] => 1232720302
    [nonce_str] => A8l0nrVMiG7N5Q9b
    [result_code] => FAIL
    [return_code] => SUCCESS
    [return_msg] => OK
    [sign] => F7FD44E881A197A8AFEE99424D998243
)

         */
        if($wxOrder['return_code']==='FAIL') {//SUCCESS/FAIL
            Yii::error("微信支付失败, 支付结果".var_export($wxOrder,1));
            return Response::error(ErrCode::ORDER_PAY_ERROR, $wxOrder['return_msg']);
        }
        
        if(!empty($wxOrder['err_code_des'])) {
            Yii::error("微信支付失败, 支付结果".var_export($wxOrder,1));
            return Response::error(ErrCode::ORDER_PAY_ERROR, $wxOrder['err_code_des']);
        }

        //获取js api参数
        $jsapi = new JsApiPay($wechatPayConfig);
        $timestamp = time();
        $jsapi->SetTimeStamp("$timestamp");
        $jsapi->SetNonceStr(Api::getNonceStr());
        $jsapi->SetPackage("prepay_id=".$wxOrder['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign());

        return Response::success([
            'payParams'=>$jsapi->GetValues()
        ]);
    }
    
    /**
     * 分配订单
     */
    public function actionDispatch($id) {
        $this->layout = '/main';
        $order = $id ?  Order::findOne((int)$id) : null;
        if(!$order) {
            throw new \Exception('订单不存在');
        }
        /* @var $order Order */
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
        $order = $id>0 ? Order::findOne((int)$id) : null;
        if(!$order || ($order->user_id!=Yii::$app->user->id && !Yii::$app->getAuthManager()->checkAccess(Yii::$app->user->id, '超级管理员'))) {
            return Response::error(ErrCode::ACCESS_DEINED, '你无权限查看该页面');
        }
        /* @var $order Order */
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
                if(in_array($order->status==  Order::STATUS_UNPAY,[Order::STATUS_UNPAY, Order::STATUS_PAY_NOTIFY]) ) {
                    $order->changeStatus(Order::STATUS_PAY_CONFIRM, Yii::$app->user->id, '系统获取微信订单， 状态为已支付');
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
        $order = Order::findOne($id);
        $order->changeStatus($order->status, (int)Yii::$app->user->id, '请求改变订单状态');
        
    }
    
    /**
     * 买家确认
     * @param integer $id
     */
    public function actionUserConfirm($id) {
        $order = Order::findOne((int)$id);
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
        $order = Order::findOne((int)$id);
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
