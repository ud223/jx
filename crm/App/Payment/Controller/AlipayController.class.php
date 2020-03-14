<?php
namespace Payment\Controller;
use Think\Controller;

class AlipayController extends Controller {
    public function alipay(){
        $AlipayConfig = C("payment.alipay");
        
        /**************************请求参数**************************/
        $payment_type = "1"; //支付类型 //必填，不能修改
        $notify_url = $AlipayConfig["notify"]; //服务器异步通知页面路径
        $return_url = $AlipayConfig["return"]; //页面跳转同步通知页面路径
        $seller_email = $AlipayConfig["seller"];//卖家支付宝帐户必填
        $out_trade_no = "no" . time();//$_POST['trade_no'];//商户订单号 通过支付页面的表单进行传递，注意要唯一！
        $subject = "test subject";//$_POST['ordsubject'];  //订单名称 //必填 通过支付页面的表单进行传递
        $total_fee = 0.01;//$_POST['ordtotal_fee'];   //付款金额  //必填 通过支付页面的表单进行传递
        $body = "test order body";//$_POST['ordbody'];  //订单描述 通过支付页面的表单进行传递
        $show_url = "http://www.163.com";//$_POST['ordshow_url'];  //商品展示地址 通过支付页面的表单进行传递
        $anti_phishing_key = "";//防钓鱼时间戳 //若要使用请调用类文件submit中的query_timestamp函数
        $exter_invoke_ip = get_client_ip(); //客户端的IP地址 
        /************************************************************/
        
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($AlipayConfig['config']['partner']),
            "payment_type" => $payment_type,
            "notify_url" => $notify_url,
            "return_url" => $return_url,
            "seller_email" => $seller_email,
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "total_fee" => $total_fee,
            "body" => $body,
            "show_url" => $show_url,
            "anti_phishing_key" => $anti_phishing_key,
            "exter_invoke_ip" => $exter_invoke_ip,
            "_input_charset" => trim(strtolower($alipay_config['input_charset']))
        );
        
        $objAlipay = new \Alipay\AlipaySubmit($AlipayConfig["config"]);
        $html = $objAlipay->buildRequestForm($parameter, "get", "确认");

        $this->SaveOrder($parameter);
        
        $this->assign("html",$html);
        $this->display();
    }
    
    public function alipay_notify(){
        $AlipayConfig = C("payment.alipay");
        
        $objAlipay = new \Alipay\AlipayNotify($AlipayConfig["config"]);
        $html = $objAlipay->verifyNotify();

        //验证成功
        if($verify_result) {
            echo "success";    //请不要修改或删除
        }else{
            echo "fail";   //请不要修改或删除
        }
    }
    
    /**
     * 保存订单
     * @access private
     * @param array $_param 写入数据库的数据数组
     * @return bool
     */
    private function SaveOrder($_param){
        
    }
}