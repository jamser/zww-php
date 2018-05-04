<html style="font-size:50px">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=375,user-scalable=no" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-itunes-app" content="app-id=1214856510" />
    <title>365抓娃娃</title>
    <link rel="stylesheet" href="./resource/lib/swiper.min.css" />
    <style>
        html,
        body {
            position: relative;
        }

        .swiper-container {
            width: 100%;
        }

        .swiper-slide {
            /*text-align: center;*/
            font-size: 18px;
            background: #fff;.

            /* Center slide text vertically */
        display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            /*justify-content: center;*/
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
        }

        .swiper-wrapper{
            height: auto;
        }
        .adc_banner .swiper-slide {
            width: 100%;
            height: 40vw;
            max-height: 60px ;
            max-width: 640px;
            /*padding-bottom: 40%;*/
        }
        .ad_banner .swiper-slide a {
            display: block;
            width: 100%;
            height: 100%;
            padding-bottom: 100%;
        }

        .notice_banner {
            width: 100%;
            overflow: hidden;
            height: .5rem;
            line-height: .5rem;
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            font-size: .36rem;
            white-space: nowrap;
        }

        .notice_carousel {
            -webkit-animation: slide 8s linear infinite;
            animation: slide 8s linear infinite;
            -webkit-transform: translateX(100%);
            transform: translateX(100%);
            -webkit-animation-delay: 2s;
            animation-delay: 2s;
        }

        @-webkit-keyframes slide {
            100% {
                -webkit-transform: translateX(-200%);
                transform: translateX(-200%);
            }
        }
        @keyframes slide {
            100% {
                -webkit-transform: translateX(-200%);
                transform: translateX(-200%);
            }
        }
        .machine_leaf{
            overflow: hidden;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            padding:10px;
            position: relative;
            font-size:.32rem;
            float:left;
            background-color:white;
            width: 48%;
            border-radius:.05rem;
            margin-bottom:.2rem;
        }
        .machine_leaf:nth-child(2n+1){
            margin-left:1%;
        }
        .machine_leaf a{
            position:absolute;
            z-index: 2;
            width: 90%;
            height: 90%;
        }
        .machine_leaf:nth-child(2n){
            margin-left:2%;
        }
        .header_machine{
            width: 100%;

            position:relative;
        }
        .cover_machine{
            position: relative;
            width:100%;
            height: 0;
            border-radius:.05rem;
            padding-bottom:80%;
        }
        .cover_machine::after {
            content: '';
            display: block;
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            -webkit-transform: scale(0.5);
            transform: scale(0.5);
            border: 1px solid #e4dede;
            border-radius: .05rem;
        }
        .name_machine{
            font-weight: bold;
            margin-top: .1rem;
            font-size: .24rem;
            line-height: .36rem;
            height: .36rem;
        }
        .coinCost_machine{
            color:#4bb6dd;
            margin-right:.05rem;
        }

        .state_machine{
            color:white;
            font-size:.24rem;
            /*padding:.05rem 0;*/
            height: .35rem;
            line-height:.38rem;
            width:1.5rem;
            text-align: center;
            border-radius:5rem;
            background-color:#54df8c;
        }
        .state_machine.busy{
            background-color:#b1c770;
        }
        .collection_machine::after{
            content: "";
            display: block;
            clear:both;
        }
        .ic_hot_machine{
            z-index: 1;
            position:absolute;
            top:-4%;
            left:-4%;
        }
        .block_action_sort{
            position: relative;
            z-index: 23;
            margin-bottom:.1rem;
        }
        .df{

        }
        .confirm_ul{list-style:none;margin:0px;padding:0px;width:90%;height:70%;margin: auto;margin-top: 40%;}
        .confirm_title{background:#F2F2F2;text-align:left;padding-left:20px;line-height:60px;border:1px solid #999;}
        .confirm_content{background:#fff;text-align:center;height:80px;line-height:80px;}
        .confirm_btn-wrap{background:#fff;height:50px;line-height:18px;text-align: right;}
        .confirm_btn{cursor:pointer;color:#2bd00f;margin-right: 35px;}
        .confirm_btn-wrap > a:nth-child(1){color: #9c9898;}
    </style>
    <link href="resource/css/game.css" rel="stylesheet" />
    <script src="js/zepto.min.js"></script>
    <link type="text/css" rel="stylesheet" href="css/weui.min.css">
    <link type="text/css" rel="stylesheet" href="css/weui.css">
    <script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script src="https://res.wx.qq.com/open/libs/weuijs/1.0.0/weui.min.js"></script>
    <script src="js/jquery-3.1.1.min.js" type="text/javascript" charset="utf-8"></script>
</head>
<body ontouchstart="" rlt="1" style="background-image:url(&quot;./resource/img/index.jpg&quot;)">
<div id="app" style="">
    <header>
        <div class="notice_banner">
            <p class="notice_carousel"> 365抓娃娃内测中，测试期间请勿充值抓取。</p>
        </div>
        <section class="adc_banner">
            <div class="swiper-container swiper-container-horizontal">
                <div style="transition-duration: 0ms;" class="swiper-wrapper">
                    <div class="swiper-slide cnp" style="background-image: url(&quot;./resource/img/head.png&quot;);">
<!--                        <a href="?r=game/user-info&token=--><?php //echo $token?><!--"><img style="width: 30px;height: 30px;text-align: right;margin-left: 10px" src="resource/img/Common_Nav_Mine.png"></a>-->
<!--                        <a href="#"><img style="width: 30px;height: 30px;text-align: left;margin-left: 295px" src="resource/img/Common_Nav_Setup.png"></a>-->
                    </div>
                </div>
            </div>
        </section>
    </header>
    <div class="df block_action_sort">
    </div>
    <div style="float:right" id="content">
        <ul class="collection_machine">
        <?php
        foreach($rooms as $v) {
            $dollId = $v['id'];
            $dollName = $v['name'];
            $doll_redeem_coins = $v['redeem_coins'];
            $status = $v['machine_status'];
            $image = $v['tbimg_real_path'];
//            $type = $v['type'];
            $type = 'h5';
            if($type == 'h5'){
                $url = "<a href='?r=game/game-client&dollId=$dollId&token=$token'></a>";
            }elseif($type == 'app'){
                $url = "<a href='javascript:;' class='app-room-link'></a>";
            }
            echo "
            <li class='machine_leaf'>$url
            <div class='header_machine'>
            <span class='ic_hot_machine cnp'></span>
            </div>
            <div class='cover_machine cnp' style='background-image: url();'><img src='$image' width='130'></div>
            <div class='name_machine'>$dollName</div>
            <p></p>
            <div class='df aic jcsb'>
            <section class='df aic otherInfo_machine'>
            <span class='coinCost_machine'>$doll_redeem_coins</span>
            <span class='ic_coin cnp'></span>
            </section>
            <span class='state_machine busy'>$status</span>
            </div>
            <p></p>
            </li>
            ";
        }
        ?>
    </div>
</div>
<script>
    $(function(){
//        $("body").addEventListener('contextmenu', function(e){
//            e.preventDefault();
//        });

        $('.app-room-link').click(function(){
            Confirm('您好，该房间属于APP，请下载APP继续玩');
        })
    });

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
</script>
</body>
</html>