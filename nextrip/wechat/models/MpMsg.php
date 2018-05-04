<?php

namespace nextrip\wechat\models;

use Yii;
use Exception;
use nextrip\helpers\Format;

/**
 * 公众账号消息
 * @property integer $id 
 * @property string $from_user 发送消息的用户 由 mpLocalAppId_openId 构成
 * @property [] $msg_data 消息数据
 * @property integer $created_at 创建时间
 */
class MpMsg extends \nextrip\helpers\ActiveRecord {
  
    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';
    const TYPE_VOICE = 'voice';
    const TYPE_VIDEO = 'video';
    const TYPE_SHORTVIDEO = 'shortvideo';
    const TYPE_LOCATION = 'location';
    const TYPE_LINK = 'location';
    
    /**
     * @inheritDoc
     */
    public static function tableName() {
        return 'wechat_mp_msg';
    }
    
    public function behaviors() {
        return [
            [
                'class'=>\yii\behaviors\TimestampBehavior::className(),
                'updatedAtAttribute'=>false
            ]
        ];
    }
    
    /**
     * 添加消息
     * @param Mp $wechatMp 微信公众账号类
     * @param [] $message 消息结构体
     * @return static
     */
    public static function add($wechatMp, $message) {
        $model = new static;
        $model->from_user = $wechatMp->key.'_'.$message['FromUserName'];
        $model->msg_data = Format::toJsonStr($message, JSON_UNESCAPED_UNICODE);
        $model->save(false);
        return $model;
    }
    
    /**
     * @return Mp 
     */
    public function getWechatMp() {
        if(!$this->isRelationPopulated('wechatMp')) {
            $fromArr = explode('_', $this->from_user, 2);
            if(!$fromArr || empty($fromArr[0]) || !($mp = Mp::findAcModel($fromArr[0])) ) {
                throw new Exception('找不到消息对应的公众账号');
            }
            $this->populateRelation('wechatMp', $mp);
            return $mp;
        }
        return $this->__get('wechatMp');
    }
    
    /**
     * 获取来自的openId
     * @return string 
     */
    public function getFromOpenId() {
        $fromArr = explode('_', $this->from_user, 2);
        if(!$fromArr || empty($fromArr[1]) ) {
            throw new Exception('找不到消息来自用户的OPENID');
        }
        return $fromArr[1];
    }
}

