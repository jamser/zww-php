<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<div id="desc" align="center">
    <input type="button" value="投币" class="coin">
    <br/>
    <br/>
    <input type="button" value="上" class="forward" disabled="disabled">
    <input type="button" value="下" class="backward" disabled="disabled">
    <input type="button" value="左" class="left" disabled="disabled">
    <input type="button" value="右" class="right" disabled="disabled">
    <input type="button" value="停止" class="stop" disabled="disabled">
    <br/>
    <br/>
    <input type="button" value="抓取" class="claw" disabled="disabled">
    <br/>
    <br/>
    <input type="button" value="查询" class="query" disabled="disabled">
</div>
<hr/>
<div id="content">

</div>
</body>
</html>
<script type="text/javascript" src="./js/jquery-3.1.1.min.js"></script>
<script>
    /***
     * 判断先投币才能有接下来的操作
     * **/
    $(function(){
        $('.coin').on('click',function(){
            $('.forward').removeAttr("disabled");
            $('.backward').removeAttr("disabled");
            $('.left').removeAttr("disabled");
            $('.right').removeAttr("disabled");
            $('.stop').removeAttr("disabled");
            $('.claw').removeAttr("disabled");
            $('.query').removeAttr("disabled");
        })
    });
    /**
     * 投币**/
    $(document).on("click",".coin",function(){
        $.ajax({
            url:"?r=test/coin",
            type:"get",
//            dataType:'json',
            async:false,
            success:function(msg){
//                接msg
//                alert(msg);return false;
                $.ajax({
                    url:"?r=test/insert",
                    data:{msg:msg},
                    type:"get",
//                    dataType:'json',
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
//               alert(msg);
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
     * 展示所有信息
     * */
    $(document).ready(function(){
        $.ajax({
            url:'?r=test/select',
            dataType:'json',
            type:'get',
            success:function(msg){
                var str = '';
                $.each(msg,function(k,v){
                    str += '<ul>';
                    str += '<li>请求Id</li>';
                    str += '<li>'+v.id+'</li>';
                    str += '<li>请求MessageId</li>';
                    str += '<li>'+v.MessageId+'</li>';
                    str += '<li>请求RequestId</li>';
                    str += '<li>'+v.RequestId+'</li>';
                    str += '<li>请求Success</li>';
                    str += '<li>'+v.Success+'</li>';
                    str += '</ul>';
                });
                $('#content').html(str);
            }
        })
    })

</script>