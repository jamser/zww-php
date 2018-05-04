<?php

namespace backend\controllers;

use Yii;
use common\models\finance\WithdrawApply;
use common\models\User;
use common\models\user\Account;
use common\models\call\Caller;
use common\models\call\Order;
use nextrip\wechat\models\UnionId;
use nextrip\wechat\models\Mp;
use backend\models\search\WithdrawSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\UserWithdrawReviewForm;
use common\models\finance\WithdrawApplyLog;

/**
 * FinanceController implements the CRUD actions for WithdrawApply model.
 */
class FinanceController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all WithdrawApply models.
     * @return mixed
     */
    public function actionUserWithdrawList()
    {
        $searchModel = new WithdrawSearch();
        
        $mpKey='defaultMp';
        $wechatAppConfig = Mp::findAcModel($mpKey);
        $wechatMpAppId = $wechatAppConfig->app_id;
        $db = WithdrawApply::getDb();
        $params = [
            ':wechatMpAppId'=>$wechatMpAppId
        ]; 
        $conditions = [];
        if($searchModel->load(Yii::$app->getRequest()->get()) && $searchModel->validate()) {
            if($searchModel->payBeginTime) {
                $conditions[] = 'wa.pay_time>='.strtotime($searchModel->payBeginTime);
            }
            if($searchModel->payEndTime) {
                $conditions[] = 'wa.pay_time<='.strtotime($searchModel->payEndTime);
            }

            if($searchModel->applyBeginTime) {
                $conditions[] = 'wa.created_at>='.strtotime($searchModel->created_at);
            }
            if($searchModel->applyEndTime) {
                $conditions[] = 'wa.created_at<='.strtotime($searchModel->created_at);
            }
            if($searchModel->id) {
                $conditions[] = 'wa.id='.(int)$searchModel->id;
            }
            if($searchModel->user_id) {
                $conditions[] = 'wa.user_id='.(int)$searchModel->user_id;
            }
            if($searchModel->status) {
                $conditions[] = 'wa.`status`='.(int)$searchModel->status;
            }
            if($searchModel->out_trade_no) {
                $conditions[] = 'wa.`out_trade_no`=:out_trade_no';
                $params[':out_trade_no'] = $searchModel->out_trade_no;
            }
            if($searchModel->phone) {
                $conditions[] = 'ap.`value`=:phone';
                $params[':phone'] = $searchModel->phone;
            }
            if($searchModel->username) {
                $conditions[] = 'u.`username`=:username';
                $params[':username'] = $searchModel->username;
            }
        }
        
        $baseSql = ' FROM '.WithdrawApply::tableName().' wa '
                .' LEFT JOIN '.User::tableName().' u ON wa.user_id=u.id '
                . ' LEFT JOIN '.Account::tableName().' ap ON (ap.user_id=wa.user_id AND ap.type='.Account::TYPE_PHONE.') '
                . ' LEFT JOIN '.Account::tableName().' aw ON (aw.user_id=wa.user_id AND aw.type='.Account::TYPE_WECHAT.') '
                . ' LEFT JOIN '.UnionId::tableName().' wu ON (wu.union_id=aw.`value` AND wu.app_id=:wechatMpAppId) '
                . ' '.($conditions ? ' WHERE '. implode(' AND ', $conditions) : '');
        $count = $db->createCommand('SELECT COUNT(*) '.$baseSql, $params)->queryScalar();
        $pagination = new \yii\data\Pagination([
            'totalCount'=>$count,
            'pageSize'=>20
        ]);
        
        $select = 'wa.*, u.username, u.true_name, ap.`value` as phone, wu.open_id ';
        $rows = $db->createCommand('SELECT '.$select.$baseSql.' ORDER BY wa.id DESC LIMIT '.$pagination->getOffset().','.$pagination->getLimit(), $params)->queryAll();
        
        return $this->render('user-withdraw-list', [
            'searchModel' => $searchModel,
            'rows'=>$rows,
            'pages' => $pagination,
        ]);
    }

    /**
     * 查看提现
     * @param string $id
     * @return mixed
     */
    public function actionUserWithdrawView($id)
    {
        $model = WithdrawApply::findOne((int)$id);
        if(!$model) {
            throw new \Exception('找不到该提现申请');
        }
        $user = $model->user;
        $accounts = Account::getUserAccounts($user->id);
        $caller = Caller::findAcModel($user->id);
        $phone = $unionId = $openId = '';
        if(isset($accounts[Account::TYPE_PHONE])) {
            $phone = $accounts[Account::TYPE_PHONE]->value;
        }
        if(isset($accounts[Account::TYPE_WECHAT])) {
            $unionId = $accounts[Account::TYPE_WECHAT]->value;
        }
        if($unionId) {
            $mpKey='defaultMp';
            $mp = Mp::findAcModel($mpKey);
            $openId = UnionId::getAccount($unionId, $mp->app_id);
        }
        
        #已支付订单数量
        $payOrderCount = Order::find()->where('caller_user_id='.(int)$user->id.' AND pay_time>0')->count();
        
        $wallet = $user->wallet;
        
        $applyAmount = WithdrawApply::find()->select('SUM(`amount`)')
                ->where('user_id='.(int)$user->id.' AND `status` IN ('. implode(',', array_keys(WithdrawApply::STATUS_UNPAY_LIST)).')')
                ->scalar();
        
        $logs = WithdrawApplyLog::findAll([
            'user_id'=>(int)$model->user_id,
            'withdraw_id'=>(int)$model->id
        ]);
        
        return $this->render('user-withdraw-view', [
            'model' => $model,
            'user' => $user,
            'phone'=>$phone,
            'unionId'=>$unionId,
            'openId'=>$openId,
            'caller'=>$caller,
            'payOrderCount'=>$payOrderCount,
            'wallet'=>$wallet,
            'applyAmount'=>$applyAmount,
            'logs'=>$logs
        ]);
    }

    public function actionUserWithdrawReview($id) {
        $withdraw = WithdrawApply::findOne((int)$id);
        if(!$withdraw) {
            throw new \Exception('找不到该提现申请');
        }
        $model = new UserWithdrawReviewForm($withdraw);
        $post = Yii::$app->getRequest()->post();
        if($model->load($post) && $model->save()) {
            return $this->redirect(['user-withdraw-view','id'=>$id]);
        }
        return $this->render('user-withdraw-review', [
            'withdraw'=>$withdraw,
            'model'=>$model
        ]);
    }
    
    /**
     * Finds the WithdrawApply model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return WithdrawApply the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WithdrawApply::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
