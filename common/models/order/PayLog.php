<?php

namespace common\models\order;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pay_log".
 *
 * @property string $id
 * @property string $pay_id
 * @property integer $type
 * @property string $remark
 * @property string $created_at
 */
class PayLog extends \nextrip\helpers\ActiveRecord
{
    const TYPE_PAY_SUCCESS_CALLBACK = 1;//支付成功回调
    const TYPE_PAY_SUCCESS_QUERY = 2;//支付成功查询
    const TYPE_PAY_FAIL_CALLBACK = 3;//支付失败回调
    
    
    const TYPE_LIST = [
        self::TYPE_PAY_SUCCESS_CALLBACK=>'支付成功回调',
        self::TYPE_PAY_FAIL_CALLBACK=>'支付失败回调',
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pay_log';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->db_php;
    }
    
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'=>TimestampBehavior::className(),
                'updatedAtAttribute'=>false
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['remark'],'default', 'value'=>''],
            [['pay_id', 'remark'], 'required'],
            [['pay_id', 'type'], 'integer'],
            [['remark'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pay_id' => '支付ID',
            'type' => '类型',
            'remark' => '备注',
            'created_at' => '创建时间',
        ];
    }
    
    /**
     * 添加记录
     * @param integer $pay_id 支付ID
     * @param inteer $type 类型
     * @param string $remark 备注
     * @return \static
     */
    public static function add($pay_id, $type, $remark) {
        $model = new static([
            'pay_id'=>(int)$pay_id,
            'type'=>(int)$type,
            'remark'=>$remark
        ]);
        $model->save(false);
        return $model;
    }
}
