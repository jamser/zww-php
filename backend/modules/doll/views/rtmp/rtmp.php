<?php
$this->title = '娃娃机推流 概览';
backend\assets\DatePickerAsset::register($this);
?>
<div class="container-fluid">
    <div id="pad-wrapper" class="users-list">
        <div class="form mb20">
            <form action="<?= yii\helpers\Url::to(['/doll/rtmp/rtmp-index'])?>" method="get" class="form-inline">
                <div class="mb10">
                    <div class="form-group">
                        <label class="sr-only" for="machine_code">机器编号</label>
                        <input type="text" class="form-control" id="machine_code" placeholder="机器编码" name="machine_code"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('machine_code'))?>">
                    </div>
                    <div class="form-group">
                        <select class="form-control" id="rtmp_status" name="rtmp_status">
                            <option value="全部">推流状态</option>
                            <option value="直播中" <?='直播中'==Yii::$app->getRequest()->get('rtmp_status')?'selected':''?>>直播中</option>
                            <option value="无输入流" <?='无输入流'==Yii::$app->getRequest()->get('rtmp_status')?'selected':''?>>无输入流</option>
                        </select>
                    </div>
                    <button type="submit" id="submit" class="btn btn-primary btn-success">搜索</button>
                </div>
            </form>
        </div>
        <!-- Users table -->
        <div class="row-fluid table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th class="span3 sortable align-right">
                        <span class="line">日期</span>
                    </th>
                    <th class="span3 sortable">
                        <span class="line">机器ID</span>
                    </th>
                    <th class="span3 sortable">
                        <span class="line">机器编号</span>
                    </th>
                    <th class="span3 sortable">
                        <span class="line">机器状态</span>
                    </th>
                    <th class="span3 sortable">
                        <span class="line">机器</span>
                    </th>
                    <th class="span2 sortable">
                        <span class="line">娃娃名称</span>
                    </th>
                    <th class="span2 sortable">
                        <span class="line">推流状态</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data as $k=>$v) {
                    /* @var $model \common\models\doll\MachineStatistic */
                    ?>
                    <tr class="first">
                        <td>
                            <?php echo $v['create_date'] ?>
                        </td>
                        <td>
                            <?php echo $v['machine_id'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['machine_code'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['machine_status'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['machine_url'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['name'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $status = $v['rtmp_status'];
                            if($status == '开启'){
                            echo "<span style='color:green '>直播中</span>";
                            } else{
                            echo "<span style='color:red '>无输入流</span>";
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <?= yii\widgets\LinkPager::widget([
            'pagination' => $pages,
        ]) ?>

        <!-- end users table -->
    </div>
</div>