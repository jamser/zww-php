<?php

namespace frontend\modules\api\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;

use common\base\Response;
use common\base\ErrCode;
use common\models\User;
use common\models\call\Caller;
use common\models\call\Order;
use common\models\order\Pay;
use common\models\user\Account;
use common\models\order\PayLog;
use common\models\order\OrderPay;

use frontend\modules\api\models\CallerBookForm;

use nextrip\wechat\models\Mp;
use nextrip\wechat\models\UnionId;
use WechatSdk\pay\JsApiPay;
use WechatSdk\pay\Config;
use WechatSdk\pay\UnifiedOrder;
use WechatSdk\pay\Api;
use WechatSdk\pay\Notify;
use WechatSdk\pay\OrderQuery;

/**
 * 支付
 */
class PayController extends Controller {
    
        
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
                        'actions'=> ['index','callback'],
                        'allow'=>true
                    ],
                    [
                        'actions' => ['caller-book', 'pay-params', 'update-status'],
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
     * 支付回调
     */
    public function actionCallback() {
        $wechatPayConfig = $this->getWxPayConfig();   
        $notify = new Notify($wechatPayConfig, [$this, 'handleNotifyProcess']);
        return $notify->Handle(false);
    }
    
     /**
     * 获取微信支付配置
     * @param string $mpKey 公众号KEY
     * @return Config
     */
    protected function getWxPayConfig($mpKey='defaultMp') {
        if(!$this->wxPayConfig) {
            $wechatAppConfig = Mp::findAcModel($mpKey);
            $this->wxPayConfig = new Config($wechatAppConfig->app_id, $wechatAppConfig->app_secret, 
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
        if(!$mp) {
            throw new \Exception('找不到微信公众号配置');
        }
        $user = Yii::$app->user->getIdentity();
        $account = Account::getUserAccount($user->id, Account::TYPE_WECHAT);
        if(!$account) {
            throw new \Exception('找不到和你关联的微信号信息');
        }
        
        $openIdRecord = UnionId::getAccount($account->value, $mp->id);
        if(!$openIdRecord) {
            throw new \Exception('找不到和你关联的微信号ID');
        }
        $openId = $openIdRecord->open_id;
        if($pay->money_amount<=0) {
            throw new \Exception('支付金额不合法');
        }
        
        $wechatPayConfig = $this->getWxPayConfig();
        
        //①、获取用户openid
        //②、统一下单
        $input = new UnifiedOrder($wechatPayConfig);
        $input->SetBody($payTitle);
        $input->SetAttach($pay->id);
        $input->SetOut_trade_no($pay->getTradeNo($wechatPayConfig->getMchId()));
        //$input->SetTotal_fee($pay->money_amount*100);
        if(YII_DEBUG) {
            $input->SetTotal_fee(1);
        } else {
            $input->SetTotal_fee($pay->money_amount*100);
        }
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", $pay->expire_time));
        //$input->SetGoods_tag("test");
        $input->SetNotify_url(Url::to(['/pay/callback'], true));
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        //Yii::info("获取支付信息失败 : ".json_encode($input->GetValues(), JSON_UNESCAPED_UNICODE));
        $wxOrder = Api::unifiedOrder($input);
        if($wxOrder['return_code']==='FAIL') {//SUCCESS/FAIL
            Yii::error("获取支付信息失败 : 参数 ".json_encode($input->GetValues(), JSON_UNESCAPED_UNICODE)
                    ." 结果 ".json_encode($wxOrder, JSON_UNESCAPED_UNICODE));
            return [
                'code'=> ErrCode::ORDER_PAY_ERROR,
                'msg'=> '获取支付信息失败'.(YII_DEBUG ? ',参数:'.json_encode($input->GetValues(), JSON_UNESCAPED_UNICODE).',结果为 '.json_encode($wxOrder, JSON_UNESCAPED_UNICODE) : '')
            ];
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
        //Yii::trace('支付参数:'.var_export($jsApiParams,1));
        
        return [
            'code'=>0,
            'data'=>$jsApiParams
        ];
    }
    
        
    /**
     * 支付订单
     * @param type $id
     * @return type
     */
    public function actionPayParams($id) {
        $pay = Pay::findAcModel($id);
        $user = Yii::$app->user->identity;
        if(!$pay || $pay->user_id!=$user->id) {
            return Response::error(ErrCode::INVALID_PARAMS, '支付参数无效');
        } else if(!$pay->canPay()) {
            return Response::error(ErrCode::INVALID_PARAMS, '暂时不能支付');
        }
        $payTitle = $pay->pay_title;//date('m-d',$order->booking_date).' 起床服务预订';
        //$pay = Pay::addCallServiceOrder([$order], $user);
        $payParams = $this->getJsApiParams($pay, $payTitle);
        if($payParams['code']) {
            return Response::error($payParams['code'], $payParams['msg']);
        }
        return Response::success(['payParams'=>$payParams['data']]);
    }
      
    /**
     * 处理微信发来的消息
     * @param type $data
     * @param type $msg
     */
    public function handleNotifyProcess($data, &$msg) {
        Yii::info("支付回调参数:". json_encode($data, JSON_UNESCAPED_UNICODE));
        //$wechatPayConfig = $this->getWxPayConfig();
        $payId = isset($data['attach']) ? (int)$data['attach'] : null;
        if(!$payId) {
            $msg = '订单参数不正确';
            return false;
        } else if(! ($pay=Pay::findAcModel($payId))) {
            $msg = '找不到对应的支付信息';
            return false;
        }
        if($data['return_code']==='SUCCESS') {
            if($data['result_code']==='SUCCESS') {
                #支付成功
                PayLog::add($payId, PayLog::TYPE_PAY_SUCCESS_CALLBACK, json_encode($data, JSON_UNESCAPED_UNICODE));
                $pay->successCallback($data['transaction_id'], strtotime($data['time_end']), 'wx', "微信支付成功");
                
            } else {
                Yii::error("支付回调 {$payId} 业务码为 {$data['result_code']} 失败.数据:". json_encode($data, JSON_UNESCAPED_UNICODE));
                PayLog::add($payId, PayLog::TYPE_PAY_FAIL_CALLBACK, "业务码为 {$data['result_code']} 失败.数据:". json_encode($data, JSON_UNESCAPED_UNICODE));
            }
        } else {
            Yii::error("支付回调 {$payId} 通讯码为 {$data['return_code']} 失败.数据:". json_encode($data, JSON_UNESCAPED_UNICODE));
            PayLog::add($payId, PayLog::TYPE_PAY_FAIL_CALLBACK, "通讯码为 {$data['return_code']} 失败.数据:". json_encode($data, JSON_UNESCAPED_UNICODE));
        }
        return true;
    }
    
       
    /**
     * 更新订单状态
     * @param int $id
     */
    public function actionUpdateStatus($id) {
        $id = (int)$id;
        $pay = Pay::findAcModel($id);
        $user = Yii::$app->user->identity;
        if(!$pay || $pay->user_id!=$user->id) {
            throw new \Exception('找不到相关信息');
        }
        $params = [];
        $wechatPayConfig = $this->getWxPayConfig();
        $input = new OrderQuery($wechatPayConfig);
        $input->SetOut_trade_no($pay->getTradeNo($wechatPayConfig->getMchId()));
	$wxOrder = Api::orderQuery($input);
        Yii::error("请求更新订单结果 : " . json_encode($wxOrder,JSON_UNESCAPED_UNICODE). " 参数:". json_encode($input->GetValues(), JSON_UNESCAPED_UNICODE));
        if($wxOrder['return_code']!=='SUCCESS') {//SUCCESS/FAIL 查询失败
            return Response::renderWebErrorPage('错误提示', '查询支付结果失败', [['url'=>Url::current(),  'text'=>'重试']]);
        }
        if($wxOrder['result_code']!=='SUCCESS') {//SUCCESS/FAIL 业务失败
            if($wxOrder['result_code']!=='FAIL') {
                $pay->failCallback($user->id, '查询支付结果为FAIL');
                return $this->redirect(['caller-book-success','id'=>$pay->id]);
            } else {
                return Response::renderWebErrorPage('错误提示', '查询支付结果失败', [['url'=>Url::current(),  'text'=>'重试']]);
            }
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
                PayLog::add($pay->id, PayLog::TYPE_PAY_SUCCESS_QUERY, "查询微信支付成功, 数据:".json_encode($wxOrder,JSON_UNESCAPED_UNICODE));
                $pay->successCallback($wxOrder['transaction_id'], strtotime($wxOrder['time_end']), 
                        $user->id, '查询微信支付结果成功');
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
        return Response::success();
    }
}

