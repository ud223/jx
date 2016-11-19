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
        <div class="tt1">我的预约</div>
        <div class="tt2">My Orders</div>
        <span class="iconfont icon-angleleft historyback" parentUrl="/show/me_profile?back=1"></span>
    </div>
    <!--没有数据时显示-->
    <div class="pad-20 emptybox">
        <span class="iconfont icon-empty"></span>
        <div class="txt">您还没有预约</div>
        <div>
            <button id="go_home" class="colorbtn normalbtn">现在前往</button>
        </div>
    </div>
    <!--有数据时显示列表-->
    <div class="pad-20">
        <div id="order-list" class="ftlistbox">
            <!--<div class="itm tapquick" url="me_order.html">-->
                <!--<div class="t1">-->
                    <!--恢复状态训练-->
                <!--</div>-->
                <!--<div class="t2">-->
                    <!--Recovery workout GMTV-->
                <!--</div>-->
                <!--<div class="t3">-->
                    <!--<span class="iconfont icon-time"></span>-->
                    <!--<span>5月15日 19:00-20:00</span>-->
                <!--</div>-->
                <!--<div class="xtag">未使用</div>-->
            <!--</div>-->
            <!--<div class="itm tapquick" url="me_order.html">-->
                <!--<div class="t1">-->
                    <!--恢复状态训练-->
                <!--</div>-->
                <!--<div class="t2">-->
                    <!--Recovery workout GMTV-->
                <!--</div>-->
                <!--<div class="t3">-->
                    <!--<span class="iconfont icon-time"></span>-->
                    <!--<span>5月15日 19:00-20:00</span>-->
                <!--</div>-->
                <!--<div class="xtag">未使用</div>-->
            <!--</div>-->
            <!--<div class="itm used tapquick" url="me_order.html">-->
                <!--<div class="t1">-->
                    <!--恢复状态训练-->
                <!--</div>-->
                <!--<div class="t2">-->
                    <!--Recovery workout GMTV-->
                <!--</div>-->
                <!--<div class="t3">-->
                    <!--<span class="iconfont icon-time"></span>-->
                    <!--<span>5月15日 19:00-20:00</span>-->
                <!--</div>-->
                <!--<div class="xtag">已使用</div>-->
            <!--</div>-->
            <!--<div class="itm used tapquick" url="me_order.html">-->
                <!--<div class="t1">-->
                    <!--恢复状态训练-->
                <!--</div>-->
                <!--<div class="t2">-->
                    <!--Recovery workout GMTV-->
                <!--</div>-->
                <!--<div class="t3">-->
                    <!--<span class="iconfont icon-time"></span>-->
                    <!--<span>5月15日 19:00-20:00</span>-->
                <!--</div>-->
                <!--<div class="xtag">已使用</div>-->
            <!--</div>-->
        </div>
    </div>
    <div id="node-itm" style="display: none">
        <div class="itm tapquick node" url="">
            <div class="t1">

            </div>
            <div class="t2">

            </div>
            <div class="t3">
                <span class="iconfont icon-time"></span>
                <span id="run_date">5月15日</span> <span id="start_time">19:00</span>-<span id="end_time">20:00</span>
            </div>
            <div id="state" class="xtag">未使用</div>
        </div>
    </div>



    <script>
        $(function(){
            User.validate();

            $('#go_home').click(function() {
                location.href = '/show/home';
            })

            loadData();
        });

        function loadData() {
            var user_id = localStorage.getItem('user_id');

            var data = { 'user_id': user_id };

            fit.ajax({
                url:'/api/getMyOrders',
                data:data,
                success:function(result){
                    if (result.code == 200) {
//                        alert(JSON.stringify(result));

                        if (result.data.length > 0) {
                            $('.emptybox').hide();
                        }

                        $.each(result.data, function() {
                            var node = createNode(this);

                            $('#order-list').append(node);
                        })
                    }
                }
            });
        }

        function createNode(item) {
            var node = $('#node-itm').find('.node').clone();

            node.find('.t1').html(item.name);
            node.find('.t2').html(item.name_en);

            var date = new Date(item.run_date);

            node.find('#run_date').html(date.getMonth() + 1 + "月" + date.getDate() + "日");
            node.find('#start_time').html(item.start_time);
            node.find('#end_time').html(item.end_time);

            switch (item.state) {
                case '10': {
                    node.attr('url', '/show/gt_orderdetail?id='+ item.order_id);
                    node.find('#state').html('待支付');

                    break;
                }
                case '20': {
                    node.attr('url', '/show/me_order?id='+ item.order_id);
                    node.find('#state').html('未使用');
                    break;
                }
                default: {
                    node.addClass('used');
                    node.find('#state').html('已使用');
                    node.attr('url', '/show/me_order?id='+ item.order_id);

                    break;
                }
            }

            node.click(function() {
                location.href = $(this).attr('url');
            })

            return node;
        }
    </script>

</body>
<script type="text/javascript">
    userLogin();
</script>
</html>