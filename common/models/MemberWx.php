<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "member_wx".
 *
 * @property string $id
 * @property string $open_id
 * @property string $union_id
 * @property integer $user_id
 */
class MemberWx extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_wx';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['open_id', 'union_id', 'user_id'], 'required'],
            [['user_id'], 'integer'],
            [['open_id', 'union_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'open_id' => 'Open ID',
            'union_id' => 'Union ID',
            'user_id' => 'User ID',
        ];
    }
}
