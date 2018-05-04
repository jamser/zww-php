<?php
namespace console\modules\doll\controllers;

use Yii;

class RtmpController extends \yii\console\Controller{
    public function actionIndex(){
        $service = new \common\services\doll\RtmpService();
        $service->machineRtmp();
    }

    //礼包
    public function actionCharge(){
        $service = new \common\services\doll\ChargeService();
        $service->charge();
    }
}