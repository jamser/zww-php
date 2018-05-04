<?php
$this->title = '数据统计 概览';
backend\assets\DatePickerAsset::register($this);
?>
<script src="js/jquery-3.1.1.min.js"></script>
<div class="container-fluid">
    <div id="pad-wrapper" class="users-list">
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
                    <th class="span2 sortable">
                        <span class="line">娃娃名称</span>
                    </th>
                    <th class="span2 sortable">
                        <span class="line">娃娃编号</span>
                    </th>
                    <th class="span3 sortable align-right" id="play">
                        <span class="line">游戏次数</span>
<!--                        <span class="line" id="play_num" onclick="playNum()" style="color: dodgerblue">游戏次数</span>-->
                    </th>
                    <th class="span3 sortable align-right" id ="catch">
                        <span class="line">抓中次数</span>
<!--                        <span class="line" id="catch_num" onclick="splitNum()" style="color: dodgerblue">抓中次数</span>-->
                    </th>
                    <th class="span3 sortable align-right" id="rate">
                        <span class="line">抓中概率</span>
<!--                        <span class="line" id="catch_rate" onclick="splitRate()" style="color: dodgerblue">抓中概率</span>-->
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($models as $model=>$v) {
                    /* @var $model \common\models\doll\MachineStatistic */
                    ?>
                    <tr class="first">
                        <td>
                            <?php echo $startTime.'至'.$endTime ?>
                        </td>
                        <td>
                            <?php echo $v['machine_id'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['machine_code'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['machine_doll_name'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['machine_doll_code'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['play_count'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['grab_count'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['play_count']>0 ? round(($v['grab_count']/$v['play_count'])*100,2):0;
                            if($rate == 0){
                                echo "<span style='color:red '>$rate</span>";
                            }elseif($rate>0&&$rate<2){
                                echo "<span style='color:#ffd100 '>$rate</span>";
                            }elseif($rate>5){
                                echo "<span style='color:red '>$rate</span>";
                            }else{
                                echo "<span>$rate</span>";
                            }
                            //                                echo $model->play_count>0 ? round(($model->grab_count/$model->play_count)*100,2):0
                            ?>%
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
        <?php
        $this->beginBlock('pageJs');
        ?>
        $('#start_time').datetimepicker({
            autoclose:true,
            language:'zh-CN',
            format:'yyyy-mm-dd'
        });

        <?php
        $this->endBlock();
        $this->registerJs($this->blocks['pageJs']);
        ?>
        function playNum(){
            <?php
            if(isset($_GET['sortType']) && $_GET['sortType']==='DESC') {
            ?>
            playNum1();
            <?php
            } else {
           ?>
            playNum2();
            <?php
             }
             ?>
//            document.getElementById("play1").style.display='block';
//            document.getElementById("play2").style.display='block';
        }
        function playNum1(){
            $('#play_count').val('ASC');
            $('#sortType').val('ASC');
            $('#submit').click();
        }
        function playNum2(){
            $('#play_count').val('DESC');
            $('#submit').click();
        }

        function splitNum(){
            <?php
            if(isset($_GET['sortType']) && $_GET['sortType']==='DESC') {
            ?>
            splitNum1();
            <?php
            } else {
           ?>
            splitNum2();
            <?php
             }
             ?>
//            document.getElementById("num1").style.display='block';
//            document.getElementById("num2").style.display='block';
        }
        function splitNum1(){
            $('#play_num').val('ASC');
            $('#sortType').val('ASC');
            $('#submit').click();
        }
        function splitNum2(){
            $('#play_num').val('DESC');
            $('#submit').click();
        }

        function splitRate(){
            <?php
           if(isset($_GET['sortType']) && $_GET['sortType']==='DESC') {
           ?>
            splitRate1();
            <?php
            } else {
           ?>
            splitRate2();
            <?php
             }
             ?>
//            document.getElementById("rate1").style.display='block';
//            document.getElementById("rate2").style.display='block';
        }
        function splitRate1(){
            $('#grab_count').val('ASC');
            $('#sortType').val('ASC');
            $('#submit').click();
        }
        function splitRate2(){
            $('#grab_count').val('DESC');
            $('#submit').click();
        }
    </script>
</div>