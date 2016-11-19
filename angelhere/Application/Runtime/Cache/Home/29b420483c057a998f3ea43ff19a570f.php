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
</head>

<body ontouchstart="">

    <div class="tpcbanner">
        <div class="tt1">沸腾空间·我</div>
        <div class="tt2">My Profile</div>
        <a class="colorbtn colorshadow vipbadge" href="gt_vipcard.html">成为VIP</a>
    </div>
    <div class="btmenu">
        <div class="td tapquick" url="gt_calendar.html">
            <div class="tc1">小组课</div>
            <div class="tc2">Group Teach</div>
        </div>
        <div class="td tapquick">
            <div class="tc1">预约私教</div>
            <div class="tc2">Private Teach</div>
        </div>
        <div class="td tapquick selected" url="me_profile.html">
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
        <div class="profc02">
            <div class="imvip">VIP</div>
        </div>
        <a href="me_edit.html" class='iconfont icon-pen profc03'></a>
    </div>
    <div class="profc04">

        <div class="profc05">
            <div class="profc06">
                <span>彭于晏</span>
            </div>
            <!--<div>
                <span class="colorbtn profc07">训练新手</span>
            </div>-->
        </div>
        <div class="profc08">
            <div class="itm tapquick" url="me_orders.html">我的预约</div>
            <div class="itm tapquick" url="me_finance.html">我的账户</div>
            <div class="itm">我的教练</div>
            <div class="itm">站内信</div>

        </div>
        <div class="bcvipc">成为VIP，享受特价</div>
    </div>




    <script>
        $(function(){

        });
    </script>

</body>

</html>