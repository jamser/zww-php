<h2 style="text-align: center;margin-top:60px;margin-bottom: 60px;" class="title-group">用户数/新增用户数</h2>
<h2  style="text-align: center;display: none;margin-top:60px;margin-bottom: 60px;" class="title-group">游戏次数/交易流水</h2>
<script src="<?= yii\helpers\Url::to('/js/Chart.min.js')?>" type="text/javascript"></script>
<script src="<?= yii\helpers\Url::to('/js/jquery-3.1.1.min.js')?>" type="text/javascript"></script>
<div style="height:100%;width:100%;position: relative;" id="chart_usernum_div">
    <div class="group-1 group" style="position:absolute;height:50%;width:50%; top:0;left:0;text-align: center">
        <div style="width:600px;margin:0 auto;">
            <canvas id="chart_usernum" width="200" height="200"></canvas>
        </div>
    </div>
    <div class="group-1 group" style="position:absolute;height:50%;width:50%; top:0;right:0" id="chart_newusernum_div">
        <div style="width:600px;margin:0 auto;">
            <canvas id="chart_newusernum" width="200" height="200"></canvas>
        </div>
    </div>
    
    <div class="group-2 group" style="position:absolute;height:50%;width:50%; top:0;left:0;display:none;" id="chart_playtimes_div">
        <div style="width:600px;margin:0 auto;">
            <canvas id="chart_playtimes" width="200" height="200"></canvas>
        </div>
    </div>
    <div class="group-2 group" style="position:absolute;height:50%;width:50%; top:0;right:0;display:none;" id="chart_amount_div">
        <div style="width:600px;margin:0 auto;">
            <canvas id="chart_amount" width="200" height="200"></canvas>
        </div>
    </div>
</div>

<script>
var ctx1 = document.getElementById("chart_usernum").getContext('2d');
var ctx2 = document.getElementById("chart_newusernum").getContext('2d');
var ctx3 = document.getElementById("chart_playtimes").getContext('2d');
var ctx4 = document.getElementById("chart_amount").getContext('2d');

var chart1 = new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: ["<?= implode('","', $days)?>"],
        datasets: [{
            label: '用户数',
            data: [<?= implode(',', $userNum)?>],
            backgroundColor: [
                <?php 
                foreach($days as $key=>$day):?>
                    '#DE82B3'<?=$key==count($days)-1?'':','?>
                <?php endforeach;?>
            ],
            borderColor: [
                <?php 
                foreach($days as $key=>$day):?>
                    '#CC4089'<?=$key==count($days)-1?'':','?>
                <?php endforeach;?>
            ],
            borderWidth: 1,
            //barPercentage:0.5
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});

var chart2 = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: ["<?= implode('","', $days)?>"],
        datasets: [{
            label: '新增用户',
            data: [<?= implode(',', $newUserNum)?>],
            backgroundColor: [
                <?php 
                foreach($days as $key=>$day):?>
                    '#56BFBB'<?=$key==count($days)-1?'':','?>
                <?php endforeach;?>
            ],
            borderColor: [
                <?php 
                foreach($days as $key=>$day):?>
                    '#56BFBB'<?=$key==count($days)-1?'':','?>
                <?php endforeach;?>
            ],
            borderWidth: 1,
            //barPercentage:0.5
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});

var chart3 = new Chart(ctx3, {
    type: 'bar',
    data: {
        labels: ["<?= implode('","', $days)?>"],
        datasets: [{
            label: '游戏次数',
            data: [<?= implode(',', $playTimes)?>],
            backgroundColor: [
                <?php 
                foreach($days as $key=>$day):?>
                    '#DE82B3'<?=$key==count($days)-1?'':','?>
                <?php endforeach;?>
            ],
            borderColor: [
                <?php 
                foreach($days as $key=>$day):?>
                    '#DE82B3'<?=$key==count($days)-1?'':','?>
                <?php endforeach;?>
            ],
            borderWidth: 1,
            //barPercentage:0.5
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});

var chart4 = new Chart(ctx4, {
    type: 'bar',
    data: {
        labels: ["<?= implode('","', $days)?>"],
        datasets: [{
            label: '充值金额',
            data: [<?= implode(',', $amount)?>],
            backgroundColor: [
                <?php 
                foreach($days as $key=>$day):?>
                    '#56BFBB'<?=$key==count($days)-1?'':','?>
                <?php endforeach;?>
            ],
            borderColor: [
                <?php 
                foreach($days as $key=>$day):?>
                   '#56BFBB'<?=$key==count($days)-1?'':','?>
                <?php endforeach;?>
            ],
            borderWidth: 1,
            //barPercentage:0.5
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
function changeChart() {
    $('.group').fadeToggle();
    $('.title-group').toggle();
    setTimeout(function(){changeChart()},10000);
}

$(function(){
    //setTimeout(function(){changeChart()},10000);
    //setTimeout(function(){window.location.reload();},600000);
})
</script>