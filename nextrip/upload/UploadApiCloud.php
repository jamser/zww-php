<?php

namespace nextrip\upload;

use Yii;
use \OSS\OssClient;

/**
 * 上传文件到云端
 */
class UploadApiCloud implements UploadApiInterface {
    
    /**
     * @param \yii\web\UploadedFile $uploadFile
     * @param string $path 路径
     * @return UploadApiResult
     */
    public function saveFile(\yii\web\UploadedFile $uploadFile, $path, $fileType) {
        $ext = $uploadFile->getExtension();
        $size = in_array($ext, ['jpg','jpeg','gif','png']) ? getimagesize($uploadFile->tempName) : [0,0];
        $ossConfig = Yii::$app->params['aliOss'][$fileType];
        $this->upload($uploadFile->tempName, $path, $ossConfig);
        
        return new UploadApiResult([
            'url'=> '//'.$ossConfig['publicHost'].$path,
            'width'=> $size[0],
            'height'=> $size[1],
        ]);
    }
    
    protected function upload($filePath, $savePath, $ossConfig) {
        $accessKeyId = $ossConfig['accessKeyId'];
        $accessKeySecret = $ossConfig['accessKeySecret'];
        $endpoint = YII_ENV!=='prod' ?  $ossConfig['endpointPublic'] :  $ossConfig['endpointInner'];
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

        return $ossClient->uploadFile($ossConfig['bucket'], $ossConfig['prefix'].substr($savePath,1), $filePath);
    }
    
}


