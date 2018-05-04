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

        #pand{
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
<script src="shengwang/js/vendor-bundle.js?v=1.0"></script>
<script src="shengwang/js/index.js?v=1.0"></script>
<div class="txt_loading">
    <a class="send" href="#" ><img src="./resource/img/Grab_Video_Send.png" width="40%" /></a>
    <?php
    $dollId = $machineData['id'];
    echo "<input type='hidden' value='$dollId' id='dollId'>";
    echo "<input type='hidden' value='1' id='userId'>";
    if($machineData['machine_status'] != "空闲中"){
        echo "<img src='./resource/img/Grab_Button_PlayGray.png' />";
    }else{
        echo "<a id='pand'><img src='./resource/img/Grab_Button_Play.png' /></a>";
    }
    ?>
    <a class="pay" href="#" ><img src="./resource/img/Grab_Button_Pay.png" width="40%" /></a>
    <br/>
    <img src="./resource/img/Grab_DisplayImage.png" class="img-responsive" style="width: 550px;height:50px;float: left">
    <div class="txt_pand" style="display: none;">
        <p style="margin-right:5px"><img src="./resource/img/Grab_Video_Up.png" width="10%" id="top" class="floor" /></p>
        <p><a><img src="./resource/img/Grab_Video_Left.png" width="10%" id="left" class="floor" /></a> <a><img src="./resource/img/Grab_Video_Right.png" width="10%" id="right" class="floor" /></a></p>
        <p><img src="./resource/img/Grab_Video_Down.png" width="10%" id="forword" class="floor" /></p>
<!--        <p><button class="floor" id="stop">停止</button></p>-->
        <p><img src="./resource/img/Grab_Video_Grab.png" width="10%" class="floor" id="sub" /></p>
<!--        <p><button class="floor" id="coin">投币</button></p>-->
    </div>
</div>
<script src="js/jquery-3.1.1.min.js"></script>
<script>
    /****
     * 隐藏
     * */
    $(function(){
        $('#pand').click(function(){
            $(this).hide();
            $(".txt_pand").show();
            $(".send").hide();
            $(".pay").hide();
            $(".img-responsive").hide();
        })
    });
    /**
     * 投币**/
    $(document).on("click","#pand",function(){
        $.ajax({
            url:"?r=test/coin",
            type:"post",
            async:false,
            success:function(msg){
            }
        })
    });

    var startTime;
    var log = function (msg) {
        var div = $('<div></div>');
        div.html((new Date().getTime()) + ': ' + (new Date().getTime() - startTime) + ': ' + msg)
        $('body').append(div);
    };
    var touchEnd = function () {
        $.ajax({
            url:'?r=test/stop',
            type:'get',
            success:function(msg){
            }
        })
    };
    var mouseUp = function () {
        $.ajax({
            url:'?r=test/stop',
            type:'get',
            success:function(msg){
            }
        })
    };
    var d = $('.txt_pand');
    d.bind('mouseup', mouseUp);
    d.bind('touchend', touchEnd);

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
//    dollId=$("#dollId").val();
//    userId=$("#userId").val();
//    $(document).on("click","#sub",function(){
//        $.post("?r=game/consume-game",
//            {
//                dollId:dollId,
//                userId:userId
//            },
//    });
    $(document).on("click","#sub",function(){
        $.ajax({
            url:'?r=ali/receive',
            type:'get',
            success:function(msg){
            }
        })
    });

</script>
</body>
</html>