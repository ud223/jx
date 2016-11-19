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
        <div class="tt1">Project 0/1·我</div>
        <div class="tt2">My Profile</div>
        <!--<a id="get_vip" class="colorbtn colorshadow vipbadge" href="/show/gt_vipcard">成为VIP</a>-->
    </div>
    <div class="btmenu">
        <div class="td tapquick" url="/show/home">
            <div class="tc1">小组课</div>
            <div class="tc2">Group Training</div>
        </div>
        <div class="td tapquick" url="/show/gt_pt">
            <div class="tc1">私教课</div>
            <div class="tc2">Personal Training</div>
        </div>
        <div class="td tapquick selected" url="#">
            <div class="tc1">个人中心</div>
            <div class="tc2">My Profile</div>
        </div>
    </div>

    <style>
        body:after {
            display: block;
            content: ' ';
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            background: url(/Public/common/js/img/profilebg.jpg) center bottom no-repeat;
            background-size: cover;
        }
    </style>
    <div class="profc01">
        <div id="head_pic" class="profc02">
            <div class="imvip" style="display: none">VIP</div>
        </div>
        <a href="/show/me_edit" class='iconfont icon-pen profc03'></a>
    </div>
    <div class="profc04">
        <div class="profc05">
            <div class="profc06">
                <span id="name"></span>
            </div>
            <!--<div>
                <span class="colorbtn profc07">训练新手</span>
            </div>-->
        </div>
        <div class="profc08">
            <div class="itm tapquick" url="/show/me_orders">我的预约</div>
            <div class="itm tapquick" url="/show/me_finance">我的账户</div>
            <!--<div class="itm">我的教练</div>-->
            <!--<div class="itm">站内信</div>-->
            <div class="itm tapquick" url="/show/gt_intro">参观PROJECT 0/1</div>
        </div>
        <!--<div id="not-vip" class="bcvipc tapquick" url="/show/gt_vipcard" style="display: none">成为VIP，享受特价</div>-->
        <!--<div id="is-vip" class="bcvipc tapquick vipcharge" url="/show/gt_charge" style="display: none">VIP充值</div>-->
    </div>




    <script>
        $(function(){
            User.loadData = function (user) {
                $('#name').html(user.Name);
                $('#head_pic').css("backgroundImage","url("+ user.HeadPic +")");

                if (user.amount == '0.00') {
                    $('#not-vip').show();
                }
                else {
                    $('#get_vip').html('充值');
                    $('.imvip').show();
                    $('#is-vip').show();
                }
            };

            User.load();
        });
    </script>

</body>
<script type="text/javascript">
    userLogin();
</script>
</html>