<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "t_member_addr".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $receiver_name
 * @property string $receiver_phone
 * @property string $province
 * @property string $city
 * @property string $county
 * @property string $street
 * @property string $created_date
 * @property string $modified_date
 * @property boolean $default_flg
 */
class MemberAddr extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_member_addr';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id'], 'required'],
            [['member_id'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['default_flg'], 'boolean'],
            [['receiver_name'], 'string', 'max' => 65],
            [['receiver_phone'], 'string', 'max' => 15],
            [['province', 'city', 'county', 'street'], 'string', 'max' => 200],
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
            'receiver_name' => 'Receiver Name',
            'receiver_phone' => 'Receiver Phone',
            'province' => 'Province',
            'city' => 'City',
            'county' => 'County',
            'street' => 'Street',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
            'default_flg' => 'Default Flg',
        ];
    }
}
