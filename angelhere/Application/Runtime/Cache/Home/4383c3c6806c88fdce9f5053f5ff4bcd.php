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

    <?php
 import("Org.Fit.WeChat.jssdk"); $jssdk = new \JSSDK("wx1547fb23c014a9a0", "b7112346bcbdcef3993dcc8705562f54"); $signPackage = $jssdk->GetSignPackage(); ?>
    <div class="tpcbanner">
        <div class="tt1">确认预约</div>
        <div class="tt2">Confirm Order</div>
        <span id="btn_back" class="iconfont icon-angleleft historyback" parentUrl="/show/gt_course?id=<?php echo ($data['order'][0]["order_id"]); ?>&back=1"></span>
    </div>

    <div class="pad-20">
        <div style="padding:10px 0;">
            请确认预约信息
        </div>
        <div class="shdblked">
            <div class="cmiyitm">
                <span class="iconfont icon-timecourse"></span>
                <span class="t2"><?php echo ($data['order'][0]["name"]); ?> / <?php echo ($data['order'][0]["name_en"]); ?></span>
            </div>
            <div class="cmiyitm">
                <span class="iconfont icon-time"></span>
                <span class="t2"><?php echo date("m月d日", strtotime($data['order'][0]['run_date'])) ?> <?php $arr=array("天","一","二","三","四","五","六"); echo "星期".$arr[date("w",strtotime($data['order'][0]['run_date']))]; ?> <?php echo ($data['order'][0]["start_time"]); ?></span>
            </div>

            <!--普通用户出现-->
            <div id="no-vip-price" class="cmiyitm" style="display: none">
                <span class="iconfont icon-price"></span>
                <div class="t2">体验价：<span class="font10px">￥</span><?php echo ($data['order'][0]["price"]); ?> <span class="split"></div>
            </div>
            <!--vip会员出现-->
            <div id="vip-price" class="cmiyitm" style="display: none">
                <span class="iconfont icon-price"></span>
                <div class="t2">VIP价：<span class="font10px">￥</span><?php echo ($data['order'][0]["vip_price_1"]); ?> <span class="split"></div>
            </div>

            <div class="cmiyitm">
                <span class="iconfont icon-location"></span>
                <span class="iconfont icon-angleright"></span>
                <span id="lesson_local" class="t2"><?php echo ($data['order'][0]["local"]); ?></span>
            </div>
        </div>

        <!--普通用户出现-->
        <style>
            .notvip {
                background:rgba(255,255,255,.85);
                color:#F0AD4E;
                padding:15px;
                margin-bottom:15px;
                border-radius:4px;
                border:0.5px solid #F0AD4E;
                font-weight:bold;
            }
        </style>
        <div id="not-vip" style="margin-bottom:15px; display: none" url="#">
            <div class="notvip">
                您目前还不是VIP会员，成为VIP可以享用更优惠价格
            </div>
            <input type="hidden" id="order_id" value="<?php echo ($data['order'][0]["order_id"]); ?>">
            <button class="confirmbtn full"><span class="iconfont icon-wechat"></span>确认支付 <span id="test-price">￥<?php echo ($data['order'][0]["price"]); ?></span>元</button>
        </div>

        <!--VIP会员出现-->
        <style>
            .myyue {
                padding-top:15px;
                font-size:12px;
                color:#bbb;
            }
            .myyue span {
                color:#F0AD4E;
            }
        </style>
        <div id="is-vip" style="margin-bottom:15px; display: none" url="#">
            <!--?url属性里面的网址"?back=1"表示可以无视historyback直接强制返回到parentUrl-->
            <button class="confirmbtn full"><span class="iconfont icon-check"></span>确认预约</button>
            <div class="myyue">我的余额：￥<span id="my-amount">0</span></div>
        </div>
    </div>



    <script type="text/javascript" charset="UTF-8" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script>
        var price = '<?php echo ($data['order'][0]["price"]); ?>';
        var vip_price = '<?php echo ($data['order'][0]["vip_price_1"]); ?>';

        $(function(){
            User.loadData = function() {
                if (User.data.amount > 0) {
                    $('#is-vip').show();
                    $('#vip-price').show();
                }
                else {
                    $('#not-vip').show();
                    $('#no-vip-price').show();
                }

                $('#my-amount').html(User.data.amount);
            }

            User.load();

            $('#btn_back').click(function() {
                var lesson_id = $('#lesson_id').val();

                location.href = '/show/gt_course?id=' + lesson_id;
            })

            $('.confirmbtn').click(function() {
                var order_id = $('#order_id').val();
                var user_amount = Number($('#my-amount').html());

                //支付金额参数
                var pay_amount = 0;
                var pay_vip_amount = 0;
                //判断是否有余额
                if (user_amount > 0) {
                    var tmp_vip_price = Number(vip_price);
//                    alert(user_amount);
//                    alert(tmp_vip_price);
                    //判断余额是否比支付金额大
                    if (user_amount >= tmp_vip_price) {
                        pay_vip_amount = tmp_vip_price;
                        pay_amount = 0;
                    }
                    else {
                        pay_amount = tmp_vip_price - user_amount;
                        pay_vip_amount = user_amount;
                    }
                }
                else {
                    pay_amount = price;
                }

                Order.setAmount(order_id, pay_amount, pay_vip_amount);
            })

            $('#lesson_local').click(function() {
//                location.href = '/show/wx_map';
                wx.config({
                    debug: false,
                    appId: '<?php echo $signPackage["appId"];?>',
                    timestamp: <?php echo $signPackage["timestamp"];?>,
                nonceStr: '<?php echo $signPackage["nonceStr"];?>',
                        signature: '<?php echo $signPackage["signature"];?>',
                        jsApiList: ['openLocation']
            });

            wx.ready(function () {
                wx.openLocation({
                    latitude: 22.543460, // 纬度，浮点数，范围为90 ~ -90
                    longitude: 113.950500, // 经度，浮点数，范围为180 ~ -180  22.543460,113.950500
                    name: '嵘兴通讯大厦', // 位置名
                    address: '深圳市南山区 科发路202号嵘兴通讯大厦101房', // 地址详情说明
                    scale: 20, // 地图缩放级别,整形值,范围从1~28。默认为最大
                    infoUrl: '#' // 在查看位置界面底部显示的超链接,可点击跳转
                });
            });

            wx.error(function (res) {
                //                    alert(res.errMsg);
            });
        });
        });
    </script>

</body>
<script type="text/javascript">
    userLogin();
</script>
</html>