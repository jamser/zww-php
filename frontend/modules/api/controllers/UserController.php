<?php
namespace frontend\modules\api\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use common\models\call\Caller;
use common\models\Setting;

use common\models\gift\Gift;
use common\models\gift\SendRecord;
use common\models\gift\SendDetail;
use common\models\user\Wallet;
use common\models\user\BlanceChangeLog;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\UserUpdateForm;

use common\base\Response;
use common\base\ErrCode;
use nextrip\helpers\Lock;


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
                        'actions' => ['update', 'send-gift'],
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
     * 更新资料
     */
    public function actionUpdate() {
        $user = Yii::$app->user->identity;
        
        $form = new UserUpdateForm($user);
        if($form->load(Yii::$app->request->post(), 'data') && $form->save()) {
            
        }
        
        return $this->render('update', [
            'user'=>$user,
            'form'=>$form
        ]);
    }
    
    /**
     * 赠送礼物
     */
    public function actionSendGift() {
        $gifts = Yii::$app->request->post('gifts');
        $callerUserId = (int)Yii::$app->request->post('callerUserId');
        if(!$gifts || !is_array($gifts)) {
            return Response::error(ErrCode::INVALID_PARAMS, '参数不合法');
        }
        if(!$callerUserId || !($callerUser = User::findAcModel($callerUserId)) || !($caller=Caller::findAcModel((int)$callerUserId))) {
            return Response::error(ErrCode::INVALID_PARAMS, '找不到对应的'.Setting::getValueByKey('callerName'));
        }
        
        $callerWallet = $caller->user->wallet;
        $giftIds = $giftNumMap = [];
        foreach($gifts as $giftId=>$num) {
            $giftId = (int)$giftId;
            $num = (int)$num;
            if(!$giftId) {
                return Response::error(ErrCode::INVALID_PARAMS, '礼物不存在');
            }
            if($num<=0) {
                return Response::error(ErrCode::INVALID_PARAMS, '礼物数量不能为0');
            }
            $giftNumMap[$giftId] = $num;
            $giftIds[$giftId] = $giftId;
        }
        $gifts = Gift::findAllAcModels($giftIds);
        $virtualPrice = 0;
        foreach($gifts as $giftId=>$gift) {
            if(!$gift) {
                return Response::error(ErrCode::INVALID_PARAMS, '礼物不存在');
            }
            $virtualPrice += $gift->virtual_price*$giftNumMap[$giftId];
        }
        $trueToVirtualMoneyRate = Setting::getValueByKey('trueToVirtualMoneyRate');
        $callerGiftCommission = Setting::getValueByKey('callerGiftCommission');
        $user = Yii::$app->user->identity;
        /* @var $user \common\models\User */
        if($user->wallet->virtual_money<$virtualPrice) {
            return Response::show(ErrCode::WALLET_VIRTUAL_MONEY_NOT_ENOUGH, [
                'rechargeAmountOptions'=> Wallet::getRechargeAmounts(),
                'virtualPrice'=>$virtualPrice,
            ] , "余额不足,请充值");
        }
        if(Lock::get(($lockName="user{$user->id}SendGift"), 30)) {
            $db = Wallet::getDb();
            $transaction = $db->beginTransaction();
            try {
                #减少虚拟金额
                $trueVirtualMoney = $db->createCommand('UPDATE '.Wallet::tableName().' SET `virtual_money`=  @virtual_money := virtual_money - :cost WHERE id=:id;SELECT @virtual_money',[
                    ':cost'=>$virtualPrice,
                    ':id'=>$user->wallet->id
                ])->execute();
                
                #添加礼物记录
                $sendRecord = SendRecord::add($user->id, $caller->user_id, $virtualPrice);
                foreach($gifts as $giftId=>$gift) {
                    #添加礼物赠送记录
                    SendDetail::add($giftId, $user->id, $caller->user_id, $sendRecord->id, $giftNumMap[$giftId], $gift->virtual_price*$giftNumMap[$giftId]);
                }
                
                #兑换成真实余额发放给对方
                $trueMoney = round($virtualPrice/$trueToVirtualMoneyRate,2);
                $trueMoneyCommission = round($trueMoney*$callerGiftCommission,2)*100;
                if($trueMoneyCommission>0) {
                    $db->createCommand('UPDATE '.Wallet::tableName().' SET `income`= `income` + :money,'
                        . ' `blance`= `blance`+  :money, '
                        . ' `can_withdrawals` = `can_withdrawals` + :money'
                        . '  WHERE user_id=:user_id',
                    [
                        ':money'=>$trueMoneyCommission,
                        ':user_id'=>$caller->user_id
                    ])->execute();

                    $row = Wallet::findOne(['user_id'=>$caller->user_id]);

                    #添加余额变动日志
                    BlanceChangeLog::add($caller->user_id, $trueMoneyCommission, $callerWallet->blance, 
                            $row['blance'], '赠送礼物奖励', time());
                }
                
                $user->wallet->virtual_money = $trueVirtualMoney;
                $user->wallet->setOldAttribute('virtual_money', $trueVirtualMoney);
                $user->wallet->autoCacheCurrentModel();
                
                $callerWallet->setAttributes($row);
                $callerWallet->setOldAttributes($row);
                $callerWallet->autoCacheCurrentModel();
                
            } catch (\Exception $ex) {
                $transaction->rollBack();
                Lock::del($lockName);
                Yii::error("赠送礼物异常:{$ex->getFile()} {$ex->getLine()} {$ex->getMessage()} {$ex->getTraceAsString()}");
                return Response::error(ErrCode::WALLET_VIRTUAL_MONEY_SAVE_FAIL, "扣费失败,请重试");
            }
            
            $transaction->commit();
            Lock::del($lockName);
        }
        
        return Response::success();
    }
    

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            Response::success([
                'id'=>(int)Yii::$app->user->id
            ]);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Response::success([
                'id'=>(int)Yii::$app->user->id
            ]);
        } else {
            $firstErrors = $model->getFirstErrors();
            Response::show(ErrCode::FORM_VALIDATE_FAIL, $firstErrors, $firstErrors ? array_shift($firstErrors) : "获取数据失败");
        }
    }

}
