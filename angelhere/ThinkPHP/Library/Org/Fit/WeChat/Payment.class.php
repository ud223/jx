<?php
/**
 * Created by PhpStorm.
 * User: 淼
 * Date: 2015/4/29
 * Time: 11:41
 */

namespace Org\Fit\WeChat;


class Payment {
  public $PayType = 0;  //支付方式ID
  
  //private $MsShopUrl = "index.php/MsShop/Pay";
  
  private $SiteConfig = null;
  
  function __construct(){
  //  $this->SiteConfig = new \Org\Fit\SiteConfig();
  }

  /**
   * 获取wap支付链接
   * @access public
   * @return mixed
   */
  public function GetWapUrl($_order){
    $PayCode = $this->GetPaymentConfig();

    switch($PayCode["pay_code"]){
      case "wechat" :
        return $this->WeChatShopPayParam($_order);
        break;
    }
  }

  /**
   * 获取app支付链接
   * @access public
   * @return mixed
   */
  public function GetAppUrl($_order){
    $PayCode = $this->GetPaymentConfig();

    switch($PayCode["pay_code"]){
      case "alipay" :

        break;

      case "baidu" :

        break;
    }
  }

  /**
   * 获取wep支付链接
   * @access public
   * @return mixed
   */
  public function GetWebUrl($_order){
    $PayCode = $this->GetPaymentConfig();
    switch($PayCode["pay_code"]){
      case "alipay" :

        break;

      case "baidu" :

        break;
    }
  }
  
  //region 微信支付
  /**
   * 生成微信支付js支付参数
   * @access public
   * @param array $order 订单数据
   * @param array $_goods 订单商品数据
   * @return mixed
   */
  public function WeChatShopPayParam($_order, $openId){
    import('Org.Fit.WeChat.lib.WxPayApi');
    import('Org.Fit.WeChat.JsApiPay');

    $tools = new \JsApiPay();
    $input = new \WxPayUnifiedOrder();

    //设置商品或支付单简要描述
    $input->SetBody("沸腾时刻");//("{$this->SiteConfig->SiteName}-订单编号{$_order["order_sn"]}");
    
    //设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
    $input->SetAttach("");

    //设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
    $input->SetOut_trade_no($_order['order_id']);

    $amount = intval($_order["amount"]) * 100;

//      print_r($_order); exit;

    //设置订单总金额，只能为整数，详见支付金额，单位：分
    $input->SetTotal_fee($amount);//($_order["order_amount"] * 100);
    
    //设置订单生成时间
    $input->SetTime_start(date("YmdHis"));
    
    //设置订单失效时间 
    $input->SetTime_expire(date("YmdHis", time() + 600));
    
    //设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
    $input->SetGoods_tag("");
    
    //设置接收微信支付异步通知回调地址
    $input->SetNotify_url("http://fit.webetter100.com/show/pay_notify");
    
    //取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
    $input->SetTrade_type("JSAPI");
    
    //设置trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。
    $input->SetOpenid($openId);
//    print_r($input); exit;
    $WeChatOrder = \WxPayApi::unifiedOrder($input);

    $jsApiParameters = $tools->GetJsApiParameters($WeChatOrder);

    //region 草泥马的腾讯，自己格式化的js参数，数据类型验证都tmd不对，我操
    $ApiParameters = json_decode($jsApiParameters, true);
    $ApiParameters["timeStamp"] = "{$ApiParameters["timeStamp"]}";  //就是这个b参数，要用string类型
    $jsApiParameters = json_encode($ApiParameters);
    //endregion 草泥马的腾讯，自己格式化的js参数，数据类型验证都tmd不对，我操

    return $jsApiParameters;
  }

  /**
   * 生成微信支付js支付参数
   * @access public
   * @param array $order 订单数据
   * @param array $_goods 订单商品数据
   * @return mixed
   */
  public function WeChatShopGroupBuyPayParam($_order, $openId){
    import('Org.Hwu.WeChat.lib.WxPayApi');
    import('Org.Hwu.WeChat.JsApiPay');
    $tools = new \JsApiPay();
    $input = new \WxPayUnifiedOrder();

    //设置商品或支付单简要描述
    $input->SetBody("沸腾时刻"); //("{$this->SiteConfig->SiteName}-订单编号{$_order["order_sn"]}");

    //设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
    $input->SetAttach("");

    //设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
    $input->SetOut_trade_no($_order['order_sn']);

    //设置订单总金额，只能为整数，详见支付金额，单位：分
    $input->SetTotal_fee($_order["order_amount"] * 100);

    //设置订单生成时间
    $input->SetTime_start(date("YmdHis"));

    //设置订单失效时间 
    $input->SetTime_expire(date("YmdHis", time() + 600));

    //设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
    $input->SetGoods_tag("");

    //设置接收微信支付异步通知回调地址
    $input->SetNotify_url(SITE_DOMIAN . "/{$this->MsShopUrl}/WeChatJsPayGroupBuyNotify");

    //取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
    $input->SetTrade_type("JSAPI");

    //设置trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。
    $input->SetOpenid($openId);

    $WeChatOrder = \WxPayApi::unifiedOrder($input);
    
    if($WeChatOrder["result_code"] == "FAIL"){
      if($WeChatOrder["return_code"] == "SUCCESS"){
        redirect("User/my_groupbuy_info/sn/{$_order['order_sn']}");
        exit();
      }
    }
    
    $jsApiParameters = $tools->GetJsApiParameters($WeChatOrder);

    //region 草泥马的腾讯，自己格式化的js参数，数据类型验证都tmd不对，我操
    $ApiParameters = json_decode($jsApiParameters, true);
    $ApiParameters["timeStamp"] = "{$ApiParameters["timeStamp"]}";  //就是这个b参数，要用string类型
    $jsApiParameters = json_encode($ApiParameters);
    //endregion 草泥马的腾讯，自己格式化的js参数，数据类型验证都tmd不对，我操

    return $jsApiParameters;
  }

