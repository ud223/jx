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
  //region 预约信息缓存
  /**
   * 获取预约信息
   * @param $_order_sn 预约sn
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
      $clsUser = new \Org\ZhiHui\User();
      $clsPicture = new \Org\ZhiHui\Picture();
      $clsFile = new \Org\ZhiHui\File();
      $clsProduct = new \Org\ZhiHui\Product();
      $clsCustomer = new \Org\ZhiHui\Customer();
      
      //region 附件信息
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

      //region 先锋审核的附件信息
      if(IsNum($Info["attachment_id"], false, false)){
        
        switch($Info["attachment_type"]){
          case $clsProduct->_AttachmentType["image"]:
            $FileInfo = $clsPicture->GetPictureDetails($Info["attachment_id"]);
            $FileSizeKB = $FileInfo["pic_size"]/1024;
            $FileSizeMB = $FileSizeKB/1024;

            $FileInfo["full_file"] = FullImageUrl($FileInfo["file"]);
            $FileInfo["icon"] = "";
            break;

          case $clsProduct->_AttachmentType["file"]:
            $FileInfo = $clsFile->GetFileDetails($Info["attachment_id"]);
            $FileSizeKB = $FileInfo["file_size"]/1024;
            $FileSizeMB = $FileSizeKB/1024;

            $FileInfo["full_file"] = FullImageUrl($FileInfo["file_path"]);

            //region 获取文件图标
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

              case "pdf":
                $sIcon = "fa-file-pdf-o";
                break;

              default:
                $sIcon = "fa-file-o";
                break;
            }

            $FileInfo["icon"] = $sIcon;
            //endregion 获取文件图标
            break;
        }

        $FileInfo["file_size_kb"] = round($FileSizeKB, 2);
        $FileInfo["file_size_mb"] = round($FileSizeMB, 2);

        $FileInfo["add_date"] = Time2FullDate($FileInfo["add_time"], "Y-m-d");

        $Info["xf_attachment"] = $FileInfo;
      }else{
        $Info["xf_attachment"] = array();
      }
      //endregion 先锋审核的附件信息
      
      //region 团队长姓名
      if(IsNum($Info["seller_captain_id"], false, false)){
        $CaptainInfo = $clsUser->GetUserDetails($Info["seller_captain_id"]);
        $Info["seller_captain_name"] = $CaptainInfo["real_name"];
      }else{
        $Info["seller_captain_name"] = "";
      }
      //endregion 团队长姓名
  
      //region 客户经理姓名
      if(IsNum($Info["seller_member_id"], false, false)){
        $MemberInfo = $clsUser->GetUserDetails($Info["seller_member_id"]);
        $Info["seller_member_name"] = $MemberInfo["real_name"];
        $Info["seller_id"] = $MemberInfo["id"];
        $Info["seller_name"] = $MemberInfo["real_name"];
      }else{
        $Info["seller_member_name"] = "";
        $Info["seller_id"] = $CaptainInfo["id"];
        $Info["seller_name"] = $CaptainInfo["real_name"];
      }
      //endregion 客户经理姓名
      
      //region 客户
      if(IsNum($Info["customer_id"], false, false)){
        $Info["customer"] = $clsCustomer->GetCustomerDetails($Info["customer_id"]);
      }
      //endregion 客户
  
      //region 产品
      if(IsNum($Info["product_snapshots_id"], false, false)){
        $Info["product_snapshots"] = $clsProduct->GetProductSnapshotsDetails($Info["product_snapshots_id"]);
      }else{
        $Info["product_snapshots"] = array();
      }
      //endregion 产品
      
      if(IsNum($Info["signed_time"], false, false)){
        $Info["signed_date"] = Time2FullDate($Info["signed_time"], "Y-m-d H:i");
      }
  
      if(IsNum($Info["reservation_time"], false, false)){
        $Info["reservation_date"] = Time2FullDate($Info["reservation_time"], "Y-m-d H:i");
      }
  
      if(IsNum($Info["pay_time"], false, false)){
        $Info["pay_date"] = Time2FullDate($Info["pay_time"], "Y-m-d H:i");
      }
      
      $StatusInfo = $this->OrderStatusGetOrderStatusConfig($Info["status"]);
  
      $Info["status_info"] = $StatusInfo;
    }

    return $Info;
  }

  /**
   * 设置预约信息数据缓存
   * @param $_order_sn 预约sn
   * @param array $_data 预约信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetOrderDetailsCache($_order_sn, $_data=NULL){
    F($this->GetOrderDetailsCacheKey($_order_sn), $_data);
  }

  /**
   * 获取预约信息数据缓存
   * @param $_order_sn 预约sn
   * @return mixed
   */
  private function GetOrderDetailsCache($_order_sn){
    return F($this->GetOrderDetailsCacheKey($_order_sn));
  }

  /**
   * 获取预约信息缓存Key
   * @param $_order_sn 预约sn
   * @return mixed
   */
  private function GetOrderDetailsCacheKey($_order_sn){
    return "Order/Details/{$_order_sn}";
  }

  /**
   * todo:从数据库中获取预约信息数据
   * @param $_order_sn 预约sn
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
  //endregion 预约信息缓存

  //region 预约审批信息缓存
  /**
   * 获取预约审批信息
   * @param $_order_review_id 预约审核id
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
   * 设置预约审批信息数据缓存
   * @param $_order_review_id 预约审核id
   * @param array $_data 预约审批信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetOrderReviewDetailsCache($_order_review_id, $_data=NULL){
    F($this->GetOrderReviewDetailsCacheKey($_order_review_id), $_data);
  }

  /**
   * 获取预约审批信息数据缓存
   * @param $_order_review_id 预约审核id
   * @return mixed
   */
  private function GetOrderReviewDetailsCache($_order_review_id){
    return F($this->GetOrderReviewDetailsCacheKey($_order_review_id));
  }

  /**
   * 获取预约审批信息缓存Key
   * @param $_order_review_id 预约审核id
   * @return mixed
   */
  private function GetOrderReviewDetailsCacheKey($_order_review_id){
    return "Order/Review/Details/{$_order_review_id}";
  }

  /**
   * todo:从数据库中获取预约审批信息数据
   * @param $_order_review_id 预约审核id
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
  //endregion 预约审批信息缓存
  //endregion 缓存

  //region 预约状态
  /**
   * todo: 预约状态配置
   * @return array
   */
  public function OrderStatusConfig(){
    $StatusConfig = array(
      array(
        "status"=>10,
        "desc"=>"等待处理",
        "desc_style"=>'<span class="label label-info">等待处理</span>',
      ),
      array(
        "status"=>11,
        "desc"=>"已处理",
        "desc_style"=>'<span class="label label-success">已处理</span>',
      ),
      array(
        "status"=>12,
        "desc"=>"已拒绝",
        "desc_style"=>'<span class="label label-danger">已拒绝</span>',
      ),
      array(
        "status"=>20,
        "desc"=>"预约中",
        "desc_style"=>'<span class="label label-info">预约中</span>',
      ),
      array(
        "status"=>21,
        "desc"=>"预约成功",
        "desc_style"=>'<span class="label label-success">预约成功</span>',
      ),
      array(
        "status"=>22,
        "desc"=>"预约失败",
        "desc_style"=>'<span class="label label-danger">预约失败</span>',
      ),
      array(
        "status"=>30,
        "desc"=>"完成预约",
        "desc_style"=>'<span class="label label-primary">完成预约</span>',
      ),
      array(
        "status"=>40,
        "desc"=>"已回访",
        "desc_style"=>'<span class="label label-primary">已回访</span>',
      ),
    );

    return $StatusConfig;
  }

  /**
   * todo: 通过预约状态获取预约状态配置信息
   * @param $_status
   *
   * @return array|mixed
   */
  public function OrderStatusGetOrderStatusConfig($_status){
    if(!IsNum($_status, false, false)){
      return array();
    }

    $StatusConfig = $this->OrderStatusConfig();
    $StatusInfo = array();

    foreach($StatusConfig as $key=>$val){
      if($val["status"] == $_status){
        $StatusInfo = $val;
        break;
      }
    }

    return $StatusInfo;
  }

  //region 签约
  /**
   * todo: 订单签约状态
   * @return mixed
   */
  public function OrderStatusSignatoryInfo(){
    $StatusConfig = $this->OrderStatusConfig();

    return $StatusConfig[0];
  }

  /**
   * todo: 订单签约状态值
   * @return mixed
   */
  public function OrderStatusSignatoryStatus(){
    $StatusInfo = $this->OrderStatusSignatoryInfo();

    return $StatusInfo["status"];
  }

  /**
   * todo: 订单签约批准状态
   * @return mixed
   */
  public function OrderStatusSignatoryApproveInfo(){
    $StatusConfig = $this->OrderStatusConfig();

    return $StatusConfig[1];
  }

  /**
   * todo: 订单签约批准状态值
   * @return mixed
   */
  public function OrderStatusSignatoryApproveStatus(){
    $StatusInfo = $this->OrderStatusSignatoryApproveInfo();

    return $StatusInfo["status"];
  }

  /**
   * todo: 订单签约拒绝状态
   * @return mixed
   */
  public function OrderStatusSignatoryRejectInfo(){
    $StatusConfig = $this->OrderStatusConfig();

    return $StatusConfig[2];
  }

  /**
   * todo: 订单签约拒绝状态值
   * @return mixed
   */
  public function OrderStatusSignatoryRejectStatus(){
    $StatusInfo = $this->OrderStatusSignatoryRejectInfo();

    return $StatusInfo["status"];
  }
  //endregion 签约

  //region 预约
  /**
   * todo: 订单预约状态
   * @return mixed
   */
  public function OrderStatusSubscribeInfo(){
    $StatusConfig = $this->OrderStatusConfig();

    return $StatusConfig[3];
  }

  /**
   * todo: 订单预约状态值
   * @return mixed
   */
  public function OrderStatusSubscribeStatus(){
    $StatusInfo = $this->OrderStatusSubscribeInfo();

    return $StatusInfo["status"];
  }

  /**
   * todo: 订单预约批准状态
   * @return mixed
   */
  public function OrderStatusSubscribeApproveInfo(){
    $StatusConfig = $this->OrderStatusConfig();

    return $StatusConfig[4];
  }

  /**
   * todo: 订单预约批准状态值
   * @return mixed
   */
  public function OrderStatusSubscribeApproveStatus(){
    $StatusInfo = $this->OrderStatusSubscribeApproveInfo();

    return $StatusInfo["status"];
  }

  /**
   * todo: 订单预约拒绝状态
   * @return mixed
   */
  public function OrderStatusSubscribeRejectInfo(){
    $StatusConfig = $this->OrderStatusConfig();

    return $StatusConfig[5];
  }

  /**
   * todo: 订单预约拒绝状态值
   * @return mixed
   */
  public function OrderStatusSubscribeRejectStatus(){
    $StatusInfo = $this->OrderStatusSubscribeRejectInfo();

    return $StatusInfo["status"];
  }
  //endregion 预约

  //region 消费
  /**
   * todo: 订单消费状态
   * @return mixed
   */
  public function OrderStatusPayInfo(){
    $StatusConfig = $this->OrderStatusConfig();

    return $StatusConfig[6];
  }

  /**
   * todo: 订单消费状态值
   * @return mixed
   */
  public function OrderStatusPayStatus(){
    $StatusInfo = $this->OrderStatusPayInfo();

    return $StatusInfo["status"];
  }
  //endregion 消费

  //region 回访
  /**
   * todo: 订单回访状态
   * @return mixed
   */
  public function OrderStatusReturnVisitInfo(){
    $StatusConfig = $this->OrderStatusConfig();

    return $StatusConfig[7];
  }

  /**
   * todo: 订单回访状态值
   * @return mixed
   */
  public function OrderStatusReturnVisitStatus(){
    $StatusInfo = $this->OrderStatusReturnVisitInfo();

    return $StatusInfo["status"];
  }
  //endregion 回访

  //endregion 预约状态

  //region 预约
  /**
   * 创建预约号
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
   * todo: 获取未处理预约数量
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
    $clsOrder = new \Org\ZhiHui\Order();
    switch($_account_type){
      case $clsLogin->GetLoginTypeSpecial():
        $sWhere = "(status={$clsOrder->OrderStatusSignatoryStatus()} or status={$clsOrder->OrderStatusSignatoryStatus()})";
        break;

//      case $clsLogin->GetLoginTypeSellerMember():
//        $sWhere = "review=0";
//        $sWhere .= " and broker_company_id={$_account_info["broker_company_id"]}";
//        break;
//
//      default:
//        $sWhere = "review=0";
//        break;
    }

    //$nUntreatedNumber = $this->modOrder->where($sWhere)->count();

    if(!IsNum($nUntreatedNumber, false, false)){
      $nUntreatedNumber = 0;
    }

    return $nUntreatedNumber;
  }
  //endregion 预约

  //region 预约审批
  /**
   * todo: 预约编号获取审批数据列表
   *
   * @param $_order_sn 预约编号
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
   * todo: 根据预约编号获取最后一条审核信息
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
  //endregion 预约审批
}