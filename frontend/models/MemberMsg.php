<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "t_member_msg".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $msg_title
 * @property string $msg_body
 * @property string $send_date
 * @property boolean $read_flg
 */
class TMemberMsg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_member_msg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id'], 'required'],
            [['member_id'], 'integer'],
            [['send_date'], 'safe'],
            [['read_flg'], 'boolean'],
            [['msg_title'], 'string', 'max' => 50],
            [['msg_body'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'msg_title' => 'Msg Title',
            'msg_body' => 'Msg Body',
            'send_date' => 'Send Date',
            'read_flg' => 'Read Flg',
        ];
    }
}
