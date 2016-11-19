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

    <div class="tpcbanner">
        <div class="tt1">我的账户</div>
        <div class="tt2">Account</div>
        <span class="iconfont icon-angleleft historyback" parentUrl="/show/me_profile"></span>
    </div>

    <div class="myacnthd">
        <div class="tt">我的余额</div>
        <div class="ct">
            <span class="sm">￥</span>
            <span id="my-account" class="big">0.00</span>
            <span class="sm">元</span>
        </div>
    </div>

    <!--有数据时显示列表-->
    <div class="pad-20">
        <div class="ftlistbox">
            <!--<div class="itm">-->
                <!--<div class="t1">-->
                    <!--<span>-￥180元</span>-->
                    <!--<span class="mcit">余额:470</span>-->
                <!--</div>-->
                <!--<div class="t2">-->
                    <!--预约成功：恢复状态训练-->
                <!--</div>-->
                <!--<div class="rtb">2016.5.7 12:43:12</div>-->
            <!--</div>-->
            <!--<div class="itm">-->
                <!--<div class="t1">-->
                    <!--<span>-￥180元</span>-->
                    <!--<span class="mcit">余额:470</span>-->
                <!--</div>-->
                <!--<div class="t2">-->
                    <!--预约成功：恢复状态训练-->
                <!--</div>-->
                <!--<div class="rtb">2016.5.7 12:43:12</div>-->
            <!--</div>-->
            <!--<div class="itm charged">-->
                <!--<div class="t1">-->
                    <!--<span>+￥5000元</span>-->
                    <!--<span class="mcit">余额:5470</span>-->
                <!--</div>-->
                <!--<div class="t2">-->
                    <!--充值成功-->
                <!--</div>-->
                <!--<div class="rtb">2016.5.7 12:43:12</div>-->
            <!--</div>-->
            <!--<div class="itm">-->
                <!--<div class="t1">-->
                    <!--<span>-￥180元</span>-->
                    <!--<span class="mcit">余额:470</span>-->
                <!--</div>-->
                <!--<div class="t2">-->
                    <!--预约成功：恢复状态训练-->
                <!--</div>-->
                <!--<div class="rtb">2016.5.7 12:43:12</div>-->
            <!--</div>-->
        </div>
    </div>





    <script>
        $(function(){
            User.loadData = function (user) {
                $('#my-account').html(user.amount);
            };

            User.load();
        });
    </script>

</body>
<script type="text/javascript">
    userLogin();
</script>
</html>