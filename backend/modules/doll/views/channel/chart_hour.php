<?php
$this->title = '注册人数趋势图';
use miloschuman\highcharts\Highcharts;
use dosamigos\datepicker\DatePicker;
?>

<div class="form mb20">
    <form action="<?= yii\helpers\Url::to(['/doll/channel/chart-hour'])?>" method="get" class="form-inline">
        <div class="mb10">
            <div class="form-group">
                <?= DatePicker::widget([
                    'name' => 'date',
                    'attribute' => 'date',
                    'options' => ['placeholder' => '日期'],
                    'template' => '{addon}{input}',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ]);?>
            </div>
            <div class="form-group">
                <select class="form-control" id="channel" name="channel">
<!--                    <option value="渠道">渠道</option>-->
                    <option value="vivo" <?='vivo'==Yii::$app->getRequest()->get('channel')?'selected':''?>>vivo</option>
                    <option value="xiaomi" <?='xiaomi'==Yii::$app->getRequest()->get('channel')?'selected':''?>>xiaomi</option>
                    <option value="huawei" <?='huawei'==Yii::$app->getRequest()->get('channel')?'selected':''?>>huawei</option>
                    <option value="AnZhiLeague" <?='AnZhiLeague'==Yii::$app->getRequest()->get('channel')?'selected':''?>>AnZhiLeague</option>
                    <option value="baidu" <?='baidu'==Yii::$app->getRequest()->get('channel')?'selected':''?>>baidu</option>
                    <option value="baiduse1" <?='baiduse1'==Yii::$app->getRequest()->get('channel')?'selected':''?>>baiduse1</option>
                    <option value="baiduse2" <?='baiduse2'==Yii::$app->getRequest()->get('channel')?'selected':''?>>baiduse2</option>
                    <option value="baiduse3" <?='baiduse3'==Yii::$app->getRequest()->get('channel')?'selected':''?>>baiduse3</option>
                    <option value="baiduse4" <?='baiduse4'==Yii::$app->getRequest()->get('channel')?'selected':''?>>baiduse4</option>
                    <option value="baiduse5" <?='baiduse5'==Yii::$app->getRequest()->get('channel')?'selected':''?>>baiduse5</option>
                    <option value="huanliang1" <?='huanliang1'==Yii::$app->getRequest()->get('channel')?'selected':''?>>huanliang1</option>
                    <option value="lenovo" <?='lenovo'==Yii::$app->getRequest()->get('channel')?'selected':''?>>lenovo</option>
                    <option value="meizu" <?='meizu'==Yii::$app->getRequest()->get('channel')?'selected':''?>>meizu</option>
                    <option value="oppo" <?='oppo'==Yii::$app->getRequest()->get('channel')?'selected':''?>>oppo</option>
                    <option value="qihoo" <?='qihoo'==Yii::$app->getRequest()->get('channel')?'selected':''?>>qihoo</option>
                    <option value="QQGroup001" <?='QQGroup001'==Yii::$app->getRequest()->get('channel')?'selected':''?>>QQGroup001</option>
                    <option value="QulinGaoXiao" <?='QulinGaoXiao'==Yii::$app->getRequest()->get('channel')?'selected':''?>>QulinGaoXiao</option>
                    <option value="Smartisan" <?='Smartisan'==Yii::$app->getRequest()->get('channel')?'selected':''?>>Smartisan</option>
                    <option value="SouGou" <?='SouGou'==Yii::$app->getRequest()->get('channel')?'selected':''?>>SouGou</option>
                    <option value="Tencent" <?='Tencent'==Yii::$app->getRequest()->get('channel')?'selected':''?>>Tencent</option>
                    <option value="TencentZone" <?='TencentZone'==Yii::$app->getRequest()->get('channel')?'selected':''?>>TencentZone</option>
                    <option value="WanDoujia" <?='WanDoujia'==Yii::$app->getRequest()->get('channel')?'selected':''?>>WanDoujia</option>
                    <option value="WangHongZhiBo" <?='WangHongZhiBo'==Yii::$app->getRequest()->get('channel')?'selected':''?>>WangHongZhiBo</option>
                    <option value="WanYiGuo" <?='WanYiGuo'==Yii::$app->getRequest()->get('channel')?'selected':''?>>WanYiGuo</option>
                    <option value="WeChat" <?='WeChat'==Yii::$app->getRequest()->get('channel')?'selected':''?>>WeChat</option>
                </select>
            </div>
            <button type="submit" id="submit" class="btn btn-primary btn-success">查看</button>
        </div>
    </form>
</div>
<div class='col-lg-12'>
    <div class="row">
        <?php
        echo Highcharts::widget([
            'scripts' => [
                'modules/exporting',
                'themes/grid-light',
            ],
            'options' => [
                'title' => ['text' => '一天内注册人数趋势'],
                'credits' => [
                    'text' => 'p.365zhuawawa.com',
                    'href' => 'http://p.365zhuawawa.com',
                ],
                'xAxis' => [
                    'categories' => $hours,
                ],
                'yAxis' => [
                    'title' => ['text' => '注册人数'],
                    'tickInterval' => 100,
                ],
                'series' => [
                    ['name' => $channel, 'data' => $counts_h],
                ]
            ]
        ]);
        ?>

    </div>
</div>