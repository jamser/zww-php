<?php
$this->title = '机器在线 概览';
backend\assets\DatePickerAsset::register($this);
use dosamigos\datepicker\DatePicker;
?>
<script src="js/jquery-3.1.1.min.js"></script>
<div class="container-fluid">
    <div id="pad-wrapper" class="users-list">
        <div class="form mb20">
            <form action="<?= yii\helpers\Url::to(['/doll/device/device'])?>" method="get" class="form-inline">
                    <div class="form-group">
                        <label class="sr-only" for="machine_name">机器名</label>
                        <input type="text" class="form-control" id="machine_name" placeholder="机器名" name="machine_name"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('machine_name'))?>">
                    </div>
                    <div class="form-group">
                        <select class="form-control" id="machine_state" name="machine_state">
                            <option value="状态">机器状态</option>
                            <option value="ONLINE" <?='ONLINE'==Yii::$app->getRequest()->get('machine_state')?'selected':''?>>在线</option>
                            <option value="OFFLINE" <?='OFFLINE'==Yii::$app->getRequest()->get('machine_state')?'selected':''?>>离线</option>
                            <option value="UNACTIVE" <?='UNACTIVE'==Yii::$app->getRequest()->get('machine_state')?'selected':''?>>未激活</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" id="rtmp_status" name="rtmp_status">
                            <option value="全部">推流状态</option>
                            <option value="直播中" <?='直播中'==Yii::$app->getRequest()->get('rtmp_status')?'selected':''?>>直播中</option>
                            <option value="无输入流" <?='无输入流'==Yii::$app->getRequest()->get('rtmp_status')?'selected':''?>>无输入流</option>
                        </select>
                    </div>
<!--                    <input type="hidden" id="status" name="status"/>-->
<!--                    <input type="hidden" id="sortType" name="sortType" value="DESC"/>-->
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
                        <span class="line">机器名</span>
                    </th>
                    <th class="span3 sortable">
                        <span class="line">硬件在线状态</span>
<!--                        <span class="line" id="code" onclick="machineStatus()" style="color: dodgerblue">在线状态</span>-->
                    </th>
                    <th class="span3 sortable">
                        <span class="line">房间在线状态</span>
<!--                        <span class="line" id="code" onclick="machineStatus()" style="color: dodgerblue">在线状态</span>-->
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line">推流状态</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($models as $model=>$v) {
                    /* @var $model \common\models\doll\MachineStatistic */
                    ?>
                    <tr class="first">
                        <td>
                            <?php echo $v['machine_name'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $state = $v['machine_state'];
                            if($state == 'OFFLINE'){
                                echo "<span style='color:red '>离线</span>";
                            }elseif($state == 'UNACTIVE'){
                                echo "<span style='color:#ffd100 '>未激活</span>";
                            }else{
                                echo "<span>在线</span>";
                            }
                            ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $state = $v['machine_state'];
                            if($state == 'OFFLINE'){
                                echo "<span style='color:red '>离线</span>";
                            }elseif($state == 'UNACTIVE'){
                                echo "<span style='color:#ffd100 '>未激活</span>";
                            }else{
                                echo "<span>在线</span>";
                            }
                            ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $state = $v['rtmp_state'];;
                            if($state == '开启'){
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
        <?=
        \yii\widgets\LinkPager::widget([
            'pagination' => $pages,
            'nextPageLabel' => '下一页',
            'prevPageLabel' => '上一页',
            'firstPageLabel' => '首页',
            'lastPageLabel' => '尾页',
        ])
        ?>

        <!-- end users table -->
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