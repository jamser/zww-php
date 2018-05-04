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
            <form action="?r=export/export" method="post">
                <div class="row-fluid header">
                    <h3>输入名称：</h3>
                    <div class="span10 pull-right">
                        <input type="text" name="dateTime" id="dateTime"/>
                        <div class="ui-dropdown">
                            <div class="dialog">
                                <div class="pointer">
                                    <div class="arrow"></div>
                                    <div class="arrow_border"></div>
                                </div>
                            </div>
                            <input type="button" value="搜索" class="btn-flat success pull-right" id="btn">
                        </div>
<!--                        <input type="submit" value="导出订单数据" class="btn-flat success pull-right">-->
                    </div>
                </div>
            </form>
            <!-- Users table -->
            <div class="row-fluid table">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="span3 sortable">
                            <span class="line"></span>娃娃序号：
                        </th>
                        <th class="span3 sortable">
                            <span class="line"></span>娃娃名称：
                        </th>
                        <th class="span3 sortable">
                            <span class="line"></span>娃娃总量：
                        </th>
                        <th class="span2 sortable">
                            <span class="line"></span>娃娃头像：
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>娃娃数量：
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>添加时间：
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>娃娃编号：
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>操作
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
                                <?php echo $value['dollName'] ?>
                            </td>
                            <td>
                                <?php echo $value['dollTotal'] ?>
                            </td>
                            <td class="align-right">
                                <img src="<?php echo "../../common/upload/".$value['img'] ?>" width="100">
                            </td>
                            <td class="align-right">
                                <?php echo $value['dollNumber'] ?>
                            </td>
                            <td class="align-right">
                                <?php echo date('Y-m-d,H:i:s',$value['addTime']) ?>
                            </td>
                            <td class="align-right">
                                <?php echo $value['dollCode'] ?>

                            </td>
                            <td class="align-right">
                                <a href="?r=doll/del&id=<?php echo $value['id']?>">删除</a>|
                                <a href="?r=doll/save&id=<?php echo $value['id']?>">修改</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination pull-right">
                <ul>
                    <li><a href="#">&#8249;</a></li>
                    <li><a class="active" href="#">1</a></li>
                    <li><a href="#">2</a></li>
                    <li><a href="#">3</a></li>
                    <li><a href="#">4</a></li>
                    <li><a href="#">5</a></li>
                    <li><a href="#">&#8250;</a></li>
                </ul>
            </div>
            <!-- end users table -->
        </div>
    </div>
</div>
<!-- end main container -->
<!-- scripts -->
<script src="js/jquery-latest.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/theme.js"></script>
</body>
</html>
<script src="js/sources/jquery-3.1.1.min.js"></script>
<script>
    $(document).on('click','#btn',function(){
        var time = $('#dateTime').val();
        $.ajax({
            url:'?r=export/search',
            data:{time:time},
            dataType:'json',
//            async:false
            success:function(msg){
//                alert(msg);return false;
                if(msg==1){
                    alert('值不能为空')
                    return false;
                }else {
//                      alert(msg);return false;
                    var str = '';
                    $.each(msg,function(k,v){
//                          alert(v.id);return false;
                        str+='<tr class="first">';
                        str+='<td>'+ v.id+'</td>';
                        str+='<td>'+ v.order_number+'</td>';
                        str+='<td>'+ v.order_by+'</td>';
                        str+='<td class="align-right">'+ v.status+'</td>';
                        str+='<td class="align-right">'+ v.order_date+'</td>';
                        str+='<td class="align-right">'+ v.deliver_number+'</td>';
                        str+='<td class="align-right">'+ v.deliver_method+'</td>';
                        str+='<td class="align-right">'+ v.deliver_date+'</td>';
                        str+='<td class="align-right">修改|删除</td>';
                        str+='</tr>';
                    });
                    $('#content').html(str);
                }
            }
        })
    })
</script>