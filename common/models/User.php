<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

use common\models\user\Wallet;
use common\models\user\ChangeLog;

/**
 * User model
 *
 * @property integer $id
 * @property string $username 用户名
 * @property string $true_name 真实名字
 * @property string $password_hash 密码hash
 * @property string $auth_key 验证key
 * @property integer $status 状态
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 * 
 * profile
 * @property integer $sex 性别 0为未知 1为男 2为女
 * @property string $birthday 生日 格式为 Y-m-d 默认为 0000-00-00
 * @property integer $country_id 国家ID 默认为0
 * @property integer $province_id 省份ID 默认为0
 * @property integer $city_id 城市ID  默认为0
 * @property string $avatar 头像 URL地址
 * @property string $about 个人说明
 * 
 * @property string $password write-only password
 * 
 * @property Wallet $wallet 钱包
 */
class User extends \nextrip\helpers\ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->db_php;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }
    
    public function attributeLabels() {
        return [
            'username'=>'用户名',
            'avatar'=>'头像',
            'sex'=>'性别',
            'about'=>'简介',
            'birthday'=>'生日',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
    
    public function getWallet() {
        return $this->hasOne(Wallet::className(), ['user_id'=>'id']);
    }
    
    public function getPhoneAccount() {
        return $this->hasOne(user\Account::className(), ['user_id'=>'id', 'type'=> user\Account::TYPE_PHONE]);
    }
    
    /**
     * 过滤用户名字符
     * @param string $username 用户名
     * @return string
     */
    public static function filterUsernameCharacter($username) {
        return preg_replace("/[^\p{Han}a-zA-Z0-9]/u", '', $username);
    }
    
    /**
     * 获取头像 
     * @param integer $size 尺寸
     * @param bool $useDefault 是否使用默认头像
     */
    public function getAvatar($size=null, $useDefault=true) {
        return $this->avatar ? $this->avatar : ($useDefault ? '/imgs/default-avatar.png':'');
    }
    
    /**
     * 获取年龄
     * @return integer
     */
    public function getAge() {
        $time = $this->birthday ? strtotime($this->birthday) : null;
        $age = null;
        if($time) {
            $now = time();
            $today = strtotime('Y-m-d');
            $year = date('Y', $now);
            $birthdayYear = date('Y', $time);
            $age = $year - $birthdayYear;
            if( (strtotime($today) - strtotime("{$year}-01-01")) >= (strtotime($this->birthday) - strtotime("{$birthdayYear}-01-01")) ) {
                $age++;
            }
        }
        return $age;
    }
    
    public function updateAttributes($attributes) {
        $oldSex = $this->getOldAttribute('sex');
        $oldAttributes = $this->getOldAttributes();
        if( ($ret = parent::updateAttributes($attributes))) {
            if($this->sex!=$oldAttributes['sex']) {
                ChangeLog::add('sex', $this->id, $oldAttributes['sex'], $this->sex);
            }
            if($this->avatar!=$oldAttributes['avatar']) {
                ChangeLog::add('avatar', $this->id, $oldAttributes['avatar'], $this->avatar);
            }
            if($this->about!=$oldAttributes['about']) {
                ChangeLog::add('about', $this->id, $oldAttributes['about'], $this->about);
            }
            
            if($this->birthday!=$oldAttributes['birthday']) {
                ChangeLog::add('birthday', $this->id, $oldAttributes['birthday'], $this->birthday);
            }
            
        }
        return $ret;
    }
    
    public function getLocation() {
        return '上海';
    }
}
