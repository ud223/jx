<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8" />
    <title>BttenCMS</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta name="MobileOptimized" content="320">
   
    <!-- BEGIN GLOBAL MANDATORY STYLES -->          
    <link href="/btc/Public/res/admin/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="/btc/Public/res/admin/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/btc/Public/res/admin/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
   
    <!-- BEGIN PAGE LEVEL PLUGIN STYLES --> 
    <link href="/btc/Public/res/admin/assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" type="text/css"/>
    <link href="/btc/Public/res/admin/assets/plugins/jquery-easy-pie-chart/jquery.easy-pie-chart.css" rel="stylesheet" type="text/css"/>
    <link href="/btc/Public/res/admin/assets/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <link href="/btc/Public/res/admin/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
    <link href="/btc/Public/res/admin/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
    <link href="/btc/Public/res/admin/js/kindeditor/themes/default/default.css" rel="stylesheet" type="text/css" />
    <link href="/btc/Public/res/admin/js/kindeditor/plugins/code/prettify.css" rel="stylesheet" type="text/css" />
    <link href="/btc/Public/res/admin/assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css" />
    <link href="/btc/Public/res/admin/assets/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet" type="text/css" />
    
    <link href="/btc/Public/res/admin/assets/plugins/select2/select2_conquer.css" rel="stylesheet" type="text/css" />
    <link href="/btc/Public/res/admin/assets/plugins/data-tables/DT_bootstrap.css" rel="stylesheet" />
    <!-- END PAGE LEVEL PLUGIN STYLES -->
   
    <!-- BEGIN THEME STYLES --> 
    <link href="/btc/Public/res/admin/assets/css/style-conquer.css" rel="stylesheet" type="text/css"/>
    <link href="/btc/Public/res/admin/assets/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="/btc/Public/res/admin/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
    <link href="/btc/Public/res/admin/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="/btc/Public/res/admin/assets/css/pages/tasks.css" rel="stylesheet" type="text/css"/>
    <link href="/btc/Public/res/admin/assets/css/themes/grey.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="/btc/Public/res/admin/assets/css/custom.css" rel="stylesheet" type="text/css"/>
    <link href="/btc/Public/res/admin/assets/plugins/jcrop/css/jquery.Jcrop.min.css" rel="stylesheet"/>
    <link href="/btc/Public/res/admin/assets/css/pages/image-crop.css" rel="stylesheet"/>
    <!-- END THEME STYLES -->

    <link href="/btc/Public/res/admin/css/btten.css?2014-08-21-10" rel="stylesheet" type="text/css"/>

    <link rel="shortcut icon" href="favicon.ico" />
   
    <script src="/btc/Public/res/admin/js/jquery-1.11.1.min.js" type="text/javascript"></script>
    
    <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
    <!-- BEGIN CORE PLUGINS -->   
    <!--[if lt IE 9]>
    <script src="/btc/Public/res/admin/assets/plugins/respond.min.js"></script>
    <script src="/btc/Public/res/admin/assets/plugins/excanvas.min.js"></script> 
    <script src="/btc/Public/res/admin/js/html5.js"></script>
    <![endif]-->   
    <script src="/btc/Public/res/admin/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>

    <!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
    <script src="/btc/Public/res/admin/assets/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/assets/plugins/bootstrap-hover-dropdown/twitter-bootstrap-hover-dropdown.min.js" type="text/javascript" ></script>
    <script src="/btc/Public/res/admin/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>  
    <script src="/btc/Public/res/admin/assets/plugins/jquery.cookie.min.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->

    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="/btc/Public/res/admin/assets/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/assets/plugins/bootstrap-daterangepicker/moment.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/assets/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
    <!-- BEGIN 文件上传 -->
    <script src="/btc/Public/res/admin/assets/plugins/jquery-file-upload/js/vendor/jquery.ui.widget.js"></script>
    <script src="/btc/Public/res/admin/assets/plugins/jquery-file-upload/js/jquery.iframe-transport.js"></script>
    <script src="/btc/Public/res/admin/assets/plugins/jquery-file-upload/js/jquery.fileupload.js"></script>    
    <!-- END PAGE LEVEL SCRIPTS -->     

    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="/btc/Public/res/admin/assets/scripts/app.js?2014-08-21-10" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/js/btten.js?2014-08-21-10" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/js/lhgdialog/lhgdialog.min.js?skin=idialog" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/js/uploadify/jquery.uploadify.min.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/js/kindeditor/kindeditor-min.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/js/kindeditor/lang/zh_CN.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/js/kindeditor/plugins/code/prettify.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/assets/scripts/table-editable.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/assets/plugins/data-tables/jquery.dataTables.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/assets/plugins/data-tables/DT_bootstrap.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/assets/plugins/select2/select2.min.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/assets/plugins/jcrop/js/jquery.color.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/assets/plugins/jcrop/js/jquery.color.js" type="text/javascript"></script>
    <script src="/btc/Public/res/admin/assets/plugins/jcrop/js/jquery.Jcrop.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS --> 
    
    <script src="/btc/Public/res/admin/assets/scripts/form-image-crop.js"></script>
    
    <script>
        var __jsPublic__ = "/btc/Public/res/admin/";
        var _PUBLIC_ = "/btc/Public";
        var _MODULE_ = "/btc/index.php/Admin";
    </script>
