<?php

namespace common\models\doll;

use Yii;

/**
 * This is the model class for table "t_doll_monitor".
 *
 * @property integer $id
 * @property integer $dollId
 * @property string $alert_type
 * @property integer $alert_number
 * @property string $description
 * @property string $created_date
 * @property integer $created_by
 * @property string $modified_date
 * @property integer $modified_by
 */
class Monitor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_doll_monitor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dollId'], 'required'],
            [['dollId', 'alert_number', 'created_by', 'modified_by'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['alert_type'], 'string', 'max' => 20],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dollId' => 'Doll ID',
            'alert_type' => 'Alert Type',
            'alert_number' => 'Alert Number',
            'description' => 'Description',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_date' => 'Modified Date',
            'modified_by' => 'Modified By',
        ];
    }
}
