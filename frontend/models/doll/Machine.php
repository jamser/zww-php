<?php

namespace app\models\doll;

use Yii;

/**
 * This is the model class for table "t_doll".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $quantity
 * @property string $price
 * @property integer $redeem_coins
 * @property string $machine_status
 * @property string $machine_serial_num
 * @property string $machine_ip
 * @property string $machine_url
 * @property string $tbimg_context_path
 * @property string $tbimg_file_name
 * @property string $tbimg_real_path
 * @property string $created_date
 * @property integer $created_by
 * @property string $modified_date
 * @property integer $modified_by
 * @property string $rtmp_url_1
 * @property string $rtmp_url_2
 * @property string $rtmp_url_3
 * @property string $rtmp_push_url
 * @property string $mns_topic_name
 * @property integer $watching_number
 * @property integer $timeout
 * @property string $pili_room_name
 * @property string $machine_code
 * @property string $doll_ID
 * @property integer $machine_type
 */
class Machine extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_doll';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['quantity', 'price', 'redeem_coins', 'created_by', 'modified_by', 'watching_number', 'timeout', 'machine_type'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['name', 'mns_topic_name'], 'string', 'max' => 64],
            [['description', 'tbimg_real_path', 'pili_room_name'], 'string', 'max' => 255],
            [['machine_status'], 'string', 'max' => 10],
            [['machine_serial_num'], 'string', 'max' => 32],
            [['machine_ip'], 'string', 'max' => 50],
            [['machine_url', 'tbimg_context_path', 'rtmp_url_3'], 'string', 'max' => 100],
            [['tbimg_file_name'], 'string', 'max' => 125],
            [['rtmp_url_1', 'rtmp_url_2'], 'string', 'max' => 600],
            [['rtmp_push_url'], 'string', 'max' => 200],
            [['machine_code', 'doll_ID'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'redeem_coins' => 'Redeem Coins',
            'machine_status' => 'Machine Status',
            'machine_serial_num' => 'Machine Serial Num',
            'machine_ip' => 'Machine Ip',
            'machine_url' => 'Machine Url',
            'tbimg_context_path' => 'Tbimg Context Path',
            'tbimg_file_name' => 'Tbimg File Name',
            'tbimg_real_path' => 'Tbimg Real Path',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_date' => 'Modified Date',
            'modified_by' => 'Modified By',
            'rtmp_url_1' => 'Rtmp Url 1',
            'rtmp_url_2' => 'Rtmp Url 2',
            'rtmp_url_3' => 'Rtmp Url 3',
            'rtmp_push_url' => 'Rtmp Push Url',
            'mns_topic_name' => 'Mns Topic Name',
            'watching_number' => 'Watching Number',
            'timeout' => 'Timeout',
            'pili_room_name' => 'Pili Room Name',
            'machine_code' => 'Machine Code',
            'doll_ID' => 'Doll  ID',
            'machine_type' => 'Machine Type',
        ];
    }
}
