<?php
$this->title = '渠道支付数据';
backend\assets\DatePickerAsset::register($this);
use dosamigos\datepicker\DatePicker;
?>
<div class="container-fluid">
    <div id="pad-wrapper" class="statistic-list">
        <div class="form mb20">
            <form action="<?= yii\helpers\Url::to(['/doll/pay/channel-daily'])?>" method="get" class="form-inline">
                <div class="mb10">
                    <div class="form-group">
                        <?= DatePicker::widget([
                            'name' => 'day',
                            'attribute' => 'day',
                            'options' => ['placeholder' => '日期'],
                            'template' => '{addon}{input}',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd'
                            ]
                        ]);?>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="channel">渠道</label>
                        <select class="form-control" id="channel" name="channel">
                            <option value="渠道">渠道</option>
                            <option value="ios" <?='appstore'==Yii::$app->getRequest()->get('channel')?'selected':''?>>appstore</option>
                            <option value="vivo" <?='vivo'==Yii::$app->getRequest()->get('channel')?'selected':''?>>vivo</option>
                            <option value="oppo" <?='oppo'==Yii::$app->getRequest()->get('channel')?'selected':''?>>oppo</option>
                            <option value="huawei" <?='华为'==Yii::$app->getRequest()->get('channel')?'selected':''?>>华为</option>
                            <option value="baidu" <?='baidu'==Yii::$app->getRequest()->get('channel')?'selected':''?>>baidu</option>
                            <option value="xiaomi" <?='xiaomi'==Yii::$app->getRequest()->get('channel')?'selected':''?>>xiaomi</option>
                            <option value="HaoYunXing" <?='HaoYunXing'==Yii::$app->getRequest()->get('channel')?'selected':''?>>HaoYunXing</option>
                            <option value="QiHoo" <?='QiHoo'==Yii::$app->getRequest()->get('channel')?'selected':''?>>QiHoo</option>
                            <option value="SouGou" <?='SouGou'==Yii::$app->getRequest()->get('channel')?'selected':''?>>SouGou</option>
                            <option value="AnZhiLeague" <?='AnZhiLeague'==Yii::$app->getRequest()->get('channel')?'selected':''?>>AnZhiLeague</option>
                            <option value="Smartisan" <?='Smartisan'==Yii::$app->getRequest()->get('channel')?'selected':''?>>Smartisan</option>
                            <option value="Wandoujia" <?='Wandoujia'==Yii::$app->getRequest()->get('channel')?'selected':''?>>Wandoujia</option>
                            <option value="Tencent" <?='Tencent'==Yii::$app->getRequest()->get('channel')?'selected':''?>>Tencent</option>
                            <option value="Lenovo" <?='Lenovo'==Yii::$app->getRequest()->get('channel')?'selected':''?>>Lenovo</option>
                            <option value="QQGroup001" <?='QQGroup001'==Yii::$app->getRequest()->get('channel')?'selected':''?>>QQGroup001</option>
                        </select>
                    </div>
                    <input type="hidden" id="register_num" name="register_num" />
                    <input type="hidden" id="charge_num" name="charge_num"/>
                    <input type="hidden" id="charge_rate" name="charge_rate"/>
                    <input type="hidden" id="charge_amoun" name="charge_amoun"/>
                    <input type="hidden" id="charge_one" name="charge_one"/>
                    <input type="hidden" id="register_one" name="register_one"/>
                    <input type="hidden" id="sortType" name="sortType" value="DESC"/>
                    <button type="submit" id="submit" class="btn btn-primary btn-success">搜索</button>
                </div>
            </form>
        </div>

        <div class="head" style="width: auto;height: 20px;background-color:#dcdcdc ;font-size: 16px;color: #696969">
            充值人数占比=当日充值人数/当日总充值人数    &nbsp&nbsp&nbsp&nbsp   首充占比=首次充值用户数/当日总充值人数   &nbsp&nbsp&nbsp&nbsp   新用户人均充值=新用户充值人数/新用户注册数
        </div>
        <br/>

        <!-- Users table -->
        <div class="row-fluid table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th class="span3 sortable">
                        <span class="line"></span>日期
                    </th>
                    <th class="span3 sortable">
                        <span class="line"></span>渠道
                    </th>
                    <th class="span2 sortable">
                        <span class="line" id="register_num" onclick="registerNum()" style="color: dodgerblue">注册人数</span>
