<?php

namespace backend\modules\erp\models;

use Yii;

/**
 * This is the model class for table "t_doll_order_item".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $doll_id
 * @property integer $quantity
 * @property string $created_date
 * @property string $modified_date
 * @property string $doll_code
 */
class DollOrderItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_doll_order_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'doll_id'], 'required'],
            [['order_id', 'doll_id', 'quantity'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['doll_code'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'doll_id' => 'Doll ID',
            'quantity' => 'Quantity',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
            'doll_code' => 'Doll Code',
        ];
    }
}
