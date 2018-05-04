<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "t_member_pref".
 *
 * @property integer $member_id
 * @property boolean $music_flg
 * @property boolean $sound_flg
 * @property boolean $shock_flg
 */
class MemberPref extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_member_pref';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id'], 'required'],
            [['member_id'], 'integer'],
            [['music_flg', 'sound_flg', 'shock_flg'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_id' => 'Member ID',
            'music_flg' => 'Music Flg',
            'sound_flg' => 'Sound Flg',
            'shock_flg' => 'Shock Flg',
        ];
    }
}
