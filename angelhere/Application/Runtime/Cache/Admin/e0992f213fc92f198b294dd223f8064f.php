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
                <h3 class="box-title">课程配置</h3>
            </div>
            <div class="am-modal-hd">课程配置</div>
            <div class="am-modal-bd">
                <div style="width: 60%;margin: auto;">
                    <div class="input-group">
                        <span class="input-group-addon">课程</span>
                        <select id="lesson_list" class="form-control">
                            <?php foreach ($data['lesson'] as $itm) :?>
                                <?php if ($data['lesson_config']['lesson_id'] == $itm['id']) :?>
                                    <option value="<?php echo $itm['id'] ?>" selected="selected"><?php echo $itm['name'] ?></option>
                                <?php else :?>
                                    <option value="<?php echo $itm['id'] ?>"><?php echo $itm['name'] ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">星期</span>
                        <select id="week"  class="form-control">
                            <option value="1">星期一</option>;
                            <option value="2">星期二</option>;
                            <option value="3">星期三</option>;
                            <option value="4">星期四</option>;
                            <option value="5">星期五</option>;
                            <option value="6">星期六</option>;
                            <option value="7">星期七</option>;
                        </select>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">开始时间</span>
                        <input type="time" class="form-control" min="8" placeholder="请输入开始时间..." name="start_time_field" id="start_time_field" value="<?php echo ($data['lesson_config']["start_time"]); ?>">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">结束时间</span>
                        <input type="time" class="form-control" max="24" placeholder="请输入结束时间..." name="end_time_field" id="end_time_field" value="<?php echo ($data['lesson_config']["end_time"]); ?>">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">试练价格</span>
                        <input type="number" class="form-control" min="1" placeholder="请输入试练价格." name="price_field" id="price_field" value="<?php echo ($data['lesson_config']["price"]); ?>">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">金卡价格</span>
                        <input type="number" class="form-control" max="1" placeholder="请输入金卡价格..." name="vip_price_1_field" id="vip_price_1_field" value="<?php echo ($data['lesson_config']["vip_price_1"]); ?>">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">白金卡价格</span>
                        <input type="number" class="form-control" max="1" placeholder="请输入白金卡价格..." name="vip_price_2_field" id="vip_price_2_field" value="<?php echo ($data['lesson_config']["vip_price_2"]); ?>">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">黑卡价格</span>
                        <input type="number" class="form-control" max="1" placeholder="请输入黑卡价格..." name="vip_price_3_field" id="vip_price_3_field" value="<?php echo ($data['lesson_config']["vip_price_3"]); ?>">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">最大人数</span>
                        <input type="number" class="form-control" max="3" placeholder="请输入最大人数..." name="max_count_field" id="max_count_field" value="<?php echo ($data['lesson_config']["max_count"]); ?>">
                    </div>
                </div>
            </div>
            <div class="am-modal-footer">
                <input type="hidden" id="lesson_config_id" value="<?php echo ($data['lesson_config']["id"]); ?>">
                <span id="btn_save" class="am-modal-btn">提交</span>
                <span id="btn_return" class="am-modal-btn" >返回</span>
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
            $('#btn_save').click(function() {
                saveItem();
            })

            $('#btn_return').click(function(){
                location.href = '/admin/manage/lesson_config_list';
            });
        });

        function saveItem() {
            var id = $('#lesson_config_id').val();

            var week_num = $('#week').val();
            var lesson_id = $('#lesson_list').val();
            var start_time = $('#start_time_field').val();
            var end_time = $('#end_time_field').val();
            var price = $('#price_field').val();
            var vip_price_1 = $('#vip_price_1_field').val();
            var vip_price_2 = $('#vip_price_2_field').val();
            var vip_price_3 = $('#vip_price_3_field').val();
            var max_count = $('#max_count_field').val();

            if (!start_time) {
                alert('请输入开始时间!');

                return;
            }

            if (!end_time) {
                alert('请输入结束时间!')

                return;
            }

            if (!price) {
                alert('请输入试练价格!')

                return;
            }

            if (!vip_price_1) {
                alert('请输入vip价格!')

                return;
            }

            var data = { 'id':id, 'lesson_id':lesson_id, 'start_time': start_time, 'end_time': end_time, 'price': price, 'vip_price_1': vip_price_1, 'vip_price_2':vip_price_2, 'vip_price_3':vip_price_3, 'max_count': max_count, 'week_num': week_num };

//            alert(JSON.stringify(data));
//            return;

            if (confirm('是否保存该课程配置?')) {
                fit.ajax({
                    url:'/admin/manage/save_lesson_config',
                    data:data,
                    success:function(result){
                        alert(result.msg);

                        if (result.code == 200) {
                            location.href = '/admin/manage/lesson_config_list';
                        }
                    }
                });
            }
        }
    </script>

</body>
</html>