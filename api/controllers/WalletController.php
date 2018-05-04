<?php
namespace api\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use common\models\user\Wallet;
use common\base\Response;
use common\base\ErrCode;
use common\models\User;
use common\models\finance\WithdrawApply;

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
                        'actions' => ['withdraw-apply'],
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
    public function actionWithdrawApply() {
        $wallet = Wallet::findAcModel((int)Yii::$app->user->id);
        $user = Yii::$app->user->identity;
        /* @var $user User */
        $user->populateRelation('wallet', $wallet);
        $model = new WithdrawApply([
            'user'=>$user
        ]);
        $model->setScenario(WithdrawApply::SCENARIO_APPLY_Withdraw);
        if($model->load(Yii::$app->getRequest()->post(),'apply') && $model->save()) {
            Response::success(ErrCode::NONE);
        }
        $firstErrors = $model->getFirstErrors();
        Response::show(ErrCode::NONE, $firstErrors, $firstErrors ? array_shift($firstErrors) : '数据无效');
    }
    
}
