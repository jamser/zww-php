<?php
namespace WechatSdk\mp;

/**
 * 群发消息 
 * @todo 实现卡券群发
 */
class Mass {
    
    const API_SEND = 'https://api.weixin.qq.com/cgi-bin/message/mass/send';
    const API_BASE = 'https://api.weixin.qq.com/cgi-bin/message/mass';

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret) {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }
    
    /**
     * 上传图文消息素材 待完成 
     */
    public function uploadnews() {
        //待完成
        throw new Exception('上传图文消息方法还待完善...');
    }
    
    /**
     * 查询发送状态 
     * @param string $msgId 消息ID
     * @return [] 格式 : {"msg_id":201053012,"msg_status":"SEND_SUCCESS"}
     */
    public function querySendStatus($msgId) {
        return  $this->http->jsonPost(self::API_BASE.'/get', [
            'msg_id'=>$msgId
        ]);
    }
    
    /**
     * 删除群发  删除群发消息只能删除图文消息和视频消息
     * @param string $msgId 消息ID
     */
    public function delete($msgId) {
        return  $this->http->jsonPost(self::API_BASE.'/delete', [
            'msg_id'=>$msgId
        ]);
    }
    
    /**
     * 发送给openIds列表 
     * @param [] $message 消息体
     * @param [] $openIds openId列表
     */
    public function sendToOpenIds($message, $openIds) {
        $message['touser'] = (array)$openIds;
        return  $this->http->jsonPost(self::API_BASE.'/send', $message);
    }
    
    /**
     * 发送 
     */
    public function sendToGroup($message) {
        if (!is_array($message)) {
            throw new Exception('群发消息结构不正确', 1);
        }

        $this->http->jsonPost(self::API_SEND, $message);

        return true;
    }
    
    /**
     * 预览 
     * @param [] $message 消息结构体
     * @param string $toWxName 发送给微信用户name
     * @param string $toOpenId 发送给的微信openId 
     */
    public function preview($message, $toWxName, $toOpenId=null) {
        if($toWxName) {
            $message['towxname'] = $toWxName;
        } else if($toOpenId) {
            $message['touser'] = $toOpenId;
        } else {
            throw new Exception('未指定查看的用户');
        }
        return  $this->http->jsonPost(self::API_BASE.'/preview', $message);
    }
    
    /**
     * 获取允许消息类型
     * @reutrn [] 
     */
    public static function getAllowMessageTypes() {
        return [
            Message::TEXT,
            Message::MPNEWS,
            Message::MPVIDEO,
            Message::IMAGE,
            Message::VOICE
            //还有一个卡券消息未实现
        ];
    } 
}

