<?php

namespace nextrip\upload;

use Yii;

/**
 * 上传文件到本地服务器
 */
class UploadApiLocalServer implements UploadApiInterface {
    
    /**
     * @param \yii\web\UploadedFile $uploadFile
     * @param string $path 路径
     * @param string $fileType 文件类型 支持类型 img
     * @return UploadApiResult
     */
    public function saveFile(\yii\web\UploadedFile $uploadFile, $path, $fileType) {
        $url = '//'.Yii::getAlias('@frontendHost').'/assets/uploads'.$path;
        $path = Yii::getAlias('@frontend').'/web/assets/uploads'. $path;
        $dir = dir($path);
        mkdirs($dir,'0777');
        $uploadFile->saveAs($path);
        $ext = $uploadFile->getExtension();
        $size = in_array($ext, ['jpg','jpeg','gif','png']) ? getimagesize($path) : [0,0];
        return new UploadApiResult([
            'url'=> $url,
            'width'=> $size[0],
            'height'=> $size[1],
        ]);
    }
    
}


