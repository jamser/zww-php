<?php
namespace backend\models;

use Yii;

use OSS\OssClient;
use OSS\Core\OssException;

/**
 * 发布资源到OSS
 */
class PublishAssetForm extends \common\models\Form {

    protected $sourceBasePath = 'webAsset';

    protected $targetBasePath = 'static';

    public $source;

    public $target;

    public function rules() {
        return [
            [['source', 'target'], 'required'],
            [['source', 'target'],'string', 'length'=>[1,255]],
            [['source', 'target'], function($attribute,$params) {
                if(substr($this->$attribute, 0, 1)!=='/') {
                    $this->addError($attribute, '必须以/作为开始');
                    return false;
                } else if(strpos($this->$attribute, '..')!==false) {
                    $this->addError($attribute, '路径不能包含..');
                    return false;
                }
            }],
            [['source'], function($attribute, $params) {
                $path = Yii::getAlias('@webAssets').$this->source;
                if(!file_exists($path)) {
                    $this->addError($attribute, '来源文件不存在');
                    return false;
                }
            }, 'skipOnError'=>true]
        ];
    }

    public function attributeLabels() {
        return [
            'source'=>'来源位置',
            'target'=>'目标位置'
        ];
    }

    public function save() {
        if(!$this->validate()) {
            return false;
        }

        $fileOssConfig = Yii::$app->params['aliOss']['files'];
        $accessKeyId = $fileOssConfig['accessKeyId'];
        $accessKeySecret = $fileOssConfig['accessKeySecret'];
        $bucket = $fileOssConfig['bucket'];
        $endpoint = $fileOssConfig['endpointHost'];
        if(strpos($endpoint, $bucket)===0) {
            $endpoint = substr($endpoint, strlen($bucket)+1);
        }


        $content = file_get_contents($this->source);
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        } catch (OssException $e) {
            $this->addError('source','OSS初始化出错:'.$e->getMessage());
            return false;
        }

        $object = 'static'.$this->target;
        $options = [];
        if( strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'js') {
            $options[OssClient::OSS_HEADERS] = [
                'Content-Type' => 'application/javascript',
            ];
        }
        try{
            $ossClient->putObject($bucket, $fileOssConfig['prefix'].$object, $content, $options);
        } catch(OssException $e) {
            $this->addError('source','上传内容到OSS出错:'.$e->getMessage());
            return false;
        }
        return $fileOssConfig['publicHost'].'/static'.$this->target;
    }
}
