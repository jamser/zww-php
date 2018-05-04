<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "charge_order".
 *
 * @property integer $id
 * @property string $order_no
 * @property integer $chargeruleid
 * @property string $charge_name
 * @property string $price
 * @property integer $member_id
 * @property string $member_name
 * @property string $charge_type
 * @property integer $charge_state
 * @property string $coins_before
 * @property integer $coins_after
 * @property integer $coins_charge
 * @property integer $coins_offer
 * @property string $create_date
 * @property string $update_date
 */
class ChargeOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'charge_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chargeruleid', 'member_id', 'charge_state', 'coins_before', 'coins_after', 'coins_charge', 'coins_offer'], 'integer'],
            [['price'], 'number'],
            [['create_date', 'update_date'], 'safe'],
            [['order_no'], 'string', 'max' => 64],
            [['charge_name', 'member_name', 'charge_type'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_no' => 'Order No',
            'chargeruleid' => 'Chargeruleid',
            'charge_name' => 'Charge Name',
            'price' => 'Price',
            'member_id' => 'Member ID',
            'member_name' => 'Member Name',
            'charge_type' => 'Charge Type',
            'charge_state' => 'Charge State',
            'coins_before' => 'Coins Before',
            'coins_after' => 'Coins After',
            'coins_charge' => 'Coins Charge',
            'coins_offer' => 'Coins Offer',
            'create_date' => 'Create Date',
            'update_date' => 'Update Date',
        ];
    }
}
