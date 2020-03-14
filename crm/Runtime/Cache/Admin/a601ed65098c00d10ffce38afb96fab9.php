<?php if (!defined('THINK_PATH')) exit();?>    <div class="header navbar navbar-inverse navbar-fixed-top">
        <!-- BEGIN 顶部导航栏 -->
        <div class="header-inner">
            <!-- BEGIN LOGO -->  
            <a class="navbar-brand" href="/btc/index.php/Admin/Index/index">
                <img src="/btc/Public/res/admin/assets/img/logo.png"  class="img-responsive" /> 
            </a>
            <!-- END LOGO -->

            <!-- BEGIN 用户框 -->
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <img alt="" src="/btc/Public/<?php echo ($adminfo["headimg"]); ?>" width="29" height="29"/>
                        <span class="username"><?php echo ($adminfo["name"]); ?></span>
                        <i class="icon-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <!--li><a href="admid/<?php echo ($adminfo["id"]); ?>"><i class="icon-user"></i>个人信息</a></li>
                        <li class="divider"></li-->
                        <li><a href="javascript:void(0);" onclick="btten.Logout();"><i class="icon-ban-circle"></i> 登出系统</a></li>
                    </ul>
                </li>
            </ul>
            <!-- END 用户框 -->
      </div>
      <!-- END TOP 顶部导航栏 -->
    </div>