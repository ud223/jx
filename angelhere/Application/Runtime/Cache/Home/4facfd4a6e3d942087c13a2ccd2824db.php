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
        <input type="hidden" id="lesson_id" value="<?php echo ($data['lesson'][0]["lesson_config_id"]); ?>">
        <div class="tt1"><?php echo ($data['lesson'][0]["name"]); ?></div>
        <div class="tt2"><?php echo ($data['lesson'][0]["name_en"]); ?></div>
        <span class="iconfont icon-angleleft historyback" parentUrl="/show/home?bakc=1"></span>
    </div>

    <div style="background:url(/Public/upload/<?php echo ($data['lesson'][0]["show_pic"]); ?>) center center no-repeat;background-size:cover;height:200px;"></div>

    <!--<button id="submit" class="colorbtn bookcbtn">预定课程</button>-->
    <div style="z-index:5;box-shadow:0 0 20px rgba(0,0,0,.1);padding:10px 20px;position:fixed;bottom:0;left:0;width:100%;box-sizing: border-box;background:#FFF;">
        <button id="submit" class="colorbtn full" style="width:100%;height:40px;line-height: 40px;border:none;border-radius: 100px;" >预定课程</button>
    </div>

    <!--<button class="bookcbtn full">约满</button>-->
    <div class="pad-20">
        <div class="shdblked">
            <div class="cmiyitm">
                <span class="iconfont icon-time"></span>
                <span class="t2"><span id="date"></span> <span id="week"></span> <span id="start_time"><?php echo ($data['lesson'][0]["start_time"]); ?></span>-<span id="end_time"><?php echo ($data['lesson'][0]["end_time"]); ?></span></span>
            </div>
            <div class="cmiyitm">
                <span class="iconfont icon-price"></span>
                <div class="t2">体验价：<span class="font10px">￥</span><?php echo ($data['lesson'][0]["price"]); ?> <span class="split">/</span> 金卡价格：<span class="font10px">￥</span><?php echo ($data['lesson'][0]["vip_price_1"]); ?></div>
                <div class="t2">白金卡价格：<span class="font10px">￥</span><?php echo ($data['lesson'][0]["vip_price_2"]); ?> <span class="split">/</span> 黑卡价格：<span class="font10px">￥</span><?php echo ($data['lesson'][0]["vip_price_3"]); ?></div>
                <div id="not-vip" class="bcvipc tc tapquick" url="/show/gt_vipcard" style="display: none">成为VIP，享受特价</div>
                <div id="is-vip" class="bcvipc tapquick vipcharge" url="/show/gt_charge" style="display: none">VIP充值</div>
            </div>
            <div class="cmiyitm">
                <span class="iconfont icon-location"></span>
                <span class="iconfont icon-angleright"></span>
                <span id="lesson_local" class="t2"><?php echo ($data['lesson'][0]["local"]); ?></span>
            </div>
        </div>

        <div class="shdblked">
            <div class="t1">
                <span class="iconfont icon-coach"></span>
                <span>教练组</span>
            </div>
            <div class="contd">
                <div class="introdtxt">Project 0/1 教练组</div>
                <img src="/Public/common/js/img/title_img/teamwork.jpg" style="width:100%;border-radius:4px;" />
            </div>
        </div>

        <div class="shdblked">
            <div class="t1">
                <span class="iconfont icon-qiapian"></span>
                <span>课程介绍</span>
            </div>
            <div class="contd">
                <div class="introdtxt"><?php echo ($data['lesson'][0]["remark"]); ?></div>
            </div>
        </div>
    </div>



    <script type="text/javascript" charset="UTF-8" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script>
        $(function(){
            User.loadData = function (user) {
                if (user.amount == '0.00') {
                    $('#not-vip').show();
                }
                else {
                    $('#is-vip').show();
                }
            };

            User.load();

            Order.validate();

            initDate(Order.date);

            Order.success = createSuccess;

            $('#submit').click(function() {
                Order.create(1, 0, 0);
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

        function createSuccess(id) {
            location.href = '/show/gt_orderdetail?id=' + id;
        }

        function initDate(d) {
            var date = new Date(d);

            var date_text = date.getMonth() + 1 + '月' + date.getDate() + '日';

            $('#date').html(date_text);

            var week_text = "星期";

            switch (date.getDay()) {
                case 1: {
                    week_text = week_text + "一";

                    break;
                }
                case 2: {
                    week_text = week_text + "二";

                    break;
                }
                case 3: {
                    week_text = week_text + "三";

                    break;
                }
                case 4: {
                    week_text = week_text + "四";

                    break;
                }
                case 5: {
                    week_text = week_text + "五";

                    break;
                }
                case 6: {
                    week_text = week_text + "六";

                    break;
                }
                default :{
                    week_text = week_text + "天";

                    break;
                }
            }

            $('#week').html(week_text);
        }
    </script>

</body>
<script type="text/javascript">
    userLogin();
</script>
</html>