<?php

namespace WechatSdk\mp;

/**
 * SDK 服务别名
 */
class Alias {

    protected static $aliases = [
        'WechatAuth' => 'WechatSdk\\Auth',
        'WechatCard' => 'WechatSdk\\Card',
        'WechatException' => 'WechatSdk\\Exception',
        'WechatGroup' => 'WechatSdk\\Group',
        'WechatImage' => 'WechatSdk\\Image',
        'WechatJs' => 'WechatSdk\\Js',
        'WechatMedia' => 'WechatSdk\\Media',
        'WechatMenu' => 'WechatSdk\\Menu',
        'WechatMenuItem' => 'WechatSdk\\MenuItem',
        'WechatMessage' => 'WechatSdk\\Message',
        'WechatBaseMessage' => 'WechatSdk\\Messages\\BaseMessage',
        'WechatImageMessage' => 'WechatSdk\\Messages\\Image',
        'WechatLinkMessage' => 'WechatSdk\\Messages\\Link',
        'WechatLocationMessage' => 'WechatSdk\\Messages\\Location',
        'WechatMusicMessage' => 'WechatSdk\\Messages\\Music',
        'WechatNewsMessage' => 'WechatSdk\\Messages\\News',
        'WechatNewsMessageItem' => 'WechatSdk\\Messages\\NewsItem',
        'WechatTextMessage' => 'WechatSdk\\Messages\\Text',
        'WechatTransferMessage' => 'WechatSdk\\Messages\\Transfer',
        'WechatVideoMessage' => 'WechatSdk\\Messages\\Video',
        'WechatVoiceMessage' => 'WechatSdk\\Messages\\Voice',
        'WechatQRCode' => 'WechatSdk\\QRCode',
        'WechatServer' => 'WechatSdk\\Server',
        'WechatStaff' => 'WechatSdk\\Staff',
        'WechatStore' => 'WechatSdk\\Store',
        'WechatUrl' => 'WechatSdk\\Url',
        'WechatUser' => 'WechatSdk\\User',
        'WechatNotice' => 'WechatSdk\\Notice',
        'WechatStats' => 'WechatSdk\\Stats',
        'WechatSemantic' => 'WechatSdk\\Semantic',
        'WechatColor' => 'WechatSdk\\Color',
    ];

    /**
     * 是否已经注册过
     *
     * @var bool
     */
    protected static $registered = false;

    /**
     * 注册别名
     */
    public static function register() {
        if (!self::$registered) {
            foreach (self::$aliases as $alias => $class) {
                class_alias($class, $alias);
            }

            self::$registered = true;
        }
    }

}