  public function ReceiveWeChatShopNotify(){
    import('Org.Fit.WeChat.WeChatPayReturn');

    $notify = new \WeChatPayReturn();

    $notify->Handle(false);

    $Result = $notify->WeChatNotifyProcessResult();

    return $Result;
    
//    $PayResultParam = $Result["data"];
//
//    return $PayResultParam;
  }

  public function ReceiveWeChatShopGroupBuyNotify(){
    import('Org.Hwu.WeChat.WeChatPayReturn');

    $notify = new \WeChatPayReturn();

    $notify->Handle(false);

    $Result = $notify->WeChatNotifyProcessResult();

    if($Result["error"] == 1){
      return $Result;
    }

    $PayResultParam = $Result["data"];

    if($PayResultParam["result_code"] != "SUCCESS" || $PayResultParam["return_code"] != "SUCCESS"){
      return ReturnError(L("_WECHAT_PAY_FAILURE_"));
    }

    //获取支付日志记录
    $clsPayment = new \Org\Fit\WeChat\Payment();
    $PaymentLog = $clsPayment->GetPaymentLog($PayResultParam["out_trade_no"]);

    if(IsN($PaymentLog)){
      return ReturnError(L("_ORDER_PAY_LOG_NULL_"));
    }

//    //检查金额是否相符
//    if($PaymentLog["pay_amount"] != $sTotalFee){
//      ReturnError(L('_PAY_AMOUNT_NOT_EQUAL_'));
//    }

    if($PayResultParam["result_code"] != "SUCCESS" || $PayResultParam["return_code"] != "SUCCESS"){
      return ReturnError(L("_WECHAT_PAY_FAILURE_"));
    }

    return $clsPayment->PaySucceed($PaymentLog["order_type"], $PayResultParam["out_trade_no"], $PayResultParam["transaction_id"], $clsPayment->_ClientType_Wechat, $PayResultParam);
  }

  public function ReceiveWeChatSdkNotify(){
    import('Org.Hwu.WeChat.SDK.WeChatPayReturn');

    $notify = new \WeChatPayReturn();

    $notify->Handle(false);

    $Result = $notify->WeChatNotifyProcessResult();

    if($Result["error"] == 1){
      return $Result;
    }

    $PayResultParam = $Result["data"];

    if($PayResultParam["result_code"] != "SUCCESS" || $PayResultParam["return_code"] != "SUCCESS"){
      return ReturnError(L("_WECHAT_PAY_FAILURE_"));
    }

    $clsOrder = new \Org\Fit\Order();
    return $clsOrder->OrderPaySucceed($PayResultParam["out_trade_no"], $PayResultParam["transaction_id"]);
  }

  public function ReceiveWeChatSdkGroupBuyNotify(){
    import('Org.Hwu.WeChat.SDK.WeChatPayReturn');

    $notify = new \WeChatPayReturn();

    $notify->Handle(false);

    $Result = $notify->WeChatNotifyProcessResult();

    if($Result["error"] == 1){
      return $Result;
    }

    $PayResultParam = $Result["data"];
    
    //公众账号ID
    $sAppID = $PayResultParam["appid"];

    //商户号
    $sMchID = $PayResultParam["mch_id"];

    //设备号
    $sDeviceInfo = $PayResultParam["device_info"];

    //随机字符串
    $sNonceStr = $PayResultParam["nonce_str"];

    //签名
    $sSign = $PayResultParam["sign"];

    //业务(支付)结果，SUCCESS/FAIL
    $sResultCode = $PayResultParam["result_code"];

    //错误代码
    $sErrCode = $PayResultParam["err_code"];

    //错误代码描述
    $sErrCodeDes = $PayResultParam["err_code_des"];

    //用户标识
    $sOpenID = $PayResultParam["openid"];

    //用户是否关注公众账号，Y-关注，N-未关注，仅在公众账号类型支付有效
    $IsSubscribe = $PayResultParam["is_subscribe"];

    //交易类型,JSAPI、NATIVE、APP
    $sTradeType = $PayResultParam["trade_type"];

    //总金额
    $sTotalFee = $PayResultParam["total_fee"];

    //时间
    $sTimeEnd = $PayResultParam["time_end"];
    
    //商户订单号
    $sOutTradeNo = $PayResultParam["out_trade_no"];
    
    //微信交易号
    $sTransactionID = $PayResultParam["transaction_id"];

    //获取支付日志记录
    $clsPayment = new \Org\Fit\WeChat\Payment();
    $PaymentLog = $clsPayment->GetPaymentLog($sOutTradeNo);

//    if(IsN($PaymentLog)){
//      return ReturnError(L("_ORDER_PAY_LOG_NULL_"));
//    }
//
//    //检查金额是否相符
//    if($PaymentLog["pay_amount"] != $sTotalFee){
//      ReturnError(L('_PAY_AMOUNT_NOT_EQUAL_'));
//    }
//
//    if($PayResultParam["result_code"] != "SUCCESS" || $PayResultParam["return_code"] != "SUCCESS"){
//      return ReturnError(L("_WECHAT_PAY_FAILURE_"));
//    }
    
    return $clsPayment->PaySucceed($PaymentLog["order_type"], $sOutTradeNo, $sTransactionID, $clsPayment->_ClientType_App, $PayResultParam);
  }
  //endregion 微信支付
}