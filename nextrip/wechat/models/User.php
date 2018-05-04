<?php

namespace nextrip\wechat\models;

use Yii;
use nextrip\helpers\Format;

/**
 * This is the model class for table "wechat_user".
 *
 * @property string $id
 * @property string $union_id
 * @property string $nickname
 * @property integer $sex
 * @property string $country
 * @property string $province
 * @property string $city
 * @property string $headimgurl
 * @property string $language
 * @property string $data
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 */
class User extends \nextrip\helpers\ActiveRecord
{
    
    /**
     * auto cache config
     * @var array
     */
    protected static $autoCacheConfig = [
        'enable' => true,//set to false auto cache will be disabled
        'duration' => 14400,//cache duration(second)
        'useAttribute'=>'union_id',//support mixed attributes , Eg:['type', 'name']
        'cacheId'=>'cache',//cache component id
    ] ;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wechat_user';
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
            [['union_id', 'nickname', 'data'], 'required'],
            [['sex'], 'integer'],
            [['data'], 'string'],
            [['union_id'], 'string', 'max' => 128],
            [['nickname', 'country', 'province', 'city', 'headimgurl', 'language'], 'string', 'max' => 255],
            [['union_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'union_id' => 'Union ID',
            'nickname' => 'Nickname',
            'sex' => 'Sex',
            'country' => 'Country',
            'province' => 'Province',
            'city' => 'City',
            'headimgurl' => 'Headimgurl',
            'language' => 'Language',
            'data' => 'Data',
        ];
    }
    
    /**
     * 通过第三方用户更新资料
     * @param OpenUser $openUser 第三方用户
     */
    public function updateByOpenUser($openUser) {
        $compareAttributes = ['nickname', 'sex', 'country', 'province', 'city', 'headimgurl'];
        $updateAttributes = [];
        foreach ($compareAttributes as $attribute) {
            if ($openUser->$attribute && $openUser->$attribute != $this->$attribute) {
                $this->$attribute = $openUser->$attribute;
                $updateAttributes[] = $attribute;
            }
        }
        if ($updateAttributes) {
            $this->updateAttributes($updateAttributes);
        }
    }

    /**
     * 通过第三方用户获取模型 网页模型 没有subscribe .. , 公众账号 有subscribe
     * @param \WechatSdk\models\User $openUser 第三方用户
     * @param UnionId $unionIdRecord 联合ID记录
     * @return static
     */
    public static function getModelByOpenUser($openUser, $unionIdRecord) {
        $wechatDbUser = static::findAcModel($unionIdRecord->union_id); 
        if(!$wechatDbUser) {
            $wechatDbUser = new static;
            $wechatDbUser->setAttributes([
                'union_id' => $unionIdRecord->union_id,
                'sex' => (int) $openUser->sex,
                'country' => $openUser->country ? $openUser->country : '',
                'province' => $openUser->province ? $openUser->province : '',
                'city' => $openUser->city ? $openUser->city : '',
                'nickname' => $openUser->nickname ? $openUser->nickname : '',
                'headimgurl' => $openUser->headimgurl ? $openUser->headimgurl : '',
                'language' =>  isset($openUser->language) ? $openUser->language : '',
                'data'=>  Format::toStr([
                    'privilege'=>$openUser->privilege,
                    'subscribe'=>$openUser->subscribe,
                    'subscribe_time'=>$openUser->subscribe_time,
                ]),
            ], false);
            try {
                $wechatDbUser->save(false);
            } catch (\yii\db\IntegrityException $ex) {
                if(!($wechatDbUser = static::findAcModel($unionIdRecord->union_id))) {
                    throw new \yii\db\IntegrityException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
                }
            }
        } else {
            $wechatDbUser->updateByOpenUser($openUser);
        }
        return $wechatDbUser;
    }
    
    /**
     * 获取性别
     * @return integer
     */
    public function getGender() {
        return (int) $this->sex;
    }

    /**
     * 获取头像 最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空
     * @return string
     */
    public function getAvatar($size=0) {
        $avatar = '';
        if ($this->headimgurl) {
            if(strpos($this->headimgurl, 'http')===0) {
                $this->headimgurl = strtr($this->headimgurl, [
                    'http://'=>'//',
                    'https://'=>'//',
                ]);
            }
            $avatar = $this->headimgurl;
            $allowSizes = [0,46,64,96,132];
            if(in_array($size, $allowSizes)) {
                if($size!=0) {
                    $avatar = substr($avatar, 0, strlen($avatar)-1).$size;
                }
            } else {
                throw new \Exception("无效的头像大小 {$size}");
            }
        }
        return $avatar;
    }
}
