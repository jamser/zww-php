<?php

namespace common\services\user;

use Yii;
use common\base\ErrCode;
use common\models\User;
use common\models\user\Account;
use common\models\user\FromChannel;
use common\models\user\Wallet;
use nextrip\wechat\models\User as WechatUser;
use common\services\BaseService;

/**
 * 注册服务
 */
class RegisterService extends BaseService {
    
    /**
     * 用户类
     * @var User 
     */
    public $user;
    
    /**
     * 账号类 用于绑定用户和手机号，微信号等之间的关系
     * @var Account 
     */
    public $account;
    
    /**
     * 来源渠道类
     * @var FromChannel 
     */
    public $from_channel;
    
    /**
     * 钱包类
     * @var Wallet 
     */
    public $wallet;
    
    /**
     * 允许使用的名称
     * @var []
     */
    public $allow_use_names = [];
    
    public function __construct($user, $config = array()) {
        $this->user = $user;
        parent::__construct($config);
    }
    
    /**
     * 注册用户
     * @param User $user 未保存的用户类
     * @param Account $account 账户
     * @return User
     * @throws \Exception
     */
    public function submit() {
        $user = $this->user;
        $user->generateAuthKey();
        
        $db = User::getDb();
        $transaction = $db->beginTransaction();
        if(!$this->user->username && !$this->allow_use_names) {
             throw new \Exception("用户名为空，注册用户失败", ErrCode::INVALID_PARAMS);
        } else if(!($canUseNames = $this->filterExistsUsernames())) {
            $user = null;
            $this->setError(ErrCode::USER_NAME_EXISTS, "用户名已经存在");
            goto RETURN_RET;
        } else {
            $this->user->username = $canUseNames[0];
        }
        
        START_SAVE:
        try {
            $saveUserRet = $user->save();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            if(strpos($ex->getMessage(), 'Duplicate entry')!==false && strpos($ex->getMessage(), $user->username) ) {
                while($canUseNames) {
                    $useName = array_shift($canUseNames);
                    if($useName==$this->user->username) {
                        continue;
                    }
                    $this->user->username = $useName;
                    goto START_SAVE;
                }
                $this->setError(ErrCode::USER_NAME_EXISTS, "用户名已经存在");
            } else {
                $this->setError(ErrCode::USER_SAVE_FAIL, '用户保存失败');
            }
            Yii::error($ex);
            $user = null;
            goto RETURN_RET;
        }
        try {
            if(!$saveUserRet) {
                throw new \Exception('注册用户失败!');
            }
            if($this->account && ($this->account->user_id = $user->id) && !$this->account->save() ) {
                throw new \Exception('用户账号保存失败!');
            }
            if($this->from_channel && ($this->from_channel->user_id = $user->id) && !$this->from_channel->save() ) {
                throw new \Exception('用户来源渠道保存失败!');
            }
            
            if(!$this->wallet) {
                $this->wallet = new Wallet([]);
            }
            if(($this->wallet->user_id = $user->id) && !$this->wallet->save() ) {
                throw new \Exception('用户钱包保存失败!');
            }
            
        } catch (\Exception $ex) {
            $transaction->rollBack();
            $this->setError(ErrCode::USER_SAVE_FAIL, '用户资料保存失败');
            Yii::error($ex);
            $user = null;
        }
        
        $transaction->commit();
        
        RETURN_RET:
        return $user;
    }
    
    /**
     * 过滤已存在的用户名数据
     */
    public function filterExistsUsernames() {
        $params = $in = [];
        $n = 0;
        $usernames = $this->user->username ? array_merge([$this->user->username], $this->allow_use_names) : $this->allow_use_names;
        foreach($usernames as $username) {
            $in[] = ':param'.$n;
            $params[':param'.$n] = $username;
            $n++;
        }
        $rows = User::getDb()->createCommand('SELECT username FROM '.User::tableName().' WHERE username in ('.implode(',', $in).')', $params)->queryAll();
        $usedNames = \yii\helpers\ArrayHelper::getColumn($rows, 'username');
        $canUseNames = [];
        foreach($usernames as $username) {
            if(!in_array($username, $usedNames)) {
                $canUseNames[] = $username;
            }
        }
        return $canUseNames;
    }
}
