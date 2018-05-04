<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wechat_mp".
 *
 * @property string $id
 * @property string $key
 * @property string $name
 * @property string $app_id
 * @property string $app_secret
 * @property string $default_reply
 * @property string $default_welcome
 * @property string $access_token
 * @property string $js_ticket
 * @property string $created_at
 * @property string $updated_at
 * @property string $mch_id
 * @property string $pay_key
 * @property string $ssl_cert
 * @property string $ssl_key
 * @property string $auto_reply_token
 * @property string $auto_reply_encoding_aes_key
 */
class WechatMp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wechat_mp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name', 'app_id', 'app_secret', 'default_reply', 'default_welcome', 'created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['key', 'name'], 'string', 'max' => 64],
            [['app_id', 'app_secret', 'mch_id', 'pay_key', 'ssl_cert', 'ssl_key', 'auto_reply_token', 'auto_reply_encoding_aes_key'], 'string', 'max' => 255],
            [['default_reply', 'default_welcome'], 'string', 'max' => 2048],
            [['access_token', 'js_ticket'], 'string', 'max' => 1024],
            [['key'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'app_id' => 'App ID',
            'app_secret' => 'App Secret',
            'default_reply' => 'Default Reply',
            'default_welcome' => 'Default Welcome',
            'access_token' => 'Access Token',
            'js_ticket' => 'Js Ticket',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'mch_id' => 'Mch ID',
            'pay_key' => 'Pay Key',
            'ssl_cert' => 'Ssl Cert',
            'ssl_key' => 'Ssl Key',
            'auto_reply_token' => 'Auto Reply Token',
            'auto_reply_encoding_aes_key' => 'Auto Reply Encoding Aes Key',
        ];
    }
}
