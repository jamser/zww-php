<?php

namespace backend\modules\doll\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $true_name
 * @property string $auth_key
 * @property string $password_hash
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $sex
 * @property string $birthday
 * @property string $country_id
 * @property string $province_id
 * @property string $city_id
 * @property string $avatar
 * @property string $about
 * @property string $email
 * @property string $identity
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_php');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at', 'sex', 'country_id', 'province_id', 'city_id'], 'integer'],
            [['birthday'], 'safe'],
            [['username'], 'string', 'max' => 64],
            [['true_name'], 'string', 'max' => 15],
            [['auth_key', 'email'], 'string', 'max' => 32],
            [['password_hash', 'avatar', 'about', 'identity'], 'string', 'max' => 255],
            [['username'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'true_name' => '真名',
            'auth_key' => 'KEY',
            'password_hash' => '密码',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'sex' => 'Sex',
            'birthday' => 'Birthday',
            'country_id' => 'Country ID',
            'province_id' => 'Province ID',
            'city_id' => 'City ID',
            'avatar' => 'Avatar',
            'about' => 'About',
            'email' => '邮箱',
            'identity' => '渠道号',
        ];
    }
}
