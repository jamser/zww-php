<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "wechat_user".
 *
 * @property string $id
 * @property string $union_id
 * @property string $nickname
 * @property string $openid
 * @property integer $sex
 * @property string $country
 * @property string $province
 * @property string $city
 * @property string $headimgurl
 * @property string $language
 * @property string $data
 * @property string $created_at
 * @property integer $updated_at
 * @property string $access_token
 * @property integer $expires_in
 * @property string $refresh_token
 * @property string $scope
 * @property integer $coins
 * @property integer $invite_code
 * @property integer $invite_flg
 */
class WechatUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wechat_user';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->db_php;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nickname', 'openid', 'access_token', 'expires_in', 'refresh_token', 'scope'], 'required'],
            [['sex', 'created_at', 'updated_at', 'expires_in', 'coins', 'invite_code', 'invite_flg'], 'integer'],
            [['data'], 'string'],
            [['union_id'], 'string', 'max' => 128],
            [['nickname', 'openid', 'country', 'province', 'city', 'headimgurl', 'language', 'access_token', 'refresh_token', 'scope'], 'string', 'max' => 255],
            [['union_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'union_id' => 'Union ID',
            'nickname' => 'Nickname',
            'openid' => 'Openid',
            'sex' => 'Sex',
            'country' => 'Country',
            'province' => 'Province',
            'city' => 'City',
            'headimgurl' => 'Headimgurl',
            'language' => 'Language',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'access_token' => 'Access Token',
            'expires_in' => 'Expires In',
            'refresh_token' => 'Refresh Token',
            'scope' => 'Scope',
            'coins' => 'Coins',
            'invite_code' => 'Invite Code',
            'invite_flg' => 'Invite Flg',
        ];
    }
}
