<?php

namespace nextrip\upload;

/**
 * 上传文件接口
 */
class UploadApiResult extends \yii\base\Object{
    
    /**
     * URL
     * @var string 
     */
    public $url;
    
    /**
     * 宽度
     * @var integer 
     */
    public $width=0;
    
    /**
     * 高度
     * @var integer 
     */
    public $height=0;
    
    /**
     * 数据
     * @var type 
     */
    public $data = [];
}

