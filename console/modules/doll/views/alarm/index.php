<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="author" content="hoojo & http://hoojo.cnblogs.com">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <body>
</body>
    <script type="text/javascript">
        function alarm(){
            url = "http://p-admin.365zhuawawa.com/doll/alarm/alarm";
            $.ajax({
                type: "get",
                dataType: "json",
                async : false,
                url: url,
                success: function (result) {
//                    console.log(result.code);
//                    var code = result.code;
//                    var machines = result.machines;
//                    if (code == 200){
//                        sendMail(machines);
//                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log(XMLHttpRequest.status);
                    console.log(XMLHttpRequest.readyState);
                    console.log(textStatus);
                }
            });
        }
        setInterval('alarm()',30*60*60*10*2);

//        function sendMail(id){
//            $.post("http://localhost/zhuawawa/backend/web/index.php/doll/alarm/send-mail",
//                {
//                    toemail:"1766265569@qq.com",
//                    title:"异常报警",
//                    content:id
//                },
//                function(msg){
//                    console.log('888')
//                });
//        }
    </script>
    <script src="http://code.jquery.com/jquery-latest.js"></script>
</head>
<body>
</body>
</html>