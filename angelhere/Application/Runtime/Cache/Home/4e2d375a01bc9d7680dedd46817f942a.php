<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Project 0/1</title>
    <link href="/Public/coach/css/global.css" rel="stylesheet">
<script type="text/javascript" src="/Public/coach/js/jquery-1.11.0.js"></script>
<script type="text/javascript" src="/Public/coach/js/jquery-mobile.js"></script>
<script type="text/javascript" src="/Public/coach/js/global.js"></script>
<script type="text/javascript" src="/Public/common/js/framework.js"></script>
<script type="text/javascript" src="/Public/common/js/global.js?v=1.000001" ></script>
<script src="/Public/common/js/plugin.js"></script>
<script type="text/javascript" src="/Public/coach/js/swipe.js"></script>
<script type="text/javascript" src="/Public/common/js/js/wx.js" ></script>
</head>

<body ontouchstart="">

    <!--<div class="menu-fix">-->
        <!--&lt;!&ndash;<div class="menu-fix-itm cd_hottap grid3" hottap="p10_me.html">个人中心</div>&ndash;&gt;-->
        <!--&lt;!&ndash;<div class="menu-fix-itm cd_hottap selected grid1" hottap="p28_teacher.html">教学中心</div>&ndash;&gt;-->
    <!--</div>-->

    <div class="p28-tt1">
        <div class="teacherbag">“Project 01”认证老师</div>
    </div>

    <!--<div class="blackrowgall tran unclickable">-->
        <!--<div class="row actbtn">-->
            <!--<span class="tt">我的总收入</span>-->
            <!--<div class="holder">-->
                <!--<span class="gold">￥7889</span>-->
            <!--</div>-->
        <!--</div>-->
        <!--<div class="row actbtn">-->
            <!--<span class="tt">未提现金额</span>-->
            <!--<div class="holder">-->
                <!--<span class="gold">￥209</span>-->
            <!--</div>-->
        <!--</div>-->
    <!--</div>-->



    <div class="blackrowgall">
        <div class="row actbtn cd_hottap" hottap="/home/coach/my_lesson">
            <span class="tt">我的授课</span>
            <div class="holder arr">
                <img src="/Public/coach/img/arr-r.png" class="icoin" />
            </div>
        </div>

        <div class="row actbtn cd_hottap" hottap="/home/coach/my_calendar">
            <span class="tt">日程表</span>
            <div class="holder arr">
                <img src="/Public/coach/img/arr-r.png" class="icoin" />
            </div>
        </div>
        <div class="row actbtn cd_hottap" hottap="/home/coach/my_week">
            <span class="tt">我的周工作模版</span>
            <div class="holder arr">
                <img src="/Public/coach/img/arr-r.png" class="icoin" />
            </div>
        </div>
    </div>

    <div class="bt-distance"></div>



    <script>
        $(function() {
//            $('#didntsettel').tap(function(){
//                $('#pp-change-tel-btn').tap();
//            });

            $('.cd_hottap').click(function() {
                var url = $(this).attr('hottap');

                if (url) {
                    location.href = url;
                }
            })
        });
    </script>

</body>
<script type="text/javascript">
    userLogin();
</script>
</html>