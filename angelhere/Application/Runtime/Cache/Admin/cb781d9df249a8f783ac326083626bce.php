<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Bootstrap 3.3.5 -->
<link rel="stylesheet" href="/Public/common/lib/bootstrap/css/bootstrap.min.css">
<!-- Font Awesome -->
<link href="/Public/common/lib/adminlte/css/font-awesome.min.css" rel="stylesheet">


<!-- Theme style -->
<link rel="stylesheet" href="/Public/common/lib/adminlte/css/AdminLTE.min.css">
<!-- AdminLTE Skins. Choose a skin from the css/skins
     folder instead of downloading all of them to reduce the load. -->
<link rel="stylesheet" href="/Public/common/lib/adminlte/css/skins/_all-skins.min.css">
<!-- iCheck -->
<link rel="stylesheet" href="/Public/common/lib/iCheck/flat/blue.css">
<!-- Morris chart -->
<link rel="stylesheet" href="/Public/common/lib/morris/morris.css">
<!-- jvectormap -->
<link rel="stylesheet" href="/Public/common/lib/jvectormap/jquery-jvectormap-1.2.2.css">
<!-- Date Picker -->
<link rel="stylesheet" href="/Public/common/lib/datepicker/datepicker3.css">
<!-- Daterange picker -->
<link rel="stylesheet" href="/Public/common/lib/daterangepicker/daterangepicker-bs3.css">
<!-- bootstrap wysihtml5 - text editor -->
<link rel="stylesheet" href="/Public/common/lib/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

<link rel="stylesheet" type="text/css" href="/Public/common/lib/amazeui/css/amazeui.min.css"/>

<link rel="stylesheet" href="/Public/admin/css/admin.css">

    
    <style>
        .input-group{
            margin-top: 20px;
        }
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>


    <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="<?php echo $this->resource;?>index2.html" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b><?php echo C('project_name');?></b> 管理</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b><?php echo C('project_name');?></b> 管理</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="<?php echo $this->resource;?>#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">



                <div class="pull-right" style="padding: 5px;">
                    <a href="<?php echo U('Public/logout');?>" class="btn btn-default btn-flat">退出登录</a>
                </div>

            </div>
        </nav>
    </header>

    
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <!--<div class="user-panel">
          <div class="pull-left image">
            <img src="<?php echo $this->resource;?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
          </div>
          <div class="pull-left info">
            <p>Alexander Pierce</p>
            <a href="<?php echo $this->resource;?>#"><i class="fa fa-circle text-success"></i> Online</a>
          </div>
        </div>-->
        <!-- search form -->

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header"></li>
            <!--<li  class="manage-site">
                <a href="/manage/site">
                    <i class="fa fa-th"></i> <span>站点管理</span> <small class="label pull-right bg-green"></small>
                </a>
            </li>-->
            <!--<li  class="manage-data">
              <a href="/manage/data">
                <i class="fa fa-pie-chart"></i> <span>查看数据</span> <small class="label pull-right bg-green"></small>
              </a>
            </li>-->
            <!--<?php if($user_type == 1): ?>-->
                <!--<li class="treeview">-->
                    <!--<a href="">-->
                        <!--<i class="fa fa-pie-chart"></i> <span>站点管理</span> <i class="fa fa-angle-left pull-right"></i>-->
                    <!--</a>-->
                    <!--<ul class="treeview-menu">-->
                        <!--<li  class="manage-menu"><a href=" /admin/manage/menu"><i class="fa fa-circle-o"></i>菜单管理</a></li>-->
                        <!--<li  class="manage-member"><a href="/admin/manage/member"><i class="fa fa-circle-o"></i>成员管理</a></li>-->
                        <!--<li  class="manage-member_type"><a href="/admin/manage/member_type"><i class="fa fa-circle-o"></i>成员类型</a></li>-->
                    <!--</ul>-->
                <!--</li>-->

            <!--<?php endif; ?>-->

            <!--<?php if(is_array($menu)): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$itm): $mod = ($i % 2 );++$i;?>-->
                <!--<li class="treeview">-->
                    <!--<a href="">-->
                        <!--<i class="fa fa-pie-chart"></i> <span><?php echo ($itm['lv0']["name"]); ?></span> <i class="fa fa-angle-left pull-right"></i>-->
                    <!--</a>-->
                    <!--<ul class="treeview-menu">-->
                        <!--<?php if(is_array($itm["lv1"])): $i = 0; $__LIST__ = $itm["lv1"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m_itm): $mod = ($i % 2 );++$i;?>-->
                            <!--<li><a href="<?php echo ($m_itm["url"]); ?>"><i class="fa fa-circle-o"></i><?php echo ($m_itm["name"]); ?></a></li>-->
                        <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                    <!--</ul>-->
                <!--</li>-->
            <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->

            <?php foreach ($model as $item) :?>
                <li class="treeview">
                    <a href="javascript:void(0)">
                        <i class="fa fa-pie-chart"></i> <span><?php echo $item['name']; ?></span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php foreach ($fun as $itm) :?>
                            <?php if ($item['id'] == $itm['pid']) :?>
                                <li><a href="<?php echo $itm['url']?>"><i class="fa fa-circle-o"></i><?php echo $itm['name'] ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>

        </ul>
    </section>
    <!-- /.sidebar -->
