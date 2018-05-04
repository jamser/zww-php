<?php
namespace frontend\modules\api\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use common\models\user\Wallet;
use common\base\Response;
use common\base\ErrCode;
use common\models\User;
use common\models\finance\WithdrawalsApply;
use common\models\order\VirtualMoneyRechargeOrder;
use common\models\order\Pay;

/**
 * 钱包控制器
 */
class WalletController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['logout', 'signup', 'login'],
                'rules' => [
                    [
                        'actions' => ['withdrawals-apply', 'recharge'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * 提现
     */
    public function actionWithdrawalsApply() {
        $wallet = Wallet::findAcModel((int)Yii::$app->user->id);
        $user = Yii::$app->user->identity;
        /* @var $user User */
        $user->populateRelation('wallet', $wallet);
        $model = new WithdrawalsApply([
            'user'=>$user
        ]);
        $model->setScenario(WithdrawalsApply::SCENARIO_APPLY_WITHDRAWALS);
        if($model->load(Yii::$app->getRequest()->post(),'apply') && $model->save()) {
            Response::success(ErrCode::NONE);
        }
        $firstErrors = $model->getFirstErrors();
        Response::show(ErrCode::NONE, $firstErrors, $firstErrors ? array_shift($firstErrors) : '数据无效');
    }
    
    /**
     * 充值
     */
    public function actionRecharge($virtualMoney, $trueMoney) {
        $virtualMoney = (int)$virtualMoney;
        $trueMoney = (int)($trueMoney*100);
        $caclVirtualMoney = Wallet::caclTrueMoney($virtualMoney);
        if($trueMoney!=($caclVirtualMoney*100)) {
            return Response::error(ErrCode::INVALID_PARAMS, '选择的金额异常,请重新选择');
        }
        $model = new VirtualMoneyRechargeOrder([
            'user_id'=>Yii::$app->user->id,
            'virtual_money_amount'=>$virtualMoney,
            'money_amount'=>$caclVirtualMoney,
            'status'=> VirtualMoneyRechargeOrder::STATUS_UNPAY
        ]);
        if(!$model->save()) {
            $firstErrors = $model->getFirstErrors();
            return Response::error(ErrCode::FORM_VALIDATE_FAIL, array_shift($firstErrors));
        }
        $user = Yii::$app->user->identity;
        $pay = Pay::addOrders([$model], $user, Pay::PROD_VIRTUAL_MONEY_RECHARGE);
        return Response::success(['payId'=>$pay->id,'trueMoney'=>$trueMoney]);
    }
    
}
