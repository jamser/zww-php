<?php
namespace frontend\models;

use yii\base\Model;
use common\models\User;
use common\models\user\Account;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $phone;
    public $password;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['phone', 'filter', 'filter' => 'trim'],
            ['phone', 'required'],
            ['phone', 'checkPhone'],
            
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }
    
    public function checkPhone() {
        
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        
        $account = new Account();
        $account->type = Account::TYPE_PHONE;
        $account->value = $this->phone;
        
        $db = User::getDb();
        $transaction = $db->beginTransaction();
        try {
            if($user->save(false)) {
                $account->user_id = $user->id;
                if(!$account->save(false)) {
                    throw new \Exception("保存用户账户失败");
                }
            } else {
                throw new \Exception("保存用户失败");
            }
            
        } catch (\Exception $ex) {
            $transaction->rollBack();
            $user = null;
            Yii::error('保存用户失败:'.$ex->getMessage());
        }
        
        
        return $user;
    }
}
