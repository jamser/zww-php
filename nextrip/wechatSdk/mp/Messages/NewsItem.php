<?php

namespace WechatSdk\mp\Messages;

/**
 * 图文项
 */
class NewsItem extends BaseMessage {

	protected $properties = array('title', 'description', 'pic_url', 'url');

}
