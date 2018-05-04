<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

use nextrip\helpers\Format;
use nextrip\helpers\TimeFormat;


/**
 * This is the model class for table "file".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property string $name
 * @property string $url
 * @property string $data
 * @property string $created_at
 * @property string $updated_at
 */
class File extends \nextrip\helpers\ActiveRecord
{
    const TYPE_PANO_VIDEO=1;//全景视频
    const TYPE_PANO_IMG = 2;//全景图片
    const TYPE_IMG = 3;//普通图片
    const TYPE_VIDEO = 4;//普通视频
    const TYPE_MP3 = 5;//mp3文件
    
    const CREATE_MP3_STATUS_DISABLED = -1;
    const CREATE_MP3_STATUS_RUNNING = 1;
    const CREATE_MP3_STATUS_FINISH = 2;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->db_php;
    }
    
    public function init() {
        parent::init();
        $this->on(self::EVENT_AFTER_DELETE, ['\common\eventHandlers\File', 'afterDelete']);
        $this->on(self::EVENT_AFTER_UPDATE, ['\common\eventHandlers\File', 'afterUpdate']);
        $this->on(self::EVENT_AFTER_INSERT, ['\common\eventHandlers\File', 'afterInsert']);
    }

    public function behaviors() {
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
            [['type', 'url', 'name'], 'required'],
            [['type'], 'integer'],
            [['type'], 'in', 'range'=>array_keys(static::getAllTypes())],
            [['name'], 'string', 'length'=>[1,32], 'encoding'=>'utf-8'],
            [['url'], 'string', 'max' => 255],
            [['url'], 'url'],
            [['url'], 'checkUrl', 'skipOnError'=>true],
            [['data'], 'string'],
        ];
    }
    
    public function checkUrl($attribute, $params) {
        $urlParts=parse_url($this->url); 
        $fileParts = explode('.',$urlParts['path']); 
        $ext = $fileParts[1];
        $ossConfigs = Yii::$app->params['aliOss'];
        switch ($this->type) {
            case self::TYPE_PANO_VIDEO://全景视频  要求文件类型为mp4 并且要求放在media下
                if($ext!=='mp4') {
                    $this->addError($attribute, '该类型只支持mp4类型文件');
                    return false;
                } else if(strpos($this->url, $ossConfigs['media']['publicHost'])!==0) {
                    $this->addError($attribute, '该类型URL必须包含 '.$ossConfigs['media']['publicHost']);
                    return false;
                }
                break;
            case self::TYPE_VIDEO://普通视频 要求文件类型为mp4 可使用其他网站的资源
                if($ext!=='mp4') {
                    $this->addError($attribute, '该类型只支持mp4类型文件');
                    return false;
                }
                break;
            case self::TYPE_PANO_IMG://全景图片 要求文件类型为 jpg/png  并且要求放在media下
                if(!in_array($ext, ['jpg','png', 'jpeg'])) {
                    $this->addError($attribute, '该类型只支持 jpg, png类型文件');
                    return false;
                } else if(strpos($this->url, $ossConfigs['media']['publicHost'])!==0) {
                    $this->addError($attribute, '该类型URL必须包含 '.$ossConfigs['media']['publicHost']);
                    return false;
                }
                break;
            case self::TYPE_IMG://普通图片 要求文件类型为 jpg/png  并且要求放在img下
                if(!in_array($ext, ['jpg','png', 'jpeg'])) {
                    $this->addError($attribute, '该类型只支持 jpg, png类型文件');
                    return false;
                } else if(strpos($this->url, $ossConfigs['img']['publicHost'])!==0) {
                    $this->addError($attribute, '该类型URL必须包含 '.$ossConfigs['img']['publicHost']);
                    return false;
                }
                break;

            default:
                break;
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
            'type' => '类型',
            'name' => '名称',
            'url' => 'Url',
            'data' => '数据',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 获取类型的名称
     * @param string $value 类型值
     * @return string
     */
    public static function getTypeName($value) {
        $allTypes = static::getAllTypes();
        return isset($allTypes[$value]) ? $allTypes[$value] : '未知';
    }

    /**
     * 获取所有的类型
     * @return []
     */
    public static function getAllTypes () {
        return [
            self::TYPE_PANO_VIDEO=>'全景视频',
            self::TYPE_PANO_IMG=>'全景照片',
            self::TYPE_IMG=>'普通图片',
            self::TYPE_VIDEO=>'普通视频',
            self::TYPE_MP3=>'MP3',
        ];
    }
    
    /**
     * @return array
     */
    public function fields()
    {
        $intAttributes = [
            'id',
            'userId'=>'user_id',
            'createdAt'=>'created_at',
            'updatedAt'=>'updated_at',
            'type',
        ];
        return $this->intFields($intAttributes) + [
            'name',
            'url',
            'formatTime'=>function($model) {
                return TimeFormat::getDefault($model->created_at);
            },
            'data'=> function($model) {
                return $model->getArrayFormatAttribute('data');
            },
        ];
    }
    
    /**
     * 获取加密的URL
     * @return string
     */
    public function getAuthUrl() {
        return \common\helpers\Cdn::getAuthUrl($this->url);
    }
    
    
    /**
     * 判断用户能否使用文件
     * @param int $userId 用户ID
     * @return bool
     */
    public function canUse($userId) {
        return (int)$userId===(int)$this->user_id || Yii::$app->getAuthManager()->checkAccess((int)$userId, '管理店铺');
    }
}
