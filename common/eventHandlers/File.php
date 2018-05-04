<?php

namespace common\eventHandlers;

use common\models\File as FileModel;

use comm\video\models\Video;
use comm\video\models\HasFile;

/* 
 * file 事件处理
 */
class File extends \yii\base\Object {
    
    /**
     * 在文件删除以后触发动作 文件删除以前应该检查所有的关联  文件删除以后将会从oss删除对应的文件
     * @param \nextrip\helpers\Event $event
     */
    public static function afterDelete($event) {
        $file = $event->sender;
        /* @var FileModel $file */
        //从oss删除对应的文件
    }
    
    /**
     * 在文件更新以后触发动作
     * @param \nextrip\helpers\Event $event
     */
    public static function afterInsert($event) {
        $file = $event->sender;
        /* @var FileModel $file */
    }
    
    /**
     * 在文件更新以后修改对应的数据
     * @param \nextrip\helpers\Event $event
     */
    public static function afterUpdate($event) {
        $file = $event->sender;
        /* @var $file FileModel */
        if(!$file->getCustomData('skipUpdateHasFile')) {
            
        }
    }
    
}