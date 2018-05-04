<?php

namespace frontend\modules\api\controllers;

use Yii;
use yii\filters\AccessControl;

use common\base\Response;
use common\base\ErrCode;
use common\models\User;
use common\models\call\Caller;
use common\models\call\Order;
use common\models\order\Pay;
use frontend\modules\api\models\CallerBookForm;

class OrderController extends Controller {
    
        
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
                        'actions'=> ['index'],
                        'allow'=>true
                    ],
                    [
                        'actions' => ['caller-book', 'pay-params'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }
    
    public function actionIndex() {
        
    }
    
    /**
     * 预约Caller
     */
    public function actionCallerBook() {
        $callerUserId = Yii::$app->getRequest()->post('callerUserId');
        $bookDates = Yii::$app->getRequest()->post('bookDate');
        $bookStartTimes = Yii::$app->getRequest()->post('bookStartTime');
        $bookEndTimes = Yii::$app->getRequest()->post('bookEndTime');
        $remarks = Yii::$app->getRequest()->post('remark');
        $totalPrice = Yii::$app->getRequest()->post('totalPrice');
        $prices = Yii::$app->getRequest()->post('price');
        
        if(!$callerUserId || !($caller=Caller::findAcModel((int)$callerUserId))) {
            return Response::error(ErrCode::INVALID_PARAMS, "参数不合法");
        }
        
        if(!$bookDates || !$bookStartTimes || !$bookEndTimes || !$remarks || !$prices ||
            !is_array($bookDates) || !is_array($bookStartTimes) || !is_array($bookEndTimes)  || !is_array($remarks) || !is_array($prices)
        ) {
            return Response::error(ErrCode::INVALID_PARAMS, "参数不合法");
        }
        
        $bookDateCount = count($bookDates);
        if( ($bookDateCount!=count($bookStartTimes)) || ($bookDateCount)!=count($bookEndTimes)
                || ($bookDateCount!=count($remarks)) || ($bookDateCount!=count($prices)))
        {
            return Response::error(ErrCode::INVALID_PARAMS, "参数不合法");
        }
        $user = Yii::$app->user->identity;
        $forms = $errors = $dates = $orders = $orderIds = [];
        $hasError = false;
        $sumPrice = 0;
        while($bookDates) {
            $row = [
                'user'=>$user,
                'caller'=>$caller,
                'bookDate'=> array_shift($bookDates),
                'bookStartTime'=> array_shift($bookStartTimes),
                'bookEndTime'=> array_shift($bookEndTimes),
                'remark'=> array_shift($remarks),
                'price'=> array_shift($prices),
            ];
            $form = new CallerBookForm($row);
            if(in_array($row['bookDate'], $dates)) {
                $form->addError('bookDate', '预约日期不能重复');
            }
            
            if($form->validate(null, false)) {
                $forms[] = $form;
                $errors[] = null;
            } else {
                $hasError = true;
                $firstErrors = $form->getFirstErrors();
                $errors[] = array_shift($firstErrors);
            }
            $sumPrice += $form->price;
            $dates[] = $form->bookDate;
        }
        
        if($hasError) {
            return Response::error(ErrCode::FORM_VALIDATE_FAIL, ['errors'=>$errors], '表单验证失败');
        } else {
            $db = Order::getDb();
            $transaction = $db->beginTransaction();
            $dates = [];
            try {
                foreach($forms as $form) {
                    $order = $form->saveOrder();
                    $orders[] = $order;
                    $orderIds[] = $order->id;
                    $dates[] = date('m-d', $order->booking_date);
                }

                #保存订单支付
                if(!($pay = Pay::addCallServiceOrder($orders, $user))) {
                    throw new \Exception('订单支付信息保存失败');
                }

            } catch (\Exception $ex) {
                $transaction->rollBack();
                Yii::error("caller book order save error : {$ex->getFile()} {$ex->getLine()} {$ex->getMessage()}");
                return Response::error(ErrCode::ORDER_SAVE_FAIL, '订单保存失败');
            }
            $transaction->commit();
            return Response::success(['payId'=>$pay->id,'orderIds'=>$orderIds, 'dates'=>$dates]);
        }
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
            return Response::error(ErrCode::ORDER_PAY_ERROR, $wxOrder['return_msg']);
        }
        
        //获取js api参数
        $jsapi = new JsApiPay($wechatPayConfig);
        $timestamp = time();
        $jsapi->SetTimeStamp("$timestamp");
        $jsapi->SetNonceStr(Api::getNonceStr());
        $jsapi->SetPackage("prepay_id=".$wxOrder['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign());
        $jsApiParams = $jsapi->GetValues();

        Yii::trace('支付参数:'.var_export($jsApiParams,1));
        
        return $jsApiParams;
    }
    
        
    /**
     * 支付订单
     * @param type $id
     * @return type
     */
    public function actionPayParams($id) {
        $order = $this->findModel($id);
        $user = Yii::$app->user->identity;
        if(!$order || $order->user_id!=$user->id) {
            return Response::error(ErrCode::INVALID_PARAMS, '无效订单');
        } else if(!$order->canPay()) {
            return Response::error(ErrCode::INVALID_PARAMS, '该订单暂时不能支付');
        }
        $payTitle = date('m-d',$order->booking_date).' 起床服务预订';
        $pay = Pay::addCallServiceOrder([$order], $user);
        $payParams = $this->getJsApiParams($pay, $payTitle);
        return Response::success(['payParams'=>$payParams]);
    }
    
    /**
     * 批量支付参数
     * @param type $ids
     * @return type
     * @throws \Exception
     * @throws Exception
     */
    public function actionMultiPayParams($ids) {
        $idArr = explode(',', $ids);
        $formatIds = [];
        foreach($idArr as $id) {
            $id = (int)$id;
            if(!isset($formatIds[$id])) {
                $formatIds[$id] = $id;
            }
        }
        if(!$formatIds) {
            return Response::error(ErrCode::INVALID_PARAMS, '无效的支付订单');
        }
        $orders = Order::findAllAcModels($formatIds);
        $errorIds = $dates = [];
        /* @var $orders Order */
        foreach($orders as $id=>$order) {
            if(!$order || $order->user_id!=Yii::$app->user->id) {
                $errorIds[] = $id;
            } else if(!$order->canPay()) {
                $errorIds[] = $id;
            } else {
                $dates[] = date('m-d',$order->booking_date);
            }
        }
        if($errorIds) {
            return Response::error(ErrCode::INVALID_PARAMS, '无效的支付订单'. implode(',', $errorIds));
        }
        $user = Yii::$app->user->identity;
        $pay = Pay::addCallServiceOrder($orders, $user);
        
        $payParams = $this->getJsApiParams($pay, implode(',', $dates).' 起床服务预订');
        return Response::success($payParams);
    }
}

