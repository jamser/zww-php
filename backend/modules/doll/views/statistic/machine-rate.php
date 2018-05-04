<?php
$this->title = '数据统计 概览';
backend\assets\DatePickerAsset::register($this);
use dosamigos\datepicker\DatePicker;
?>
<script src="js/jquery-3.1.1.min.js"></script>
<div class="container-fluid">
    <div id="pad-wrapper" class="users-list">
        <div class="page-header">
            <a href="<?= \yii\helpers\Url::to(['/doll/statistic/machine-rate','refresh'=>1])?>" >刷新今天数据</a>
        </div>

        <div class="form mb20">
            <form action="<?= yii\helpers\Url::to(['/doll/statistic/machine-rate'])?>" method="get" class="form-inline">
                <div class="mb10">
                    <div class="form-group" style="width: 140px">
                        <?= DatePicker::widget([
                            'name' => 'start_time',
                            'attribute' => 'start_time',
                            'options' => ['placeholder' => '日期'],
                            'template' => '{addon}{input}',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd'
                            ]
                        ]);?>
                    </div>
                    <div class="form-group">
                        <label class="sr-only" for="machine_id">机器ID</label>
                        <input type="text" style="width: 110px" class="form-control" id="machine_id" placeholder="机器ID" name="machine_id"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('machine_id'))?>">
                    </div>
                    <div class="form-group">
                        <label class="sr-only" for="machine_code">机器编号</label>
                        <input type="text" style="width: 110px" class="form-control" id="machine_code" placeholder="机器编码" name="machine_code"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('machine_code'))?>">
                    </div>
                    <div class="form-group">
                        <label class="sr-only" for="name">娃娃名称</label>
                        <input type="text" style="width: 110px" class="form-control" id="name" placeholder="娃娃名称" name="name"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('name'))?>">
                    </div>
                    <div class="form-group">
                        <label class="sr-only" for="machine_device_name">机器名称</label>
                        <input type="text" style="width: 110px" class="form-control" id="machine_device_name" placeholder="机器名称" name="machine_device_name"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('machine_device_name'))?>">
                    </div>
                    <div class="form-group">
                        <select class="form-control" id="machine_type" name="machine_type">
                            <option value="种类">房间类型</option>
                            <option value="普通" <?='普通'==Yii::$app->getRequest()->get('machine_type')?'selected':''?>>普通房间</option>
                            <option value="1" <?='1'==Yii::$app->getRequest()->get('machine_type')?'selected':''?>>练习房</option>
                            <option value="2" <?='2'==Yii::$app->getRequest()->get('machine_type')?'selected':''?>>钻石房</option>
                            <option value="3" <?='3'==Yii::$app->getRequest()->get('machine_type')?'selected':''?>>占卜房</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" id="machine_state" name="machine_state">
                            <option value="状态">机器状态</option>
                            <option value="在线" <?='在线'==Yii::$app->getRequest()->get('machine_state')?'selected':''?>>在线</option>
                            <option value="维修中" <?='维修中'==Yii::$app->getRequest()->get('machine_state')?'selected':''?>>维修中</option>
                            <option value="未上线" <?='未上线'==Yii::$app->getRequest()->get('machine_state')?'selected':''?>>未上线</option>
                        </select>
                    </div>
                    <?php
                    $s_time = date('Y-m-d',strtotime('-7 days'));
                    $e_time = date('Y-m-d',time());
                    echo "<input type='hidden' id='s_time' name='s_time' value=$s_time />";
                    echo "<input type='hidden' id='e_time' name='e_time' value=$e_time />";
                    ?>
                    <input type="hidden" id="play_num" name="play_num" />
                    <input type="hidden" id="grab_count" name="grab_count"/>
                    <input type="hidden" id="play_count" name="play_count"/>
                    <input type="hidden" id="machine_name" name="machine_name"/>
                    <input type="hidden" id="code" name="code"/>
                    <input type="hidden" id="status" name="status"/>
                    <input type="hidden" id="price" name="price"/>
                    <input type="hidden" id="sortType" name="sortType" value="DESC"/>
                    <button type="submit" id="submit" class="btn btn-primary btn-success">搜索</button>
                    <button type="button" class="btn btn-primary btn-success" onclick="week()">查看上周机器数据</button>
                </div>
