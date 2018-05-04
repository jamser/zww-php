<?php

namespace nextrip\upload;

use Yii;
use nextrip\helpers\Format;

/**
 * This is the model class for table "upload_file".
 *
 * @property string $id
 * @property string $user_id 用户ID
 * @property integer $type 类型
 * @property integer $file_type 文件类型
 * @property string $url 链接地址
 * @property string $width 图片宽度
 * @property string $height 图片高度
 * @property string $data 数据
 * @property integer $status 状态
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class UploadFile extends \nextrip\helpers\ActiveRecord
{
    /**
     * 上传的文件
     * @var \yii\web\UploadedFile 
     */
    public $file;

    const FILE_TYPE_IMAGE = 1;//照片
    const FILE_TYPE_VIDEO = 2;//视频
    
    const FILE_TYPE_LIST = [
        self::FILE_TYPE_IMAGE=>'照片',
        self::FILE_TYPE_VIDEO=>'视频'
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'upload_file';
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
            [['data'],'default', 'value'=>''],
            [['file'], 'required'],
            [['file'], 'file', //'extensions'=>['gif','png','jpg','jpeg','mp4'],
                'minSize'=>10240,//最小10KB
                'maxSize'=>1024*1024*20,//最大8M
                'tooBig'=>'文件太大',
                'tooSmall'=>'文件太小(必须大于10K)',
            ],
            [['type', 'width', 'height', 'file_type'], 'integer'],
            [['file_type'],'in', 'range'=>self::FILE_TYPE_LIST],
            [['data'], 'string'],
            [['url'], 'string', 'max' => 255],
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
            'file_type' => '文件类型',
            'url' => '链接地址',
            'width' => '图片宽度',
            'height' => '图片高度',
            'data' => '数据',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * 保存文件到模型
     * @param \nextrip\upload\UploadApiInterface $uploadApi 上传API
     * @param string $path 路径
     * @param string $fileType 类型 img
     * @return bool
     * 
     */
    public function saveFile(UploadApiInterface $uploadApi, $path, $fileType) {
        if($this->validate()) {
            $uploadApiResult = $uploadApi->saveFile($this->file, $path, $fileType);
            $this->file_type = static::getFileType($this->file->getExtension());
            $this->url = $uploadApiResult->url;
            $this->height = $uploadApiResult->height;
            $this->width = $uploadApiResult->width;
            $this->data = Format::toJsonStr($uploadApiResult->data);
            return $this->save(false);
        }
        return false;
    }
    
    /**
     * 获取文件类型
     * @param string $ext 扩展名
     * @return int 文件类型
     */
    public static function getFileType($ext) {
        if(in_array($ext, ['jpg','jpeg', 'gif', 'png'])) {
            $fileType = self::FILE_TYPE_IMAGE;
        } else if(in_array($ext, ['mp4'])) {
            $fileType = self::FILE_TYPE_VIDEO;
        } else {
            $fileType = 0;
        }
        return $fileType;
    }
}