</aside>



    <div class="content-wrapper">
        
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">课程管理</h3>
            </div>

            <div class="col-md-2" style="padding: 20px;">
                <a class="btn btn-block btn-primary add_lesson_config_btn" href="javascript:void(0)">添加课程配置</a>
            </div>


            <!-- /.box-header -->
            <div class="box-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 10px">id</th>
                        <th style="width: 100px">星期</th>
                        <th style="width: 200px">课程名称</th>
                        <th style="width: 250px">开始时间</th>
                        <th style="width: 100px">结束时间</th>
                        <th style="width: 100px">试练价格</th>
                        <th style="width: 100px">金卡价格</th>
                        <th style="width: 100px">白金卡价格</th>
                        <th style="width: 100px">黑卡价格</th>
                        <th style="width: 100px">最大人数</th>
                        <th style="width: 100px">操作</th>
                        <th>删除</th>
                    </tr>

                    <?php if(is_array($data["lesson_config"])): $i = 0; $__LIST__ = $data["lesson_config"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$itm): $mod = ($i % 2 );++$i;?><tr>
                            <td><?php echo ($itm["id"]); ?></td>
                            <td><?php echo ($itm["week_num"]); ?></td>
                            <td><?php echo ($itm["name"]); ?></td>
                            <td><?php echo ($itm["start_time"]); ?></td>
                            <td><?php echo ($itm["end_time"]); ?></td>
                            <td><?php echo ($itm["price"]); ?></td>
                            <td><?php echo ($itm["vip_price_1"]); ?></td>
                            <td><?php echo ($itm["vip_price_2"]); ?></td>
                            <td><?php echo ($itm["vip_price_3"]); ?></td>
                            <td><?php echo ($itm["max_count"]); ?></td>
                            <td><a href="javascript:void(0)" class="itm-edit" val="<?php echo ($itm["id"]); ?>">编辑</a></td>
                            <td><a href="javascript:void(0)" class="itm-del" val="<?php echo ($itm["id"]); ?>">删除</a></td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                </table>
            </div><!-- /.box-body -->

            <div class="box-footer clearfix ">

            </div>
        </div><!-- /.box -->
    </div>

        <div style="clear: both"></div>
    </div>



    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 1.0
        </div>
        <strong>Copyright &copy;  <a href=""><?php echo C('project_name');?></a>.</strong> All rights reserved.
    </footer>

    <div class="am-modal am-modal-confirm" tabindex="-1" id="my-confirm">
        <div class="am-modal-dialog">
            <div class="am-modal-hd"><?php echo C('project_name');?>管理</div>
            <div class="am-modal-bd title" style="font-weight: bold;">

            </div>
            <div class="am-modal-footer">
                <span class="am-modal-btn" data-am-modal-cancel>取消</span>
                <span class="am-modal-btn" data-am-modal-confirm>确定</span>
            </div>
        </div>
    </div>

    <!-- Control Sidebar -->

    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div><!-- ./wrapper -->



<!-- jQuery 2.1.4 -->
<script src="/Public/common/lib/jQuery/jQuery-2.1.4.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="/Public/common/lib/jQueryUI/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

<!-- Bootstrap 3.3.5 -->
<script src="/Public/common/lib/bootstrap/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="/Public/common/lib/morris/morris.js"></script>-->
<!-- Sparkline -->
<script src="/Public/common/lib/sparkline/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="/Public/common/lib/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="/Public/common/lib/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="/Public/common/lib/knob/jquery.knob.js"></script>
<!-- daterangepicker -->
<script src="/Public/common/lib/moment/moment.min.js"></script>
<script src="/Public/common/lib/daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->

<script src="/Public/common/lib/datepicker/bootstrap-datepicker.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="/Public/common/lib/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="/Public/common/lib/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="/Public/common/lib/fastclick/fastclick.min.js"></script>


<!-- AdminLTE App -->
<script src="/Public/admin/js/app.min.js"></script>
<script src="/Public/admin/js/main.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!--<script src="<?php echo $this->resource;?>dist/js/pages/dashboard.js"></script>-->
<!-- AdminLTE for demo purposes -->
<!--<script src="<?php echo $this->resource;?>dist/js/demo.js"></script>-->
<script src="/Public/common/lib/amazeui/amazeui.min.js"></script>


<script src="/Public/common/lib/pinyin.js"></script>
<script src="/Public/common/js/plugin.js"></script>


<script src="/Public/common/js/exif.js"></script>
<script src="/Public/common/js/mega-pixImage.js"></script>
<script src="/Public/common/js/img-tools.js"></script>
<script type="text/javascript" src="/Public/common/js/js/order_tools.js" ></script>

<link href="/Public/common/js/umeditor/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">
<script src="/Public/common/js/umeditor/umeditor.config.js"></script>
<script src="/Public/common/js/umeditor/umeditor.min.js"></script>
<script src="/Public/common/js/umeditor/lang/zh-cn/zh-cn.js"></script>

    <script>
        $(function(){
            $('.itm-del').click(function() {
                var id = $(this).attr('val');

                if (confirm('是否删除该配置?')) {
                    fit.ajax({
                        url:'/admin/manage/del_lesson_config',
                        data:{
                            'id':id
                        },
                        success:function(result){
                            alert(result.msg);

                            window.location.reload();
                        }
                    });
                }
            })

            //编辑课程
            $('.itm-edit').click(function() {
                var id = $(this).attr('val');

                location.href = '/admin/manage/lesson_config_edit?id='+ id;
            });

            $('.add_lesson_config_btn').click(function(){
                location.href = '/admin/manage/lesson_config_edit?id=0';
            });
        });
    </script>

</body>
</html>