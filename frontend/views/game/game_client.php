<html style="font-size:50px">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>游戏房间</title>
    <meta name="description" content="游戏房间" />
    <meta name="viewport" content="target-densitydpi=device-dpi, width=640, user-scalable=no" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
    <link href="./resource/css/game.css?t=9" rel="stylesheet" />
    <style>
        .txt_loading{
            position: fixed;
            top: 75%;
            /*left: 40%;*/
            -webkit-transform: translate3d(-50%,-50%,0);
            transform: translate3d(-50%,-50%,0);
            white-space: nowrap;
            color: white;
            font-weight: bold;
            font-size: .48rem;
            text-shadow: 1px 1px;
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
        #forward{
            border-radius: 80%;
            width: 40px;
            height: 40px;
            position: absolute;
            margin: 7px 400px 100px 90px;
            /*left:10%;*/
            /*top:10%;*/
            font-size: 120%;
            background:0 0 url('./resource/img/Grab_Button_Up.png?v=1') no-repeat;
            display:block;
        }
        #left{
            border-radius: 80%;
            width: 40px;
            height: 40px;
            position: absolute;
            margin:50px 300px 50px 25px;
            /*right:90%;*/
            /*top:70%;*/
            font-size: 120%;
            background:0 0 url('./resource/img/Grab_Button_Left.png?v=1') no-repeat;
            display:block;
        }
        #right{
            border-radius: 80%;
            width: 40px;
            height: 40px;
            position: absolute;
            margin:50px 100px 50px 155px;
            /*right:60%;*/
            /*top:70%;*/
            font-size: 120%;
            background:0 0 url('./resource/img/Grab_Button_Right.png?v=1') no-repeat;
            display:block;
        }
        #backward{
            border-radius: 80%;
            width: 40px;
            height: 40px;
            border-radius: 100%;
            position: absolute;
            margin:90px 200px 7px 90px;
            /*left:30%;*/
            /*top:90%;*/
            font-size: 120%;
            background:0 0 url('./resource/img/Grab_Button_Down.png?v=1') no-repeat;
            display:block;
        }
        #claw{
            width: 100px;
            height: 100px;
            border-radius: 80%;
            position: absolute;
            margin:20px 5px 5px 250px;
            /*left:80%;*/
            /*top:60%;*/
            font-size: 120%;
            background: 0 0 url('./resource/img/Grab_Button_Grab.png?v=1') no-repeat;
            display:block;
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

        #pay{
            border-radius: 25px;
            padding: 20px;
            /*width: 50%;*/
            /*height: 10%;*/
            position: absolute;
            margin:0px 500px 100px 100px;
            font-size: 50%;
            /*left:50%;*/
            /*right:50%;*/
        }

        #no_pay{
            border-radius: 25px;
            padding: 20px;
            /*width: 50%;*/
            /*height: 10%;*/
            position: absolute;
            margin:0px 500px 100px 100px;
            font-size: 50%;
            /*left:50%;*/
            /*right:50%;*/
        }
        .boxs{
            background:#b2e2fa ;
        }
        .header{
            background:pink;
            height: 10%;
        }
        .jsmpeg{
            width:100%;
            height:100%;
        }
        .return{
            position: fixed;
            width: 40px;
            height: 40px;
            margin-left: 10px;
            margin-top: 10px;
            z-index: 1;
        }
        canvas{
            width: 100%;
            height: 100%;
        }
        
        *{
            -webkit-touch-callout:none;  /*系统默认菜单被禁用*/
            -webkit-user-select:none; /*webkit浏览器*/
            -khtml-user-select:none; /*早期浏览器*/
            -moz-user-select:none;/*火狐*/
            -ms-user-select:none; /*IE10*/
            user-select:none;
        }
        
        input {
            -webkit-user-select:auto; /*webkit浏览器*/
       }

        .confirm_ul{list-style:none;margin:0px;padding:0px;width:90%;height:70%;margin: auto;margin-top: 40%;}
        .confirm_title{background:#F2F2F2;text-align:left;padding-left:20px;line-height:60px;border:1px solid #999;}
        .confirm_content{background:#fff;text-align:center;height:80px;line-height:80px;}
        .confirm_btn-wrap{background:#fff;height:50px;line-height:18px;text-align: right;}
        .confirm_btn{cursor:pointer;color:#2bd00f;margin-right: 35px;}
        .confirm_btn-wrap > a:nth-child(1){color: #9c9898;}

    </style>
</head>
<body rlt="1" class="boxs" onload="video()">
<div class="return">
    <a href="javascript:history.go(-1)"><img src="./resource/img/Common_Nav_Return.png" style="width: 40px;height: 40px;text-align: left;margin-right:400px"></a>
</div>
<!--<header class="header">-->
<!--    <span class="ic_back im cnp" onclick="tryBack()"></span>-->
<!--    <span class="ic_recharge im cnp" onclick="location.href='/ally/user/toUserRecharge'"></span>-->
<!--    <div class="audience_header df aic">-->
<!--        <a href="#" onClick="javascript :history.back(-1);"><img src="./resource/img/Common_Nav_Return.png" style="width: 60px;height: 60px;text-align: left;margin-right:400px"></a>-->
<!--        <span class="num">--><?php //echo $machineData['watching_number']?><!--</span>-->
<!--        <div class="avatar cnp" style="background-image:url(http://passport.xiaohulu.com/avatar/paomianfan/bbfa074b2b3938ac32c4d22d901401f9)"></div>-->
<!--        <div class="avatar cnp" style="background-image:url(http://passport.xiaohulu.com/avatar/paomianfan/21cd35b41c0cec144783f679019ce4cf)"></div>-->
<!--        <div class="avatar cnp" style="background-image:url(http://passport.xiaohulu.com/avatar/paomianfan/cd20f4e7507c09c184cee68da3925068)"></div>-->
<!--    </div>-->
<!--</header>-->
<div class="jsmpeg" data-url="wss://h5gw-wujiang-ctel-1.agoraio.cn:8100/?camera=main&channel=<?php echo $swData['sw_channel']?>&appid=<?php echo $swData['sw_appid']?>" ></div>

<div class="txt_loading">
<!--    <a class="send" href="#" ><img src="./resource/img/Grab_Video_Send.png" width="40%" /></a>-->
    <?php
    echo "<input type='hidden' value='$roomId' id='dollId'>";
    echo "<input type='hidden' value='$userId' id='userId'>";
    if($machineData['machine_status'] != "空闲中"){
        echo "<a id='no_pay'><img src='./resource/img/Grab_Button_PlayGray.png' style='width: 130px;height: 130px;' /></a>";
    }else{
        echo "<a id='pay'><img src='./resource/img/Grab_Button_Play.png' style='width: 130px;height: 130px;'/></a>";
    }
    ?>
    <div class="txt_pand" style="display: none;">
        <button class="floor btn-control" id="forward"></button>
        <button class="floor btn-control" id="left"></button>
        <button class="floor btn-control" id="right"></button>
        <button class="floor btn-control" id="backward"></button>
        <button class="floor btn-control" id="claw"></button>
    </div>
</div>
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/socket.io.js"></script>
<script src="js/jsmpeg.min.js"></script>
<!--<script src="js/showBo.js"></script>-->
<!--<link type="text/css" rel="stylesheet" href="css/showBo.css" />-->
<!--<script src="js/weui.mini.js"></script>-->
<script src="js/zepto.min.js"></script>
<link type="text/css" rel="stylesheet" href="css/weui.min.css">
<link type="text/css" rel="stylesheet" href="css/weui.css">
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="https://res.wx.qq.com/open/libs/weuijs/1.0.0/weui.min.js"></script>

<script>

    
    var actionSocket,msgSocket,inGame,gameNo,controlKey,startTime;

    $(function(){
//        $(".boxs").addEventListener('contextmenu', function(e){
//            e.preventDefault();
//        });
        //$('.btn-control').bind('contextmenu', function(e) {
        //    e.preventDefault();
        //});
        
        //发信息
        $('#forward').off('touchstart').on('touchstart', function() {
            console.log('forward down');
            if(actionSocket) {
                actionSocket.emit('control', {
                    machineId: '<?=$machineId?>',
                    machineCode:'<?=$machineCode?>',
                    userId:'<?=$userId?>',
                    startTime: startTime,
                    key:controlKey,
                    action:'forward'
                });
            }
        }).off('touchend').on('touchend', function() {
            console.log('forward mouseup');
            if(actionSocket) {
                actionSocket.emit('control', {
                    machineId: '<?=$machineId?>',
                    machineCode:'<?=$machineCode?>',
                    userId:'<?=$userId?>',
                    startTime: startTime,
                    key:controlKey,
                    action:'stop'
                });
            }
        });

        $('#left').off('touchstart').on('touchstart', function() {
            console.log('left mousedown');
            if(actionSocket) {
                actionSocket.emit('control', {
                    machineId: '<?=$machineId?>',
                    machineCode:'<?=$machineCode?>',
                    userId:'<?=$userId?>',
                    startTime: startTime,
                    key:controlKey,
                    action:'left'
                });
            }
        }).off('touchend').on('touchend', function() {
            console.log('left mouseup');
            if(actionSocket) {
                actionSocket.emit('control', {
                    machineId: '<?=$machineId?>',
                    machineCode:'<?=$machineCode?>',
                    userId:'<?=$userId?>',
                    startTime: startTime,
                    key:controlKey,
                    action:'stop'
                });
            }
        });

        $('#right').off('touchstart').on('touchstart', function() {
            console.log('right touchstart');
            if(actionSocket) {
                actionSocket.emit('control', {
                    machineId: '<?=$machineId?>',
                    machineCode:'<?=$machineCode?>',
                    userId:'<?=$userId?>',
                    startTime: startTime,
                    key:controlKey,
                    action:'right'
                });
            }
        }).off('touchend').on('touchend', function() {
            console.log('right touchend');
            if(actionSocket) {
                actionSocket.emit('control', {
                    machineId: '<?=$machineId?>',
                    machineCode:'<?=$machineCode?>',
                    userId:'<?=$userId?>',
                    startTime: startTime,
                    key:controlKey,
                    action:'stop'
                });
            }
        });

        $('#backward').off('touchstart').on('touchstart', function(e) {
            console.log('backward mousedown');
            if(actionSocket) {
                actionSocket.emit('control', {
                    machineId: '<?=$machineId?>',
                    machineCode:'<?=$machineCode?>',
                    userId:'<?=$userId?>',
                    startTime: startTime,
                    key:controlKey,
                    action:'backward'
                });
            }
        }).off('touchend').on('touchend', function() {
            console.log('backward mouseup');
            if(actionSocket) {
                actionSocket.emit('control', {
                    machineId: '<?=$machineId?>',
                    machineCode:'<?=$machineCode?>',
                    userId:'<?=$userId?>',
                    startTime: startTime,
                    key:controlKey,
                    action:'stop'});
            }
        });

        $('#claw').off('touchstart').on('touchstart', function() {
            console.log('backward mousedown');
            if(actionSocket) {
                actionSocket.emit('control', {
                    machineId: '<?=$machineId?>',
                    machineCode:'<?=$machineCode?>',
                    userId:'<?=$userId?>',
                    startTime: startTime,
                    key:controlKey,
                    action:'claw'});
            }
        });
        $('#pay').click(function(){
            $.get("?r=game/coin",
                {
                    userId:'<?=$userId?>',
                    roomId:'<?=$roomId?>'
                },
                function(data){
                    var obj = JSON.parse(data);
                    console.log(obj.code);
                    if(obj.code == 200){
//                        $(this).hide();

                        // 如果服务端不在本机，请把127.0.0.1改成服务端ip
                        actionSocket = io('ws://dev.365zhuawawa.com:2345');
                        // 当连接服务端成功时触发connect默认事件
                        actionSocket.on('connect', function(){
                            actionSocket.emit('coin', {
                                machineId: '<?=$machineId?>',
                                machineCode:'<?=$machineCode?>',
                                userId:'<?=$userId?>',
                                key:'<?=$key?>'
                            });
                            console.log('action connect success');
                        });

                        actionSocket.on('disconnect', function(){
                            console.log('断开链接');
                        });

                        actionSocket.on('feedback',function(data) {
                            console.log('feedback:'+data);
                            var msg = JSON.parse(data);
                            if(msg && msg.s && msg.s==='idle') {
                                endGame(false);
                            } else if(msg && msg.s && msg.s==='gotToy') {
                                endGame(true);
                            }
                        });

                        actionSocket.on('noMsg',function(data) {
                            console.log('noMsg'+data);
                        });

                        actionSocket.on('end',function(data) {
                            inGame = false;
                            console.log('end : '+ (data.msg ? data.msg : '') );
                            if(data.show && data.msg) {
                                weui.alert(data.msg);
                            }
                        });

                        actionSocket.on('time',function(data){
                            console.log('time'+data);
                        });

                        actionSocket.on('ready', function(data) {
                            $("#pay").hide();
                            $(".txt_pand").show();
                            $(".send").hide();
                            $(".pand").hide();
                            $(".img-responsive").hide();
//                            updateStatus('游戏中');
                            inGame=true;
                            console.log('machine ready');
                            startTime = data.startTime;
                            controlKey = data.controlKey;

//                            setTimeout(function(){
//                                endGame(false);
//                            },40000);

//                            setTimeout(function(){
//                                updateStatus('空闲中');
//                            },40000);

                            listenMsg(actionSocket);

                        });

                    }else{
                        Confirm('您好，您的金币不足，请下载官方app');
                    }
                });

            function listenMsg(socket) {
                if(inGame) {
                    console.log('listenMsg');
                    socket.emit('listenMsg', {
                        machineId: '<?=$machineId?>',
                        machineCode:'<?=$machineCode?>',
                        userId:'<?=$userId?>',
                        startTime: startTime,
                        key:controlKey
                    });
                    setTimeout(function(){listenMsg(socket);},2000);
                }
            }

            function endGame(success) {
                if(inGame){
                    inGame = false;
                    $(".txt_pand").hide();
                    $(".send").show();
                    $(".pand").show();
                    $(".img-responsive").show();

                    weui.alert(success ? '恭喜您， 抓中啦！' :  '很抱歉， 您没有抓中..');
                    $("#pay").show();
//                    updateStatus('空闲中');
                    h
                    actionSocket.emit('del',{
                        machineId:'<?=$machineId?>'
                    });
                    if(actionSocket) {
                        actionSocket.disconnect();
                        actionSocket = null;
                    }
                }
            }

            function updateStatus(status){
                $.get("?r=game/status",
                    {
                        dollId:'<?=$machineId?>',
                        status:status
                    },
                    function(data){
                        console.log(data);
                    });
            }



            /**
            actionWs =  new WebSocket("ws://101.132.166.121:2345");
            actionWs.onopen = function(){
                actionWs.send('devicequeue001');
            }
            actionWs.send();
            */
        })
    });

    function video(){
        $.ajax({
            url: "https://h5gw-wujiang-ctel-1.agoraio.cn:4000/v1/machine",
            type: "POST",
            headers: {
                "Content-type": "application/json; charset=utf-8"
            },
            data: JSON.stringify({
                "appid": '<?=$swData['sw_appid']?>',
                "channel": '<?=$swData['sw_channel']?>',
                "uid1": '<?=$swData['sw_uid']?>'
            }),
            success:function(msg){
//                console.log(JSON.stringify(msg))
                var data = JSON.stringify(msg);
                var obj = JSON.parse(data);
                var video = obj.cameras;
                var cameras = JSON.stringify(video);
                var srcs = JSON.parse(cameras);
                console.log(srcs.main);
//                console.log(cameras);
//                console.log("数据：" + obj.name );
            }
        });
    }

    function download(){
        $.ajax({
            url:'?r=share/is-ios-android',
            type:'get',
            success:function(msg){
                if(msg==2){
                    window.location.replace("https://itunes.apple.com/cn/app/365%E6%8A%93%E5%A8%83%E5%A8%83/id1314921684?mt=8");
                }else if(msg==1){
                    window.location.replace("http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365");
                }else{
                    weui.alert('没有该类型的手机');
                }
            }
        })
    }

    function Confirm(str,click='function')
    {

        var confirmFram = document.createElement("DIV");
        confirmFram.id="confirmFram";
        confirmFram.style.position = "absolute";
        confirmFram.style.width = "100%";
        confirmFram.style.height = "100%";
        confirmFram.style.top = "0";
        confirmFram.style.textAlign = "center";
        confirmFram.style.lineHeight = "150px";
        confirmFram.style.zIndex = "300";
        confirmFram.style.backgroundColor="rgba(0, 0, 0, 0.58)";
        confirmFram.style.fontSize="12px";
        strHtml = '<ul class="confirm_ul">';
        strHtml += '<li class="confirm_content">'+str+'</li>';
        strHtml += '<li class="confirm_btn-wrap"><a type="button" value="确定" onclick="doFalse()" class="confirm_btn">取消</a><a type="button" value="确定" onclick="doOk()" class="confirm_btn">确定</a></li>';
        strHtml += '</ul>';
        confirmFram.innerHTML = strHtml;
        document.body.appendChild(confirmFram);
        this.doOk = function(){
            download();
        }
        this.doFalse = function(){
            confirmFram.style.display = "none";
            if(typeof click=="function"){
                return false;
            }

        }
    }


    /**
     * 投币**/
     /**
    $(document).on("click","#pay",function(){
        actionWs = new WebSocket("ws://101.132.166.121:2345");
        actionWs.onopen = function(){
            actionWs.send('devicequeue001');
        }
        actionWs.onmessage = function(e){
            console.log("收到队列消息时间："+ e.data);
        }
    });

    $(document).on("click","#pay",function() {
        actionWs = new WebSocket("ws://101.132.166.121:2345");
        actionWs.onopen = function(){
            actionWs.send('coin');
        }
        actionWs.onmessage = function(e){
            console.log("指令消息coin："+ e.data);
        }
    });

    //上
    $(document).on("click","#forward",function(){
        actionWs = new WebSocket("ws://101.132.166.121:2345");
        actionWs.onopen = function(){
            actionWs.send('forward');
        }
        actionWs.onmessage = function(e){
            console.log("指令消息forward："+ e.data);
        }
    });
    //下
    $(document).on("click","#backward",function(){
        actionWs = new WebSocket("ws://101.132.166.121:2345");
        actionWs.onopen = function(){
            actionWs.send('backward');
        }
        actionWs.onmessage = function(e){
            console.log("指令消息backward："+ e.data);
        }
    });
    //左
    $(document).on("click","#left",function(){
        actionWs = new WebSocket("ws://101.132.166.121:2345");
        actionWs.onopen = function(){
            actionWs.send('left');
        }
        actionWs.onmessage = function(e){
            console.log("指令消息left："+ e.data);
        }
    });
    //右
    $(document).on("click","#right",function(){
        actionWs = new WebSocket("ws://101.132.166.121:2345");
        actionWs.onopen = function(){
            actionWs.send('right');
        }
        actionWs.onmessage = function(e){
            console.log("指令消息right："+ e.data);
        }
    });
    //抓取
    $(document).on("click","#claw",function(){
        actionWs = new WebSocket("ws://101.132.166.121:2345");
        actionWs.onopen = function(){
            actionWs.send('claw');
        }
        actionWs.onmessage = function(e){
            console.log("指令消息claw："+ e.data);
        }
    });
    $(document).on("click","#claw",function(){
        actionWs = new WebSocket("ws://101.132.166.121:2345");
        actionWs.onopen = function(){
            actionWs.send('devicequeue001');
        }
        actionWs.onmessage = function(e){
            console.log("收到队列消息时间："+ e.data);
            var obj = JSON.parse(e.data);
            if(obj.s == 'idle'){
                alert("很遗憾没抓到娃娃");
            }
            if(obj.s == 'gotToy'){
                alert("恭喜抓到娃娃");
            }
        }
    });

    //停止
    var startTime;
    var log = function (msg) {
        var div = $('<div></div>');
        div.html((new Date().getTime()) + ': ' + (new Date().getTime() - startTime) + ': ' + msg)
        $('body').append(div);
    };
    var touchEnd = function () {
        actionWs = new WebSocket("ws://101.132.166.121:2345");
        actionWs.onopen = function(){
            actionWs.send('stop');
        }
        actionWs.onmessage = function(e){
            console.log("指令消息stop1："+ e.data);
        }
    };
    var mouseUp = function () {
        actionWs = new WebSocket("ws://101.132.166.121:2345");
        actionWs.onopen = function(){
            actionWs.send('stop');
        }
        actionWs.onmessage = function(e){
            console.log("指令消息stop2："+ e.data);
        }
    };
    var d = $('.txt_pand');
    d.bind('mouseup', mouseUp);
    d.bind('touchend', touchEnd);
    */
</script>
</body>
</html>