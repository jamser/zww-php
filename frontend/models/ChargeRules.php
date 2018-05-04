<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "t_charge_rules".
 *
 * @property integer $id
 * @property string $charge_price
 * @property integer $coins_charge
 * @property integer $coins_offer
 * @property string $discount
 * @property string $description
 * @property string $created_date
 * @property integer $created_by
 * @property string $modified_date
 * @property integer $modified_by
 */
class ChargeRules extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_charge_rules';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['charge_price', 'discount'], 'number'],
            [['coins_charge', 'coins_offer', 'created_by', 'modified_by'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['description'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'charge_price' => 'Charge Price',
            'coins_charge' => 'Coins Charge',
            'coins_offer' => 'Coins Offer',
            'discount' => 'Discount',
            'description' => 'Description',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_date' => 'Modified Date',
            'modified_by' => 'Modified By',
        ];
    }
}
