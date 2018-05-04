<!DOCTYPE html>
<html>
<head>
    <title>365抓娃娃-后台管理</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- bootstrap -->
    <link href="./statistic/css/bootstrap/bootstrap.css" rel="stylesheet" />
    <link href="./statistic/css/bootstrap/bootstrap-responsive.css" rel="stylesheet" />
    <link href="./statistic/css/bootstrap/bootstrap-overrides.css" type="text/css" rel="stylesheet" />

    <!-- global styles -->
    <link rel="stylesheet" type="text/css" href="./statistic/css/layout.css" />
    <link rel="stylesheet" type="text/css" href="./statistic/css/elements.css" />
    <link rel="stylesheet" type="text/css" href="./statistic/css/icons.css" />

    <!-- libraries -->
    <link href="./statistic/css/lib/font-awesome.css" type="text/css" rel="stylesheet" />

    <!-- this page specific styles -->
    <link rel="stylesheet" href="./statistic/css/compiled/user-list.css" type="text/css" media="screen" />

    <!-- open sans font -->

    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
<body>

<!-- navbar -->
<div class="navbar navbar-inverse">
    <div class="navbar-inner">
        <button type="button" class="btn btn-navbar visible-phone" id="menu-toggler">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

        <a class="brand" href="index.html" style="font-weight:700;font-family:Microsoft Yahei">365抓娃娃后台管理</a>

        <ul class="nav pull-right">
            <li class="hidden-phone">
                <input class="search" type="text" />
            </li>
            <li class="notification-dropdown hidden-phone">
                <a href="#" class="trigger">
                    <i class="icon-warning-sign"></i>
                    <span class="count">6</span>
                </a>
                <div class="pop-dialog">
                    <div class="pointer right">
                        <div class="arrow"></div>
                        <div class="arrow_border"></div>
                    </div>
                    <div class="body">
                        <a href="#" class="close-icon"><i class="icon-remove-sign"></i></a>
                        <div class="notifications">
                            <h3>你有 6 个新通知</h3>
                            <a href="#" class="item">
                                <i class="icon-signin"></i> 新用户注册
                                <span class="time"><i class="icon-time"></i> 13 分钟前.</span>
                            </a>
                            <a href="#" class="item">
                                <i class="icon-signin"></i> 新用户注册
                                <span class="time"><i class="icon-time"></i> 18 分钟前.</span>
                            </a>
                            <a href="#" class="item">
                                <i class="icon-signin"></i> 新用户注册
                                <span class="time"><i class="icon-time"></i> 49 分钟前.</span>
                            </a>
                            <a href="#" class="item">
                                <i class="icon-download-alt"></i> 新订单
                                <span class="time"><i class="icon-time"></i> 1 天前.</span>
                            </a>
                            <div class="footer">
                                <a href="#" class="logout">查看所有通知</a>
                            </div>
                        </div>
                    </div>
                </div>
            </li>

            <li class="notification-dropdown hidden-phone">
                <a href="#" class="trigger">
                    <i class="icon-envelope-alt"></i>
                </a>
                <div class="pop-dialog">
                    <div class="pointer right">
                        <div class="arrow"></div>
                        <div class="arrow_border"></div>
                    </div>
                    <div class="body">
                        <a href="#" class="close-icon"><i class="icon-remove-sign"></i></a>
                        <div class="messages">
                            <a href="#" class="item">
                                <img src="img/contact-img.png" class="display" />
                                <div class="name">Alejandra Galván</div>
                                <div class="msg">
                                    There are many variations of available, but the majority have suffered alterations.
                                </div>
                                <span class="time"><i class="icon-time"></i> 13 min.</span>
                            </a>
                            <a href="#" class="item">
                                <img src="img/contact-img2.png" class="display" />
                                <div class="name">Alejandra Galván</div>
                                <div class="msg">
                                    There are many variations of available, have suffered alterations.
                                </div>
                                <span class="time"><i class="icon-time"></i> 26 min.</span>
                            </a>
                            <a href="#" class="item last">
                                <img src="img/contact-img.png" class="display" />
                                <div class="name">Alejandra Galván</div>
                                <div class="msg">
                                    There are many variations of available, but the majority have suffered alterations.
                                </div>
                                <span class="time"><i class="icon-time"></i> 48 min.</span>
                            </a>
                            <div class="footer">
                                <a href="#" class="logout">View all messages</a>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle hidden-phone" data-toggle="dropdown">
                    账户管理
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="personal-info.html">个人信息管理</a></li>
                    <li><a href="#">修改密码</a></li>
                    <li><a href="#">订单管理</a></li>
                </ul>
            </li>
            <li class="settings hidden-phone">
                <a href="personal-info.html" role="button">
                    <i class="icon-cog"></i>
                </a>
            </li>
            <li class="settings hidden-phone">
                <a href="signin.html" role="button">
                    <i class="icon-share-alt"></i>
                </a>
            </li>
        </ul>
    </div>
