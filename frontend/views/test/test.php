<html>
<head>
</head>
<body>
<button>投币</button>
<input type="button" id="btn" value="投币">
<?php
echo "<input type='hidden' value='yuxiuhong' id='dollid'>";
?>
<input type="text" value="" id="txt">
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>
    $(document).ready(function(){
        $("button").click(function(){
            $.ajax({
                url:"?r=test/test",
                type:"get",
                async:false,
                success:function(msg){
                    var obj = JSON.parse(msg);
                    console.log(obj.access_token);
                    $("#txt").val(obj.access_token);
                }
            })
        });
    });

    token = $("#txt").val();
    $(document).on("click","#btn",function(){
        $.get("?r=test/data",
            {
                access_token:token
            },
            function(data){
//                var obj = JSON.parse(data);
                console.log(data);
            });
    });
</script>
</body>
</html>