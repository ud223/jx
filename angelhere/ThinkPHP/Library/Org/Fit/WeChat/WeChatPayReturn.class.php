<?php
//namespace Org\Fit\WeChat;

require_once "lib/WxPayApi.class.php";
require_once 'lib/WxPay.Notify.php';

class WeChatPayReturn extends WxPayNotify {
  private $NotifyResult = null;
  
  //查询订单
  public function Queryorder($transaction_id){
    $input = new WxPayOrderQuery();

    $input->SetTransaction_id($transaction_id);

    $result = WxPayApi::orderQuery($input);
    
    if(array_key_exists("return_code", $result)
      && array_key_exists("result_code", $result)
      && $result["return_code"] == "SUCCESS"
      && $result["result_code"] == "SUCCESS")
    {
      return true;
    }
    return false;
  }

  //重写回调处理函数
  public function NotifyProcess($data, $msg){
    if(!array_key_exists("transaction_id", $data)){
      $msg = "输入参数不正确";

      $this->NotifyResult = $msg;
      
      return false;
    }
    
    //查询订单，判断订单真实性
    if(!$this->Queryorder($data["transaction_id"])){
      $msg = "订单号不正确";

      $this->NotifyResult = $msg;

      return false;
    }

    $this->NotifyResult = $data;

    return true;
  }
  
  public function WeChatNotifyProcessResult(){
    return $this->NotifyResult;
  }
}