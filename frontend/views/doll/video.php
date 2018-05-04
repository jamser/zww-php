<!DOCTYPE HTML>
<html><head><title>抓娃娃</title>
    <meta charset="utf-8">
<body>
<div class="container">
    <img id="image">
    <form>
        <input type="hidden" class="form-control idField" value="5c194e8fa3874df3b1ebb349ce09a5a2">
        <input type="hidden" class="form-control channelField"  value="wanyiguo">
        <input type="hidden" class="form-check-input forceCheck" value="checked">
        <button type="button" class="btn btn-success record_btn">进入游戏</button>
        <button type="button" class="btn btn-info connect_btn" disabled="disabled">开始游戏</button>
    </form>
</div>
<script src="shengwang/js/respond.min.js"></script>
<script src="shengwang/js/vendor-bundle.js?v=1.14.0"></script>
<script src="shengwang/js/index.js?v=1.14.0"></script>

<button class="floor" id="top">上</button></p>
<p><button class="floor" id="left">左</button></p>
<p><button class="floor" id="right">右</button></p>
<p><button class="floor" id="forword">下</button></p>
<p><button class="floor" id="stop">停止</button></p>
<p><button class="floor" id="sub">确定</button></p>
<p><button class="floor" id="coin">投币</button>
<div class="txt"></div>

<script>

    /**
     * 投币**/
    $(document).on("click","#coin",function(){
        $.ajax({
            url:"?r=test/coin",
            type:"post",
            async:false,
            success:function(msg){
            }
        })
    });

    /****
     * 向上移动
     * */
    $(document).on("click","#top",function(){
        $.ajax({
            url:'?r=test/forward',
            type:'post',
            async:false,
            success:function(msg){
            }
        })
    });
    /***
     * 向下移动
     * */
    $(document).on("click","#forword",function(){
        $.ajax({
            url:'?r=test/backward',
            type:'post',
            success:function(msg){
            }
        })
    });
    /***
     * 向左移动
     * */
    $(document).on("click","#left",function(){
        $.ajax({
            url:'?r=test/left',
            type:'post',
            success:function(msg){
            }
        })
    });
    /***
     * 向右移动
     * */
    $(document).on("click","#right",function(){
        $.ajax({
            url:'?r=test/right',
            type:'get',
            success:function(msg){
            }
        })
    });
    /***
     * 停止
     * */
    $(document).on("click","#stop",function(){
        $.ajax({
            url:'?r=test/stop',
            type:'get',
            success:function(msg){
            }
        })
    });
    /****
     * 抓取
     * */
    $(document).on("click","#sub",function(){
        $.ajax({
            url:'?r=test/claw',
            type:'get',
            success:function(msg){
            }
        })
    });

</script>
</body></html>