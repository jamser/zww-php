<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '娃娃机监控数据';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="monitor-index">

    <div class="form mb20">
        <form action="<?= yii\helpers\Url::to(['/doll/monitor/index'])?>" method="get" class="form-inline">
            <div class="mb10">
                <div class="form-group">
                    <label class="sr-only" for="machine_id">机器ID</label>
                    <input type="text" class="form-control" id="machine_id" placeholder="机器ID" name="machine_id"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('machine_id'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="machine_code">机器编号</label>
                    <input type="text" class="form-control" id="machine_code" placeholder="机器编码" name="machine_code"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('machine_code'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="machine_name">机器名称</label>
                    <input type="text" class="form-control" id="machine_name" placeholder="机器名称" name="machine_name"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('machine_name'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="alert_type">报警类型</label>
                    <input type="text" class="form-control" id="alert_type" placeholder="报警类型" name="alert_type"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('alert_type'))?>">
                </div>
                <button type="submit" id="submit" class="btn btn-primary btn-success">搜索</button>
            </div>
        </form>
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
                            <span class="line"></span>机器编号
                        </th>
                        <th class="span2 sortable">
                            <span class="line"></span>机器名称
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>报警类型
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>描述
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row) {
                        ?>
                        <tr class="first">
                            <td>
                                <?php echo $row['created_date'] ?>
                            </td>
                            <td>
                                <?php echo $row['dollId'] ?>
                            </td>
                            <td>
                                <?php echo $row['machine_code'] ?>
                            </td>
                            <td>
                                <?php echo $row['name'] ?>
                            </td>
                            <td>
                                <?php echo $row['alert_type'] ?>
                            </td>
                            <td>
                                <?php echo $row['description'] ?>
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
