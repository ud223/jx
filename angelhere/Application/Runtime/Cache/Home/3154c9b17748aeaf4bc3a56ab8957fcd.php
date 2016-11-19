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

    <div class="tc p0y-c1">
        <!--<img class="spin" src="/Public/common/js/img/loading-spin.svg" />-->
        <div class="txt">
            <!--正在跳转至微信支付<br/>请稍后 ...-->
        </div>
    </div>



    <script>
        $(document).ready(function () {
            var is_back = localStorage.getItem('is_back');

            if (is_back == '1') {
                location.href = '/show/index';

                return;
            }

            //调用微信JS api 支付
            function jsApiCall() {
                WeixinJSBridge.invoke(
                        'getBrandWCPayRequest',
                        <?php echo ($JsParam); ?>,
                        function(res){
                            WeixinJSBridge.log(res.err_msg);
                            //alert(res.err_code + ' | ' + res.err_desc + ' | ' + res.err_msg);
                            //get_brand_wcpay_request:cancel
                            if(res.err_msg == 'get_brand_wcpay_request:ok'){
                                window.location.href = "/show/pay_success?id=<?php echo ($OrderSn); ?>&back=1";
                            }else{
                                window.location.href = "/show/pay_fail?id=<?php echo ($OrderSn); ?>&back=1";
                            }
                        }
                );
            }

            function callpay() {
                if (typeof WeixinJSBridge == "undefined"){
                    if( document.addEventListener ){
                        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                    }else if (document.attachEvent){
                        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                    }
                }else{
                    jsApiCall();
                }
            }
            callpay();
        })

    </script>

</body>
<script type="text/javascript">
    userLogin();
</script>
</html>