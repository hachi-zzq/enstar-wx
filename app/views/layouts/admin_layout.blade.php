<!DOCTYPE html>
<!--[if IE 8]>         <html class="ie8"> <![endif]-->
<!--[if IE 9]>         <html class="ie9 gt-ie8"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="gt-ie8 gt-ie9 not-ie"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>@section('title') @show - EnStar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

    <!-- Open Sans font from Google CDN -->
<!--    <link href="/pixel/stylesheets/css.css" rel="stylesheet" type="text/css">-->

    <!-- Pixel Admin's stylesheets -->
    <link href="{{asset('pixel/stylesheets/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('pixel/stylesheets/pixel-admin.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('pixel/stylesheets/widgets.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('pixel/stylesheets/rtl.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('pixel/stylesheets/themes.min.css')}}" rel="stylesheet" type="text/css">

    <!--[if lt IE 9]>
    <script src="{{asset('pixel/javascripts/ie.min.js')}}"></script>
    <![endif]-->
    @section('css')

    @show
    <style>
        #mm-howdy {
            color: #fff;
        }

        .theme-clean #mm-howdy,
        .theme-white #mm-howdy {
            color: #444;
        }
    </style>
</head>


<!-- 1. $BODY ======================================================================================

	Body

	Classes:
	* 'theme-{THEME NAME}'
	* 'right-to-left'      - Sets text direction to right-to-left
	* 'main-menu-right'    - Places the main menu on the right side
	* 'no-main-menu'       - Hides the main menu
	* 'main-navbar-fixed'  - Fixes the main navigation
	* 'main-menu-fixed'    - Fixes the main menu
	* 'main-menu-animated' - Animate main menu
-->
<body class="theme-default main-menu-animated">

<script>var init = [];</script>


<div id="main-wrapper">


<!-- 2. $MAIN_NAVIGATION ===========================================================================

	Main navigation
-->
<div id="main-navbar" class="navbar navbar-inverse" role="navigation">
<!-- Main menu toggle -->
<button type="button" id="main-menu-toggle"><i class="navbar-icon fa fa-bars icon"></i><span class="hide-menu-text">HIDE MENU</span></button>

<div class="navbar-inner">
<!-- Main navbar header -->
<div class="navbar-header">

    <!-- Logo -->
    <a href="{{URL::route('adminHome')}}" class="navbar-brand">
        <div><img alt="Pixel Admin" src="{{asset('pixel/images/pixel-admin/main-navbar-logo.png')}}"></div>
        EnStar
    </a>

    <!-- Main navbar toggle -->
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-navbar-collapse"><i class="navbar-icon fa fa-bars"></i></button>

</div> <!-- / .navbar-header -->

<div id="main-navbar-collapse" class="collapse navbar-collapse main-navbar-collapse">
<div>


<div class="right clearfix">
<ul class="nav navbar-nav pull-right right-navbar-nav">

<!-- 3. $NAVBAR_ICON_BUTTONS =======================================================================

							Navbar Icon Buttons

							NOTE: .nav-icon-btn triggers a dropdown menu on desktop screens only. On small screens .nav-icon-btn acts like a hyperlink.

							Classes:
							* 'nav-icon-btn-info'
							* 'nav-icon-btn-success'
							* 'nav-icon-btn-warning'
							* 'nav-icon-btn-danger'
-->
<li class="nav-icon-btn nav-icon-btn-danger dropdown">
    <a href="#notifications" class="dropdown-toggle" data-toggle="dropdown">
        <span class="label">5</span>
        <i class="nav-icon fa fa-bullhorn"></i>
        <span class="small-screen-text">Notifications</span>
    </a>

    <!-- NOTIFICATIONS -->

    <!-- Javascript -->
    <script>
        init.push(function () {
            $('#main-navbar-notifications').slimScroll({ height: 250 });
        });
    </script>
    <!-- / Javascript -->

    <div class="dropdown-menu widget-notifications no-padding" style="width: 300px">
        <div class="notifications-list" id="main-navbar-notifications">

            <div class="notification">
                <div class="notification-title text-danger">SYSTEM</div>
                <div class="notification-description"><strong>Error 500</strong>: Syntax error in index.php at line <strong>461</strong>.</div>
                <div class="notification-ago">12h ago</div>
                <div class="notification-icon fa fa-hdd-o bg-danger"></div>
            </div> <!-- / .notification -->

            <div class="notification">
                <div class="notification-title text-info">STORE</div>
                <div class="notification-description">You have <strong>9</strong> new orders.</div>
                <div class="notification-ago">12h ago</div>
                <div class="notification-icon fa fa-truck bg-info"></div>
            </div> <!-- / .notification -->

            <div class="notification">
                <div class="notification-title text-default">CRON DAEMON</div>
                <div class="notification-description">Job <strong>"Clean DB"</strong> has been completed.</div>
                <div class="notification-ago">12h ago</div>
                <div class="notification-icon fa fa-clock-o bg-default"></div>
            </div> <!-- / .notification -->

            <div class="notification">
                <div class="notification-title text-success">SYSTEM</div>
                <div class="notification-description">Server <strong>up</strong>.</div>
                <div class="notification-ago">12h ago</div>
                <div class="notification-icon fa fa-hdd-o bg-success"></div>
            </div> <!-- / .notification -->

            <div class="notification">
                <div class="notification-title text-warning">SYSTEM</div>
                <div class="notification-description"><strong>Warning</strong>: Processor load <strong>92%</strong>.</div>
                <div class="notification-ago">12h ago</div>
                <div class="notification-icon fa fa-hdd-o bg-warning"></div>
            </div> <!-- / .notification -->

        </div> <!-- / .notifications-list -->
        <a href="#" class="notifications-link">MORE NOTIFICATIONS</a>
    </div> <!-- / .dropdown-menu -->
