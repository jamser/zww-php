<?php
$this->title = '游戏日志';
backend\assets\DatePickerAsset::register($this);
use dosamigos\datepicker\DatePicker;
?>

<script src="js/jquery-3.1.1.min.js"></script>
<div class="container-fluid">
    <div id="pad-wrapper" class="users-list">
        <div class="form mb20">
            <form action="<?= yii\helpers\Url::to(['/doll/game/index'])?>" method="get" class="form-inline">
                <div class="form-group">
                    <label class="sr-only" for="log_type">日志类型</label>
                    <input type="text" class="form-control" id="log_type" placeholder="日志类型" name="log_type"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('log_type'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="log_date">日志日期</label>
                    <input type="text" class="form-control" id="log_date" placeholder="日志日期" name="log_date"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('log_date'))?>">
                </div>
                <button type="submit" id="submit" class="btn btn-primary btn-success">搜索</button>
            </form>
        </div>
    </div>

    <!-- Users table -->
    <div class="row-fluid table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th class="span3 sortable align-right">
                    <span class="line">日志id</span>
                </th>
                <th class="span3 sortable align-right">
                    <span class="line">日志日期</span>
                </th>
                <th class="span3 sortable align-right">
                    <span class="line">日志名称</span>
                </th>
                <th class="span3 sortable align-right">
                    <span class="line">用户名称</span>
                </th>
                <th class="span3 sortable align-right">
                    <span class="line">类名</span>
                </th>
                <th class="span3 sortable align-right">
                    <span class="line">方法名</span>
                </th>
                <th class="span3 sortable">
                    <span class="line">日志类型</span>
                </th>
                <th class="span3 sortable">
                    <span class="line">日志内容</span>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data['hits']['hits'] as $k=>$v) {
                /* @var $model \common\models\doll\MachineStatistic */
                ?>
                <tr class="first">
                    <td>
                        <?php echo $v['_id'] ?>
                    </td>
                    <td class="align-right">
                        <?php echo $v['_source']['date'] ?>
                    </td>
                    <td class="align-right">
                        <?php echo $v['_source']['name'] ?>
                    </td>
                    <td class="align-right">
                        <?php echo $v['_source']['user_name'] ?>
                    </td>
                    <td class="align-right">
                        <?php echo $v['_source']['class_name'] ?>
                    </td>
                    <td class="align-right">
                        <?php echo $v['_source']['function_name'] ?>
                    </td>
                    <td class="align-right">
                        <?php echo $v['_source']['log_type'] ?>
                    </td>
                    <td class="align-right">
                        <?php echo $v['_source']['content'] ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?=
    \yii\widgets\LinkPager::widget([
        'pagination' => $pages,
        'nextPageLabel' => '下一页',
        'prevPageLabel' => '上一页',
        'firstPageLabel' => '首页',
        'lastPageLabel' => '尾页',
    ])
    ?>
</div>
<script>
    function machineStatus(){
        <?php
        if(isset($_GET['sortType']) && $_GET['sortType']==='DESC') {
        ?>
        machineStatus1();
        <?php
        } else {
       ?>
        machineStatus2();
        <?php
         }
         ?>
    }
    function machineStatus1(){
        $('#status').val('ASC');
        $('#sortType').val('ASC');
        $('#submit').click();
    }
    function machineStatus2(){
        $('#status').val('DESC');
        $('#submit').click();
    }
</script>