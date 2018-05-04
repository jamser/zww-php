<html>
<head>
    <script src="js/jquery-3.1.1.min.js"></script>
</head>
<body>
<button>投币</button>
<input type="button" id="btn" value="投币">
<input type="button" id="sub" value="投币">
<input type="button" id="video" value="视频">
<?php
echo "<input type='hidden' value='yuxiuhong' id='dollid'>";
    ?>
<input type="text" value="" id="txt">
<script>
    $(document).ready(function(){
        $("button").click(function(){
            $.post("?r=doll/ajax",
                {
                    name:"Donald Duck",
                    city:"Duckburg"
                },
                function(data,status){
                    var obj = JSON.parse(data);
                    alert("数据：" + obj.name + "\n状态：" + status);
                    $("#txt").val(obj.city);
                });
        });
    });

    name = "Donald Duck";
//    city = "Duckburg";
    city=$("#dollid").val();
    $(document).on("click","#btn",function(){
        $.post("?r=doll/ajax",
            {
                name:name,
                city:city
            },
            function(name,city){
                alert("数据：" + name + "\n状态：" + city);
            });
    });

    $(document).on("click","#sub",function(){
        url = "http://dev.365zhuawawa.com/icrane/api/game/start";
        $.ajax({
            type: "post",
            dataType: "json",
            async : false,
            url: url,
            data:{memberId:123456,dollId:123456,token:123456},
            success: function (result) {
                alert('99');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(XMLHttpRequest.status);
                alert(XMLHttpRequest.readyState);
                alert(textStatus);
            }
        });
    });

    $(document).ready(function(){
        $("#video").click(function(){
            $.ajax({
                url: "https://h5cs-1.agoraio.cn:7668/geth5gw/jsonp",
                type: "POST",
                headers: {
                    "Content-type": "application/json; charset=utf-8"
                },
                data: JSON.stringify({
                    "key": "5c194e8fa3874df3b1ebb349ce09a5a2",
                    "cname": "wanyiguo"
                }),
                success:function(msg){
                    alert("数据：" + msg );
                }
            }
        });
    });
</script>
</body>
</html>