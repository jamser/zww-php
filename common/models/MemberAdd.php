<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "member_add".
 *
 * @property integer $id
 * @property string $openid
 * @property string $unionid
 * @property integer $add_flg
 */
class MemberAdd extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_add';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['openid'], 'required'],
            [['add_flg'], 'integer'],
            [['openid', 'unionid'], 'string', 'max' => 255],
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
            'unionid' => 'Unionid',
            'add_flg' => 'Add Flg',
        ];
    }
}
