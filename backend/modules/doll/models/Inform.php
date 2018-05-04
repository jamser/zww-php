<?php

namespace backend\modules\doll\models;

use Yii;

/**
 * This is the model class for table "Inform".
 *
 * @property integer $id
 * @property integer $memberID
 * @property string $name
 */
class Inform extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Inform';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['id'], 'required'],
            [['id', 'memberID'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'memberID' => '游戏ID',
            'name' => '通知人名字',
        ];
    }
}
