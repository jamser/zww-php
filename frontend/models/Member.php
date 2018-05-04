<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "t_member".
 *
 * @property integer $id
 * @property string $memberID
 * @property string $name
 * @property string $mobile
 * @property string $password
 * @property string $weixin_id
 * @property string $gender
 * @property string $birthday
 * @property integer $coins
 * @property integer $points
 * @property integer $catch_number
 * @property string $register_date
 * @property string $modified_date
 * @property integer $modified_by
 * @property string $last_login_date
 * @property string $last_logoff_date
 * @property boolean $online_flg
 * @property string $icon_context_path
 * @property string $icon_file_name
 * @property string $icon_real_path
 * @property string $easemob_uuid
 * @property boolean $active_flg
 * @property boolean $invite_flg
 */
class Member extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['birthday', 'register_date', 'modified_date', 'last_login_date', 'last_logoff_date'], 'safe'],
            [['coins', 'points', 'catch_number', 'modified_by'], 'integer'],
            [['online_flg', 'active_flg', 'invite_flg'], 'boolean'],
            [['memberID', 'gender'], 'string', 'max' => 10],
            [['name', 'password'], 'string', 'max' => 64],
            [['mobile'], 'string', 'max' => 15],
            [['weixin_id'], 'string', 'max' => 32],
            [['icon_context_path'], 'string', 'max' => 100],
            [['icon_file_name'], 'string', 'max' => 125],
            [['icon_real_path'], 'string', 'max' => 255],
            [['easemob_uuid'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'memberID' => 'Member ID',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'password' => 'Password',
            'weixin_id' => 'Weixin ID',
            'gender' => 'Gender',
            'birthday' => 'Birthday',
            'coins' => 'Coins',
            'points' => 'Points',
            'catch_number' => 'Catch Number',
            'register_date' => 'Register Date',
            'modified_date' => 'Modified Date',
            'modified_by' => 'Modified By',
            'last_login_date' => 'Last Login Date',
            'last_logoff_date' => 'Last Logoff Date',
            'online_flg' => 'Online Flg',
            'icon_context_path' => 'Icon Context Path',
            'icon_file_name' => 'Icon File Name',
            'icon_real_path' => 'Icon Real Path',
            'easemob_uuid' => 'Easemob Uuid',
            'active_flg' => 'Active Flg',
            'invite_flg' => 'Invite Flg',
        ];
    }
}
