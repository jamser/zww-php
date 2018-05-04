<?php

namespace nextrip\wechat\models;

use Yii;

use WechatSdk\mp\Notice;
use WechatSdk\mp\Exception as WechatException;

/**
 * This is the model class for table "wechat_mp".
 *
 * @property string $id
 * @property string $key 唯一KEY
 * @property string $name  名称
 * @property string $default_reply 自动回复
 * @property string $default_welcome 默认欢迎语
 * @property string $access_token 授权
 * @property string $js_ticket Js ticket
 * @property string $created_at
 * @property string $updated_at
 * @property string $app_id APP ID
 * @property string $app_secret APP 密钥
 * @property string $mch_id 商户ID
 * @property string $pay_key 支付KEY
 * @property string $ssl_cert SsL key
 * @property string $ssl_key SsL key
 * @property string $auto_reply_token 自动回复授权
 * @property string $auto_reply_encoding_aes_key 自动回复授权加密
 * 
 */
class Mp extends \nextrip\helpers\ActiveRecord
{
    public $sendTplMsgError;
    
    /**
     * auto cache config
     * @var array
     */
    protected static $autoCacheConfig = [
        'enable' => true,//set to false auto cache will be disabled
        'duration' => 14400,//cache duration(second)
        'useAttribute'=>'key',//support mixed attributes , Eg:['type', 'name']
        'cacheId'=>'cache',//cache component id
    ];
    
    public $msgHandlerClass = '\nextrip\wechat\helpers\MpMessageHandler';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wechat_mp';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_php');
    }


    public function behaviors() {
        return [
            \yii\behaviors\TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key','name'], 'required'],
            [['key','name',], 'string', 'max' => 64],
            [['app_id','app_secret', 'mch_id', 'pay_key', 'ssl_cert',
                'ssl_key', 'auto_reply_token', 'auto_reply_encoding_aes_key'
             ], 'string', 'max' => 255],
            [['default_reply', 'default_welcome'], 'string', 'max' => 2048],
            [['key'], 'unique', 'filter'=>function($query){
                if(!$this->isNewRecord) {
                    $query->andWhere('id!='.(int)$this->id);
                }
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => '公众号KEY',
            'name' => '名称',
            'default_reply' => '默认回复消息',
            'default_welcome' => '默认欢迎消息',
            'access_token' => 'Access Token',
            'js_ticket' => 'Js Ticket',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'app_id' => 'APP ID',
            'app_secret' => 'APP SECRET',
            'mch_id' => '商户ID',
            'pay_key' => '支付KEY',
            'ssl_cert' => 'SSL CERT',
            'ssl_key' => 'SSL KEY',
            'auto_reply_token' => '自动回复TOKEN',
            'auto_reply_encoding_aes_key' => '自动回复加密KEY',
        ];
    }
    
    /**
     * 返回所有公众号的名称 key为公众号的Key
     * @return type
     */
    public static function getAllMpNames() {
        $allMpOptions = [];
        foreach(Yii::$app->params['wechatMps'] as $key=>$config) {
            $allMpOptions[$key] = $config['name'];
        }
        return $allMpOptions;
    }
    
    /**
     * 获取公众号配置
     */
    public function getConfig($configKey=null) {
        return \yii\helpers\ArrayHelper::getValue(Yii::$app->params, "wechatMps.{$this->key}".($configKey?'.'.$configKey:''));
    }
    
    /**
     * 通过APPID获取
     * @param string $appId
     * @return Mp
     */
    public static function getByAppId($appId) {
        foreach(Yii::$app->params['wechatMps'] as $key=>$config) {
            if($config['appId']==$appId) {
                return static::findAcModel($key);
            }
        }
        return null;
    }
    
    /**
     * 接收消息
     * @param [] $message 微信服务器发送的消息体
     */
    public function handlerMessage($message) {
        $class = $this->msgHandlerClass;
        $mpMesssageHandler = new $class($this, $message);
        return $mpMesssageHandler->recordMsg()->recordWechatUser()->parse();
    }
    
     /**
     * 发送模板消息
     * @param string $toUserOpenId 接收信息用户的openId
     * @param string $templateId 模板参数
     * @param array $data 模板参数
     * @parma string $url 链接
     * @param string $color 颜色
     */
    public function sendTemplateMsg($toUserOpenId, $templateId, $data, $url, $color='FF00000') {
        try {
            $api = new Notice($this->appId, $this->appSecret);
        } catch (WechatException $ex) {
            $this->sendTplMsgError = 'Notice构建失败:'.$ex->getMessage();
            return false;
        }
        
//        try {
//            $templateId = static::getTemplateId($type, $this->localAppId);
//        } catch (Exception $ex) {
//            $this->sendTplMsgError = '获取模板ID失败:'.$ex->getMessage();
//            $templateId = null;
//        }
        
        if($templateId) {
            try{
                $ret = $api->send($toUserOpenId, $templateId, $data, $url, $color);
            } catch (WechatException $ex) {
                $this->sendTplMsgError = '发送模板消息失败:'.$ex->getMessage();
                $ret = false;
                //static::log('微信发送模板消息错误 : '.$ex->getMessage(), 'error');
            }
        } else {
            $ret = false;
        }
        return $ret;
    }
}
