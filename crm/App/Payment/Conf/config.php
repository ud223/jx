<?php
return array(
    "payment"=>array(
        //支付宝参数
        'alipay'=>array(
            //配置
            "config"=>array(
                "partner"=>"2088611719838383",
                "key"=>"416n106bqq0gd4ydvv3l8g6ojj6p3o2z",
                "sign_type"=>strtoupper('MD5'),
                "input_charset"=> strtolower('utf-8'),
                "cacert"=>getcwd().'\\Payment\\Alipay\\pem\\cacert.pem',
                "alipay_public_key"=>getcwd().'\\Payment\\Alipay\\pem\\alipay_public_key.pem',
                "rsa_private_key"=>getcwd().'\\Payment\\Alipay\\pem\\rsa_private_key.pem',
                "transport"=> 'http',
            ),
            
            //卖家支付宝帐户
            'seller'=>"yewenpeng@btten.com",
            
            //服务器异步通知页面路径，需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
            'notify'=>ALIPAY_NOTIFY,
            
            //页面跳转同步通知页面路径，需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
            'return'=>ALIPAY_RETURN,
        ),
    ),
);