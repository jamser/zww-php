<?php

namespace WechatSdk\mp\Messages;

use WechatSdk\mp\Media;

/**
 * 群发的图片消息
 *
 * @property string $media_id
 */
class Mpnews extends BaseMessage {

    protected $properties = array('media_id');

    /*
     * @return array
     */
    public function toBroadcast() {
        return array(
            'mpnews' => array(
                'media_id' => $this->media_id,
            ),
        );
    }
}