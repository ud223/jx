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
        <div class="tt1">Project 0/1·最新课表</div>
        <div class="tt2">Project 0/1 Latest Schedule</div>
        <!--<a id="get_vip" class="colorbtn colorshadow vipbadge" href="/show/gt_vipcard">成为VIP</a>-->
    </div>

    <div class="btmenu">
        <div class="td tapquick selected" url="#">
            <div class="tc1">小组课</div>
            <div class="tc2">Group Training</div>
        </div>
        <div class="td tapquick" url="/show/gt_pt">
            <div class="tc1">私教课</div>
            <div class="tc2">Personal Training</div>
        </div>
        <div class="td tapquick" url="/show/me_profile">
            <div class="tc1">个人中心</div>
            <div class="tc2">My Profile</div>
        </div>
    </div>

    <style>
        body {
            padding-top:110px;
        }
    </style>
    <div class="weektbs">
        <div class="td date selected" val="<?php echo date("Y-m-d",strtotime("+1 day")) ?>">
            <div class="day"><?php echo date("D",strtotime("+1 day")) ?></div>
            <div class="date"><?php echo date("m.d",strtotime("+1 day")) ?></div>
        </div>
        <div class="td date" val="<?php echo date("Y-m-d",strtotime("+2 day")) ?>">
            <div class="day"><?php echo date("D",strtotime("+2 day")) ?></div>
            <div class="date" val="<?php echo date("Y-m-d",strtotime("+2 day")) ?>"><?php echo date("m.d",strtotime("+2 day")) ?></div>
        </div>
        <div class="td date" val="<?php echo date("Y-m-d",strtotime("+3 day")) ?>">
            <div class="day"><?php echo date("D",strtotime("+3 day")) ?></div>
            <div class="date"><?php echo date("m.d",strtotime("+3 day")) ?></div>
        </div>
        <div class="td date" val="<?php echo date("Y-m-d",strtotime("+4 day")) ?>">
            <div class="day"><?php echo date("D",strtotime("+4 day")) ?></div>
            <div class="date"><?php echo date("m.d",strtotime("+4 day")) ?></div>
        </div>
    </div>
    <div class="gallitms">
        <div id="lesson-list">

        </div>

        <div class="nomore">
            没有更多了
        </div>
    </div>
    <div id="lesson-node" style="display: none">
        <div class="gallitm tapquick lesson-itm node">
            <div class="whitesharp">
                <div class="mt1">
                    <span class="iconfont icon-time"></span>
                    <span class="time"></span>
                </div>
                <div class="mt2 lesson-name">

                </div>
                <div class="mt3 lesson-name-en">

                </div>

                <div class="mt4 clearfix" style="margin-bottom:5px;">
                    <div class="gbe1">
                        体验价：<span class="gbe2">￥</span><span class="price"></span>
                    </div>

                </div>

                <!--<div class="mt4 clearfix" style="margin-bottom:5px;">-->
                    <!--<div class="gbe1">-->
                        <!--体验价：<span class="gbe2">￥</span><span class="price"></span>-->
                    <!--</div>-->
                    <!--<div class="gbe1 vip">-->
                        <!--金卡价：<span class="gbe2">￥</span><span class="vip-price1"></span>-->
                    <!--</div>-->
                <!--</div>-->
                <!--<div class="mt4 clearfix">-->
                    <!--<div class="gbe1 vip">-->
                        <!--白金卡价：<span class="gbe2">￥</span><span class="vip-price2"></span>-->
                    <!--</div>-->
                    <!--<div class="gbe1 vip">-->
                        <!--黑卡价：<span class="gbe2">￥</span><span class="vip-price3"></span>-->
                    <!--</div>-->
                <!--</div>-->
                <div class="bdibt submit">
                    <div class="inner">
                        <span class="yibt1">预约</span>
                        <span class="yibt2">Booking</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="lesson_id" value="">



    <script>
        $(function(){
            var sel_date = '<?php echo date("Y-m-d",strtotime("+1 day")) ?>';

            localStorage.setItem('order_date', sel_date);

            $('.td.date').click(function() {
                $('.td.date').removeClass('selected');

                $(this).addClass('selected');
            })

            $('.td.date').click(function() {
                var date = $('.td.date.selected').attr('val');

                localStorage.setItem('order_date', date);

                loadData(date);
            })

            User.loadData = function (user) {
                if (user.amount.toString() != '0.00') {
                    $('#get_vip').html('充值');
                }
            };

            User.load();

            loadData(sel_date);
        });

        function loadData(date) {
            var day = new Date(date);

            var data = { 'week_num': day.getDay() };

            fit.ajax({
                url:'/api/getLessonByWeekNum',
                data:data,
                success:function(result){
                    if (result.code == 200) {
                        $('#lesson-list').empty();
//                        alert(JSON.stringify(result));

                        $.each(result.data, function() {
                            var node = createNode(this);

                            $('#lesson-list').append(node);
                        })
                    }
                }
            });
        }

        function createNode(item) {
            var node = $('#lesson-node').find('.node').clone();

            node.attr('url', '/show/gt_course?id=' + item.lesson_config_id);
            node.attr('val', item.lesson_id);
            node.css("backgroundImage","url(<?php echo UPLOAD_DIR; ?>"+ item.show_pic +")");

            node.find('.time').html(item.start_time + "-" + item.end_time)
            node.find('.lesson-name').html(item.name);
            node.find('.lesson-name-en').html(item.name_en);
            node.find('.price').html(item.price);
            node.find('.vip-price1').html(item.vip_price_1);
            node.find('.vip-price2').html(item.vip_price_2);
            node.find('.vip-price3').html(item.vip_price_3);

            node.click(function() {
                location.href = $(this).attr('url') + '&date='+ $('.td.date.selected').attr('val');
            });

            node.find('.submit').click(function() {
                Order.success = function(id) {
                    location.href = '/show/gt_orderdetail?id=' + id;
                }

                $('#lesson_id').val(item.lesson_config_id);

                Order.validate();

                Order.create(1, 0, 0);

                return false;
            });

            return node;
        }
    </script>

</body>
<script type="text/javascript">
    userLogin();
</script>
</html>