</div>
<!-- end navbar -->

<!-- sidebar -->
<div id="sidebar-nav">
    <ul id="dashboard-menu">
        <li class="active">
            <div class="pointer">
                <div class="arrow"></div>
                <div class="arrow_border"></div>
            </div>
            <a href="index.html">
                <i class="icon-home"></i>
                <span>后台首页</span>
            </a>
        </li>
        <li>
            <a href="?r=export/show">
                <i class="icon-signal"></i>
                <span>导出所有订单</span>
            </a>
        </li>
        <li>
            <a href="?r=export/delivery">
                <i class="icon-signal"></i>
                <span>已发货订单</span>
            </a>
        </li>
        <li>
            <a href="?r=export/unshipped">
                <i class="icon-signal"></i>
                <span>未发货订单</span>
            </a>
        </li>
        <li>
            <a href="?r=doll/add">
                <i class="icon-signal"></i>
                <span>添加娃娃</span>
            </a>
        </li>
        <li>
            <a href="?r=doll/show">
                <i class="icon-signal"></i>
                <span>展示娃娃</span>
            </a>
        </li>
        <li>
            <a href="?r=import/import">
                <i class="icon-signal"></i>
                <span>导入数据</span>
            </a>
        </li>
    </ul>
</div>
<!-- end sidebar -->

