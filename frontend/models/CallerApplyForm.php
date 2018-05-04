<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\call\Caller;
use common\models\UploadFile;
use nextrip\helpers\Format;
use common\models\user\Account;

/**
 * Caller申请资料表单
 */
class CallerApplyForm extends Model
{
    /**
     * 用户头像
     * @var string 
     */
    public $avatar;


    /**
     * 头像ID 
     * @var string 
     */
    public $avatar_id;
    
    /**
     * 用户名
     * @var string 
     */
    public $username;
    
    /**
     * 真实名称
     * @var string 
     */
    public $true_name;
    
    /**
     * 性别
     * @var integer 
     */
    public $sex;
    
    /**
     * 生日
     * @var string
     */
    public $birthday;

    /**
     * 个人说明
     * @var string 
     */
    public $about;
    
    /**
     * 封面
     * @var array 
     */
    public $covers;
    
    /**
     * 服务时间段 
     * @var string 
     */
    public $service_time;

    /**
     * 手机
     * @var string 
     */
    public $phone;
    
    /**
     * 验证码
     * @var string 
     */
    public $code;
    
    /**
     * 用户模型
     * @var User 
     */
    public $user;
    
    /**
     * caller
     * @var Caller 
     */
    public $caller;
    
    /**
     * 手机账号
     * @var type 
     */
    public $phoneAccount;
    
    /**
     * 
     * @param User $user 用户模型
     * @param Caller $caller caller模型
     * @param [] $config 配置
     */
    public function __construct($user, $caller, $config = array()) {
        $this->user = $user;
        $this->caller = $caller;
        $this->phoneAccount = Account::findOne([
            'user_id'=>(int)$user->id,
            'type'=>Account::TYPE_PHONE
        ]);
        parent::__construct([
            'avatar'=>$user->avatar,
            'username'=>$user->username,
            'true_name'=>$user->true_name,
            'sex'=>$user->sex,
            'birthday'=>$user->birthday,
            'about'=>$user->about,
            'service_time'=>$caller->service_time,
            'covers'=>$caller->getArrayFormatAttribute('covers')
        ]+$config);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        
        $rules = [
            [['username','birthday','about', 'service_time', 'true_name'], 'filter', 'filter' => 'trim'],
            [['sex','username', 'birthday', 'service_time', 'covers', 'true_name'],'required'],
            
            ['sex','in','range'=>[1,2],'message'=>'请选择性别'],
            
            ['username','checkUsername'],
            ['true_name','string', 'min'=>2, 'max'=>32, 'encoding'=>'utf-8'],
            
            ['avatar_id', 'checkAvatar'],
            ['covers', 'checkCovers'],
        ];
        
        if(!$this->user->avatar) {
            $rules[] = ['avatar_id', 'required'];
        }
        if(!$this->phoneAccount || !$this->phoneAccount->id) {
            $rules = array_merge($rules, [
                [['phone', 'code'], 'filter', 'filter' => 'trim'],
                [['phone', 'code'],'required'],

                ['code', 'checkCode'],
            ]);
        }
        return $rules;
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
    
    public function checkAvatar($attribute, $params) {
        if($this->avatar_id) {
            $uploadFile = \common\models\UploadFile::findOne((int) $this->avatar_id);
            if(!$uploadFile || $uploadFile->user_id != $this->user->id) {
                $this->addError($attribute, '找不到对应的头像文件, 请重新上传');
                return false;
            }
            $this->avatar = $uploadFile->url;
        }
    }
    
    public function checkCovers($attribute, $params) {
        if(!$this->covers) {
            $this->addError($attribute,'封面相册不能为空');
            return false;
        } else if(!is_array($this->covers)) {
            $this->addError($attribute,'封面相册数据不正确');
            return false;
        }
        
        $coverIds = $covers = [];
        foreach($this->covers as $coverId) {
            $coverId = (int)$coverId;
            if($coverId>0 && !in_array($coverId, $coverIds)) {
                $coverIds[] = $coverId;
            }
        }
        
        if(!$coverIds) {
            $this->addError($attribute,'需要上传封面相册');
            return false;
        }
        
        $files = UploadFile::findAllAcModels($coverIds);
        foreach($files as $file) {
            if($file->user_id!=$this->user->id) {
                continue;
            }
            $covers[] = [
                'id'=>$file->id,
                'url'=>$file->url,
            ]; 
        }
        $this->covers = $covers;
    }
    
    public function checkCode($attribute, $params) {
        if($this->hasErrors('phone')) {
            return false;
        }
        $smsCode = new \nextrip\smsCode\SmsCode([
           'userId'=>$this->user->id,
           'type'=>'applyCaller',
           'phoneNum'=>$this->phone,
        ]);
        if($smsCode->verifyCode($this->code)) {
           //验证成功...
        } else {
           //验证失败...
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
        
        $attributes = ['username', 'sex', 'birthday', 'about', 'avatar', 'true_name'];
        $this->user->username = $this->username;
        $this->user->sex = $this->sex;
        $this->user->birthday = $this->birthday;
        $this->user->about = $this->about;
        $this->user->true_name = $this->true_name;
        if($this->avatar_id) {
            $this->user->avatar = $this->avatar;
        }
        $this->caller->covers = Format::toStr($this->covers);
        $this->caller->service_time = $this->service_time;
        
        if(!$this->phoneAccount || !$this->phoneAccount->id) {
            $this->phoneAccount = new Account([
                'type'=> Account::TYPE_PHONE,
                'user_id'=>$this->user->id,
                'value'=> $this->phone
            ]);
        }
        
        $db = User::getDb();
        $transaction = $db->beginTransaction();
        
        try {
            $this->user->updateAttributes($attributes);

            $this->caller->save(false);
            
            $this->phoneAccount->save(false);
            
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error("申请Caller数据保存失败, 异常信息: {$ex->getFile()} 第 {$ex->getLine()} 行 {$ex->getMessage()}");
            $this->addError('username', '保存数据失败');
        }
        
        return true;
    }
    
    public function attributeLabels() {
        return [
            'username'=>'用户名',
            'sex'=>'性别',
            'birthday'=>'生日',
            'about'=>'个性签名',
            'avatar'=>'头像',
            'avatar_id'=>'头像',
            'service_time'=>'服务时间',
            'covers'=>'封面相册',
            'phone'=>'手机号',
            'code'=>'验证码',
            'true_name'=>'真实姓名'
        ];
    }
}
