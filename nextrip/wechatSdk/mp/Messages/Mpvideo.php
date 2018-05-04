<?php

namespace WechatSdk\mp\Messages;

use WechatSdk\mp\Media;

/**
 * 群发的图片消息
 *
 * @property string $media_id
 */
class Mpvideo extends BaseMessage {

    protected $properties = array('media_id');

    /*
     * @return array
     */
    public function toBroadcast() {
        return array(
            'mpvideo' => array(
                'media_id' => $this->media_id,
            ),
        );
    }
}