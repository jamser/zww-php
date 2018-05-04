<?php 
use yii\helpers\Url;
?>
<h2>推广渠道链接</h2>
<div class="form" style="margin:40px;">
    <div class="row">
        <label class="control-label">渠道名称</label>
        <input type="text" id="channel" class="form-control"/>
    </div>

    <div class="row">
        <button class="btn" id="submit">提交</button>
    </div>
    
    
</div>
<div id="ret"></div>
<script src="js/jquery-3.1.1.min.js"></script>
<script>
    $(function(){
        $('#submit').click(function(){
            var channel = $('#channel').val();
            $.get('<?= Url::to(['get-ck'])?>',
            {channel:channel},
            function(url){
                $('#ret').append('渠道:'+channel+';  推广链接: '+ url+'<br/><br/>');
            });
        });
    })
</script>