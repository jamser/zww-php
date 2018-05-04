<?php
namespace backend\models;

use Yii;
use common\models\finance\WithdrawApply;
use common\models\finance\WithdrawApplyLog;

/**
 * 用户提现审核
 */
class UserWithdrawReviewForm extends \yii\base\Model {

    /**
     *
     * @var WithdrawApply
     */
    public $withdraw;
    
    public $type;

    public $remark;

    public function __construct($withdraw) {
        $this->withdraw = $withdraw;
    }
    
    public function rules() {
        return [
            [['type'], 'required'],
            [['type'], 'in', 'range'=>['pass','rejected']],
            [['remark'],'string', 'max'=>1024, 'encoding'=>'utf-8'],
            [['remark'],'required', 'when'=>function($model){
                return $model->type==='rejected';
            }]
        ];
    }

    public function attributeLabels() {
        return [
            'type'=>'类型',
            'remark'=>'备注'
        ];
    }

    public function save() {
        if(!$this->validate()) {
            return false;
        }
        
        if($this->type==='pass') {
            $logType = WithdrawApplyLog::TYPE_REVIEW_PASS;
            $this->withdraw->status = WithdrawApply::STATUS_REVIEW_PASS;
            $event = WithdrawApply::EVENT_AFTER_REVIEW_PASS;
        } else if($this->type==='rejected') {
            $logType = WithdrawApplyLog::TYPE_REJECTED;
            $this->withdraw->status = WithdrawApply::STATUS_REVIEW_REJECTED;
            $event = WithdrawApply::EVENT_AFTER_REVIEW_REJECTED;
        } else {
            throw new \Exception('状态异常');
        }
        $adminId = Yii::$app->user->id;
        $db = WithdrawApply::getDb();
        $transaction = $db->beginTransaction();
        try {
            $this->withdraw->updateAttributes(['status']);
            WithdrawApplyLog::add($this->withdraw->user_id, $this->withdraw->id, $logType, $this->remark, $adminId);
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            $this->addError('type', $ex->getMessage());
            return false;
        }
        $this->withdraw->trigger($event);
        
        return true;
    }
}
