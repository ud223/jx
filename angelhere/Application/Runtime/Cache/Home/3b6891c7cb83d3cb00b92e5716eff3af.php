<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Project 0/1</title>
    
<link rel="stylesheet" href="/Public/common/js/css/base.css" />
<link rel="stylesheet" href="/Public/common/js/css/global.css" />
<script type="text/javascript" src="/Public/common/js/framework.js"></script>
<script type="text/javascript" src="/Public/common/js/global.js?v=1.000001" ></script>
<script src="/Public/common/js/plugin.js"></script>
<script type="text/javascript" src="/Public/common/js/js/user_tools.js" ></script>
<script type="text/javascript" src="/Public/common/js/js/order_tools.js" ></script>
<script type="text/javascript" src="/Public/common/js/js/wx.js" ></script>
</head>

<body ontouchstart="">

    <div style="padding:10px;text-align:center;background:rgba(255,216,0,.6);color:#333;bottom:0;left:0;width:100%;position: fixed;">
        正在进入 Project 01 ...
    </div>
    <style>
        body {
            padding-top:200px;
        }
        body:after {
            background-image:url(/Public/common/js/img/entrybg.jpg);
        }
        #ft_waitinglayer {
            background:none !important;
        }
    </style>



    <script>
        $(function(){
            waiting();
            //测试代码
            localStorage.setItem('user_id', '20');

            var user_id = localStorage.getItem('user_id');

            if (user_id) {
                location.href = '/show/gt_pt';
            }
        })
    </script>

</body>
<script type="text/javascript">
    userLogin();
</script>
</html>