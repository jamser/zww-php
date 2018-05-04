<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "doll_rtmp".
 *
 * @property integer $id
 * @property string $machine_id
 * @property string $machine_code
 * @property string $name
 * @property string $create_date
 * @property string $rtmp_status
 */
class DollRtmp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'doll_rtmp';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_php');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'machine_id', 'machine_code', 'name', 'create_date', 'rtmp_status'], 'required'],
            [['id'], 'integer'],
            [['create_date'], 'safe'],
            [['machine_id', 'machine_code', 'rtmp_status'], 'string', 'max' => 30],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'machine_id' => 'Machine ID',
            'machine_code' => 'Machine Code',
            'name' => 'Name',
            'create_date' => 'Create Date',
            'rtmp_status' => 'Rtmp Status',
        ];
    }
}
