<?php

namespace common\services;

use Yii;
use common\models\User;
use common\models\user\Account;
use nextrip\wechat\models\User as WechatUser;

class AccountService extends \yii\base\Object {
    
    /**
     * 由微信用户注册
     * @param WechatUser $wechatUser 微信用户
     */
    public function getWechatAccount($wechatUser) {
        $account = Account::findAcModel([Account::TYPE_WECHAT, $wechatUser->union_id]);
        if(!$account) {
            $user = new User([
                'status'=>User::STATUS_ACTIVE,
                'sex'=>$wechatUser->getGender(),
                'avatar'=>$wechatUser->getAvatar()
            ]);
            $user = $this->registerUser($user);
        } else {
            $user = User::findAcModel($account->user_id);
        }
        
        return $user;
    }
    
    /**
     * 注册用户
     * @param User $user 未保存的用户类
     * @param Account $account 账户
     * @return User
     * @throws \Exception
     */
    public function registerUser($user, $account) {
        $user->username = $this->username;
        $user->generateAuthKey();
        
        $db = User::getDb();
        $transaction = $db->beginTransaction();
        try {
            if(!$user->save()) {
                throw new \Exception('注册用户失败!');
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error("注册用户失败");
        }
        
        
        return $user;
    }
    
}