<!--                <div class="mb10">-->
<!--                    <input type="checkbox" value="ASC" name="s" checked="checked">升序  <input type="checkbox" value="DESC" name="s">降序-->
<!--                </div>-->
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
                            <span class="line" id="code" onclick="machineCode()" style="color: dodgerblue">机器编号</span>
                        </th>
                        <th class="span3 sortable">
                            <span class="line">机器名称</span>
                        </th>
                        <th class="span2 sortable">
                            <span class="line" id="status" onclick="machineStatus()" style="color: dodgerblue">机器状态</span>
                        </th>
                        <th class="span2 sortable">
                            <span class="line">硬件状态</span>
                        </th>
                        <th class="span2 sortable">
                            <span class="line">视频状态</span>
                        </th>
                        <th class="span2 sortable">
                            <span class="line" id="machine_name" onclick="machineName()" style="color: dodgerblue">娃娃名称</span>
                        </th>
                        <th class="span2 sortable">
                            <span class="line" id="price" onclick="machinePrice()" style="color: dodgerblue">价格</span>
                        </th>
                        <th class="span2 sortable">
                            <span class="line">娃娃编号</span>
                        </th>
                        <th class="span2 sortable">
                            <span class="line">房间类型</span>
                        </th>
                        <th class="span3 sortable align-right" id="play">
                            <span class="line" id="play_num" onclick="playNum()" style="color: dodgerblue">游戏次数</span>
<!--                            <li style="display: none;font-size: smaller" id="play1" onclick="playNum1()">升序排列</li>-->
<!--                            <li style="display: none;font-size: smaller" id="play2" onclick="playNum2()">降序排列</li>-->
                        </th>
                        <th class="span3 sortable align-right" id ="catch">
                            <span class="line" id="catch_num" onclick="splitNum()" style="color: dodgerblue">抓中次数</span>
<!--                            <li style="display: none;font-size: smaller" id="num1" onclick="splitNum1()">升序排列</li>-->
<!--                            <li style="display: none;font-size: smaller" id="num2" onclick="splitNum2()">降序排列</li>-->
                        </th>
                        <th class="span3 sortable align-right" id="rate">
                            <span class="line" id="catch_rate" onclick="splitRate()" style="color: dodgerblue">抓中概率</span>
