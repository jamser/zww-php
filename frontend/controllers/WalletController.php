<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use common\base\Response;
use common\models\user\Wallet;
use common\models\user\BlanceChangeLog;
use common\models\User;
use common\models\Setting;
use common\models\user\VirtualMoneyChangeLog;
use common\models\user\Account;
use frontend\models\WithdrawApplyForm;

use frontend\models\wallet\WithdrawApply;

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
                        'actions' => ['index', 'virtual-money', 'virtual-money-log', 'virtual-money-recharge', 'withdraw','blance','blance-log'],
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
     * 账户余额
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        /* @var $user User */
        $wallet = Wallet::findAcModel($user->id);
        return $this->render('index', [
            'virtualMoneyName'=> Setting::getValueByKey('virtualMoneyName'),
            'wallet'=>$wallet
        ]);
    }
    
    /**
     * 余额
     */
    public function actionBlance() {
        return $this->render('blance', [
           'wallet'=>Wallet::findAcModel((int)Yii::$app->user->id)
        ]);
    }
    
    /**
     * 日志
     * @param integer $last_id 上次ID
     */
    public function actionBlanceLog($last_id=null) {
        $logs = BlanceChangeLog::find()->where('user_id='.(int)Yii::$app->user->id.($last_id ? ' AND id<'.(int)$last_id : ''))
                ->orderBy('`id` DESC')->limit(20)->all();
        return $this->render('blance-log', [
            'logs'=>$logs
        ]);
    }
    
    /**
     * 提现
     */
    public function actionWithdraw() {
        $user = Yii::$app->user->identity;
        $wallet = Wallet::findAcModel((int)$user->id);
        $account = Account::findOne([
            'user_id'=>(int)$user->id,
            'type'=>Account::TYPE_PHONE
        ]);
        if(!$account) {
            return Response::renderWebErrorPage('请先设置手机号', '你还没设置手机号, 请先设置手机号');
        }
        $form = new WithdrawApplyForm($user, $wallet, $account);
        if($form->load(Yii::$app->request->post(), 'data') && $form->save()) {
            return Response::renderMsg('申请提现成功', '你的申请将在5个工作日内进行审核和打款');
        }
        return $this->render('withdraw',[
            'wallet'=>$wallet,
            'account'=>$account,
            'form'=>$form
        ]);
    }
    
    /**
     * 虚拟货币主页
     */
    public function actionVirtualMoney() {
        return $this->render('virtual-money',[
            'virtualMoneyName'=> Setting::getValueByKey('virtualMoneyName'),
            'wallet'=>Wallet::findAcModel((int)Yii::$app->user->id)
        ]);
    }
    
    /**
     * 虚拟货币充值
     */
    public function actionVirtualMoneyRecharge() {
        return $this->render('virtual-money-recharge',[
            'virtualMoneyName'=> Setting::getValueByKey('virtualMoneyName'),
            'wallet'=>Wallet::findAcModel((int)Yii::$app->user->id)
        ]);
    }
    
    /**
     * 虚拟货币日志
     */
    public function actionVirtualMoneyLog() {
        $logs = VirtualMoneyChangeLog::find()->where('user_id='.(int)Yii::$app->user->id.($last_id ? ' AND id<'.(int)$last_id : ''))->orderBy('`id` DESC')->limit(20);
        return $this->render('virtual-money-log', [
            'virtualMoneyName'=> Setting::getValueByKey('virtualMoneyName'),
            'logs'=>$logs
        ]);
    }
}
