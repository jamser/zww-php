<?php

namespace common\models\doll;

use Yii;

/**
 * This is the model class for table "doll_machine_statistic".
 *
 * @property integer $id
 * @property string $machine_id
 * @property string $machine_code
 * @property string $machine_device_name
 * @property string $play_count
 * @property string $grab_count
 * @property string $start_time
 * @property string $end_time
 * @property string $created_at
 * @property string $updated_at
 * @property integer $type
 */
class MachineStatistic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'doll_machine_statistic';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
//        return Yii::$app->get('db_php');
        return Yii::$app->get('db');
    }
    
    public function behaviors() {
        return [
            \yii\behaviors\TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['machine_id', 'machine_code', 'machine_device_name', 'play_count', 'grab_count'], 'required'],
            [['machine_id', 'play_count', 'grab_count', 'start_time', 'end_time', 'type'], 'integer'],
            [['machine_code', 'machine_device_name'], 'string', 'max' => 255],
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
            'machine_device_name' => 'Machine Device Name',
            'play_count' => 'Play Count',
            'grab_count' => 'Grab Count',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'type' => 'Type',
        ];
    }
}
