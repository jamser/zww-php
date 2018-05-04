<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "t_member_token".
 *
 * @property string $token
 * @property integer $member_id
 * @property string $valid_start_date
 * @property string $valid_end_date
 */
class MemberToken extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_member_token';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['token', 'member_id'], 'required'],
            [['member_id'], 'integer'],
            [['valid_start_date', 'valid_end_date'], 'safe'],
            [['token'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'token' => 'Token',
            'member_id' => 'Member ID',
            'valid_start_date' => 'Valid Start Date',
            'valid_end_date' => 'Valid End Date',
        ];
    }
}
