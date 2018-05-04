<?php

namespace common\models\call;

use Yii;

/**
 * This is the model class for table "prodcall_caller_apply_review".
 *
 * @property string $id
 * @property string $user_id
 * @property integer $review_admin_id
 * @property integer $pass
 * @property string $remark
 * @property string $created_at
 * @property string $updated_at
 */
class CallerApplyReview extends \nextrip\helpers\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'prodcall_caller_apply_review';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->db_php;
    }
    
    public function behaviors() {
        return [
            \yii\behaviors\TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pass'], 'required'],
            [['pass'], 'in', 'range'=>[Caller::STATUS_REVIEW_REJECTED,Caller::STATUS_REVIEW_PASS]],
            [['remark'], 'string', 'max' => 255],
            [['remark'], 'checkRemark', 'skipOnEmpty'=>false],
        ];
    }
    
    public function checkRemark($attribute, $params) {
        
        if( ($this->pass==Caller::STATUS_REVIEW_REJECTED) && !$this->remark) {
            $this->addError($attribute, '审核拒绝需要填写备注');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'review_admin_id' => '审核管理员ID',
            'pass' => '是否通过',
            'remark' => '备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
