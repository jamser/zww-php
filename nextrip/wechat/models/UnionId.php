<?php

namespace nextrip\wechat\models;

use Yii;

/**
 * This is the model class for table "wechat_unionid".
 *
 * @property string $id
 * @property string $open_id
 * @property string $union_id
 * @property string $app_id
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class UnionId extends \nextrip\helpers\ActiveRecord
{
    const STATUS_UNSUBSCRIBE = -1;//已取消关注
    const STATUS_UNKNOW = 0;//未知
    const STATUS_SUBSCRIBE = 1;//已关注
    
    /**
     * auto cache config
     * @var array
     */
    protected static $autoCacheConfig = [
        'enable' => true,//set to false auto cache will be disabled
        'duration' => 14400,//cache duration(second)
        'useAttribute'=>['open_id','app_id'],//support mixed attributes , Eg:['type', 'name']
        'cacheId'=>'cache',//cache component id
    ] ;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wechat_unionid';
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
            [['open_id', 'union_id', 'app_id', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['open_id', 'union_id'], 'string', 'max' => 128],
            [['app_id'], 'string', 'max' => 64],
            [['open_id', 'app_id', 'union_id'], 'unique', 'targetAttribute' => ['open_id', 'app_id', 'union_id'], 'message' => 'The combination of Open ID, Union ID and Mp Key has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'open_id' => 'Open ID',
            'union_id' => 'Union ID',
            'app_id' => 'App ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    
    /**
     * 通过openId获取unionId
     * @param \WechatSdk\models\User $openUser
     * @param bool $autoCreate 不存在记录时是否自动创建  默认为否
     * @return UnionId 返回获取的记录模型
     */
    public static function getByOpenId($openUser, $autoCreate = false) {
        $unionIdRecord = static::findAcModel([
            $openUser->openid, $openUser->getAppId()
        ]);
        if(!$unionIdRecord) {
            if($openUser->subscribe) {
                $status = self::STATUS_SUBSCRIBE;
            } else if($openUser->subscribe!==null) {
                $status = self::STATUS_UNSUBSCRIBE;
            } else {
                $status = self::STATUS_UNKNOW;
            }
            $unionIdRecord = new UnionId;
            $unionIdRecord->open_id = $openUser->openid;
            $unionIdRecord->union_id = $openUser->getUnionId();
            $unionIdRecord->app_id = $openUser->getAppId();
            $unionIdRecord->status = $status;
            try {
                $unionIdRecord->save(false);
            } catch (\yii\db\IntegrityException $ex) {
                $unionIdRecord = static::findAcModel([
                    $openUser->openid, $openUser->getAppId()
                ]);
                if(!$unionIdRecord) {
                    throw new \yii\db\IntegrityException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
                }
            }
        }
        return $unionIdRecord;
    }
    
    /**
     * 获取账号
     * @param string $unionId 联合ID
     * @param integer $appId 微信APP ID
     * @return static
     */
    public static function getAccount($unionId, $appId) {
        return static::find()->where('union_id=:unionId AND app_id=:appId', [
            ':unionId'=>$unionId,
            ':appId'=>$appId
        ])->one();
    }
}
