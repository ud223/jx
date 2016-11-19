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
        <div class="tt1">预约详细</div>
        <div class="tt2">Order Detail</div>
        <span class="iconfont icon-angleleft historyback" parentUrl="/show/me_orders?back=1"></span>
    </div>

    <div class="pad-20">
        <div style="background:#40cf76;margin:0 0 10px 0;padding:10px;text-align:center;color:#FFF;border-radius:5px;">
            <div style="font-size:10px;opacity:.7;"><?php echo date("Y.m.d", strtotime($data['order'][0]['run_date'])) ?>  <?php echo ($data['order'][0]["start_time"]); ?></div>
            <div style="font-size:16px;">成功预约,请准时上课</div>
        </div>
        <!--<div style="text-align:center;padding:0 0 30px 0;color:#40cf76;">-->
            <!--请提前10分钟到场，<br/>并向教练展示本次预约的二维码。-->
        <!--</div>-->
        <!--<div class="qrsamplewp">-->
            <!--<img src="/Public/common/js/img/qrsample.png" class="qrsample" />-->
        <!--</div>-->
        <div class="shdblked">
            <div class="cmiyitm">
                <span class="iconfont icon-timecourse"></span>
                <span class="t2"><?php echo ($data['order'][0]["name"]); ?> / <?php echo ($data['order'][0]["name_en"]); ?></span>
            </div>
            <div class="cmiyitm">
                <span class="iconfont icon-time"></span>
                <span class="t2"><?php echo date("m月d日", strtotime($data['order'][0]['run_date'])) ?> <?php $arr=array("天","一","二","三","四","五","六"); echo "星期".$arr[date("w",strtotime($data['order'][0]['run_date']))]; ?> <?php echo ($data['order'][0]["start_time"]); ?></span>
            </div>
            <div class="cmiyitm">
                <span class="iconfont icon-price"></span>
                <div class="t2">价格：<span class="font10px">￥</span><?php echo ($data['order'][0]["vip_price_1"]); ?> <span class="split"></span></div>
            </div>
            <div class="cmiyitm">
                <span class="iconfont icon-location"></span>
                <span class="iconfont icon-angleright"></span>
                <span id="lesson_local" class="t2"><?php echo ($data['order'][0]["local"]); ?></span>
            </div>
            <div class="cmiyitm">
                <span class="iconfont icon-phone"></span>
                <span class="t2">0755-394994056, 18039938485</span>
            </div>
            <?php if ($data['order'][0]['type'] != 3) :?>
                <div class="cmiyitm tc gocourse tapquick" url="/show/gt_course?id=<?php echo ($data['order'][0]["lesson_id"]); ?>">
                    <span>更多详情</span>
                </div>
            <?php else :?>
                <div class="cmiyitm tc gocourse tapquick" url="/show/gt_ptdetail?id=<?php echo ($data['order'][0]["coach_id"]); ?>">
                    <span>更多详情</span>
                </div>
            <?php endif; ?>
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
                }
                else {
                    $('#not-vip').show();
                }

                $('#my-amount').html(User.data.amount);
            }

            User.load();

            $('.confirmbtn').click(function() {
                location.href = '/show/me_order?id=<?php echo ($data['order'][0]["order_id"]); ?>';
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
                    longitude: 113.950500, // 经度，浮点数，范围为180 ~ -180  113.950500,22.543460
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