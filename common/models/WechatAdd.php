<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wechat_add".
 *
 * @property integer $id
 * @property string $openid
 * @property integer $add_flg
 */
class WechatAdd extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wechat_add';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['openid'], 'required'],
            [['add_flg'], 'integer'],
            [['openid'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'openid' => 'Openid',
            'add_flg' => 'Add Flg',
        ];
    }
}
