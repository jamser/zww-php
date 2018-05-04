<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "share_invite".
 *
 * @property integer $id
 * @property string $invite_code
 * @property string $invite_member_id
 * @property string $invited_id
 * @property string $create_date
 * @property integer $state
 */
class ShareInvite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'share_invite';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_date'], 'safe'],
            [['state'], 'integer'],
            [['invite_code'], 'string', 'max' => 12],
            [['invite_member_id', 'invited_id'], 'string', 'max' => 10],
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
            'invite_member_id' => 'Invite Member ID',
            'invited_id' => 'Invited ID',
            'create_date' => 'Create Date',
            'state' => 'State',
        ];
    }
}
