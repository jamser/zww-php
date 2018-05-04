<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "t_member_smscode".
 *
 * @property string $mobile
 * @property integer $smscode
 * @property string $valid_start_time
 * @property string $valid_end_time
 */
class MemberSmscode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_member_smscode';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'smscode'], 'required'],
            [['smscode'], 'integer'],
            [['valid_start_time', 'valid_end_time'], 'safe'],
            [['mobile'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mobile' => 'Mobile',
            'smscode' => 'Smscode',
            'valid_start_time' => 'Valid Start Time',
            'valid_end_time' => 'Valid End Time',
        ];
    }
}
