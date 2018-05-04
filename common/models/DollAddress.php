<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "doll_address".
 *
 * @property integer $id
 * @property string $province
 * @property string $city
 * @property string $county
 * @property string $street
 * @property string $created_date
 * @property string $modified_date
 * @property boolean $default_flg
 */
class DollAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'doll_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_date', 'modified_date'], 'safe'],
            [['default_flg'], 'boolean'],
            [['province', 'city', 'county'], 'string', 'max' => 10],
            [['street'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
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
