<?php
/**
 * Created by PhpStorm.
 * User: 淼
 * Date: 2015/4/29
 * Time: 11:41
 */

namespace Org\ZhiHui;

class Order {
  private $modOrder = null;
  private $modOrderReview = null;
  
  function __construct() {
    $this->modOrder = M("order");
    $this->modOrderReview = M("order_review");
  }
  
  //region 缓存
  //region 工单信息缓存
  /**
   * 获取工单信息
   * @param $_order_sn 工单sn
   * @return mixed
   */
  public function GetOrderDetails($_order_sn){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetOrderDetailsCache($_order_sn);

      if(empty($Info)){
        $Info = $this->GetOrderDetailsDb($_order_sn);
      }
    }else{
      $Info = $this->GetOrderDetailsDb($_order_sn);
    }

    if(IsArray($Info)){
      
      //region 附件信息
      $clsPicture = new \Org\ZhiHui\Picture();

      if(IsNum($Info["attachment"], false, false)){
        $FileInfo = $clsPicture->GetPictureDetails($Info["attachment"]);
        $FileSizeKB = $FileInfo["pic_size"]/1024;
        $FileSizeMB = $FileSizeKB/1024;

        $FileInfo["full_file"] = FullImageUrl($FileInfo["file"]);
        $FileInfo["pic_size_kb"] = round($FileSizeKB, 2);
        $FileInfo["pic_size_mb"] = round($FileSizeMB, 2);
        
        $FileInfo["add_date"] = Time2FullDate($FileInfo["add_time"], "Y-m-d");
        
        //region 获取文件图标
        $sIcon = "";
        switch($FileInfo["file_ext"]){
          case "docx":
            $sIcon = "fa-file-word-o";
            break;

          case "doc":
            $sIcon = "fa-file-word-o";
            break;

          case "xls":
            $sIcon = "fa-file-excel-o";
            break;

          case "xlsx":
            $sIcon = "fa-file-excel-o";
            break;

          case "rar":
            $sIcon = "fa-file-zip-o";
            break;

          case "zip":
            $sIcon = "fa-file-zip-o";
            break;
          
          default:
            $sIcon = "fa-file-o";
            break;
        }

        $FileInfo["icon"] = $sIcon;
        //endregion 获取文件图标

        $Info["attachment"] = $FileInfo;

        if(!IsNum($Info["reservation_time"], false, false)){
          $Info["reservation_time"] = "";
        }

        if(!IsNum($Info["pay_time"], false, false)){
          $Info["pay_time"] = "";
        }

        if(!IsNum($Info["visit_time"], false, false)){
          $Info["visit_time"] = "";
        }

        if(!IsFloat($Info["pay_amount"], 2, false, false)){
          $Info["pay_amount"] = "";
        }
      }
      //endregion 附件信息
    }

