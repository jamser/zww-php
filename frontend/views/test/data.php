
<div style="width: 100%;" id="desc">
    <img src="http://img16.3lian.com/gif2016/q1/39/d/61.jpg" />
</div>
<div id="desc" align="center">

    <div style="margin-top:10px; width:200px;text-align: center">
        <div style="margin-top:10px;text-align:center;">
            <input type="button" value="上" class="forward btn btn-primary">
        </div>

        <div style="text-align:center;">
            <input type="button" style="margin: 10px;" value="左" class="left btn btn-primary">
            <input type="button" style="margin: 10px;" value="停止" class="stop btn btn-primary">
            <input type="button" style="margin: 10px;" value="右" class="right btn btn-primary">
        </div>


        <div style="margin:10px text-align:center;">
            <input type="button" value="下" class="backward btn btn-primary">
        </div>

        <div style="padding-top:30px;text-align:center;">
            <input type="button" value="投币" style="margin: 10px;" class="coin btn btn-primary">
            <input type="button" value="抓取" style="margin: 10px;" class="claw btn btn-success">
        </div>

    </div>

</div>
<div id="content" style="display: none">
</div>
<script type='text/javascript' src="js/jquery-3.1.1.min.js"></script>
<script>

    /**
     * 投币**/
    $(document).on("click",".coin",function(){
//        alert(1);return false;
        $.ajax({
            url:"?r=test/coin",
            type:"post",
//            dataType:'json',
//            data:{control:control,productKey:productKey,deviceName:deviceName},
//            async:false,
            success:function(msg){
//                接msg
//                alert(msg);return false;
                $.ajax({
                    url:"?r=test/insert",
                   data:{msg:msg},
                    type:"get",
//                   dataType:'json',
                   success:function(msg){
                        if(msg == 1){
//                           alert('入库成功');
//                           window.location.href='';
                           window.location.reload()
                        }else {
                            alert('入库失败');
                        }
                    }
                })
            }
        })
    });
    /****
     * 向上移动
     * */
    $(document).on("click",".forward",function(){
       var forward = $('.forward').val();
       var coin = $('.coin').val();
        $.ajax({
            url:'?r=test/forward',
            type:'get',
           async:false,
            success:function(msg){
//              alert(msg);
               $.ajax({
                   url:"?r=test/insert",
                   data:{msg:msg},
                   type:"get",
//                    dataType:'json',
                    success:function(msg){
                        if(msg == 1){
//                             alert('入库成功');
//                             window.location.href='';
                            window.location.reload();
                        }else {
                            alert('入库失败');
                       }
                    }
                })
            }
        })
    });
    /***
     * 向下移动
     * */
    $(document).on("click",".backward",function(){
        $.ajax({
            url:'?r=test/backward',
           type:'get',
            success:function(msg){
//                alert(msg);
                $.ajax({
                    url:"?r=test/insert",
                    data:{msg:msg},
                    type:"get",
//                    dataType:'json',
                    success:function(msg){
                        if(msg == 1){
//                            alert('入库成功');
//                            window.location.href='';
                            window.location.reload();
                        }else {
                            alert('入库失败');
                        }
                    }
                })
            }
        })
    });
    /***
     * 向左移动
     * */
    $(document).on("click",".left",function(){
        $.ajax({
            url:'?r=test/left',
           type:'post',
           success:function(msg){
//                alert(msg);
                $.ajax({
                   url:"?r=test/insert",
                    data:{msg:msg},
                   type:"get",
//                    dataType:'json',
                   success:function(msg){
                       if(msg == 1){
                            alert('入库成功');
//                            window.location.href='';
                            window.location.reload();
                        }else {
                            alert('入库失败');
                       }
                    }
                })
            }
        })
    });
    /***
     * 向右移动
     * */
    $(document).on("click",".right",function(){
       $.ajax({
            url:'?r=test/right',
           type:'get',
           success:function(msg){
//                alert(msg);
                $.ajax({
                   url:"?r=test/insert",
                    data:{msg:msg},
                   type:"get",
//                   dataType:'json',
                   success:function(msg){
                       if(msg == 1){
//                            alert('入库成功');
//                           window.location.href='';
                           window.location.reload();
                        }else {
                            alert('入库失败');
                       }
                    }
                })
            }
        })
    });
    /***
     * 停止
     * */
   $(document).on("click",".stop",function(){
       $.ajax({
           url:'?r=test/stop',
           type:'get',
           success:function(msg){
//                alert(msg);
                $.ajax({
                    url:"?r=test/insert",
                    data:{msg:msg},
                   type:"get",
//                    dataType:'json',
                   success:function(msg){
                        if(msg == 1){
//                            alert('入库成功');
//                            window.location.href='';
                            window.location.reload();
                       }else {
                           alert('入库失败');
                        }
                    }
                })
           }
        })
    });
    /****
     * 抓取
     * */
   $(document).on("click",".claw",function(){
       $.ajax({
            url:'?r=test/claw',
//            type:'get',
            success:function(msg){
////                alert(msg);
                $.ajax({
                    url:"?r=test/insert",
                    data:{msg:msg},
                    type:"get",
//                   dataType:'json',
                    success:function(msg){
                        if(msg == 1){
//                            alert('入库成功');
//                            window.location.href='';
                            window.location.reload();
                       }else {
                            alert('入库失败');
                        }
                    }
                })
            }
        })
    });
    /***
     * 查询
     * */
    $(document).on("click",".query",function(){
        $.ajax({
           url:'?r=test/query',
            type:'get',
           success:function(msg){
//                alert(msg);
               $.ajax({
                    url:"?r=test/insert",
                    data:{msg:msg},
                    type:"get",
//                    dataType:'json',
                   success:function(msg){
                        if(msg == 1){
//                            alert('入库成功');
//                           window.location.href='';
                            window.location.reload();
                       }else {
                            alert('入库失败');
                        }
                    }
                })
            }
        })
    });
    /****
     * 展示所有信息
     * */
    $(document).ready(function(){
        $.ajax({
            url:'?r=test/decode_select',
            type:'get',
            success:function(msg){
               //alert(msg);return false;
                $('#content').html(msg);
            }
        })
    })


    /**
     * 掉视频的接口
     * **/
//    $(document).ready(function(){
////         var url = 'http://101.132.166.121:3000';
////          console.log(url);return false;
////        alert($);
//            $.ajax({
//                url:'?r=test/video',
//                type:'get',
////                dataType:'jsonp',
//                success:function(msg){
////                   alert(msg);
//                    $('#desc').html(msg);
//                }
//            })
//        })
</script>