<!--                        <li style="display: none;font-size: smaller" id="register1" onclick="registerNum1()">升序排列</li>-->
<!--                        <li style="display: none;font-size: smaller" id="register2" onclick="registerNum2()">降序排列</li>-->
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line" id="charge_num" onclick="chargeNum()" style="color: dodgerblue">充值人数</span>
<!--                        <li style="display: none;font-size: smaller" id="charge1" onclick="chargeNum1()">升序排列</li>-->
<!--                        <li style="display: none;font-size: smaller" id="charge2" onclick="chargeNum2()">降序排列</li>-->
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line" id="charge_num" onclick="chargeRate()" style="color: dodgerblue">充值人数占比</span>
<!--                        <li style="display: none;font-size: smaller" id="chargeRate1" onclick="chargeRate1()">升序排列</li>-->
<!--                        <li style="display: none;font-size: smaller" id="chargeRate2" onclick="chargeRate2()">降序排列</li>-->
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line" id="charge_amount" onclick="chargeAmount()" style="color: dodgerblue">充值金额</span>
<!--                        <li style="display: none;font-size: smaller" id="chargeAmount1" onclick="chargeAmount1()">升序排列</li>-->
<!--                        <li style="display: none;font-size: smaller" id="chargeAmount2" onclick="chargeAmount2()">降序排列</li>-->
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line" id="charge_one" onclick="chargeOne()" style="color: dodgerblue">注册人均充值金额</span>
<!--                        <li style="display: none;font-size: smaller" id="chargeOne1" onclick="chargeOne1()">升序排列</li>-->
<!--                        <li style="display: none;font-size: smaller" id="chargeOne2" onclick="chargeOne2()">降序排列</li>-->
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line" id="register_one" onclick="registerOne()" style="color: dodgerblue">充值用户人均充值</span>
<!--                        <li style="display: none;font-size: smaller" id="registerOne1" onclick="registerOne1()">升序排列</li>-->
<!--                        <li style="display: none;font-size: smaller" id="registerOne2" onclick="registerOne2()">降序排列</li>-->
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($models as $model=>$v) {
                    /* @var $model \common\models\doll\Statistic */
                    ?>
                    <tr class="first">
                        <td>
                            <?php echo $v['day'] ?>
                        </td>
                        <td>
                            <?php echo $v['channel'] ?>
                        </td>
                        <td>
                            <?php echo $v['registration_num'] ?>
                        </td>
                        <td>
                            <?php echo $v['charge_user_num'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['charge_user_num']>0 ? round(($v['charge_user_num']/$v['registration_num'])*100,2):0;
                            echo $rate;
                            ?>%
                        </td>
                        <td class="align-right">
                            <?php echo $v['charge_amount'] ?>
                        </td>
                        <td>
                            <?php echo $v['registration_user_avg_amount'] ?>
                        </td>
                        <td>
                            <?php echo $v['charge_user_avg_amount'] ?>
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
        <?php
        $this->beginBlock('pageJs');
        ?>
        $('#day').datetimepicker({
            autoclose:true,
            language:'zh-CN',
            format:'yyyy-mm-dd'
        });

        <?php
        $this->endBlock();
        $this->registerJs($this->blocks['pageJs']);
        ?>

        function registerNum(){
            <?php
             if(isset($_GET['sortType']) && $_GET['sortType']==='DESC') {
             ?>
            registerNum1();
             <?php
             } else {
            ?>
            registerNum2();
            <?php
             }
             ?>
//            document.getElementById("register1").style.display='block';
//            document.getElementById("register2").style.display='block';
        }
        function registerNum1(){
            $('#register_num').val('ASC');
            $('#sortType').val('ASC');
            $('#submit').click();
        }
        function registerNum2(){
            $('#register_num').val('DESC');
            $('#submit').click();
        }

        function chargeNum(){
            <?php
             if(isset($_GET['sortType']) && $_GET['sortType']==='DESC') {
             ?>
            chargeNum1();
            <?php
            } else {
           ?>
            chargeNum2();
            <?php
             }
             ?>
//            document.getElementById("charge1").style.display='block';
//            document.getElementById("charge2").style.display='block';
        }
        function chargeNum1(){
            $('#charge_num').val('ASC');
            $('#sortType').val('ASC');
            $('#submit').click();
        }
        function chargeNum2(){
            $('#charge_num').val('DESC');
            $('#submit').click();
        }

        function chargeRate(){
            <?php
             if(isset($_GET['sortType']) && $_GET['sortType']==='DESC') {
             ?>
            chargeRate1();
            <?php
            } else {
           ?>
            chargeRate2();
            <?php
             }
             ?>
//            document.getElementById("chargeRate1").style.display='block';
//            document.getElementById("chargeRate2").style.display='block';
        }
        function chargeRate1(){
            $('#charge_rate').val('ASC');
            $('#sortType').val('ASC');
            $('#submit').click();
        }
        function chargeRate2(){
            $('#charge_rate').val('DESC');
            $('#submit').click();
        }

        function chargeAmount(){
            <?php
            if(isset($_GET['sortType']) && $_GET['sortType']==='DESC') {
            ?>
            chargeAmount1();
            <?php
            } else {
           ?>
            chargeAmount2();
            <?php
             }
             ?>
//            document.getElementById("chargeAmount1").style.display='block';
//            document.getElementById("chargeAmount2").style.display='block';
        }
        function chargeAmount1(){
            $('#charge_amoun').val('ASC');
            $('#sortType').val('ASC');
            $('#submit').click();
        }
        function chargeAmount2(){
            $('#charge_amoun').val('DESC');
            $('#submit').click();
        }

        function chargeOne(){
            <?php
           if(isset($_GET['sortType']) && $_GET['sortType']==='DESC') {
           ?>
            chargeOne1();
            <?php
            } else {
           ?>
            chargeOne2();
            <?php
             }
             ?>
//            document.getElementById("chargeOne1").style.display='block';
//            document.getElementById("chargeOne2").style.display='block';
        }
        function chargeOne1(){
            $('#charge_one').val('ASC');
            $('#sortType').val('ASC');
            $('#submit').click();
        }
        function chargeOne2(){
            $('#charge_one').val('DESC');
            $('#submit').click();
        }

        function registerOne(){
            <?php
          if(isset($_GET['sortType']) && $_GET['sortType']==='DESC') {
          ?>
            registerOne1();
            <?php
            } else {
           ?>
            registerOne2();
            <?php
             }
             ?>
//            document.getElementById("registerOne1").style.display='block';
//            document.getElementById("registerOne2").style.display='block';
        }
        function registerOne1(){
            $('#register_one').val('ASC');
            $('#sortType').val('ASC');
            $('#submit').click();
        }
        function registerOne2(){
            $('#register_one').val('DESC');
            $('#submit').click();
        }

    </script>
</div>