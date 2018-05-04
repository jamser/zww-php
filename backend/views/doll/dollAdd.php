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
<div class="content">

    <div class="container-fluid">
        <div id="pad-wrapper" class="new-user">
            <div class="row-fluid header">
                <h3>添加娃娃</h3>
            </div>

            <div class="row-fluid form-wrapper">
                <!-- left column -->
                <div class="span9 with-sidebar">
                    <div class="container">
                        <form class="new_user_form inline-input" action="?r=doll/add_do" method="post" enctype="multipart/form-data"/>
                        <div class="span12 field-box">
                            <label>娃娃名称：</label>
                            <input class="span9" type="text" name="dollName"/>
                        </div>
                        <div class="span12 field-box">
                            <label>娃娃总量：</label>
                            <input class="span9" type="text" name="dollTotal"/>
                        </div>
                        <div class="span12 field-box">
                            <label>娃娃图片：</label>
                            <input class="span9" type="file" name="song_img"/>
                        </div>
                        <div class="span12 field-box">
                            <label>娃娃数量：</label>
                            <input class="span9" type="text" name="dollNumber"/>
                        </div>
                        <div class="span12 field-box">
                            <label>娃娃编号：</label>
                            <input class="span9" type="text" name="dollCode"/>
                        </div>
                        <div class="span11 field-box actions">
                            <input type="submit" class="btn-glow primary" value="创建" />
                        </div>
                        </form>
                    </div>
                </div>
                <!-- side right column -->
            </div>
        </div>
    </div>
</div>
</div>
</body>
<!-- end main container -->
