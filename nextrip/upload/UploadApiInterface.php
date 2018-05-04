<?php

namespace nextrip\upload;

use yii\web\UploadedFile as UploadFileObj;

/**
 * 上传文件接口
 */
interface UploadApiInterface {
    
    /**
     * 保存文件
     * @param \yii\web\UploadedFile $uploadFile
     * @param string $path 路径
     * @param string $fileType 文件类型 支持类型 img
     * @return UploadApiResult
     */
    public function saveFile(UploadFileObj $uploadFile, $path, $fileType);
    
}