</head>
<!-- END HEAD -->

<!-- BEGIN BODY -->
<body class="page-header-fixed page-sidebar-fixed">
    <!-- BEGIN 页头 --> 
    <?php W('Topbar/index');?>
    <!-- END 页头 -->
    
    <!-- BEGIN CONTAINER -->
    <div class="page-container">
        <!-- BEGIN 左侧栏 -->
        <div class="page-sidebar navbar-collapse collapse">
            <!-- BEGIN 菜单 -->
            <?php W('Nav/leftmenu');?>
            <!-- END 菜单 -->
        </div>
        <!-- END 左侧栏 -->
      
        <!-- BEGIN 内容区-->
        <div class="page-content">
            <!-- BEGIN 内容区域头部-->
            <?php W('Nav/menuroot');?>
            <!-- END 内容区域头部-->
            <div class="clearfix"></div>

<!-- BEGIN 主体区域-->
<div class="row">
    <div class="col-md-12">
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption"><i class="icon-reorder"></i>更新系统缓存</div>
            </div>        

            <div class="portlet-body">
                <div class="table-toolbar">
                    <div class="btn-group">
                        <a href="javascript:void(0);" class="btn btn-info" onclick="UpdateCache();"><i class="icon-refresh"></i> 更新系统缓存</a>
                    </div>
                </div>
                <table class="table table-striped table-bordered table-hover" id="table_update_ifr" style="display: none;">
                    <thead>
                       <tr>
                          <th>执行状态</th>
                       </tr>
                    </thead>
                    <tbody>
                        <tr class="odd gradeX">
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- END 主体区域-->

<script>
 function UpdateCache(){
     $('#table_update_ifr tr td').html('');
     $('#table_update_ifr').show();
     $('#table_update_ifr tr td').append('<p><span class="label label-success">开始更新缓存......</span></p>');

     ExeUpdate("SyncSystemMenu");
     ExeUpdate("SyncAppMenu");
     ExeUpdate("SyncAppMenu_DLL");

     $('#table_update_ifr tr td').append('<p><span class="label label-success">缓存更新完成！</span></p>');
  }

 function ExeUpdate(_FunName){
     $.ajax({
          type: "POST",
          url:  "/btc/index.php/Admin/System/update_cache",
          data: {"act":"update", "fun":_FunName},
          dataType: "json",
          timeout : 100000,
          cache: false,
          async: false,
          success: function(result){
              if(btten.IsN(result)){
                  $('#table_update_ifr tr td').append('<p><span class="label label-danger">没有获取到服务器返回消息！</span></p>');
                  return;
              }

              if(!result.status){
                  $('#table_update_ifr tr td').append('<p><span class="label label-danger">'+ result.info +'</span></p>');
              }else{
                  $('#table_update_ifr tr td').append('<p><span class="label label-info">'+ result.info +'</span></p>');
              }
          },
          error: function (xhr, ajaxOptions, thrownError) {
              $('#table_update_ifr tr td').append('<p><span class="label label-danger">无法加载请求的内容！</span></p>');
          }
      });
 }
</script>

      </div>
      <!-- BEGIN 内容区-->
   </div>
   <!-- END CONTAINER -->

    <!-- BEGIN FOOTER -->
    <div class="footer">
        <div class="footer-inner"><?php echo (C("support")); ?></div>
        <div class="footer-tools">
            <span class="go-top">
                <i class="icon-angle-up"></i>
            </span>
        </div>
    </div>
    <!-- END FOOTER -->
    
     <script>
     jQuery(document).ready(function() {    
         App.init(); // initlayout and core plugins
     });
     </script>
    <!-- END JAVASCRIPTS -->
    
   </body>
</html> 

</body>
</html>