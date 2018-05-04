<!DOCTYPE html>
<html style="font-size:50px">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>游戏房间</title>
    <meta name="description" content="游戏房间" />
    <meta name="viewport" content="target-densitydpi=device-dpi, width=640, user-scalable=no" />
    <link href="./resource/css/game.css?t=9" rel="stylesheet" />
    <style>
        .txt_loading{
            position: absolute;
            top: 100%;
            left: 30%;
            -webkit-transform: translate3d(-50%,-50%,0);
            transform: translate3d(-50%,-50%,0);
            white-space: nowrap;
            color: white;
            font-weight: bold;
            font-size: .48rem;
            text-shadow: 1px 1px;
            width: 300px;
            height: 300px;
        }
        .audience_header{
            position: absolute;
            right: 0;
            top:25px;
            font-size: .70rem;
        }
        .audience_header .num{
            color: #9a642a;
        }
        .audience_header .avatar{
            width:.8rem;
            height: .8rem;
            border-radius:50%;
            margin-left: .05rem;
        }
        .ic_recharge,.main-footer{
            display: none;
        }
        .but{
            background-color: forestgreen;
            border-radius: 25px;
            padding: 20px;
            width: 150%;
            height: 80%;
            margin:0 auto;
            font-size: 120%;
            top:150%;
            left:50%;
            right:50%;
        }
        /*
        隐藏操作按钮
         */
        .floor{
            width: 100px;
            border-radius: 30px;
        }
        #top{
            border-radius: 80%;
            width: 30%;
            height: 30%;
            position: absolute;
            left:30%;
            top:50%;
            font-size: 120%;
        }
        #left{
            border-radius: 80%;
            width: 30%;
            height: 30%;
            position: absolute;
            right:80%;
            top:70%;
            font-size: 120%;
        }
        #right{
            border-radius: 80%;
            width: 30%;
            height: 30%;
            position: absolute;
            left:70%;
            top:70%;
            font-size: 120%;
        }
        #forword{
            border-radius: 80%;
            width: 30%;
            height: 30%
        border-radius: 100%;
            position: absolute;
            left:30%;
            top:90%;
            font-size: 120%;
        }
        #sub{
            width: 50%;
            height: 50%
        border-radius: 80%;
            position: absolute;
            left:130%;
            top:60%;
            font-size: 120%;
        }
        #coin{
            position: absolute;
            left:100%;
            top:110%;
            width: 30%;
            height: 30%;
            font-size: 80%;
            background-color: dodgerblue;
            color: white;
        }
        #stop{
            position: absolute;
            left:60%;
            top:110%;
            width: 30%;
            height: 30%;
            font-size: 80%;
            background-color: dodgerblue;
            color: white;
        }

        #game_start{
            border-radius: 25px;
            padding: 20px;
            width: 50%;
            height: 10%;
            margin:0 auto;
            font-size: 50%;
            left:50%;
            right:50%;
        }
        .boxs{
            background:#b2e2fa ;
        }
        .header{
            background:pink;
            height: 10%;
        }
    </style>
</head>
<body rlt="1" class="boxs">
<header class="header">
    <span class="ic_back im cnp" onclick="tryBack()"></span>
    <span class="ic_recharge im cnp" onclick="location.href='/ally/user/toUserRecharge'"></span>
    <div class="audience_header df aic">
        <a href="#" onClick="javascript :history.back(-1);"><img src="./resource/img/Common_Nav_Return.png" style="width: 60px;height: 60px;text-align: left;margin-right:270px"></a>
        <span class="num"><?php echo $machineData['watching_number']?></span>
        <div class="avatar cnp" style="background-image:url(http://passport.xiaohulu.com/avatar/paomianfan/bbfa074b2b3938ac32c4d22d901401f9)"></div>
        <div class="avatar cnp" style="background-image:url(http://passport.xiaohulu.com/avatar/paomianfan/21cd35b41c0cec144783f679019ce4cf)"></div>
        <div class="avatar cnp" style="background-image:url(http://passport.xiaohulu.com/avatar/paomianfan/cd20f4e7507c09c184cee68da3925068)"></div>
    </div>
</header>
<div class="container" id="desc" align="center">
    <img id="image" width="100%" height="80%" class="col-xs-12 col-sm-12 col-md-12" />
    <form>
        <input type="hidden" class="form-control idField" value="5c194e8fa3874df3b1ebb349ce09a5a2" />
        <input type="hidden" class="form-control channelField" value="wanyiguo" />
        <input type="hidden" class="form-check-input forceCheck" value="checked" />
        <br />
        <button type="button" class="btn btn-success record_btn">进入游戏</button>
        <br />
        <br />
        <button type="button" class="btn btn-info connect_btn" disabled="disabled" id="divs">开始游戏</button>
    </form>
