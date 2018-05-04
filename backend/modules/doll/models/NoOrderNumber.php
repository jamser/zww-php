<?php

namespace backend\modules\doll\models;

use Yii;

/**
 * This is the model class for table "no_order_number".
 *
 * @property integer $id
 * @property string $no_order_number
 * @property string $no_deliver_method
 * @property string $no_deliver_number
 * @property string $create_date
 */
class NoOrderNumber extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'no_order_number';
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
            [['create_date'], 'safe'],
            [['no_order_number', 'no_deliver_method', 'no_deliver_number'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'no_order_number' => 'No Order Number',
            'no_deliver_method' => 'No Deliver Method',
            'no_deliver_number' => 'No Deliver Number',
            'create_date' => 'Create Date',
        ];
    }
}
