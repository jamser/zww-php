<?php

namespace WechatSdk\mp\Messages;

/**
 * 文本消息
 *
 * @property string $content
 */
class Text extends BaseMessage {

    protected $properties = array('content');

    /**
     * 生成主动消息数组
     *
     * @return array
     */
    public function toStaff() {
        return array(
            'text' => array(
                'content' => $this->content,
            ),
        );
    }

    /**
     * 生成回复消息数组
     *
     * @return array
     */
    public function toReply() {
        return array(
            'Content' => $this->content,
        );
    }

    /**
     * 生成群发消息数组 
     */
    public function toBroadcast() {
        return array(
            'text' => array(
                'content' => $this->content,
            ),
        );
    }
}
