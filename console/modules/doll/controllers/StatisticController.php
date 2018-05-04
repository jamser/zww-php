<?php

namespace console\modules\doll\controllers;
use common\services\doll\StatisticService;

/**
 * Statistic controller for the `doll` module
 */
class StatisticController extends \yii\console\Controller
{
    /**
     * 运行统计
     * @param string $day 日期 默认为昨天
     * @param integer $insert 是否插入 默认为1
     * @return string
     */
    public function actionOverview($day=null, $insert = 1)
    {
        $service = new \common\services\doll\StatisticService();
        $data = $service->run($day, $insert);
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * 机器抓中比例统计
     */
    public function actionMachineRate($day=null) {
        if($day===null) {
            $day = date('Y-m-d H:i:s', strtotime('yesterday'));
        }
        $startTime = strtotime($day);
        $endTime = $startTime + 86400 -1;
        $service = new \common\services\doll\StatisticService();
        $data = $service->machineRate($startTime, $endTime, 1, \common\enums\StatisticTypeEnum::TYPE_DAY);
        return 0;
    }
    
    /**
     * 支付日报统计
     * @param string $day 日期 默认为昨天
     * @return string
     */
    public function actionPayDaily($day=null)
    {
        if(!$day || ($day==='yesterday')) {
            $day = date('Y-m-d', time() - 86400);
        } else if($day==='today') {
            $day = date('Y-m-d');
        }
        $service = new \common\services\doll\StatisticService();
        $service->payDaily($day);
        echo "run ok\n";
        return 0;
    }
    
    /**
     * 支付次数日报统计
     * @param string $day 日期 默认为昨天
     * @return string
     */
    public function actionPayCountDaily($day=null)
    {
        if(!$day || ($day==='yesterday')) {
            $day = date('Y-m-d', time() - 86400*4);
        } else if($day==='today') {
            $day = date('Y-m-d', time() - 86400*3);
        }
        $service = new \common\services\doll\StatisticService();
        $service->payCountDaily($day);
        echo "run ok\n";
        return 0;
    }
    
    /**
     * 渠道日报统计
     * @param string $day 日期 默认为昨天
     * @return string
     */
    public function actionChannelDaily($day=null)
    {
        if(!$day || ($day==='yesterday')) {
            $day = date('Y-m-d', time() - 86400);
        } else if($day==='today') {
            $day = date('Y-m-d');
        }
        $service = new \common\services\doll\StatisticService();
        $service->channelDaily($day);
        echo "run ok\n";
        return 0;
    }

    //注册，充值，发货数据统计
    public function actionRecord(){
        $service = new StatisticService();
        $service->record();
    }

    //金币 钻石 统计
    public function actionCoins(){
        $service = new StatisticService();
        $service->coins();
    }
}
