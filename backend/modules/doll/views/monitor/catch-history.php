<?php

use yii\helpers\Html;
use yii\grid\GridView;
backend\assets\DatePickerAsset::register($this);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '娃娃机抓取记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="monitor-index">
    <div class="form mb20">
        <form action="<?= yii\helpers\Url::to(['/doll/monitor/catch-history'])?>" method="get" class="form-inline">
            <div class="mb10">
                <div class="form-group">
                    <label class="sr-only" for="startTime">开始时间</label>
                    <input type="text" class="form-control" id="startTime" name="startTime" placeholder="开始时间"
                           data-date-format="yyyy-mm-dd hh:ii:ss"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('startTime'))?>">
                  </div>
                  <div class="form-group">
                    <label class="sr-only" for="endTime">结束时间</label>
                    <input type="text" class="form-control" id="endTime" name="endTime" placeholder="结束时间"
                           data-date-format="yyyy-mm-dd hh:ii:ss"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('endTime'))?>">
                  </div>
                  <div class="form-group">
                    <label class="sr-only" for="memberCode">用户ID</label>
                    <input type="text" class="form-control" id="memberCode" placeholder="用户ID" name="memberCode"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('memberCode'))?>">
                  </div>
                  <div class="form-group">
                    <label class="sr-only" for="machineId">机器ID</label>
                    <input type="text" class="form-control" id="machineId" placeholder="机器ID" name="machineId"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('machineId'))?>">
                  </div>
                  <div class="form-group">
                    <label class="sr-only" for="machineCode">机器编码</label>
                    <input type="text" class="form-control" id="machineCode" placeholder="机器编码"  name="machineCode" value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('machineCode'))?>">
                  </div>
                  <div class="form-group">
                    <label class="control-label" for="status">抓取状态</label>
                    <select class="form-control" id="status" name="status">
                        <option value="全部">全部</option>
                        <option value="抓取成功" <?='抓取成功'==Yii::$app->getRequest()->get('status')?'selected':''?>>抓取成功</option>
                        <option value="抓取失败" <?='抓取失败'==Yii::$app->getRequest()->get('status')?'selected':''?>>抓取失败</option>
                    </select>
                  </div>
                
                <button type="submit" class="btn btn-primary btn-success">搜索</button>

            </div>
            
    </div>
    <div class="row-fluid table">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="span3 sortable">
                            <span class="line"></span>时间
                        </th>
                        <th class="span3 sortable">
                            <span class="line"></span>机器ID
                        </th>
                        <th class="span3 sortable">
                            <span class="line"></span>机器编码
                        </th>
                        <th class="span2 sortable">
                            <span class="line"></span>机器名称
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>用户ID
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>用户名
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>抓取状态                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row) {
                        ?>
                        <tr class="first">
                            <td>
                                <?php echo $row['catch_date'] ?>
                            </td>
                            <td>
                                <?php echo $row['doll_id'] ?>
                            </td>
                            <td>
                                <?php echo $row['machine_code'] ?>
                            </td>
                            <td>
                                <?php echo $row['dollName'] ?>
                            </td>
                            <td>
                                <?php echo $row['memberID'] ?>
                            </td>
                            <td>
                                <?php echo $row['username'] ?>
                            </td>
                            <td>
                                <?php echo $row['catch_status'] ?>
                            </td>
                            
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?= yii\widgets\LinkPager::widget([
            'pagination' => $pages,
        ]) ?>
</div>

<script>
<?php
$this->beginBlock('pageJs');
?>
$('#startTime').datetimepicker({
    autoclose:true,
    language:'zh-CN',
    format:'yyyy-mm-dd hh:ii:ss'
});
$('#endTime').datetimepicker({
    autoclose:true,
    language:'zh-CN',
    format:'yyyy-mm-dd hh:ii:ss'
});
<?php
$this->endBlock();
$this->registerJs($this->blocks['pageJs']);
?>
</script>