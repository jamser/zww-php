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
    </style>
    <link href="resource/css/game.css" rel="stylesheet" />
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
                        <a href="?r=doll/info&token=<?php echo $token?>"><img style="width: 30px;height: 30px;text-align: right;margin-left: 10px" src="resource/img/Common_Nav_Mine.png"></a>
                        <a href="#"><img style="width: 30px;height: 30px;text-align: left;margin-left: 295px" src="resource/img/Common_Nav_Setup.png"></a>
                    </div>
                </div>
            </div>
        </section>
    </header>
        <div class="df block_action_sort">
        </div>
        <div style="" id="content">
        </div>
</div>
<script src="js/jquery-3.1.1.min.js"></script>
<script>
   $(function(){
       $.ajax({
           url:'?r=doll/get-doll-room',
           async:false,
           dataType:'json',
           success:function(msg){
               var str = '';
               str += '<div style="float: right" id="content">';
               str += '<ul class="collection_machine">';

               $.each(msg.resultData,function(k,v){

                   str += '<li class="machine_leaf"> <a href="?r=doll/room&id='+ v.id+'"></a>';
                   str += '<div class="header_machine">';
                   str += '<div class="type_machine cnp">';
                   str += '<span class="ic_hot_machine cnp"></span>';
                   str += '</div>';
                   str += '<div class="cover_machine cnp" style="background-image: url();"><img src="'+v.tbimg_real_path+'"width="130"></div>';
                   str += '</div>';
                   str += ' <div class="name_machine">'+v.name+'</div>';
                   str += '<p></p>';
                   str += ' <div class="df aic jcsb">';
                   str += '<section class="df aic otherInfo_machine">';
                   str += '<span class="coinCost_machine">'+ v.redeem_coins+'</span>';
                   str += '<span class="ic_coin cnp"></span>';
                   str += '</section>';
                   str += '<span class="state_machine busy">'+ v.machine_status+'</span>';
                   str += '</div>';
                   str += '<p></p>';
                   str += '</li>';
               });
               $('#content').html(str);
           }
       })
   })
</script>
</body>
</html>