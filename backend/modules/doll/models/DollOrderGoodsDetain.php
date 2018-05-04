<?php

namespace backend\modules\doll\models;

use Yii;

/**
 * This is the model class for table "doll_order_goods_detain".
 *
 * @property integer $id
 * @property string $order_number
 * @property string $order_date
 * @property integer $member_id
 * @property string $status
 * @property string $stock_valid_date
 * @property string $deliver_date
 * @property string $deliver_method
 * @property string $deliver_number
 * @property string $deliver_amount
 * @property integer $deliver_coins
 * @property string $dollitemids
 * @property string $dolls_info
 * @property string $receiver_name
 * @property string $receiver_phone
 * @property string $province
 * @property string $city
 * @property string $county
 * @property string $street
 * @property string $comment
 * @property string $created_date
 * @property string $modified_date
 * @property string $modified_by
 * @property string $note
 * @property string $detain_date
 */
class DollOrderGoodsDetain extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'doll_order_goods_detain';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'member_id'], 'required'],
            [['id', 'member_id', 'deliver_coins'], 'integer'],
            [['order_date', 'stock_valid_date', 'deliver_date', 'created_date', 'modified_date', 'detain_date'], 'safe'],
            [['deliver_amount'], 'number'],
            [['dollitemids', 'dolls_info'], 'string'],
            [['order_number'], 'string', 'max' => 20],
            [['status'], 'string', 'max' => 8],
            [['deliver_method'], 'string', 'max' => 10],
            [['deliver_number', 'modified_by'], 'string', 'max' => 32],
            [['receiver_name'], 'string', 'max' => 65],
            [['receiver_phone'], 'string', 'max' => 15],
            [['province', 'city', 'county', 'street'], 'string', 'max' => 200],
            [['comment', 'note'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_number' => '订单号',
            'order_date' => '订单生成日期',
            'member_id' => 'Member ID',
            'status' => '发货状态',
            'stock_valid_date' => 'Stock Valid Date',
            'deliver_date' => '发货日期',
            'deliver_method' => '发货方式',
            'deliver_number' => '快递单号',
            'deliver_amount' => '邮费',
            'deliver_coins' => 'Deliver Coins',
            'dollitemids' => 'Dollitemids',
            'dolls_info' => 'Dolls Info',
            'receiver_name' => '收货人姓名',
            'receiver_phone' => '收货人手机',
            'province' => '省份',
            'city' => '城市',
            'county' => '城镇',
            'street' => '街道',
            'comment' => 'Comment',
            'created_date' => 'Created Date',
            'modified_date' => 'Modified Date',
            'modified_by' => 'Modified By',
            'note' => '备注',
            'detain_date' => '扣留日期',
        ];
    }
}
