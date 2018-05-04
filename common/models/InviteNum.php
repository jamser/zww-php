<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invite_num".
 *
 * @property integer $id
 * @property integer $invite_code
 * @property integer $invite_num
 */
class InviteNum extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invite_num';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'invite_code'], 'required'],
            [['id', 'invite_code', 'invite_num'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invite_code' => 'Invite Code',
            'invite_num' => 'Invite Num',
        ];
    }
}
