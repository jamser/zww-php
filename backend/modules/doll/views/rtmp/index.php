<html xmlns="http://www.w3.org/1999/html">
<meta charset="utf-8">
<script src="http://code.jquery.com/jquery-latest.js"></script>
<head>
    <title>生成直播流</title>
</head>
<body>
    推流地址的唯一id:<input class="form-control" type="text" id="streamId"/>
    <button id="btn" class="btn btn-primary" />生成</button>
<div style="display: none" id="play">
    <br/>
    推流地址 : <p id="url1"></p>
    播放地址（RTMP）: <p id="url2"></p>
    播放地址（FLV）: <p id="url3"></p>
    播放地址(HLS) : <p id="url4"></p>
</div>
<script>
    $(document).on("click","#btn",function(){
        id=$('#streamId').val();
        $.get("http://p-admin.365zhuawawa.com/doll/rtmp/get-push-url",
            {
                streamId:id
            },
            function(result){
                var obj = JSON.parse(result);
                console.log(obj.url1);
                document.getElementById("play").style.display='block';
                $("#url1").html(obj.url1);
                $("#url2").html(obj.url2);
                $("#url3").html(obj.url3);
                $("#url4").html(obj.url4);
            });
    });

</script>
</body>
</html>