</div>
<?php
$dollId = $machineData['id'];
$key = $machineData['machine_serial_num'];
$device = $machineData['machine_url'];
$queue = $machineData['machine_ip'];
echo "<input type='hidden' value='$dollId' id='dollId'>";
echo "<input type='hidden' value='$userId' id='userId'>";
echo "<input type='hidden' value='$token' id='token'>";
echo "<input type='hidden' value='$key' id='key'>";
echo "<input type='hidden' value='$device' id='device'>";
echo "<input type='hidden' value='$queue' id='queue'>";
?>
<script src="shengwang/js/vendor-bundle.js?v=1.0"></script>
<script src="shengwang/js/index.js?v=1.0"></script>
<div class="txt_loading">
<!--    开始游戏模块-->
    <a class="send" href="#" ><img src="./resource/img/Grab_Video_Send.png" width="40%" /></a>
    <a id='game_loading'><img src='./resource/img/Grab_Button_PlayGray.png' /></a>
    <a id='game_start'><img src='./resource/img/Grab_Button_Play.png' /></a>
    <a class="pay" href="#" ><img src="./resource/img/Grab_Button_Pay.png" width="40%" /></a>
    <br/>
    <img src="./resource/img/Grab_DisplayImage.png" class="img-responsive" style="width: 550px;height:50px;float: left">

    <!--    操作按钮模块-->
    <div class="txt_pand" style="display: none;">
        <p style="margin-right:5px"><img src="./resource/img/Grab_Video_Up.png" width="10%" id="top" class="floor" /></p>
        <p><a><img src="./resource/img/Grab_Video_Left.png" width="10%" id="left" class="floor" /></a> <a><img src="./resource/img/Grab_Video_Right.png" width="10%" id="right" class="floor" /></a></p>
        <p><img src="./resource/img/Grab_Video_Down.png" width="10%" id="forword" class="floor" /></p>
        <p><img src="./resource/img/Grab_Video_Grab.png" width="10%" class="floor" id="claw" /></p>
    </div>
</div>
<script src="js/jquery-3.1.1.min.js"></script>
<script>
    //开始本轮游戏建立socket
    dollId=$("#dollId").val();
    memberId=$("#userId").val();
    token=$("#token").val();
    key=$("#key").val();
    device=$("#device").val();
    queue=$("#queue").val();
    $(document).on("click","#game_start",function(){
        url = "http://dev.365zhuawawa.com/icrane/api/game/start";
        $.ajax({
            type: "post",
            dataType: "json",
            async : false,
            url: url,
            data:{
                memberId:memberId,
                dollId:dollId,
                token:token
            },
            success: function (result) {
                var obj = JSON.parse(result);
                if(obj.statusCode == 200){
                    $(this).hide();
                    $(".txt_pand").show();
                    $(".send").hide();
                    $(".pay").hide();
                    $(".img-responsive").hide();
                }else{
                    alert(obj.message);
                }
            },
            error: function() {
                alert("接口请求错误");
            }
        });
    });

    //收到claw的消息后，扣费操作，生成消费记录
    $(document).on("click","#claw",function(){
        url = "http://dev.365zhuawawa.com/icrane/api/game/claw";
        $.ajax({
            type: "post",
            dataType: "json",
            async : false,
            url: url,
            data:{
                memberId:memberId,
                dollId:dollId,
                token:token
            },
            success: function (result) {
                var obj = JSON.parse(result);
                if(obj.statusCode == 200){
                    alert("扣费成功");
                }else{
                    alert("扣费失败");
                }
            },
            error: function() {
                alert("接口请求失败");
            }
        });
    });

    //查询房间状态和在线人数，自主刷新
    function re_fresh(){
        $(document).ready(function(){
            url = "http://dev.365zhuwawa.com/icrane/api/doll/getDollStatus";
            $.ajax({
                type: "post",
                dataType: "json",
                async : false,
                url: url,
                data:{
                    dollId:dollId,
                    token:token
                },
                success: function (result) {
                    var obj = JSON.parse(result);
                    var data = JSON.parse(obj.resultData);
                    if(data.status == "空闲中"){
                        $(".game_loading").hide();
                        $(".game_start").show();
                    }else{
                        $(".game_loading").show();
                        $(".game_start").hide();
                    }
                },
                error: function() {
                    alert("请求接口失败");
                }
            });
    }
    setTimeout(re_fresh,1000*60*3);

    //接收到套件的接收数据，调用结束游戏接口
    $(document).on("click","#claw",function(){
        url = "http://dev.365zhuawawa.com/icrane/api/game/endRound";
        $.ajax({
            type: "post",
            dataType: "json",
            async : false,
            url: url,
            data:{
                memberId:memberId,
                dollId:dollId,
                token:token,
                gotDoll:gotDoll
            },
            success: function (result) {
                var obj = JSON.parse(result);
                if(obj.statusCode==200){
                    alert("操作成功");
                }else{
                    alert("操作失败");
                }
            },
            error: function() {
                alert("请求失败");
            }
        });
    });

    //websocket操作机器
    $(document).on("click","#top",function(){
        $.ajax({
            url:"ws://dev.365zhuwawa.com/icrane/api/webSocket/{"memberId"}/{"dollId"}/{"key"}/{"device"}/{"queue"}/{"token"}",
            type:"post",
            async:false,
            success:function(msg){
            }
        })
    });
    $(document).on("click","#left",function(){
        $.ajax({
            url:"ws://dev.365zhuwawa.com/icrane/api/webSocket/{"memberId"}/{"dollId"}/{"key"}/{"device"}/{"queue"}/{"token"}",
            type:"post",
            async:false,
            success:function(msg){
            }
        })
    });
    $(document).on("click","#right",function(){
        $.ajax({
            url:"ws://dev.365zhuwawa.com/icrane/api/webSocket/{"memberId"}/{"dollId"}/{"key"}/{"device"}/{"queue"}/{"token"}",
            type:"post",
            async:false,
            success:function(msg){
            }
        })
    });
    $(document).on("click","#forword",function(){
        $.ajax({
            url:"ws://dev.365zhuwawa.com/icrane/api/webSocket/{"memberId"}/{"dollId"}/{"key"}/{"device"}/{"queue"}/{"token"}",
            type:"post",
            async:false,
            success:function(msg){
            }
        })
    });
    $(document).on("click","#claw",function(){
        $.ajax({
            url:"http://dev.365zhuawawa.com/icrane/api/game/claw",
            type:"post",
            async:false,
            success:function(msg){
            }
        })
    });
</script>
</body>
</html>