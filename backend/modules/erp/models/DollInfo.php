<?php

namespace backend\modules\erp\models;

use Yii;

/**
 * This is the model class for table "doll_info".
 *
 * @property integer $id
 * @property string $dollName
 * @property integer $dollTotal
 * @property string $img_url
 * @property string $addTime
 * @property string $dollCode
 */
class DollInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'doll_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dollName','dollCode'],'unique','message'=>'{attribute}已经被占用了'],
            [['dollName','dollTotal','dollCode'],'required'],
            [['dollTotal',], 'integer'],
            [['addTime'], 'safe'],
            [['dollName', 'dollCode','agency','size','type','note','dollCoins','deliverCoins'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dollName' => '娃娃名称',
            'dollTotal' => '娃娃总量',
            'img_url' => '娃娃图片',
            'addTime' => '上架时间',
            'dollCode' => '娃娃编码',
            'agency' => '发货地',
            'size' => '尺寸',
            'type' => '材质',
            'note' => '备注',
            'dollCoins' => '娃娃成本',
            'deliverCoins' => '快递费',
            'redeemCoins' => '返币数',
        ];
    }
}