</li>
<li class="nav-icon-btn nav-icon-btn-success dropdown">
    <a href="#messages" class="dropdown-toggle" data-toggle="dropdown">
        <span class="label">10</span>
        <i class="nav-icon fa fa-envelope"></i>
        <span class="small-screen-text">Income messages</span>
    </a>

    <!-- MESSAGES -->

    <!-- Javascript -->
    <script>
        init.push(function () {
            $('#main-navbar-messages').slimScroll({ height: 250 });
        });
    </script>
    <!-- / Javascript -->

    <div class="dropdown-menu widget-messages-alt no-padding" style="width: 300px;">
        <div class="messages-list" id="main-navbar-messages">

            <div class="message">
                <img src="{{asset('pixel/demo/avatars/2.jpg')}}" alt="" class="message-avatar">
                <a href="#" class="message-subject">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</a>
                <div class="message-description">
                    from <a href="#">Robert Jang</a>
                    &nbsp;&nbsp;·&nbsp;&nbsp;
                    2h ago
                </div>
            </div> <!-- / .message -->



            <div class="message">
                <img src="{{asset('pixel/demo/avatars/2.jpg')}}" alt="" class="message-avatar">
                <a href="#" class="message-subject">Lorem ipsum dolor sit amet.</a>
                <div class="message-description">
                    from <a href="#">Robert Jang</a>
                    &nbsp;&nbsp;·&nbsp;&nbsp;
                    2h ago
                </div>
            </div> <!-- / .message -->

        </div> <!-- / .messages-list -->
        <a href="#" class="messages-link">MORE MESSAGES</a>
    </div> <!-- / .dropdown-menu -->
</li>
<!-- /3. $END_NAVBAR_ICON_BUTTONS -->

<li>
    <form class="navbar-form pull-left">
        <input type="text" class="form-control" placeholder="Search">
    </form>
</li>

<li class="dropdown">
    <a href="#" class="dropdown-toggle user-menu" data-toggle="dropdown">
        <img src="{{asset('static/img/avatar_40.png')}}">
        <span>{{{Auth::user()->name}}}</span>
    </a>
    <ul class="dropdown-menu">
        <li><a href="#"><span class="label label-warning pull-right">New</span>Profile</a></li>
        <li><a href="#"><span class="badge badge-primary pull-right">New</span>Account</a></li>
        <li><a href="#"><i class="dropdown-icon fa fa-cog"></i>&nbsp;&nbsp;Settings</a></li>
        <li class="divider"></li>
        <li><a href="{{route('logout')}}"><i class="dropdown-icon fa fa-power-off"></i>&nbsp;&nbsp;Log Out</a></li>
    </ul>
</li>
</ul> <!-- / .navbar-nav -->
</div> <!-- / .right -->
</div>
</div> <!-- / #main-navbar-collapse -->
</div> <!-- / .navbar-inner -->
</div> <!-- / #main-navbar -->
<!-- /2. $END_MAIN_NAVIGATION -->


<!-- 4. $MAIN_MENU =================================================================================

		Main menu

		Notes:
		* to make the menu item active, add a class 'active' to the <li>
		  example: <li class="active">...</li>
		* multilevel submenu example:
			<li class="mm-dropdown">
			  <a href="#"><span class="mm-text">Submenu item text 1</span></a>
			  <ul>
				<li>...</li>
				<li class="mm-dropdown">
				  <a href="#"><span class="mm-text">Submenu item text 2</span></a>
				  <ul>
					<li>...</li>
					...
				  </ul>
				</li>
				...
			  </ul>
			</li>