<!-- main container -->
<div class="content">
    <div class="container-fluid">
        <div id="pad-wrapper" class="users-list">
            <form action="?r=export/export-unshipped" method="post">
                <div class="row-fluid header">
                    <h3>输入日期：</h3>
                    <div class="span10 pull-right">
                        <input type="text" name="startTime" id="startTime" placeholder="请输入开始时间" value=""/>
                        <input type="text" name="endTime" id="endTime" placeholder="请输入结束时间"/>
                        <div class="ui-dropdown">
                            <div class="dialog">
                                <div class="pointer">
                                    <div class="arrow"></div>
                                    <div class="arrow_border"></div>
                                </div>
                            </div>
                            <input type="button" value="搜索" class="btn-flat success pull-right" id="btn">
                        </div>
                        <input type="submit" value="导出订单数据" class="btn-flat success pull-right">
                        <div id="list-name-input" class="list-name-input">
                            <select type="text" class="list-select" id="list-select">
                                <option value="">
                                </option>
                                <option value="1">
                                    手机号
                                </option>
                                <option value="2">
                                    订单号
                                </option>
                                <option value="3">
                                    名称
                                </option>
                            </select>
                            <input type="text" class="name item-width list-name-for-select" id="name" name="name">
                        </div>
                    </div>
                </div>
            </form>
            <!-- Users table -->
            <div class="row-fluid table">
                <table class="table table-hover" border="1">
                    <thead>
                    <tr>
                        <th class="span3 sortable" style="width: 50px;">
                            <span class="line"></span>ID
                        </th>
                        <th class="span3 sortable" style="width: 50px;">
                            <span class="line"></span>订单号
                        </th>
                        <th class="span3 sortable">
                            <span class="line"></span>发件人姓名
                        </th>
                        <th class="span3 sortable">
                            <span class="line"></span>发件人手机
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>发件人详细地址
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>自定义区域1*（商品编码、娃娃名称、数量）
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>收件人姓名
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>收件人手机
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>收件人详细地址
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>自定义区域
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>日期
                        </th>
                    </tr>
                    </thead>
                    <tbody id="content">
                    <?php foreach($data as $key=>$value) { ?>
                        <tr class="first">
                            <td>
                                <?php echo $value['id'] ?>
                            </td>
                            <td>
                                <?php echo $value['order_number'] ?>
                            </td>
                            <td>
                                <?php echo $value['addrPerson'] ?>
                            </td>
                            <td class="align-left">
                                <?php echo $value['phone'] ?>
                            </td>
                            <td>
                                <?php echo $value['faddress'] ?>
                            </td>
                            <td class="align-right">
                                <?php echo $value['dollinfos'] ?>
                            </td>
                            <td class="align-right">
                                <?php echo $value['receiver_name'] ?>
                            </td>
                            <td class="align-right">
                                <?php echo $value['receiver_phone'] ?>
                            </td>
                            <td class="align-right">
                                <?php echo $value['taddress'] ?>
                            </td>
                            <td></td>
                            <td class="align-right" style="width: 20px;">
                                <?php echo $value['order_date'] ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination pull-right">
                <ul>
                    <a href="?r=export/unshipped&page=1">首页</a>
                    <a href="?r=export/unshipped&page=<?php echo $last_page?>">上一页</a>
                    <a href="?r=export/unshipped&page=<?php echo $next_page?>">下一页</a>
                    <a href="?r=export/unshipped&page=<?php echo $total_page?>">尾页</a>
                    一共（<?php echo $total_page?>）页
                </ul>
                <!-- end users table -->
            </div>
        </div>
    </div>
    <!-- end main container -->
    <!-- scripts -->
</body>
</html>
<script  type="text/javascript" src="js/codebase/jquery-3.1.1.min.js"></script>
<script>
    var $min=$;
    //    alert($min);
</script>


<link rel="stylesheet" type="text/css" href="js/codebase/GooCalendar.css"/>
<script  type="text/javascript" src="js/codebase/jquery-1.3.2.js"></script>
<script  type="text/javascript" src="js/codebase/GooFunc.js"></script>
<script  type="text/javascript" src="js/codebase/GooCalendar.js"></script>

<!--<script src="js/sources/jquery-1.3.2.min.js"></script>-->
<script>
    //alert($)

    var property2={
        divId:"demo2",//日历控件最外层DIV的ID
        needTime:true,//是否需要显示精确到秒的时间选择器，即输出时间中是否需要精确到小时：分：秒 默认为FALSE可不填
        yearRange:[1970,2030],//可选年份的范围,数组第一个为开始年份，第二个为结束年份,如[1970,2030],可不填
        week:['日','一','二','三','四','五','六'],//数组，设定了周日至周六的显示格式,可不填
        month:['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],//数组，设定了12个月份的显示格式,可不填
        format:"yyyy-MM-dd hh:mm:ss"
        /*设定日期的输出格式,可不填*/
    };
    $(document).ready(function(){
        canva2=$.createGooCalendar("startTime",property2);
        canva2=$.createGooCalendar("endTime",property2);
    });
    //  搜索
    $min(document).on('click','#btn',function(){
        var startTime = $('#startTime').val();
        var endTime = $('#endTime').val();
        var name = $('#name').val();
//        alert(name);return false;
        $.ajax({
            url:'?r=export/search2',
            data:{startTime:startTime,endTime:endTime,name:name},
            dataType:'json',
//            async:false
            success:function(msg){
//                alert(msg);return false;
//                if(msg==1){
//                    alert('请输入区间日期')
//                    return false;
//                }else {
//                      alert(msg);return false;
                    var str = '';
                    $.each(msg,function(k,v){
//                          alert(v.id);return false;
                        str+='<tr class="first">';
                        str+='<td>'+ v.id+'</td>';
                        str+='<td>'+ v.order_number+'</td>';
                        str+='<td class="align-right">'+ v.addrPerson+'</td>';
                        str+='<td class="align-right">'+ v.phone+'</td>';
                        str+='<td class="align-right">'+ v.faddress+'</td>';
                        str+='<td class="align-right">'+ v.dollinfos+'</td>';
                        str+='<td class="align-right">'+ v.receiver_name+'</td>';
                        str+='<td class="align-right">'+ v.receiver_phone+'</td>';
                        str+='<td class="align-right">'+ v.taddress+'</td>';
                        str+='<td class="align-right"></td>';
                        str+='<td class="align-right">'+ v.order_date+'</td>';
                        str+='</tr>';
                    });
                    $('#content').html(str);
//                }
            }
        })
    });
</script>