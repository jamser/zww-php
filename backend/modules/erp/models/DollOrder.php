<?php

namespace backend\modules\erp\models;

use Yii;

/**
 * This is the model class for table "t_doll_order".
 *
 * @property integer $id
 * @property string $order_number
 * @property string $order_date
 * @property integer $order_by
 * @property string $status
 * @property string $stock_valid_date
 * @property string $deliver_date
 * @property string $deliver_method
 * @property string $deliver_number
 * @property string $deliver_amount
 * @property integer $deliver_coins
 * @property integer $address_id
 * @property string $modified_date
 * @property integer $modified_by
 * @property string $comment
 */
class DollOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_doll_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_date', 'stock_valid_date', 'deliver_date', 'modified_date'], 'safe'],
            [['order_by', 'deliver_coins', 'address_id', 'modified_by'], 'integer'],
            [['deliver_amount'], 'number'],
            [['order_number'], 'string', 'max' => 20],
            [['status'], 'string', 'max' => 8],
            [['deliver_method'], 'string', 'max' => 10],
            [['deliver_number'], 'string', 'max' => 32],
            [['comment'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_number' => 'Order Number',
            'order_date' => 'Order Date',
            'order_by' => 'Order By',
            'status' => 'Status',
            'stock_valid_date' => 'Stock Valid Date',
            'deliver_date' => 'Deliver Date',
            'deliver_method' => 'Deliver Method',
            'deliver_number' => 'Deliver Number',
            'deliver_amount' => 'Deliver Amount',
            'deliver_coins' => 'Deliver Coins',
            'address_id' => 'Address ID',
            'modified_date' => 'Modified Date',
            'modified_by' => 'Modified By',
            'comment' => 'Comment',
        ];
    }
}