    return $Info;
  }

  /**
   * 设置工单信息数据缓存
   * @param $_order_sn 工单sn
   * @param array $_data 工单信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetOrderDetailsCache($_order_sn, $_data=NULL){
    F($this->GetOrderDetailsCacheKey($_order_sn), $_data);
  }

  /**
   * 获取工单信息数据缓存
   * @param $_order_sn 工单sn
   * @return mixed
   */
  private function GetOrderDetailsCache($_order_sn){
    return F($this->GetOrderDetailsCacheKey($_order_sn));
  }

  /**
   * 获取工单信息缓存Key
   * @param $_order_sn 工单sn
   * @return mixed
   */
  private function GetOrderDetailsCacheKey($_order_sn){
    return "Order/Details/{$_order_sn}";
  }

  /**
   * todo:从数据库中获取工单信息数据
   * @param $_order_sn 工单sn
   *
   * @return mixed
   */
  private function GetOrderDetailsDb($_order_sn){
    $OrderDetails = array();

    if(!IsNum($_order_sn, false, false)){
      return $OrderDetails;
    }

    $sWhere = "order_sn='{$_order_sn}'";
    $OrderDetails = $this->modOrder->where($sWhere)->find();

    if(!empty($OrderDetails)){
      $this->SetOrderDetailsCache($_order_sn, $OrderDetails);
    }

    return $OrderDetails;
  }
  //endregion 工单信息缓存

  //region 工单审批信息缓存
  /**
   * 获取工单审批信息
   * @param $_order_review_id 工单审核id
   * @return mixed
   */
  public function GetOrderReviewDetails($_order_review_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetOrderReviewDetailsCache($_order_review_id);

      if(empty($Info)){
        $Info = $this->GetOrderReviewDetailsDb($_order_review_id);
      }
    }else{
      $Info = $this->GetOrderReviewDetailsDb($_order_review_id);
    }
    
    if(IsArray($Info)){
      if(IsNum($Info["account_id"], false, false)){
        if($Info["account_type"] === "admin"){
          $clsAdmin = new \Org\ZhiHui\Admin();
          $AccountInfo = $clsAdmin->GetAdminDetails($Info["account_id"]);

          $sAccountName = $AccountInfo["real_name"]."（{$AccountInfo["group_name"]}）";
        }else{
          $clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
          $AccountInfo = $clsBrokerCompany->GetBrokerCompanyAccountDetails($Info["account_id"]);
          $sAccountName = $AccountInfo["real_name"];
        }

        $Info["account_name"] = $sAccountName;
      }
    }

    return $Info;
  }

  /**
   * 设置工单审批信息数据缓存
   * @param $_order_review_id 工单审核id
   * @param array $_data 工单审批信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetOrderReviewDetailsCache($_order_review_id, $_data=NULL){
    F($this->GetOrderReviewDetailsCacheKey($_order_review_id), $_data);
  }

  /**
   * 获取工单审批信息数据缓存
   * @param $_order_review_id 工单审核id
   * @return mixed
   */
  private function GetOrderReviewDetailsCache($_order_review_id){
    return F($this->GetOrderReviewDetailsCacheKey($_order_review_id));
  }

  /**
   * 获取工单审批信息缓存Key
   * @param $_order_review_id 工单审核id
   * @return mixed
   */
  private function GetOrderReviewDetailsCacheKey($_order_review_id){
    return "Order/Review/Details/{$_order_review_id}";
  }

  /**
   * todo:从数据库中获取工单审批信息数据
   * @param $_order_review_id 工单审核id
   *
   * @return mixed
   */
  private function GetOrderReviewDetailsDb($_order_review_id){
    $OrderReviewDetails = array();

    if(!IsNum($_order_review_id, false, false)){
      return $OrderReviewDetails;
    }

    $sWhere = "id='{$_order_review_id}'";
    $OrderReviewDetails = $this->modOrderReview->where($sWhere)->find();

    if(!empty($OrderReviewDetails)){
      $this->SetOrderReviewDetailsCache($_order_review_id, $OrderReviewDetails);
    }

    return $OrderReviewDetails;
  }
  //endregion 工单审批信息缓存
  //endregion 缓存

  //region 工单
  /**
   * 创建工单号
   */
  public function CreateOrderSn(){
    mt_srand((double) microtime() * 1000000);
    $OrderSn = date('YmdHis') . str_pad(mt_rand(1, 99999), 5, '0', 0);
    $dbOrderData = $this->modOrder->where("order_sn='{$OrderSn}'")->find();

    if(!empty($dbOrderData)){
      $this->CreateOrderSn();
    }

    return $OrderSn;
  }

  /**
   * todo: 获取未处理工单数量
   *
   * @param $_account_info
   *
   * @return int
   */
  public function GetUntreatedOrderNumber($_account_info, $_account_type){
    $nUntreatedNumber = 0;

    if(!IsArray($_account_info)){
      return $nUntreatedNumber;
    }

    $clsLogin = new \Org\ZhiHui\Login();

    switch($_account_type){
      case $clsLogin->GetLoginTypeSellerManager():
        $sWhere = "(review=1 or review=2) and (status=0 or status=1)";
        $sWhere .= " and broker_company_id={$_account_info["broker_company_id"]}";
        break;

      case $clsLogin->GetLoginTypeBrokerCompany():
        $sWhere = "review=0";
        $sWhere .= " and broker_company_id={$_account_info["broker_company_id"]}";
        break;

      default:
        $sWhere = "review=0";
        break;
    }

    $nUntreatedNumber = $this->modOrder->where($sWhere)->count();

    if(!IsNum($nUntreatedNumber, false, false)){
      $nUntreatedNumber = 0;
    }

    return $nUntreatedNumber;
  }
  //endregion 工单

  //region 工单审批
  /**
   * todo: 工单编号获取审批数据列表
   *
   * @param $_order_sn 工单编号
   *
   * @return array
   */
  public function OrderSnGetReviewList($_order_sn){
    $ReviewList = array();
    
    if(IsN($_order_sn)){
      return $ReviewList;
    }
    
    $sField = "id";
    $sWhere = "order_sn='{$_order_sn}'";
    $sOrder = "add_time desc";
    $ReviewIdList = $this->modOrderReview->field($sField)->where($sWhere)->order($sOrder)->select();
    
    foreach($ReviewIdList as $key=>$val){
      $ReviewInfo = $this->GetOrderReviewDetails($val["id"]);
      
      array_push($ReviewList, $ReviewInfo);
    }
    
    return $ReviewList;
  }

  /**
   * todo: 根据工单编号获取最后一条审核信息
   * @param $_order_sn
   */
  public function OrderSnGetLastReviewInfo($_order_sn, $_status){
    $ReviewInfo = array();

    if(IsN($_order_sn)){
      return $ReviewInfo;
    }
    
    if(!IsNum($_status, true, false)){
      return $ReviewInfo;
    }

    $sField = "id";
    $sWhere = "order_sn='{$_order_sn}'";
    $sWhere .= " and status='{$_status}'";
    $sOrder = "id desc";
    
    $ReviewId = $this->modOrderReview->field($sField)->where($sWhere)->order($sOrder)->getField($sField);

    if(IsNum($ReviewId, false, false)){
      $ReviewInfo = $this->GetOrderReviewDetails($ReviewId);
    }

    return $ReviewInfo;
  }
  //endregion 工单审批
}