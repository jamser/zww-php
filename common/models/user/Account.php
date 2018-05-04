<?php

namespace common\models\user;

use Yii;

/**
 * 用户登录账号
 * This is the model class for table "user_account".
 *
 * @property string $id
 * @property string $user_id
 * @property integer $type
 * @property string $value
 * @property string $created_at
 * @property string $updated_at
 */
class Account extends \nextrip\helpers\ActiveRecord
{
    const TYPE_PHONE = 1;
    const TYPE_WECHAT = 2;
    
//    const TYPE_LIST = [
//        self::TYPE_PHONE => '手机',
//        self::TYPE_WECHAT => '微信',
//    ];
    
    /**
     * auto cache config
     * @var array
     */
    protected static $autoCacheConfig = [
        'enable' => false,//set to false auto cache will be disabled
        'duration' => 14400,//cache duration(second)
        'useAttribute'=>['type', 'value'],//support mixed attributes , Eg:['type', 'name']
        'cacheId'=>'cache',//cache component id
    ] ;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_account';
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
    public function rules()
    {
        return [
            [['user_id', 'type', 'value'], 'required'],
            [['user_id', 'type'], 'integer'],
            [['value'], 'string', 'max' => 64],
            [['user_id', 'type'], 'unique', 'targetAttribute' => ['user_id', 'type'], 'message' => 'The combination of 用户ID and 类型 has already been taken.'],
            [['type', 'value'], 'unique', 'targetAttribute' => ['type', 'value'], 'message' => 'The combination of 类型 and 账号值 has already been taken.'],
        ];
    }

    public function behaviors() {
        return [
            \yii\behaviors\TimestampBehavior::className()
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'type' => '类型',
            'value' => '账号值',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * 获取用户所有的关联账号
     * @param integer $userId 用户ID
     * @return static[] 以类型为key
     */
    public static function getUserAccounts($userId) {
        return static::find()->where('user_id='.(int)$userId)->indexBy('type')->all();
    }
    
    /**
     * 获取用户账号
     * @param integer $userId 用户ID
     * @param integer $type 类型
     * @return static 
     */
    public static function getUserAccount($userId, $type) {
        return static::find()->where('user_id='.(int)$userId.' AND type='.(int)$type)->one();
    }
    
}
