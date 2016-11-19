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
        <div class="tt1">准备上课</div>
        <div class="tt2">lesson start</div>
        <span class="iconfont icon-angleleft historyback" parentUrl="/show/coach_home"></span>
    </div>

    <div class="pad-20">
        <div class="shdblked">
            <div class="cmiyitm">
                <span class="iconfont icon-timecourse"></span>
                <?php if($data['order'][0]['type'] == 1) :?>
                    <span class="t2"><?php echo ($data['order'][0]["name"]); ?> / <?php echo ($data['order'][0]["name_en"]); ?></span>
                <?php else :?>
                    <span class="t2">私教 / <?php echo ($data['order'][0]["user_name"]); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="is-vip" style="margin-bottom:15px" url="#">
        <!--?url属性里面的网址"?back=1"表示可以无视historyback直接强制返回到parentUrl-->
        <button class="confirmbtn full"><span class="iconfont icon-check"></span>开始上课</button>
    </div>



    <script>
        var order_id = '<?php echo ($data['order'][0]["order_id"]); ?>';

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

            $('#btn_back').click(function() {
                location.href = '/home/coach/home';
            })

            $('.confirmbtn').click(function() {
                Order.start(order_id);
            })
        });
    </script>

</body>
<script type="text/javascript">
    userLogin();
</script>
</html>