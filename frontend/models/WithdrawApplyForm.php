<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\user\Wallet;
use common\models\call\Caller;
use common\models\UploadFile;
use nextrip\helpers\Format;
use common\models\user\Account;
use nextrip\smsCode\SmsCode;
use common\models\finance\WithdrawalsApply;
/**
 * 提现申请表单
 */
class WithdrawApplyForm extends Model
{
    /**
     * 用户模型
     * @var User
     */
    public $user;
    
    /**
     * 账号
     * @var Account 
     */
    public $account;
    
    /**
     * 
     * @var Wallet
     */
    public $wallet;
    
    /**
     * 申请提现金额
     */
    public $amount;
    
    /**
     * 验证码
     * @var string 
     */
    public $sms_code;
    
    
    /**
     * 
     * @param User $user 用户模型
     * @param Wallet $wallet 钱包模型
     * @param Account $phoneAccount 手机账号
     * @param [] $config 配置
     */
    public function __construct($user, $wallet, $phoneAccount, $config = array()) {
        $this->user = $user;
        $this->wallet = $wallet;
        $this->account = $phoneAccount;
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        
        $rules = [
            [['amount', 'sms_code'], 'filter', 'filter' => 'trim'],
            [['amount', 'sms_code'],'required'],
            
            [['sms_code'],'checkSmsCode'],
            [['amount'],'number', 'min'=>6, 'max'=>$this->wallet->can_withdrawals, 
                'tooSmall'=>'提现金额不能少于6元', 'tooBig'=>'提现金额超过上限'],
        ];
        return $rules;
    }
    
    /**
     * 检查用户名
     * @param string $attribute 属性
     * @param [] $params 参数
     */
    public function checkSmsCode($attribute, $params) {
        $smsCode = new SmsCode([
            'userId'=>$this->user->id,
            'type'=>'applyWithdrawals',
            'phoneNum'=>$this->account->value,
        ]);
        if(!$smsCode->verifyCode($this->sms_code)) {
            $this->addError($attribute, $smsCode->errorMsg);
        }
    }
    
    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function save()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $apply = new WithdrawalsApply([
            'user_id'=>$this->user->id,
            'amount'=>$this->amount*100,
            'status'=>WithdrawalsApply::STATUS_DEFAULT
        ]);
        
        $db = User::getDb();
        $transaction = $db->beginTransaction();
        
        try {
            $apply->save(false);
            
            $updateRet = $db->createCommand('UPDATE '.Wallet::tableName().' SET `can_withdrawals`=`can_withdrawals`-'.$this->amount*100)
                    ->execute();
            
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error("申请Caller数据保存失败, 异常信息: {$ex->getFile()} 第 {$ex->getLine()} 行 {$ex->getMessage()}");
            $this->addError('amount', '保存数据失败');
        }
        
        return true;
        
        
    }
    
    public function attributeLabels() {
        return [
            'sms_code'=>'验证码',
            'amount'=>'提现金额',
        ];
    }
}
