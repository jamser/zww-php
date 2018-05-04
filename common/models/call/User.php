<?php

namespace common\call\models;

use Yii;
use nextrip\smsCode\SmsCode;
use common\modules\user\models\SetRecord;

/**
 * This is the model class for table "{{%prodcall_user}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $nickname
 * @property integer $gender 
 * @property string $phone
 * @property string $email
 * @property string $money
 * @property string $created_at
 * @property string $updated_at
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * 验证码
     * @var string 
     */
    public $sms_code;
    
    public $location = '上海';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%prodcall_user}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->db_php;
    }
    
    public function behaviors() {
        return [
            \yii\behaviors\TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nickname', 'phone'], 'required'],
            [['money'], 'number'],
            [['gender'],'integer'],
            [['gender'],'in','range'=>[1,2], 'message'=>'请选择性别'],
            [['gender'],'checkGender'],
            [['nickname', 'phone', 'sms_code'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 64],
            [['email'], 'email'],
            [['sms_code'], 'checkSmsCode', 'skipOnEmpty'=>false],
            [['phone'], 'checkPhone']
        ];
    }
    
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        if($insert || in_array('gender', $changedAttributes)) {
            SetRecord::add('call_gender', $this->user_id);
        }
    }
    
    public function checkGender($attribute, $params) {
        if($this->getOldAttribute('gender') && $this->getOldAttribute('gender')!=$this->gender) {
            if($this->canSetGender()) {
                $this->addError($attribute, '性别设定后无法修改');
                return false;
            }
        }
    }
    
    public function canSetGender() {
        return SetRecord::getLastRecord((int)$this->user_id, 'call_gender') ? false : true;
    }
    
    public function checkPhone($attribute, $params) {
        if(!preg_match('/1[3|4|5|6|7|8|9][0-9]{9}/', $this->phone)) {
            $this->addError($attribute, '请输入正确手机号');
            return false;
        }
    }
    
    public function checkSmsCode($attribute, $params) {
        if($this->phone!=$this->oldAttributes['phone']) {
            if(!$this->sms_code) {
                $this->addError($attribute, '验证码不能为空');
                return false;
            }
            $smsCode = new SmsCode([
                'userId' =>  $this->user_id,
                'type' => 'setPhone',
                'phoneNum' => $this->phone
            ]);
            if(!$smsCode->verifyCode($this->sms_code)) {
                //验证失败...
                $this->addError($attribute, $smsCode->errorMsg);
                return false;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'sms_code' => '验证码',
            'nickname' => '昵称',
            'gender' => '性别',
            'phone' => '手机号',
            'email' => '邮箱',
            'money' => '账户余额',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * 通过用户ID获取模型
     * @param type $user_id
     * @return \static
     */
    public static function getByUserId($user_id) {
        $model = static::findOne((int)$user_id);
        if(!$model) {
            $model = new static;
            $model->user_id = $user_id;
            $model->save(false);
        }
        return $model;
    }
    
    public function getLocation() {
        return '上海';
    }
    
    public function getAge() {
        return 26;
    }
    
    /**
     * 获取性别
     * @param integer $gender
     * @return string
     */
    public static function getGenderStr($gender) {
        switch ($gender) {
            case 1:
                $str = '男';
                break;
            case 2:
                $str = '女';
                break;
            default:
                $str = '未知';
                break;
        }
        return $str;
    }
}
