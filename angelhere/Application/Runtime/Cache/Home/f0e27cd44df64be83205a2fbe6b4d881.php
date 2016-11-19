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
        <div class="tt1">私教课</div>
        <div class="tt2">Personal Training</div>
        <!--<a class="colorbtn colorshadow vipbadge" href="/show/gt_vipcard">成为VIP</a>-->
    </div>

    <div class="btmenu">
        <div class="td tapquick" url="/show/home">
            <div class="tc1">小组课</div>
            <div class="tc2">Group Training</div>
        </div>
        <div class="td tapquick selected" url="#">
            <div class="tc1">私教课</div>
            <div class="tc2">Personal Training</div>
        </div>
        <div class="td tapquick" url="/show/me_profile">
            <div class="tc1">个人中心</div>
            <div class="tc2">My Profile</div>
        </div>
    </div>
    <style>
        .biggallcoac {

        }
        .biggallcoac:after {
            content:' ';
            clear: both;
            display:block;
        }
        .biggallcoac .itm {
            padding:20px 0;
            width: 50%;
            display:inline-block;
            float:left;
            background:#FFF;
            border-bottom:0.5px solid #DDD;
            box-sizing: border-box;
            text-align:center;
            height:260px;
            box-sizing: border-box;
        }
        .biggallcoac .itm:nth-child(odd) {
            border-right: 0.5px solid #DDD;
        }
        .biggallcoac .itm .crv {
            position:absolute;
            left:0;
            top:0;
            width:100%;
            height:100%;
            box-shadow: inset 0 1px 35px rgba(0,0,0,.07);
            z-index:10;
        }
        .biggallcoac .itm:active .crv {
            box-shadow: inset 0 1px 50px rgba(0,0,0,.15);
        }
        .biggallcoac .itm .img {
            width:100%;
            height:150px;
            background-size: contain;
            background-position: center bottom;
            background-repeat: no-repeat;
            background-color: #FFF;
            margin-bottom:10px;
        }
        .biggallcoac .itm .name {
            text-align:center;
            height:20px;
            line-height:20px;
            font-size:16px;
            position:relative;

        }
        .biggallcoac .itm .jobtitle {
            border:1px solid #F0F0F0;
            color:#aaa;
            display:inline-block;
            padding:0 10px;
            border-radius:2px;
            text-align:center;
            font-size:10px;
            margin-bottom:5px;
        }
        .biggallcoac .itm .desc {
            font-size:10px;
            color:#f9b126;
        }
    </style>
    <div style="padding:20px;background:#fbfbfb;border-bottom:0.5px solid #DDD">
        <div style="border-left:5px solid #FFD800;padding-left:20px;">
            私教课
        </div>
    </div>
    <div class="biggallcoac">
        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$itm): $mod = ($i % 2 );++$i;?><div class="itm tapquick" url="/show/gt_ptdetail?id=<?php echo ($itm["config_id"]); ?>">
                <div class="crv"></div>
                <div class="img" style="background-image:url(<?php echo UPLOAD_DIR; echo ($itm["show_pic"]); ?>);"></div>
                <!--<div class="name"><?php echo ($itm["Name"]); ?></div>-->
                <div class="jobtitle"><?php echo ($itm["title"]); ?></div>
                <div class="desc"><?php echo ($itm["Name"]); ?></div>
                <div><span class="font10px">￥</span><?php echo ($itm["price"]); ?></div>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
        <!--<div class="itm tapquick" url="gt_ptdetail.html">-->
            <!--<div class="crv"></div>-->
            <!--<div class="img" style="background-image:url(/Public/common/js/img/coach/john.jpg);"></div>-->
            <!--<div class="name">John Graham</div>-->
            <!--<div class="jobtitle">国际知名教练</div>-->
            <!--<div class="desc">力量 / 健美 / 瑜伽 / 搏击</div>-->
        <!--</div>-->
        <!--<div class="itm tapquick" url="gt_ptdetail.html">-->
            <!--<div class="crv"></div>-->
            <!--<div class="img" style="background-image:url(img/coach/k1.jpg);"></div>-->
            <!--<div class="name">John Graham</div>-->
            <!--<div class="jobtitle">国际知名教练</div>-->
            <!--<div class="desc">拳击 / 自由搏击 / TRX</div>-->
        <!--</div>-->
        <!--<div class="itm tapquick" url="gt_ptdetail.html">-->
            <!--<div class="crv"></div>-->
            <!--<div class="img" style="background-image:url(img/coach/k2.jpg);"></div>-->
            <!--<div class="name">John Graham</div>-->
            <!--<div class="jobtitle">国际知名教练</div>-->
            <!--<div class="desc">美体 / 塑性 / TRX</div>-->
        <!--</div>-->
        <!--<div class="itm tapquick" url="gt_ptdetail.html">-->
            <!--<div class="crv"></div>-->
            <!--<div class="img" style="background-image:url(img/coach/k4.jpg);"></div>-->
            <!--<div class="name">John Graham</div>-->
            <!--<div class="jobtitle">国际知名教练</div>-->
            <!--<div class="desc">美体 / 塑性 / TRX</div>-->
        <!--</div>-->
        <!--<div class="itm tapquick" url="gt_ptdetail.html">-->
            <!--<div class="crv"></div>-->
            <!--<div class="img" style="background-image:url(img/coach/nothing.jpg);"></div>-->
            <!--<div class="name">John Graham</div>-->
            <!--<div class="jobtitle">国际知名教练</div>-->
            <!--<div class="desc">美体 / 塑性 / TRX</div>-->
        <!--</div>-->
        <!--<div class="itm tapquick" url="gt_ptdetail.html">-->
            <!--<div class="crv"></div>-->
            <!--<div class="img" style="background-image:url(img/coach/nothing.jpg);"></div>-->
            <!--<div class="name">John Graham</div>-->
            <!--<div class="jobtitle">国际知名教练</div>-->
            <!--<div class="desc">美体 / 塑性 / TRX</div>-->
        <!--</div>-->
    </div>



    <script>
        $(function(){
            User.load();
        });
    </script>

</body>
<script type="text/javascript">
    userLogin();
</script>
</html>