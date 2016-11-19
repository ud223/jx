<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>沸腾空间</title>
    
<link rel="stylesheet" href="/Public/common/js/css/base.css" />
<link rel="stylesheet" href="/Public/common/js/css/global.css" />
<script type="text/javascript" src="/Public/common/js/framework.js"></script>
<script type="text/javascript" src="/Public/common/js/global.js" ></script>
<script src="/Public/common/js/plugin.js"></script>
<script type="text/javascript" src="/Public/common/js/js/user_tools.js" ></script>
<script type="text/javascript" src="/Public/common/js/js/order_tools.js" ></script>
</head>

<body ontouchstart="">

    <div class="tpcbanner">
        <div class="tt1">修改个人资料</div>
        <div class="tt2">Edit Profile</div>
        <span class="iconfont icon-angleleft historyback" parentUrl="/show/ me_profilel"></span>
    </div>
    <style>
        .migrow {
            margin-bottom:10px;
            position:relative;
        }
        .migrow .tt {
            height:20px;
            line-height:20px;
            display:inline-block;
            position:absolute;
            left:15px;
            top:50%;
            margin-top:-10px;
            z-index:10;
            color:#bbb;
            font-size:14px;
        }
        .migrow input[type="text"], .migrow input[type="tel"] {
            padding-left:80px;
        }
    </style>
    <div class="pad-20">
        <div class="migrows">
            <div class="migrow">
                <span class="tt">昵称</span>
                <input id="name" type="text" placeholder="请输入昵称" value="" />
            </div>
            <div class="migrow">
                <span class="tt">手机号</span>
                <input id="tel" type="text" placeholder="请输入手机号" value="" />
            </div>
            <div class="migrow">
                <input type="text" placeholder="请输入验证码" style="padding-left:15px;" />
                <button class="getvcode">获取验证码</button>
            </div>

            <button class="whitebtn full" onclick="$.toastMsg('错误信息')">保存信息</button>
        </div>
    </div>



    <script>
        $(function(){
            User.loadData = function () {
                $('#name').val(User.data.Name);
                $('#tel').val(User.data.Tel);
            };

            User.load();

            $('.whitebtn').click(function() {
                var name = $('#name').val();
                var tel = $('#tel').val();

                User.update(name, tel);
            })
        });
    </script>

</body>

</html>