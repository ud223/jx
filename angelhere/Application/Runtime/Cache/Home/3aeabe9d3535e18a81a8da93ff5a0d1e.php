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

    <div class="pad-20" style="text-align: center;">
        <div>
            <span style="font-size:42px;color:#4CAE4C" class="iconfont icon-iconfontcheck"></span>
        </div>
        <div style="font-size:18px;margin-bottom:20px;color:#4CAE4C">
            Congratulation!
        </div>
        <div class="alert alert-success">
            充值成功，您已经成为VIP会员<br/>享受VIP特价以及其他特权服务
        </div>
        <style>
            .vietb {
                display:table;
                width:100%;
                border-collapse: collapse;
                background: #FFF;
            }

            .vietb .td {
                display:table-cell;
                width:50%;
                box-sizing: border-box;
                border:1px solid #ddd;
            }
            .vietb .tr {
                display:table-row;
            }
        </style>
        <div class="vietb" style="line-height:32px;margin-bottom:15px;">
            <div class="tr">
                <div class="td col1">充值金额 </div>
                <div class="td col2"><?php echo ($data['order'][0]["amount"]); ?>元</div>
            </div>
            <div class="tr">
                <div class="td col1">到账金额 </div>
                <div class="td col2"><?php echo ($data['order'][0]["vip_amount"]); ?>元</div>
            </div>
        </div>
        <div>
            <button class="whitebtn full tapquick" url="/show/home" style="height:40px;">预约吧！</button>
            <span style="display:inline-block;height:70px;line-height:70px;color:#aaa;font-size:12px;" class="tapquick" url="/show/me_finance">查看我的账户</span>
        </div>
    </div>



    <!--onclick="$.toastMsg('错误信息')"-->
    <script>

    </script>

</body>
<script type="text/javascript">
    userLogin();
</script>
</html>