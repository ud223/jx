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
        <div class="tt1">开通VIP / 充值</div>
        <div class="tt2">Become VIP / Charge</div>
        <span class="iconfont icon-angleleft historyback" parentUrl="/show/gt_vipcard"></span>
    </div>
    <style>
        .cimebtb {
            display: table;
            width: 100%;
        }

        .cimebtb .r {
            display: table-row;
        }

        .cimebtb .r .td {
            font-size: 16px;
            padding: 10px 0;
            width: 50%;
            display: table-cell;
            border-bottom: 1px solid rgba(0, 0, 0, .06);
        }

        .cimebtb .r:last-child .td {
            border-bottom: none;
        }

        .cimebtb .r .td.col2 {
            text-align: right;
            color: #F0AD4E;
        }

        .cniwetetb {
            width: 100%;
            display: table;
        }

        .cniwetetb .cgeitm1 {
            display: table-cell;
            width: 20%;
            color: #666;
            box-sizing: border-box;
        }
        .cniwetetb .cgeitm1 .inner {
            text-align: center;
            border: .5px solid #666;
            padding:10px 0;
        }
        .cniwetetb .cgeitm1 .t2 {
            font-size: 12px;
            color:#F60;
        }
        .cniwetetb .cgeitm1:nth-child(even) {
            width: 24%;
        }

        .cniwetetb .cgeitm1:nth-child(even) {
            padding: 0 10px;
        }
    </style>
    <div class="pad-20">
        <div class="cimebtb">
            <div class="r">
                <div class="td col1">充值账户：</div>
                <div id="name" class="td col2"></div>
            </div>
            <div class="r">
                <div class="td col1">账户余额：</div>
                <div id="my-account" class="td col2"></div>
            </div>
            <div class="r">
                <div class="td col1">本次充值额度：</div>
                <div class="td col2"></div>
            </div>
        </div>

        <div class="cniwetetb">
            <div id="vip888" class="cgeitm1" val="800">
                <div class="inner">
                    <div class="t1">充值888</div>
                    <div class="t2">到账888</div>
                </div>
            </div>
            <div id="vip1000" class="cgeitm1" val="1100">
                <div class="inner">
                    <div class="t1">充值1000</div>
                    <div class="t2">到账1100</div>
                </div>
            </div>
            <div id="vip2500" class="cgeitm1" val="5600">
                <div class="inner">
                    <div class="t1">充值5000</div>
                    <div class="t2">到账5600</div>
                </div>
            </div>
            <div id="vip5000" class="cgeitm1" val="11500">
                <div class="inner">
                    <div class="t1">充值10000</div>
                    <div class="t2">到账11500</div>
                </div>
            </div>
        </div>
    </div>



    <script>
        //11500
        $(function(){
            User.loadData = function (user) {
                $('#name').html(user.Name);

                if (user.amount == '0.00') {
                    $('#my-account').html('暂未开通VIP');
                }
                else {
                    $('#my-account').html(user.amount);
                }
            };

            User.load();

            $('#vip888').click(function() {
                var today = new Date();

                var month = today.getMonth() + 1;

                Order.data = today.getFullYear() + month + today.getDate();

                Order.success = createSuccess;

                var amount = $(this).attr('val');
                var pay_amount = 888;

                Order.create(2, amount, pay_amount);
            });

            $('#vip1000').click(function() {
                var today = new Date();

                var month = today.getMonth() + 1;

                Order.data = today.getFullYear() + month + today.getDate();

                Order.success = createSuccess;

                var amount = $(this).attr('val');
                var pay_amount = 1000;

                Order.create(2, amount, pay_amount);
            });

            $('#vip2500').click(function() {
                var today = new Date();

                var month = today.getMonth() + 1;

                Order.data = today.getFullYear() + month + today.getDate();

                Order.success = createSuccess;

                var amount = $(this).attr('val');
                var pay_amount = 5000;

                Order.create(2, amount, pay_amount);
            });

            $('#vip5000').click(function() {
                var today = new Date();

                var month = today.getMonth() + 1;

                Order.data = today.getFullYear() + month + today.getDate();

                Order.success = createSuccess;

                var amount = $(this).attr('val');
                var pay_amount = 10000;

                Order.create(2, amount, pay_amount);
            });
        });

        function createSuccess() {
            localStorage.setItem('is_back', 0);

            var url = '/show/order_pay?id='+ Order.id;

            location.href = url;
        }
    </script>

</body>
<script type="text/javascript">
    userLogin();
</script>
</html>