<!--                            <li style="display: none;font-size: smaller" id="rate1" onclick="splitRate1()">升序排列</li>-->
<!--                            <li style="display: none;font-size: smaller" id="rate2" onclick="splitRate2()">降序排列</li>-->
                        </th>
                        <th class="span2 sortable">
                            <span class="line">未充值抓中概率</span>
                        </th>
                        <th class="span2 sortable">
                            <span class="line">充值100以下概率</span>
                        </th>
                        <th class="span2 sortable">
                            <span class="line">100以上概率</span>
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line">操作</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($models as $model=>$v) {
     /* @var $model \common\models\doll\MachineStatistic */
                        ?>
                        <tr class="first">
                            <td>
                                <?php echo date('Y-m-d', $v['start_time']) ?>
                            </td>
                            <td>
                                <?php echo $v['machine_id'] ?>
                            </td>
                            <td class="align-right">
                                <?php echo $v['machine_code'] ?>
                            </td>
                            <td class="align-right">
                                <?php echo $v['machine_device_name'] ?>
                            </td>
                            <td class="align-right">
                                <?php
                                $state = isset($status[$v['machine_id']]) ? $status[$v['machine_id']] : '';
                                if($state == '未上线' || $state == '维修中'){
                                    echo "<span style='color:red '>$state</span>";
                                }else{
                                    echo "<span>$state</span>";
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
                            <td class="align-right">
                                <?php echo $v['machine_doll_name'] ?>
                            </td>
                            <td class="align-right">
                                <?php echo isset($prices[$v['machine_id']]) ? $prices[$v['machine_id']] : ''  ?>
                            </td>
                            <td class="align-right">
                                <?php echo $v['machine_doll_code'] ?>
                            </td>
                            <td class="align-right">
                                <?php
                                $type= $v['machine_type'];
                                if($type == 0){
                                    echo '普通房';
                                }elseif($type == 1){
                                    echo '练习房';
                                }elseif($type == 2){
                                    echo '钻石房';
                                }else{
                                    echo '占卜房';
                                }
                                ?>
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
                            <td class="align-right">
                                <?php
                                $rate = $v['no_playCount']>0 ? round(($v['no_grabCount']/$v['no_playCount'])*100,2):0;
                                if($rate < 1){
                                    echo "<span style='color:red '>$rate</span>";
                                }elseif($rate>2.5){
                                    echo "<span style='color:red '>$rate</span>";
                                }else{
                                    echo "<span>$rate</span>";
                                }
                                //                                echo $model->play_count>0 ? round(($model->grab_count/$model->play_count)*100,2):0
                                ?>%
                            </td>
                            <td class="align-right">
                                <?php
                                $rate = $v['s_playCount']>0 ? round(($v['s_grabCount']/$v['s_playCount'])*100,2):0;
                                if($rate < 1.5){
                                    echo "<span style='color:red '>$rate</span>";
                                }elseif($rate>3.3){
                                    echo "<span style='color:red '>$rate</span>";
                                }else{
                                    echo "<span>$rate</span>";
                                }
                                //                                echo $model->play_count>0 ? round(($model->grab_count/$model->play_count)*100,2):0
                                ?>%
                            </td>
                            <td class="align-right">
                                <?php
                                $rate = $v['l_playCount']>0 ? round(($v['l_grabCount']/$v['l_playCount'])*100,2):0;
                                if($rate < 2){
                                    echo "<span style='color:red '>$rate</span>";
                                }elseif($rate>5){
                                    echo "<span style='color:red '>$rate</span>";
                                }else{
                                    echo "<span>$rate</span>";
                                }
                                //                                echo $model->play_count>0 ? round(($model->grab_count/$model->play_count)*100,2):0
                                ?>%
                            </td>
                            <td class="align-right">
                                <?php
                                $id = $v['machine_id'];
                                $url = "http://admin.365zhuawawa.com/zwwAdmin/tDoll/tDoll_update/".$id;
                                echo "<a href=$url class='btn btn-primary btn-success' target='_blank'>修改</a>";
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
        <?php
        $this->beginBlock('pageJs');
        ?>
        $('#start_time').datetimepicker({
            autoclose:true,
            language:'zh-CN',
            format:'yyyy-mm-dd'
        });

        $('#end_time').datetimepicker({
            autoclose:true,
            language:'zh-CN',
            format:'yyyy-mm-dd'
        });

//        $('#play_num').click(){
//            $('$a_p').style.display="inline"
//            $('$d_p').style.display="inline"
//        }

        <?php
        $this->endBlock();
        $this->registerJs($this->blocks['pageJs']);
        ?>
//        var num_n=0;
//        function splitNum(){
//            num_n = num_n+1;
//            console.log(num_n);
//            if(num_p%2 == 0){
//                $('#play_num').val('ASC');
//                $('#submit').click();
//            }else{
//                $('#play_num').val('DESC');
//                $('#submit').click();
//            }
//        }
//
//        var num_r=0;
//        function splitRate(){
//            num_r = num_r+1;
//            if(num_p%2 == 0){
//                $('#grab_count').val('ASC');
//                $('#submit').click();
//            }else{
//                $('#grab_count').val('DESC');
//                $('#submit').click();
//            }
//        }

//        var num_p=0;
//        function playNum(){
//            num_p = num_p+1;
//            if(num_p%2 == 0){
//                $('#play_count').val('ASC');
//                $('#submit').click();
//            }else{
//                $('#play_count').val('DESC');
//                $('#submit').click();
//            }
//        }

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

        function machineName(){
            <?php
           if(isset($_GET['sortType']) && $_GET['sortType']==='DESC') {
           ?>
            machineName1();
            <?php
            } else {
           ?>
            machineName2();
            <?php
             }
             ?>
        }
        function machineName1(){
            $('#machine_name').val('ASC');
            $('#sortType').val('ASC');
            $('#submit').click();
        }
        function machineName2(){
            $('#machine_name').val('DESC');
            $('#submit').click();
        }

        function machineCode(){
            <?php
           if(isset($_GET['sortType']) && $_GET['sortType']==='DESC') {
           ?>
            machineCode1();
            <?php
            } else {
           ?>
            machineCode2();
            <?php
             }
             ?>
        }
        function machineCode1(){
            $('#code').val('ASC');
            $('#sortType').val('ASC');
            $('#submit').click();
        }
        function machineCode2(){
            $('#code').val('DESC');
            $('#submit').click();
        }

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

        function machinePrice(){
            <?php
           if(isset($_GET['sortType']) && $_GET['sortType']==='DESC') {
           ?>
            machinePrice1();
            <?php
            } else {
           ?>
            machinePrice2();
            <?php
             }
             ?>
        }
        function machinePrice1(){
            $('#price').val('ASC');
            $('#sortType').val('ASC');
            $('#submit').click();
        }
        function machinePrice2(){
            $('#price').val('DESC');
            $('#submit').click();
        }


        function week(){
        var start_time = $("#s_time").val();
        var end_time = $("#e_time").val();
        var url = "/doll/statistic/week-rate?start_time="+start_time+"&end_time="+end_time;
        window.location.href = url;
        }
    </script>
</div>