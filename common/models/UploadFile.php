<?php

namespace common\models;

use Yii;

/**
 * 用户文件上传类
 */
class UploadFile extends \nextrip\upload\UploadFile
{
    
    const TYPE_AVATAR = 1;//头像
    const TYPE_COVER = 2;//封面相册
    
    const TYPE_LIST = [
        self::TYPE_AVATAR=>'头像',
        self::TYPE_COVER=>'封面相册'
    ];
    
    const TYPE_EN_LIST = [
        self::TYPE_AVATAR=>'avatars',
        self::TYPE_COVER=>'covers'
    ];
    

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['file', 'file', 'extensions'=>['gif','png','jpg','jpeg'], 'when'=>function($model) {
            return $model->type==self::TYPE_AVATAR;
        }];
        $rules[] = ['file', 'file', 'extensions'=>['gif','png','jpg','jpeg','mp4'], 'when'=>function($model) {
            return $model->type==self::TYPE_COVER;
        }];
        return $rules;
    }
    
}
