<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 2 | Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="/Public/common/lib/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link href="/Public/common/lib/adminlte/css/font-awesome.min.css" rel="stylesheet">

    <link href="//cdn.bootcss.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet">
    <!-- Theme style -->
    <link rel="stylesheet" href="/Public/common/lib/adminlte/css/AdminLTE.min.css">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link rel="stylesheet" href="/Public/admin/css/admin.css">
  </head>
  <body class="hold-transition login-page" style="padding-top: 15em;">
    <div class="login-box" style="margin: 0 auto;">
      <div class="login-logo">
        <a href=""><b><?php echo C('project_name');?></b>管理后台</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg"></p>
        <form action="<?php echo U('login');?>" method="post">
          <div class="form-group has-feedback">
            <input  class="form-control" placeholder="用户名" name="username">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="密码" name="password">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>


        <div class="col-xs-6" style="margin: auto">
          <button type="submit" class="btn btn-primary btn-block btn-flat">登录</button>
        </div>
          <div class="clearfix"></div>
        </form>


      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.4 -->
    <script src="/Public/common/lib/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="/Public/common/lib/bootstrap/js/bootstrap.min.js"></script>


  </body>
</html>