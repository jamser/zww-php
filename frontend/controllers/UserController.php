<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\UserUpdateForm;
use frontend\models\WechatUnionid;
use frontend\models\Member;
use common\helpers\MyFunction;

use WechatSdk\web\Auth;
use common\models\User;
use common\models\user\Wallet;
use common\models\user\Account;
use nextrip\wechat\models\User as WechatUser;
use nextrip\wechat\models\UnionId;
use nextrip\helpers\Helper;
use common\services\user\RegisterService;

/**
 * 用户控制器
 */
class UserController extends Controller
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
                        'actions' => ['signup', 'login', 'wechat-login', 'h5-wechat-login','h5-wechat-login-test','h5-wechat-login-t'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout','index','update','account', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
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
     * 用户中心
     * @return mixed
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $wallet = Wallet::findAcModel($user->id);
        $isCaller = true;
        return $this->render('index', [
            'user'=>$user,
            'wallet'=>$wallet,
            'isCaller'=>$isCaller,
        ]);
    }

    /**
     * 更新资料
     */
    public function actionUpdate() {
        $user = Yii::$app->user->identity;
        
        $form = new UserUpdateForm($user);
        if($form->load(Yii::$app->request->post(), 'data') && $form->save()) {
            Yii::$app->session->setFlash('pageMsg', ['type'=>'success','content'=>'保存成功！']);
        }
        
        return $this->render('update', [
            'user'=>$user,
            'form'=>$form
        ]);
    }
    
    /**
     * 账号设置
     */
    public function actionAccount() {
        $user = Yii::$app->user->identity;
        $accounts = Account::getUserAccounts($user->id);
        return $this->render('account', [
            'user'=>$user,
            'accounts'=>$accounts
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        if(isWeixinBrowser()) {
            return $this->redirect(['wechat-login']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        if(!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
    
    /**
     * 微信登录
     */
    public function actionWechatLogin() {
        #获取UNION_ID
        Yii::$app->session->open();
        $mp = \nextrip\wechat\models\Mp::findAcModel('defaultMp');
        $wechatMpAuth = new \WechatSdk\mp\Auth($mp->app_id, $mp->app_secret);
        $wechatMpAuth->setBeforeExit([Yii::getLogger(), 'flush'], [true]);
        $authScope = filter_input(INPUT_GET, 'as')==='snsapi_base' ? 'snsapi_base' : 'snsapi_userinfo';

        try {
            if($authScope==='snsapi_base') {
                $openIdData = $wechatMpAuth->authorize(null, 'snsapi_base')->all();
                $userApi = new \WechatSdk\mp\User($mp->app_id, $mp->app_secret);
                $openData = $userApi->get($openIdData['openid']);
                if (!$openData->get('subscribe')) {
                    $wechatMpAuth->delInput(['code','state']);
                    goto AUTH_SNSAPI_USERINFO;
                }
            } else {
                AUTH_SNSAPI_USERINFO:
                $openData = $wechatMpAuth->authorize(\WechatSdk\helper\Url::current(['as' => 'snsapi_userinfo', 'code' => null]), 'snsapi_userinfo')->all();
            }
        } catch (\WechatSdk\mp\Exception $ex) {
            $wechatMpAuth->delInput(['code','state']);
            $openData = $wechatMpAuth->authorize(\WechatSdk\helper\Url::current(['as' => 'snsapi_userinfo', 'code' => null]), 'snsapi_userinfo')->all();
        }
        #保存 微信用户资料，union_id关联
        $openUser = new \WechatSdk\models\User($mp->app_id, $openData, 1);
        $unionIdRecord = UnionId::getByOpenId($openUser, true);
        $wechatUser = WechatUser::getModelByOpenUser($openUser, $unionIdRecord);

        #保存账号 用户
        $account = UnionId::findAcModel([UnionId::TYPE_WECHAT, $wechatUser->union_id]);
        if(!$account) {
            $account = new UnionId([
                'type'=>UnionId::TYPE_WECHAT,
                'value'=> $wechatUser->union_id
            ]);
            $baseName = Account::filterUsernameCharacter($wechatUser->nickname);
            if($baseName) {
                $names = [$baseName];
            } else {
                $names = [];
            }
            $names = array_merge($names, [
                'wx_'. randStr(16),
                'wx_'. randStr(16).'_'.$wechatUser->id,
            ]);

            $oriUser = new Account([
                'status'=>Account::STATUS_ACTIVE,
                'sex'=>$wechatUser->getGender(),
                'avatar'=>$wechatUser->getAvatar(),
                'password_hash'=>'',
            ]);
            $registerService = new RegisterService($oriUser,[
                'account' => $account,
                'allow_use_names' => $names
            ]);
            if(!($user = $registerService->submit())) {
                throw new \Exception($registerService->error_msg, $registerService->error_code);
            }


        } else {
            $user = User::findAcModel((int)$account->user_id);
        }
        #通过用户登录
        Yii::$app->user->login($user, 86400);
        return $this->goBack();
    }
    
    public function actionTestLogin($id) {
        $id = (int)$id;
        $user = User::findOne($id);
        if(!$user) {
            throw new \Exception('用户不存在');
        }
        //Yii::$app->user->login($user);
        //return $this->redirect(['/call/caller/explore']);
    }

    private function getToken(){
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()+-';
        $random = $chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)];//Random 5 times
        $content = uniqid().$random;   // 类似  5443e09c27bf4aB4uT
        return sha1($content);
    }
    
    
    /**
     * H5微信登陆
     * @param type $key
     * @return type
     */
    public function actionH5WechatLogin($code=null) {
        //微信登录  然后给对应的用户加币， 并且保存记录
        #获取UNION_ID
        Yii::$app->session->open();
        $mp = \nextrip\wechat\models\Mp::findAcModel('defaultMp');
        $wechatMpAuth = new \WechatSdk\mp\Auth($mp->app_id, $mp->app_secret);
        $wechatMpAuth->setBeforeExit([Yii::getLogger(), 'flush'], [true]);
        $authScope = filter_input(INPUT_GET, 'as')==='snsapi_base' ? 'snsapi_base' : 'snsapi_userinfo';


        try {
            if($authScope==='snsapi_base') {
                $openIdData = $wechatMpAuth->authorize(null, 'snsapi_base')->all();
                $userApi = new \WechatSdk\mp\User($mp->app_id, $mp->app_secret);
                $openData = $userApi->get($openIdData['openid']);
                if (!$openData->get('subscribe')) {
                    $wechatMpAuth->delInput(['code','state']);
                    goto AUTH_SNSAPI_USERINFO;
                }
            } else {
                AUTH_SNSAPI_USERINFO:
                $code = $wechatMpAuth->authorize(\WechatSdk\helper\Url::current(['as' => 'snsapi_userinfo', 'code' => null]), 'snsapi_userinfo', null, false);
            }
        } catch (\WechatSdk\mp\Exception $ex) {
            $wechatMpAuth->delInput(['code','state']);
            $code = $wechatMpAuth->authorize(\WechatSdk\helper\Url::current(['as' => 'snsapi_userinfo', 'code' => null]), 'snsapi_userinfo', null, false);
        }

        $redirectUrl = "http://h5.365zhuawawa.com/H5/index.html?code=".$code;
        return $this->redirect($redirectUrl);
    }

    public function actionH5WechatLoginTest($code=null) {
        //微信登录  然后给对应的用户加币， 并且保存记录
        #获取UNION_ID
        $request = Yii::$app->request;
        $channel = $request->post('channel') ? $request->post('channel') : $request->get('channel');
        Yii::$app->session->open();
        $mp = \nextrip\wechat\models\Mp::findAcModel('defaultMp');
        $wechatMpAuth = new \WechatSdk\mp\Auth($mp->app_id, $mp->app_secret);
        $wechatMpAuth->setBeforeExit([Yii::getLogger(), 'flush'], [true]);
        $authScope = filter_input(INPUT_GET, 'as')==='snsapi_base' ? 'snsapi_base' : 'snsapi_userinfo';


        try {
            if($authScope==='snsapi_base') {
                $openIdData = $wechatMpAuth->authorize(null, 'snsapi_base')->all();
                $userApi = new \WechatSdk\mp\User($mp->app_id, $mp->app_secret);
                $openData = $userApi->get($openIdData['openid']);
                if (!$openData->get('subscribe')) {
                    $wechatMpAuth->delInput(['code','state']);
                    goto AUTH_SNSAPI_USERINFO;
                }
            } else {
                AUTH_SNSAPI_USERINFO:
                $code = $wechatMpAuth->authorize(\WechatSdk\helper\Url::current(['as' => 'snsapi_userinfo', 'code' => null]), 'snsapi_userinfo', null, false);
            }
        } catch (\WechatSdk\mp\Exception $ex) {
            $wechatMpAuth->delInput(['code','state']);
            $code = $wechatMpAuth->authorize(\WechatSdk\helper\Url::current(['as' => 'snsapi_userinfo', 'code' => null]), 'snsapi_userinfo', null, false);
        }

        $redirectUrl = "http://h5.365zhuawawa.com/h5majia/index.html?code=".$code."&channel=".$channel;
        return $this->redirect($redirectUrl);
    }

    public function actionH5WechatLoginT($code=null) {
        //微信登录  然后给对应的用户加币， 并且保存记录
        #获取UNION_ID
        $request = Yii::$app->request;
        $channel = $request->post('channel') ? $request->post('channel') : $request->get('channel');
        $url = $request->post('url') ? $request->post('url') : $request->get('url');
        $memberID = $request->post('memberID') ? $request->post('memberID') : $request->get('memberID');
        Yii::$app->session->open();
        $mp = \nextrip\wechat\models\Mp::findAcModel('defaultMp');
        $wechatMpAuth = new \WechatSdk\mp\Auth($mp->app_id, $mp->app_secret);
        $wechatMpAuth->setBeforeExit([Yii::getLogger(), 'flush'], [true]);
        $authScope = filter_input(INPUT_GET, 'as')==='snsapi_base' ? 'snsapi_base' : 'snsapi_userinfo';


        try {
            if($authScope==='snsapi_base') {
                $openIdData = $wechatMpAuth->authorize(null, 'snsapi_base')->all();
                $userApi = new \WechatSdk\mp\User($mp->app_id, $mp->app_secret);
                $openData = $userApi->get($openIdData['openid']);
                if (!$openData->get('subscribe')) {
                    $wechatMpAuth->delInput(['code','state']);
                    goto AUTH_SNSAPI_USERINFO;
                }
            } else {
                AUTH_SNSAPI_USERINFO:
                $code = $wechatMpAuth->authorize(\WechatSdk\helper\Url::current(['as' => 'snsapi_userinfo', 'code' => null]), 'snsapi_userinfo', null, false);
            }
        } catch (\WechatSdk\mp\Exception $ex) {
            $wechatMpAuth->delInput(['code','state']);
            $code = $wechatMpAuth->authorize(\WechatSdk\helper\Url::current(['as' => 'snsapi_userinfo', 'code' => null]), 'snsapi_userinfo', null, false);
        }

        $redirectUrl = $url."?code=".$code."&channel=".$channel."&memberID=".$memberID;
        return $this->redirect($redirectUrl);
    }
}