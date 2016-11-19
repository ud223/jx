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
            <div class="am-modal-hd"><?php echo ($title["text"]); ?></div>
            <div class="am-modal-bd">
                <div style="width: 60%;margin: auto;">
                    <div class="input-group">
                        <span class="input-group-addon">中文名称</span>
                        <input type="text" class="form-control" placeholder="请输入课程中文名称..." name="name_field" id="name_field" value="<?php echo ($data[lesson]["name"]); ?>" maxlength="50">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">英文名称</span>
                        <input type="text" class="form-control" placeholder="请输入课程英文名称..." name="name_en_field" id="name_en_field" value="<?php echo ($data[lesson]["name_en"]); ?>" maxlength="50">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">地址</span>
                        <input type="text" class="form-control" placeholder="请输入地址..." name="local_field" id="local_field" value="<?php echo ($data[lesson]["local"]); ?>" maxlength="100">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">展示图片</span>
                        <input type="text" class="form-control" readonly name="tmp_show" id="tmp_show" value="请点击选择上传的图片..." >
                    </div>
                    <div class="input-group">
                        <?php if ($data['lesson']['show_pic']) :?>
                            <img id="pre-image" src="<?php echo UPLOAD_DIR . $data['lesson']['show_pic'] ?>" />
                        <?php else :?>
                            <img id="pre-image" style="width:100px; height: 100px" />
                        <?php endif; ?>
                        <input type="file"  accept="image/*" id="upload_show_pic" style="display: none">
                        <input id="old_show_pic" name="old_show_pic" type="text" value="<?php echo ($data['lesson']['show_pic']); ?>" style="display: none">
                        <input id="show_pic_field" name="show_pic_field" type="text" value="" style="display: none">
                        <canvas id="upload-canvas" style="display:none"></canvas>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">课程描述</span>
                        <script type="text/plain" id="editor_remark" name="editor_remark" style="width:100%;height:240px;">
                            <p>请输入课程描述</p>
                        </script>
                    </div>
                </div>
            </div>
            <div class="am-modal-footer">
                <input type="hidden" id="lesson_id" value="<?php echo ($data['lesson']['id']); ?>">
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
        var tmp_pic = null;

        $(function(){
            //实例化编辑器
            var um = UM.getEditor('editor_remark');

            um.addListener('blur',function(){
                $('#focush2').html('编辑器失去焦点了')
            });
            um.addListener('focus',function(){
                $('#focush2').html('')
            });

            um.setContent('<?php echo ($data[lesson]["remark"]); ?>');

            $('#tmp_show').click(function() {
                $('#upload_show_pic').click();
            })
            //file控件选择改变 同步修改页面上image标签
            $('#upload_show_pic').change(function () {
                var file = this.files[0];

                tmp_pic = file;

                ImageOpt(file, 'pre-image');
            });

            $('#btn_save').click(function() {
                if ( $('#upload_show_pic').val() == '') {
                    saveItem();
                }
                else {
                    getImageCode();
                }
            })

            $('#btn_return').click(function(){
                location.href = '/admin/manage/lesson_list';
            });
        });

        function saveItem() {
            var id = $('#lesson_id').val();

            var name = $('#name_field').val();
            var name_en = $('#name_en_field').val();
            var local = $('#local_field').val();
            var old_show_pic = $('#old_show_pic').val();
            var show_pic = $('#show_pic_field').val();
            var html_remark = UM.getEditor('editor_remark').getContent();

            var data = { 'id':id, 'name':name, 'name_en':name_en, 'local':local, 'remark':html_remark, 'old_show_pic':old_show_pic, 'show_pic':show_pic };

//            alert(JSON.stringify(data));
//            return;

            if (confirm('是否保存该课程?')) {
                fit.ajax({
                    url:'/admin/manage/save_lesson',
                    data:data,
                    success:function(result){
                        alert(result.msg);

                        if (result.code == 200) {
                            location.href = '/admin/manage/lesson_edit?id=' + result.data;
                        }
                    }
                });
            }
        }

        function getImageCode() {
            var image = document.getElementById('pre-image');
            var is_def = false;

            EXIF.getData(image, function () {
                if (EXIF.getTag(image, 'Orientation') == 6) {
                    is_def = true;
                }
                //使用插件初始化图片
                var mpImg = new MegaPixImage(tmp_pic);
                var option = null;

                if (is_def)
                    option = { maxWidth: image.width * 2, maxHeight: image.height * 2, orientation: 6 };
                else
                    option = { maxWidth: image.width * 2, maxHeight: image.height * 2, quality: 0.5 };

                var canvas = document.getElementById('upload-canvas');

                mpImg.render(canvas, option, function () {
                    var base64 = canvas.toDataURL('image/jpeg');

                    $('#show_pic_field').val(base64);

                    saveItem();
                });
            });
        }
    </script>

</body>
</html>