-->
<div id="main-menu" role="navigation">
<div id="main-menu-inner">
<div class="menu-content top" id="menu-content-demo">
    <!-- Menu custom content demo
         CSS:        styles/pixel-admin-less/demo.less or styles/pixel-admin-scss/_demo.scss
         Javascript: html/pixel/demo/demo.js
     -->
    <div>
        <div class="text-bg"><span class="text-slim">Welcome,</span> <span class="text-semibold">{{{Auth::user()->name}}}</span></div>

            <img src="{{asset('static/img/avatar_40.png')}}" alt="" class="">
        <div class="btn-group">
            <a href="#" class="btn btn-xs btn-primary btn-outline dark"><i class="fa fa-envelope"></i></a>
            <a href="#" class="btn btn-xs btn-primary btn-outline dark"><i class="fa fa-user"></i></a>
            <a href="#" class="btn btn-xs btn-primary btn-outline dark"><i class="fa fa-cog"></i></a>
            <a href="{{route('logout')}}" class="btn btn-xs btn-danger btn-outline dark"><i class="fa fa-power-off"></i></a>
        </div>
        <a href="#" class="close">&times;</a>
    </div>
</div>
<ul class="navigation">
    <li>
        <a href="{{URL::route('adminHome')}}"><i class="menu-icon fa fa-desktop"></i><span class="mm-text">首页</span></a>
    </li>
    <li class="mm-dropdown">
        <a href="#"><i class="menu-icon fa fa-book"></i><span class="mm-text">教材课程</span></a>
        <ul>
            <li>
                <a tabindex="-1" href="{{Route('adminBookCreate')}}"><span class="mm-text">添加教材</span></a>
            </li>
            <li>
                <a tabindex="-1" href="{{route('adminBookIndex')}}"><span class="mm-text">教材管理</span></a>
            </li>
            <li>
                <a tabindex="-1" href="{{Route('adminLessonCreate',array('book_id'=>Book::getFirstBook()))}}"><span class="mm-text">添加课文</span></a>
            </li>
            <li>
                <a tabindex="-1" href="{{route('adminLessonIndex',array('book_id'=>Book::getFirstBook()))}}"><span class="mm-text">课文管理</span></a>
            </li>
            <li>
                <a tabindex="-1" href="{{route('adminUnitIndex')}}"><span class="mm-text">单元管理</span></a>
            </li>
        </ul>
    </li>



    <li class="mm-dropdown">
        <a href="#"><i class="menu-icon fa fa-user"></i><span class="mm-text">用户</span></a>

        <ul>
            <li>
                <a href="{{route('adminUserIndex')}}"><span class="mm-text">全部用户</span></a>
            </li>
        </ul>
    </li>

    <li class="mm-dropdown">
        <a href="#"><i class="menu-icon fa fa-suitcase"></i><span class="mm-text">用户内容</span></a>

        <ul>
            <li>
                <a href="{{route('adminReadingList')}}"><span class="mm-text">阅读记录</span></a>
            </li>

        </ul>
    </li>

    <li class="mm-dropdown">
        <a href="#"><i class="menu-icon fa fa-list"></i><span class="mm-text">队列</span></a>

        <ul>
            <li>
                <a href="{{route('readingQueue')}}"><span class="mm-text">阅读评分队列</span></a>
            </li>
            <li>
                <a href="{{{route('lessonQueue')}}}"><span class="mm-text">课文分析队列</span></a>
            </li>
            <li>
                <a href="{{{route('retryQueue')}}}"><span class="mm-text">重试队列</span></a>
            </li>
        </ul>
    </li>

    <li class="mm-dropdown">
        <a href="#"><i class="menu-icon fa fa-gears"></i><span class="mm-text">系统</span></a>
        <ul>
            <li>
                <a href="#"><span class="mm-text">评测服务</span></a>
            </li>
        </ul>

        <ul>
        </ul>
    </li>


</ul> <!-- / .navigation -->
<!--<div class="menu-content">-->
<!--    <a href="pages-invoice.html" class="btn btn-primary btn-block btn-outline dark">Create Invoice</a>-->
<!--</div>-->
</div> <!-- / #main-menu-inner -->
</div> <!-- / #main-menu -->
<!-- /4. $MAIN_MENU -->

@yield('content')

<div id="main-menu-bg"></div>
</div> <!-- / #main-wrapper -->

<!-- Get jQuery from Google CDN -->
<!--[if !IE]> -->
<script type="text/javascript"> window.jQuery || document.write('<script src="/pixel/javascripts/jquery-2.0.3.min.js">'+"<"+"/script>"); </script>
<!-- <![endif]-->
<!--[if lte IE 9]>
<script type="text/javascript"> window.jQuery || document.write('<script src="/pixel/javascripts/jquery-1.8.3.min.js">'+"<"+"/script>"); </script>
<![endif]-->


<!-- Pixel Admin's javascripts -->
<script src="{{asset('pixel/javascripts/bootstrap.min.js')}}"></script>
<script src="{{asset('pixel/javascripts/pixel-admin.min.js')}}"></script>

<script type="text/javascript">
    init.push(function () {
        // Javascript code here
    })
    window.PixelAdmin.start(init);
</script>
@section('js')

@show
</body>
</html>