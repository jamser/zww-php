<?php
namespace frontend\models;

use yii\base\Model;
use common\models\User;

/**
 * 用户更新资料表单
 */
class UserUpdateForm extends Model
{
    public $username;
    
    public $sex;
    
    public $birthday;

    public $about;

    /**
     * 用户模型
     * @var User 
     */
    public $user;
    
    /**
     * 
     * @param User $user 用户模型
     * @param [] $config 配置
     */
    public function __construct($user, $config = array()) {
        $this->user = $user;
        parent::__construct([
            'username'=>$user->username,
            'sex'=>$user->sex,
            'birthday'=>$user->birthday,
            'about'=>$user->about,
        ]+$config);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','birthday','about'], 'filter', 'filter' => 'trim'],
            [['sex','username'],'required'],
            
            ['sex','in','range'=>[1,2],'message'=>'请选择性别'],
            
            ['username','checkUsername'],
        ];
    }
    
    /**
     * 检查用户名
     * @param string $attribute 属性
     * @param [] $params 参数
     */
    public function checkUsername($attribute, $params) {
        if($this->username== $this->user->username) {
            return true;
        }
        
        if(strlen($this->username)<2 || mb_strlen($this->username, 'utf-8')<2 ) {
            $this->addError($attribute, '用户名不能少于2个字符');
            return false;
        } else if(mb_strlen($this->username, 'utf-8')>15 ) {
            $this->addError($attribute, '用户名不能超过15个字符');
            return false;
        } else if(User::filterUsernameCharacter($this->username)!= $this->username) {
            $this->addError($attribute, '用户名只能包含汉字英文字母');
            return false;
        } else if($nameUser = User::findOne(['username'=> $this->username])) {
            $this->addError($attribute, '该用户名已经被占用');
            return false;
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
        
        $attributes = ['username', 'sex', 'birthday', 'about'];
        $this->user->username = $this->username;
        $this->user->sex = $this->sex;
        $this->user->birthday = $this->birthday;
        $this->user->about = $this->about;
        
        $this->user->updateAttributes($attributes);
        return true;
    }
    
    public function attributeLabels() {
        return [
            'username'=>'用户名',
            'sex'=>'性别',
            'birthday'=>'生日',
            'about'=>'个性签名',
        ];
    }
}
