<?php
namespace CustomerRreservationSystem\Controller;
use Think\Controller;

class OrderController extends CommonController {
  private $modOrder = null;
  
  function __construct() {
    parent::__construct();

    $this->_Model = "预约";

    $this->modOrder = D("Order");
  }

  //region 申请
  //region 等待审批的预约
  /**
   * todo: 等待审批的预约列表
   */
  public function SignatoryOrderList(){
    $SearchParam = $this->GetOrderListSearchParam();

    if(
      $this->_AccountType == $this->_AdminAccountType
      || $this->_AccountType == $this->_BrokerCompanyAccountType
    ){
      //region 团队长
      $SellerCaptainOption = $this->modOrder->clsUser->SellerCaptainOption();
      $this->assign("SellerCaptainOption", $SellerCaptainOption);
      //endregion 团队长
    }else{
      //region 团队长
      if($this->_AccountType == $this->_SellerCaptainAccountType){
        $SellerCaptainInfo = $this->_AccountInfo;

        //region 客户经理
        $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($SellerCaptainInfo["id"]);
        $this->assign("SellerMemberOption", $SellMemberOption);
        //endregion 客户经理
      }else{
        $SellerCaptainInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["parent_user_id"]);
        $SellerMemberInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["id"]);
      }

      $this->assign("SellerCaptainInfo", $SellerCaptainInfo);
      //endregion 团队长
    }

    $OrderList = $this->modOrder->SignatoryOrderList($SellerCaptainInfo["id"], $SellerMemberInfo["id"], $this->_PagingRowCount, $SearchParam);

    $this->assign("OrderList", $OrderList["DataList"]);
    $this->assign("Page", $OrderList["PageInfo"]);

    $this->CustomDisplay("signatory_order_list");
  }

  /**
   * todo: 等待审批的预约信息
   */
  public function OrderInfo(){
    $OrderSn = RR("order_sn");

    $OrderInfo = $this->modOrder->OrderInfo($OrderSn);
    $this->assign("OrderInfo", $OrderInfo);

    if(IsArray($OrderInfo)){
      //region 客户经理
      $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($OrderInfo["seller_captain_id"]);
      $this->assign("SellerMemberOption", $SellMemberOption);
      //endregion 客户经理

      //region 客户
      if(IsNum($OrderInfo["seller_member_id"], false, false)){
        $CustomerOption = $this->modOrder->clsCustomer->CustomerOption($OrderInfo["seller_member_id"]);
      }else{
        $CustomerOption = $this->modOrder->clsCustomer->CustomerOption(0, $OrderInfo["seller_captain_id"]);
      }

      $this->assign("CustomerOption", $CustomerOption);

      $CustomerInfo = $this->modOrder->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
      $this->assign("CustomerInfo", $CustomerInfo);
      //endregion 客户
      
      //region 产品
      $ProductSnapshotsInfo = $this->modOrder->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
      $this->assign("ProductInfo", $ProductSnapshotsInfo);

      $ProductOption = $this->modOrder->clsProduct->ProductOption("type_id", $ProductSnapshotsInfo["type_id"]);
      $this->assign("ProductOption", $ProductOption);
      //endregion 产品
    }

    if(
      $this->_AccountType == $this->_AdminAccountType
      || $this->_AccountType == $this->_BrokerCompanyAccountType
    ){
      //region 团队长
      $SellerCaptainOption = $this->modOrder->clsUser->SellerCaptainOption();
      $this->assign("SellerCaptainOption", $SellerCaptainOption);
      //endregion 团队长
    }else{
      //region 团队长
      if($this->_AccountType == $this->_SellerCaptainAccountType){
        $SellerCaptainInfo = $this->_AccountInfo;

        //region 客户经理
        if(!IsArray($SellMemberOption)){
          $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($SellerCaptainInfo["id"]);
          $this->assign("SellerMemberOption", $SellMemberOption);
        }
        //endregion 客户经理
      }else{
        $SellerCaptainInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["parent_user_id"]);
        $SellerMemberInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["id"]);
        $this->assign("SellerMemberInfo", $SellerMemberInfo);
      }

      $this->assign("SellerCaptainInfo", $SellerCaptainInfo);
      //endregion 团队长

      //region 客户
      $CustomerOption = $this->modOrder->clsCustomer->CustomerOption($SellerMemberInfo["id"], $OrderInfo["seller_captain_id"]);
      $this->assign("CustomerOption", $CustomerOption);
      //endregion 客户
    }

    //region 产品类别
    $ProductTypeOption = $this->modOrder->clsProduct->ProductTypeOption();
    $this->assign("ProductTypeOption", $ProductTypeOption);
    //endregion 产品类别

    //p($OrderInfo);


    $this->CustomDisplay("order_info");
  }

  /**
   * todo: 保存销售经理创建的预约信息
   */
  public function AjaxOrderSave(){
    $sOrderSn = RR("order_sn");
    $nSellerCaptainID = RR("seller_captain_id");
    $sSellerMemberID = RR("seller_member_id");
    $nCustomerID = RR("customer");
    $nProductID = RR("product");
    $nPaymentYear = RR("payment_years");
    $nGuaranteeAmount = RR("guarantee_amount");
    $nYearPremiumAmount = RR("year_premium_amount");
    $sSellerManagerRemarks = RR("seller_manager_remarks");

    $fileAttachment = $_FILES["attachment"];

    $Result = $this->modOrder->OrderInfoSave($sOrderSn, $nSellerCaptainID, $sSellerMemberID, $nCustomerID, $nProductID, $nPaymentYear, $nGuaranteeAmount, $nYearPremiumAmount, $sSellerManagerRemarks, $fileAttachment);

    AjaxReturn($Result);
  }

  /**
   * todo: Ajax获取客户经理option
   */
  public function AjaxGetSellerMemberOption(){
    $nSellerCaptainID = RR("seller_captain_id");

    $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($nSellerCaptainID);

    AjaxReturnCorrect("", $SellMemberOption);
  }

  /**
   * todo: Ajax获取客户option
   */
  public function AjaxGetCustomerOption(){
    $nSellerCaptainID = RR("seller_captain_id");
    $nSellerMemberID = RR("seller_member_id");
    $SellMemberOption = $this->modOrder->clsCustomer->CustomerOption($nSellerMemberID, $nSellerCaptainID);

    AjaxReturnCorrect("", $SellMemberOption);
  }

  //endregion 等待审批的预约

  //region 签约预约
  /**
   * todo: 签约预约列表
   */
  public function SubscribeOrderList(){
    $SearchParam = $this->GetOrderListSearchParam();

    if(
      $this->_AccountType == $this->_AdminAccountType
      || $this->_AccountType == $this->_BrokerCompanyAccountType
    ){
      //region 团队长
      $SellerCaptainOption = $this->modOrder->clsUser->SellerCaptainOption();
      $this->assign("SellerCaptainOption", $SellerCaptainOption);
      //endregion 团队长
    }else{
      //region 团队长
      if($this->_AccountType == $this->_SellerCaptainAccountType){
        $SellerCaptainInfo = $this->_AccountInfo;

        //region 客户经理
        $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($SellerCaptainInfo["id"]);
        $this->assign("SellerMemberOption", $SellMemberOption);
        //endregion 客户经理
      }else{
        $SellerCaptainInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["parent_user_id"]);
        $SellerMemberInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["id"]);
      }

      $this->assign("SellerCaptainInfo", $SellerCaptainInfo);
      //endregion 团队长
    }

    $OrderList = $this->modOrder->SubscribeOrderList($SellerCaptainInfo["id"], $SellerMemberInfo["id"], $this->_PagingRowCount, $SearchParam);
    
    $OrderStatusConfig = $this->modOrder->clsOrder->OrderStatusConfig();

    $this->assign("OrderList", $OrderList["DataList"]);
    $this->assign("Page", $OrderList["PageInfo"]);
    $this->assign("OrderStatusConfig", $OrderStatusConfig);
  
    if($this->_MobileClient){
      $this->CustomDisplay("wap_subscribe_order_list");
    }else{
      $this->CustomDisplay("subscribe_order_list");
    }
  }
  
  /**
   * todo: 签约预约列表
   */
  public function AjaxSubscribeOrderList(){
    $nPage = RR("page");
    
    if(
      $this->_AccountType == $this->_AdminAccountType
      || $this->_AccountType == $this->_BrokerCompanyAccountType
    ){
      //region 团队长
      $SellerCaptainOption = $this->modOrder->clsUser->SellerCaptainOption();
      $this->assign("SellerCaptainOption", $SellerCaptainOption);
      //endregion 团队长
    }else{
      //region 团队长
      if($this->_AccountType == $this->_SellerCaptainAccountType){
        $SellerCaptainInfo = $this->_AccountInfo;
        
        //region 客户经理
        $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($SellerCaptainInfo["id"]);
        $this->assign("SellerMemberOption", $SellMemberOption);
        //endregion 客户经理
      }else{
        $SellerCaptainInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["parent_user_id"]);
        $SellerMemberInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["id"]);
      }
      
      $this->assign("SellerCaptainInfo", $SellerCaptainInfo);
      //endregion 团队长
    }
    
    $OrderList = $this->modOrder->AjaxSubscribeOrderList($SellerCaptainInfo["id"], $SellerMemberInfo["id"], $nPage, $this->_PagingRowCount);
  
    AjaxReturnCorrect("", $OrderList);
  }
  
  /**
   * todo: 添加预约预约
   */
  public function SubscribeOrderInfoAdd(){
    $OrderSn = RR("order_sn");
    
    $OrderInfo = $this->modOrder->OrderInfo($OrderSn);
    $this->assign("OrderInfo", $OrderInfo);

    if(IsArray($OrderInfo)){
      //region 客户经理
      $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($OrderInfo["seller_captain_id"]);
      $this->assign("SellerMemberOption", $SellMemberOption);
      //endregion 客户经理
    
      //region 客户
      if(IsNum($OrderInfo["seller_member_id"], false, false)){
        $CustomerOption = $this->modOrder->clsCustomer->CustomerOption($OrderInfo["seller_member_id"]);
      }else{
        $CustomerOption = $this->modOrder->clsCustomer->CustomerOption(0, $OrderInfo["seller_captain_id"]);
      }
    
      $this->assign("CustomerOption", $CustomerOption);
    
      $CustomerInfo = $this->modOrder->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
      $this->assign("CustomerInfo", $CustomerInfo);
      //endregion 客户
    
      //region 产品
      $ProductSnapshotsInfo = $this->modOrder->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
      $this->assign("ProductInfo", $ProductSnapshotsInfo);
    
      $ProductOption = $this->modOrder->clsProduct->ProductOption("type_id", $ProductSnapshotsInfo["type_id"]);
      $this->assign("ProductOption", $ProductOption);
      //endregion 产品
    }
  
    if(
      $this->_AccountType == $this->_AdminAccountType
      || $this->_AccountType == $this->_BrokerCompanyAccountType
    ){
      //region 团队长
      $SellerCaptainOption = $this->modOrder->clsUser->SellerCaptainOption();
      $this->assign("SellerCaptainOption", $SellerCaptainOption);
      //endregion 团队长
    }else{
      //region 团队长
      if($this->_AccountType == $this->_SellerCaptainAccountType){
        $SellerCaptainInfo = $this->_AccountInfo;
      
        //region 客户经理
        if(!IsArray($SellMemberOption)){
          $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($SellerCaptainInfo["id"]);
          $this->assign("SellerMemberOption", $SellMemberOption);
        }
        //endregion 客户经理
      }else{
        $SellerCaptainInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["parent_user_id"]);
        $SellerMemberInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["id"]);
        $this->assign("SellerMemberInfo", $SellerMemberInfo);
      }
    
      $this->assign("SellerCaptainInfo", $SellerCaptainInfo);
      //endregion 团队长
    
      //region 客户
      $CustomerOption = $this->modOrder->clsCustomer->CustomerOption($SellerMemberInfo["id"], $OrderInfo["seller_captain_id"]);
      $this->assign("CustomerOption", $CustomerOption);
      //endregion 客户
    }
  
    //region 产品类别
    $ProductTypeOption = $this->modOrder->clsProduct->ProductTypeOption();
    $this->assign("ProductTypeOption", $ProductTypeOption);
    //endregion 产品类别
    
    //region 客户
    $CustomerInfo = $this->modOrder->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
    $this->assign("CustomerInfo", $CustomerInfo);
    //endregion 客户
    
    $this->assign("AttachmentFileType", $this->modOrder->clsProduct->_AttachmentType["file"]);
    $this->assign("AttachmentImageType", $this->modOrder->clsProduct->_AttachmentType["image"]);
    
    $this->CustomDisplay("subscribe_order_info_add");
  }

  /**
   * todo: 预约预约时间
   */
  public function SubscribeOrderInfo(){
    $OrderSn = RR("order_sn");

    $OrderInfo = $this->modOrder->OrderInfo($OrderSn);
    $this->assign("OrderInfo", $OrderInfo);

    //region 产品
    $ProductSnapshotsInfo = $this->modOrder->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
    $this->assign("ProductInfo", $ProductSnapshotsInfo);
    //endregion 产品

    //region 客户
    $CustomerInfo = $this->modOrder->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
    $this->assign("CustomerInfo", $CustomerInfo);
    //endregion 客户

    $this->assign("AttachmentFileType", $this->modOrder->clsProduct->_AttachmentType["file"]);
    $this->assign("AttachmentImageType", $this->modOrder->clsProduct->_AttachmentType["image"]);

    $this->CustomDisplay("subscribe_order_info");
  }
  
  public function AjaxOrderViewInfo(){
    $OrderSn = RR("order_sn");
  
    $OrderInfo = $this->modOrder->OrderInfo($OrderSn);
    
    AjaxReturnCorrect("", $OrderInfo);
  }
  
  /**
   * todo: 保存预约预约时间
   */
  public function SubscribeOrderInfoSave(){
    $sOrderSn = RR("order_sn");
    $nSellerCaptainID = RR("seller_captain_id");
    $sSellerMemberID = RR("seller_member_id");
    $nCustomerID = RR("customer");
    $nProductID = RR("product");
    $sReservationDate = RR("reservation_time");
    $nPaymentYear = RR("payment_years");
    $nGuaranteeAmount = RR("guarantee_amount");
    $nYearPremiumAmount = RR("year_premium_amount");
    $sSellerManagerRemarks = RR("seller_manager_remarks");
    
    $fileAttachment = $_FILES["attachment"];
    
    $Result = $this->modOrder->SubscribeOrderInfoSave($sOrderSn, $nSellerCaptainID, $sSellerMemberID, $nCustomerID, $nProductID, $sReservationDate, $nPaymentYear, $nGuaranteeAmount, $nYearPremiumAmount, $sSellerManagerRemarks, $fileAttachment);
    
    AjaxReturn($Result);
  }

  /**
   * todo: 保存预约预约时间
   */
  public function ChangeReservationTime(){
    $sOrderSn = RR("order_sn");
    $sReservationDate = RR("reservation_time");

    $Result = $this->modOrder->ChangeReservationTime($sOrderSn, $sReservationDate);

    AjaxReturn($Result);
  }
  
  /**
   * todo: 保存预约预约时间
   */
  public function SubscribeOrderInfoSaveBak(){
    $sOrderSn = RR("order_sn");
    $nSellerCaptainID = RR("seller_captain_id");
    $sSellerMemberID = RR("seller_member_id");
    $nCustomerID = RR("customer");
    $nProductID = RR("product");
    $sReservationDate = RR("reservation_time");
    $nPaymentYear = RR("payment_years");
    $nGuaranteeAmount = RR("guarantee_amount");
    $nYearPremiumAmount = RR("year_premium_amount");
    $sSellerManagerRemarks = RR("seller_manager_remarks");
    
    $fileAttachment = $_FILES["attachment"];
    
    $Result = $this->modOrder->SubscribeOrderInfoSave($sOrderSn, $nSellerCaptainID, $sSellerMemberID, $nCustomerID, $nProductID, $sReservationDate, $nPaymentYear, $nGuaranteeAmount, $nYearPremiumAmount, $sSellerManagerRemarks, $fileAttachment);
    
    AjaxReturn($Result);
  }
  
  public function AjaxSubscribeOrderInfo(){
    $sOrderSn = RR("order_sn");
  
    $OrderInfo = $this->modOrder->PlanInfo($sOrderSn);
  
    AjaxReturnCorrect("", $OrderInfo);
  }
  
  public function AjaxPlanList(){
    $AuditedPlanList = $this->modOrder->AuditedPlanList($this->_AccountID);
    
    AjaxReturnCorrect("", $AuditedPlanList);
  }
  //endregion 签约预约

  //region 消费预约
  /**
   * todo: 消费预约列表
   */
  public function PayOrderList(){
    $SearchParam = $this->GetOrderListSearchParam();

    if(
      $this->_AccountType == $this->_AdminAccountType
      || $this->_AccountType == $this->_BrokerCompanyAccountType
    ){
      //region 团队长
      $SellerCaptainOption = $this->modOrder->clsUser->SellerCaptainOption();
      $this->assign("SellerCaptainOption", $SellerCaptainOption);
      //endregion 团队长
    }else{
      //region 团队长
      if($this->_AccountType == $this->_SellerCaptainAccountType){
        $SellerCaptainInfo = $this->_AccountInfo;

        //region 客户经理
        $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($SellerCaptainInfo["id"]);
        $this->assign("SellerMemberOption", $SellMemberOption);
        //endregion 客户经理
      }else{
        $SellerCaptainInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["parent_user_id"]);
        $SellerMemberInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["id"]);
      }

      $this->assign("SellerCaptainInfo", $SellerCaptainInfo);
      //endregion 团队长
    }

    $OrderList = $this->modOrder->PayOrderList($SellerCaptainInfo["id"], $SellerMemberInfo["id"], $this->_PagingRowCount, $SearchParam);

    $this->assign("OrderList", $OrderList["DataList"]);
    $this->assign("Page", $OrderList["PageInfo"]);

    $this->CustomDisplay("pay_order_list");
  }

  /**
   * todo: 消费预约时间
   */
  public function PayOrderInfo(){
    $OrderSn = RR("order_sn");

    $OrderInfo = $this->modOrder->OrderInfo($OrderSn);
    $this->assign("OrderInfo", $OrderInfo);

    //region 产品
    $ProductInfo = $this->modOrder->clsProduct->GetProductDetails($OrderInfo["product_id"]);
    $this->assign("ProductInfo", $ProductInfo);
    //endregion 产品

    //region 客户
    $CustomerInfo = $this->modOrder->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
    $this->assign("CustomerInfo", $CustomerInfo);
    //endregion 客户

    $this->CustomDisplay("pay_order_info");
  }

  /**
   * todo: 保存预约预约时间
   */
  public function PayOrderInfoSave(){
    $OrderSn = RR("order_sn");
    $nPayAmount = RR("pay_amount");
    $nPayTime = RR("pay_time");

    $Result = $this->modOrder->PayOrderInfoSave($OrderSn, $nPayAmount, $nPayTime);

    AjaxReturn($Result);
  }

  /**
   * todo: 确认消费预约
   */
  public function AjaxConfirmPaymentOrderInfo(){
    $sOrderSn = RR("order_sn");
    $nYearPremiumAmount = RR("year_premium_amount");
    $nPaymentYears = RR("payment_years");
    $nGuaranteeAmount = RR("guarantee_amount");
    $nProductID = RR("product_id");
    $Attachment = $_FILES["attachment"];

    $Result = $this->modOrder->ConfirmPaymentOrderInfoSave($sOrderSn, $nYearPremiumAmount, $nPaymentYears, $nGuaranteeAmount, $nProductID, $Attachment);

    AjaxReturn($Result);
  }
  //endregion 消费预约

  //region 回访预约
  /**
   * todo: 回访预约列表
   */
  public function ReturnVisitOrderList(){
    $SearchParam = $this->GetOrderListSearchParam();

    if(
      $this->_AccountType == $this->_AdminAccountType
      || $this->_AccountType == $this->_BrokerCompanyAccountType
    ){
      //region 团队长
      $SellerCaptainOption = $this->modOrder->clsUser->SellerCaptainOption();
      $this->assign("SellerCaptainOption", $SellerCaptainOption);
      //endregion 团队长
    }else{
      //region 团队长
      if($this->_AccountType == $this->_SellerCaptainAccountType){
        $SellerCaptainInfo = $this->_AccountInfo;

        //region 客户经理
        $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($SellerCaptainInfo["id"]);
        $this->assign("SellerMemberOption", $SellMemberOption);
        //endregion 客户经理
      }else{
        $SellerCaptainInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["parent_user_id"]);
        $SellerMemberInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["id"]);
      }

      $this->assign("SellerCaptainInfo", $SellerCaptainInfo);
      //endregion 团队长
    }

    $OrderList = $this->modOrder->ReturnVisitOrderList($SellerCaptainInfo["id"], $SellerMemberInfo["id"], $this->_PagingRowCount, $SearchParam);

    $this->assign("OrderList", $OrderList["DataList"]);
    $this->assign("Page", $OrderList["PageInfo"]);

    $this->CustomDisplay("return_visit_order_list");
  }

  /**
   * todo: 回访预约时间
   */
  public function ReturnVisitOrderInfo(){
    $OrderSn = RR("order_sn");

    $OrderInfo = $this->modOrder->OrderInfo($OrderSn);
    $this->assign("OrderInfo", $OrderInfo);

    //region 产品
    $ProductInfo = $this->modOrder->clsProduct->GetProductDetails($OrderInfo["product_id"]);
    $this->assign("ProductInfo", $ProductInfo);
    //endregion 产品

    //region 客户
    $CustomerInfo = $this->modOrder->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
    $this->assign("CustomerInfo", $CustomerInfo);
    //endregion 客户

    $this->CustomDisplay("return_visit_order_info");
  }

  /**
   * todo: 保存预约预约时间
   */
  public function ReturnVisitOrderInfoSave(){
    $OrderSn = RR("order_sn");
    $nVisitTime = RR("visit_time");

    $Result = $this->modOrder->ReturnVisitOrderInfoSave($OrderSn, $nVisitTime);

    AjaxReturn($Result);
  }
  //endregion 回访预约

  //region 保险计划书
  public function PlanList(){
    $SearchParam = $this->GetOrderListSearchParam();

    if(
      $this->_AccountType == $this->_AdminAccountType
      || $this->_AccountType == $this->_BrokerCompanyAccountType
    ){
      //region 团队长
      $SellerCaptainOption = $this->modOrder->clsUser->SellerCaptainOption();
      $this->assign("SellerCaptainOption", $SellerCaptainOption);
      //endregion 团队长
    }else{
      //region 团队长
      if($this->_AccountType == $this->_SellerCaptainAccountType){
        $SellerCaptainInfo = $this->_AccountInfo;

        //region 客户经理
        $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($SellerCaptainInfo["id"]);
        $this->assign("SellerMemberOption", $SellMemberOption);
        //endregion 客户经理
      }else{
        $SellerCaptainInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["parent_user_id"]);
        $SellerMemberInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["id"]);
      }

      $this->assign("SellerCaptainInfo", $SellerCaptainInfo);
      //endregion 团队长
    }

    $OrderList = $this->modOrder->PlanList($SellerCaptainInfo["id"], $SellerMemberInfo["id"], $this->_PagingRowCount, $SearchParam);

    $this->assign("AccountType", $this->_AccountType);
    $this->assign("SellerMemberAccountType", $this->_SellerMemberAccountType);
    $this->assign("OrderList", $OrderList["DataList"]);
    $this->assign("Page", $OrderList["PageInfo"]);

    $this->CustomDisplay("plan_order_list");
  }
  
  public function AjaxPlanInfo(){
    $sOrderSn = RR("order_sn");
  
    $OrderInfo = $this->modOrder->PlanInfo($sOrderSn);
    
    AjaxReturnCorrect("", $OrderInfo);
  }

  /**
   * todo: 计划书信息
   */
  public function PlanInfo(){
    $OrderSn = RR("order_sn");

    $OrderInfo = $this->modOrder->PlanInfo($OrderSn);
    $this->assign("OrderInfo", $OrderInfo);

    if(IsArray($OrderInfo)){
      //region 客户经理
      $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($OrderInfo["seller_captain_id"]);
      $this->assign("SellerMemberOption", $SellMemberOption);
      //endregion 客户经理

      //region 客户
      if(IsNum($OrderInfo["seller_member_id"], false, false)){
        $CustomerOption = $this->modOrder->clsCustomer->CustomerOption($OrderInfo["seller_member_id"]);
      }else{
        $CustomerOption = $this->modOrder->clsCustomer->CustomerOption(0, $OrderInfo["seller_captain_id"]);
      }

      $this->assign("CustomerOption", $CustomerOption);

      $CustomerInfo = $this->modOrder->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
      $this->assign("CustomerInfo", $CustomerInfo);
      //endregion 客户

      //region 产品
      $ProductSnapshotsInfo = $this->modOrder->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
      $this->assign("ProductInfo", $ProductSnapshotsInfo);

      $ProductOption = $this->modOrder->clsProduct->ProductOption("type_id", $ProductSnapshotsInfo["type_id"]);
      $this->assign("ProductOption", $ProductOption);
      //endregion 产品
    }

    if(
      $this->_AccountType == $this->_AdminAccountType
      || $this->_AccountType == $this->_BrokerCompanyAccountType
    ){
      //region 团队长
      $SellerCaptainOption = $this->modOrder->clsUser->SellerCaptainOption();
      $this->assign("SellerCaptainOption", $SellerCaptainOption);
      //endregion 团队长
    }else{
      //region 团队长
      if($this->_AccountType == $this->_SellerCaptainAccountType){
        $SellerCaptainInfo = $this->_AccountInfo;

        //region 客户经理
        if(!IsArray($SellMemberOption)){
          $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($SellerCaptainInfo["id"]);
          $this->assign("SellerMemberOption", $SellMemberOption);
        }
        //endregion 客户经理
      }else{
        $SellerCaptainInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["parent_user_id"]);
        $SellerMemberInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["id"]);
        $this->assign("SellerMemberInfo", $SellerMemberInfo);
      }

      $this->assign("SellerCaptainInfo", $SellerCaptainInfo);
      //endregion 团队长

      //region 客户
      $CustomerOption = $this->modOrder->clsCustomer->CustomerOption($SellerMemberInfo["id"], $OrderInfo["seller_captain_id"]);
      $this->assign("CustomerOption", $CustomerOption);
      //endregion 客户
    }

    //region 产品类别
    $ProductTypeOption = $this->modOrder->clsProduct->ProductTypeOption();
    $this->assign("ProductTypeOption", $ProductTypeOption);
    //endregion 产品类别

    //p($OrderInfo);


    $this->CustomDisplay("plan_info");
  }
  
  /**
   * todo: 保存计划书信息
   */
  public function AjaxPlanSave(){
    $sOrderSn = RR("order_sn");
    $nSellerCaptainID = RR("seller_captain_id");
    $sSellerMemberID = RR("seller_member_id");
    $nCustomerID = RR("customer");
    $nProductID = RR("product");
    $nPaymentYear = RR("payment_years");
    $nGuaranteeAmount = RR("guarantee_amount");
    $nYearPremiumAmount = RR("year_premium_amount");
    $sSellerManagerRemarks = RR("seller_manager_remarks");
    
    $fileAttachment = $_FILES["attachment"];
    
    $Result = $this->modOrder->PlanInfoSave($sOrderSn, $nSellerCaptainID, $sSellerMemberID, $nCustomerID, $nProductID, $nPaymentYear, $nGuaranteeAmount, $nYearPremiumAmount, $sSellerManagerRemarks, $fileAttachment);
    
    AjaxReturn($Result);
  }
  //endregion 保险计划书

  //endregion 申请

  //region 审核
  //region 待审核的签约预约
  /**
   * todo: 待审核的签约预约列表
   */
  public function UnauditSignatoryOrderList(){
    $SearchParam = $this->GetOrderListSearchParam();

    $OrderStatus = $this->modOrder->clsOrder->OrderStatusSignatoryStatus();
    $OrderList = $this->modOrder->OrderList(0, 0, $OrderStatus, $this->_PagingRowCount, $SearchParam);

    $this->assign("OrderList", $OrderList["DataList"]);
    $this->assign("Page", $OrderList["PageInfo"]);

    $this->CustomDisplay("unaudit_signatory_order_list");
  }

  /**
   * todo: 签约预约信息
   */
  public function AuditSignatoryOrderInfo(){
    $OrderSn = RR("order_sn");

    $OrderInfo = $this->modOrder->OrderInfo($OrderSn);
    $this->assign("OrderInfo", $OrderInfo);

    //region 产品快照
    $ProductSnapshotsInfo = $this->modOrder->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
    $this->assign("ProductInfo", $ProductSnapshotsInfo);
    //endregion 产品快照

    //region 客户
    $CustomerInfo = $this->modOrder->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
    $this->assign("CustomerInfo", $CustomerInfo);
    //endregion 客户

    //region 附件上传配置
    $ProductAttachmentImgCfg = GetProductAttachmentImgCfg();
    $this->assign("ProductAttachmentImgCfg", $ProductAttachmentImgCfg);

    $ProductAttachmentFileCfg = GetProductAttachmentFileCfg();
    $this->assign("ProductAttachmentFileCfg", $ProductAttachmentFileCfg);

    $this->assign("AttachmentFileType", $this->modOrder->clsProduct->_AttachmentType["file"]);
    $this->assign("AttachmentImageType", $this->modOrder->clsProduct->_AttachmentType["image"]);
    //endregion 附件上传配置

    $this->CustomDisplay("audit_signatory_order_info");
  }

  /**
   * todo: 保存预约审核
   */
  public function AuditSignatoryOrderInfoSave(){
    $sOrderSn = RR("order_sn");
    $sReviewRemarks = RR("review_remarks");
    $nReviewStatus = RR("review_status");
    $Attachment = $_FILES["attachment"];

    $Result = $this->modOrder->AuditSignatoryOrderInfoSave($this->_AccountID, $sOrderSn, $sReviewRemarks, $nReviewStatus, $Attachment);

    AjaxReturn($Result);
  }
  //endregion 待审核的签约预约

  //region 待审核的预约预约
  /**
   * todo: 待审核的预约预约列表
   */
  public function UnauditSubscribeOrderList(){
    $SearchParam = $this->GetOrderListSearchParam();
    $OrderList = $this->modOrder->AuditSubscribeOrderList($this->_PagingRowCount, $SearchParam);

    $ProductTypeOption = $this->modOrder->clsProduct->ProductTypeOption();
    $this->assign("ProductTypeOption", $ProductTypeOption);
  
    $ProductAttachmentImgCfg = GetProductAttachmentImgCfg();
    $this->assign("ProductAttachmentImgCfg", $ProductAttachmentImgCfg);
  
    $ProductAttachmentFileCfg = GetProductAttachmentFileCfg();
    $this->assign("ProductAttachmentFileCfg", $ProductAttachmentFileCfg);
    
    $this->assign("OrderList", $OrderList["DataList"]);
    $this->assign("Page", $OrderList["PageInfo"]);

    $this->CustomDisplay("unaudit_subscribe_order_list");
  }

  /**
   * todo: 待审核的预约预约信息
   */
  public function AuditSubscribeOrderInfo(){
    $OrderSn = RR("order_sn");

    $OrderInfo = $this->modOrder->SubscribeOrderInfo($OrderSn);
    $this->assign("OrderInfo", $OrderInfo);
    
    //region 产品快照
    $ProductSnapshotsInfo = $this->modOrder->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
    $this->assign("ProductInfo", $ProductSnapshotsInfo);
    //endregion 产品快照

    //region 客户
    $CustomerInfo = $this->modOrder->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
    $this->assign("CustomerInfo", $CustomerInfo);
    //endregion 客户

    //region 附件上传配置
    $ProductAttachmentImgCfg = GetProductAttachmentImgCfg();
    $this->assign("ProductAttachmentImgCfg", $ProductAttachmentImgCfg);

    $ProductAttachmentFileCfg = GetProductAttachmentFileCfg();
    $this->assign("ProductAttachmentFileCfg", $ProductAttachmentFileCfg);
    //endregion 附件上传配置
  
    $this->assign("AttachmentFileType", $this->modOrder->clsProduct->_AttachmentType["file"]);
    $this->assign("AttachmentImageType", $this->modOrder->clsProduct->_AttachmentType["image"]);

    $this->CustomDisplay("audit_subscribe_order_info");
  }

  /**
   * todo: 保存预约审核
   */
  public function AuditSubscribeOrderInfoSave(){
    $sOrderSn = RR("order_sn");
    $sReviewRemarks = RR("review_remarks");
    $nReviewStatus = RR("review_status");
    $Attachment = $_FILES["attachment"];

    $Result = $this->modOrder->AuditSubscribeOrderInfoSave($this->_AccountID, $sOrderSn, $sReviewRemarks, $nReviewStatus, $Attachment);

    AjaxReturn($Result);
  }
  //endregion 待审核的预约预约
  
  //region 待审核计划书
  public function AuditPlanList(){
    $SearchParam = $this->GetOrderListSearchParam();
    $OrderList = $this->modOrder->AuditPlanList($this->_PagingRowCount, $SearchParam);
    
    $this->assign("OrderList", $OrderList["DataList"]);
    $this->assign("Page", $OrderList["PageInfo"]);
    
    $this->CustomDisplay("audit_plan_order_list");
  }
  
  public function AjaxAuditPlanInfo(){
    $sOrderSn = RR("order_sn");
    
    $OrderInfo = $this->modOrder->PlanInfo($sOrderSn);
    
    AjaxReturnCorrect("", $OrderInfo);
  }
  
  /**
   * todo: 计划书信息
   */
  public function AuditPlanInfo(){
    $OrderSn = RR("order_sn");
    
    $OrderInfo = $this->modOrder->AuditPlanInfo($OrderSn);
    $this->assign("OrderInfo", $OrderInfo);
    
    if(IsArray($OrderInfo)){
      //region 客户经理
      $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($OrderInfo["seller_captain_id"]);
      $this->assign("SellerMemberOption", $SellMemberOption);
      //endregion 客户经理
      
      //region 客户
      if(IsNum($OrderInfo["seller_member_id"], false, false)){
        $CustomerOption = $this->modOrder->clsCustomer->CustomerOption($OrderInfo["seller_member_id"]);
      }else{
        $CustomerOption = $this->modOrder->clsCustomer->CustomerOption(0, $OrderInfo["seller_captain_id"]);
      }
      
      $this->assign("CustomerOption", $CustomerOption);
      
      $CustomerInfo = $this->modOrder->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
      $this->assign("CustomerInfo", $CustomerInfo);
      //endregion 客户
      
      //region 产品
      $ProductSnapshotsInfo = $this->modOrder->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
      $this->assign("ProductInfo", $ProductSnapshotsInfo);
      
      $ProductOption = $this->modOrder->clsProduct->ProductOption("type_id", $ProductSnapshotsInfo["type_id"]);
      $this->assign("ProductOption", $ProductOption);
      //endregion 产品
    }
    
    if(
      $this->_AccountType == $this->_AdminAccountType
      || $this->_AccountType == $this->_BrokerCompanyAccountType
    ){
      //region 团队长
      $SellerCaptainOption = $this->modOrder->clsUser->SellerCaptainOption();
      $this->assign("SellerCaptainOption", $SellerCaptainOption);
      //endregion 团队长
    }else{
      //region 团队长
      if($this->_AccountType == $this->_SellerCaptainAccountType){
        $SellerCaptainInfo = $this->_AccountInfo;
        
        //region 客户经理
        if(!IsArray($SellMemberOption)){
          $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($SellerCaptainInfo["id"]);
          $this->assign("SellerMemberOption", $SellMemberOption);
        }
        //endregion 客户经理
      }else{
        $SellerCaptainInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["parent_user_id"]);
        $SellerMemberInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["id"]);
        $this->assign("SellerMemberInfo", $SellerMemberInfo);
      }
      
      $this->assign("SellerCaptainInfo", $SellerCaptainInfo);
      //endregion 团队长
      
      //region 客户
      $CustomerOption = $this->modOrder->clsCustomer->CustomerOption($SellerMemberInfo["id"], $OrderInfo["seller_captain_id"]);
      $this->assign("CustomerOption", $CustomerOption);
      //endregion 客户
    }
    
    //region 产品类别
    $ProductTypeOption = $this->modOrder->clsProduct->ProductTypeOption();
    $this->assign("ProductTypeOption", $ProductTypeOption);
    //endregion 产品类别
    
    //p($OrderInfo);
    
    $this->CustomDisplay("audit_plan_info");
  }
  //endregion 待审核计划书
  
  //region 确认消费
  /**
   * todo: 完善确认消费预约的界面
   */
  public function AuditUpdatesSubscribeOrderInfo(){
    $sOrderSn = RR("order_sn");
    
    $OrderInfo = $this->modOrder->clsOrder->GetOrderDetails($sOrderSn);
    $this->assign("OrderInfo", $OrderInfo);

    $ProductTypeOption = $this->modOrder->clsProduct->ProductTypeOption();
    $this->assign("ProductTypeOption", $ProductTypeOption);
    
    if(IsArray($OrderInfo["product_snapshots"])){
      $ProductOption = $this->modOrder->clsProduct->ProductOption($OrderInfo["product_snapshots"]["type_id"]);
      $this->assign("ProductOption", $ProductOption);
    }
  
    $ProductAttachmentImgCfg = GetProductAttachmentImgCfg();
    $this->assign("ProductAttachmentImgCfg", $ProductAttachmentImgCfg);
  
    $ProductAttachmentFileCfg = GetProductAttachmentFileCfg();
    $this->assign("ProductAttachmentFileCfg", $ProductAttachmentFileCfg);
  
    $this->assign("AttachmentFileType", $this->modOrder->clsProduct->_AttachmentType["file"]);
    $this->assign("AttachmentImageType", $this->modOrder->clsProduct->_AttachmentType["image"]);
    
    $this->CustomDisplay("updates_subscribe_order_info");
  }
  
  public function AuditUpdatesSubscribeOrderInfoSave(){
    
  }
  //endregion 确认消费
  //endregion 审核

  //region 已完成预约
  /**
   * todo: 已完成预约列表
   */
  public function CompletedOrderList(){
    $SearchParam = $this->GetOrderListSearchParam();

    if(
      $this->_AccountType == $this->_AdminAccountType
      || $this->_AccountType == $this->_BrokerCompanyAccountType
    ){
      //region 团队长
      $SellerCaptainOption = $this->modOrder->clsUser->SellerCaptainOption();
      $this->assign("SellerCaptainOption", $SellerCaptainOption);
      //endregion 团队长
    }else{
      //region 团队长
      if($this->_AccountType == $this->_SellerCaptainAccountType){
        $SellerCaptainInfo = $this->_AccountInfo;

        //region 客户经理
        $SellMemberOption = $this->modOrder->clsUser->SellerMemberOption($SellerCaptainInfo["id"]);
        $this->assign("SellerMemberOption", $SellMemberOption);
        //endregion 客户经理
      }else{
        $SellerCaptainInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["parent_user_id"]);
        $SellerMemberInfo = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["id"]);
      }

      $this->assign("SellerCaptainInfo", $SellerCaptainInfo);
      //endregion 团队长
    }

    $OrderList = $this->modOrder->CompletedOrderList($SellerCaptainInfo["id"], $SellerMemberInfo["id"], $this->_PagingRowCount, $SearchParam);

    $this->assign("OrderList", $OrderList["DataList"]);
    $this->assign("Page", $OrderList["PageInfo"]);

    $this->CustomDisplay("completed_order_list");
  }

  /**
   * todo: 已完成预约时间
   */
  public function CompletedOrderInfo(){
    $OrderSn = RR("order_sn");

    $OrderInfo = $this->modOrder->OrderInfo($OrderSn);
    $this->assign("OrderInfo", $OrderInfo);

    //region 产品
    $ProductInfo = $this->modOrder->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
    $this->assign("ProductInfo", $ProductInfo);
    //endregion 产品

    //region 客户
    $CustomerInfo = $this->modOrder->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
    $this->assign("CustomerInfo", $CustomerInfo);
    //endregion 客户

    $this->assign("AttachmentFileType", $this->modOrder->clsProduct->_AttachmentType["file"]);
    $this->assign("AttachmentImageType", $this->modOrder->clsProduct->_AttachmentType["image"]);

    $this->CustomDisplay("completed_order_info");
  }
  //endregion 已完成预约
  
  //region 已交易订单
  /**
   * todo: 已完成预约列表
   */
  public function BusinessOrderList(){
    $SearchParam = $this->GetOrderListSearchParam();
    $OrderList = $this->modOrder->CompletedOrderList(0, 0, $this->_PagingRowCount, $SearchParam);

    $this->assign("OrderList", $OrderList["DataList"]);
    $this->assign("Page", $OrderList["PageInfo"]);

    $this->CustomDisplay("business_order_list");
  }

  /**
   * todo: 已完成预约时间
   */
  public function BusinessOrderInfo(){
    $OrderSn = RR("order_sn");

    $OrderInfo = $this->modOrder->OrderInfo($OrderSn);
    $this->assign("OrderInfo", $OrderInfo);

    $ProductInfo = $this->modOrder->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
    $this->assign("ProductInfo", $ProductInfo);

    //region 客户
    $CustomerInfo = $this->modOrder->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
    $this->assign("CustomerInfo", $CustomerInfo);
    //endregion 客户

    $this->assign("AttachmentFileType", $this->modOrder->clsProduct->_AttachmentType["file"]);
    $this->assign("AttachmentImageType", $this->modOrder->clsProduct->_AttachmentType["image"]);

    $this->CustomDisplay("business_order_info");
  }
  //endregion 已交易订单

  //region 添加客户
  public function AjaxCustomerAboutData(){
    switch($this->_AccountInfo["user_type"]){
      case $this->_SellerCaptainAccountType:
        $Result["SellerCaptainOption"] = array();
        $Result["SellerCaptainInfo"] = $this->_AccountInfo;
        
        $Result["SellerMemberOption"] = $this->modOrder->clsUser->SellerMemberOption($this->_AccountInfo["id"]);
        $Result["SellerMemberInfo"] = array();
        break;

      case $this->_SellerMemberAccountType:
        $Result["SellerCaptainOption"] = array();
        $Result["SellerCaptainInfo"] = $this->modOrder->clsUser->GetUserDetails($this->_AccountInfo["parent_user_id"]);

        $Result["SellerMemberInfo"] = $this->_AccountInfo;
        $Result["SellerMemberOption"] = array();
        break;

      case $this->_AdminAccountType:
        $Result["SellerCaptainOption"] = $this->modOrder->clsUser->SellerCaptainOption();
        $Result["SellerCaptainInfo"] = array();

        $Result["SellerMemberOption"] = array();
        $Result["SellerMemberInfo"] = array();
    }

    $clsCustomer = new \Org\ZhiHui\Customer();

    $Result["GenderOption"] = $clsCustomer->_Gender;

    AjaxReturnCorrect("", $Result);
  }

  public function AjaxCustomerSave(){
    $nCustomerID = RR("customer_id");
    $nSellerCaptainID = RR("modal_seller_captain_id");
    $nSellerMemberID = RR("modal_seller_member_id");
    $sRealName = RR("real_name");
    $sGender = RR("gender");
    $sNationality = RR("nationality");
    $sIdCardType = RR("idcard_type");
    $sIdCardNo = RR("idcard_no");
    $fileIdCardImgA = $_FILES["idcard_img"];
    $nIdCardImgA = RR("idcard_img_id");
    $sBirthday = RR("birthday");
    $nProvinceID = RR("province_id");
    $nCityID = RR("city_id");
    $nDistrictID = RR("district_id");
    $sAddress = RR("address");
    $sPhone = RR("phone");
    $sWechat = RR("wechat");

    $modCustomer = D("Customer");

    $SaveResult = $modCustomer->CustomerSave($nCustomerID, $nSellerCaptainID, $nSellerMemberID, $sRealName, $sGender, $sNationality, $sIdCardType, $sIdCardNo, $fileIdCardImgA, $nIdCardImgA, $sBirthday, $nProvinceID, $nCityID, $nDistrictID, $sAddress, $sPhone, $sWechat);
    
    if($SaveResult["error"] == 0){
      if(IsNum($SaveResult["data"], false, false)){
        $CustomerInfo = $modCustomer->clsCustomer->GetCustomerDetails($SaveResult["data"]);
        $SaveResult["data"] = $CustomerInfo;
      }
    }

    AjaxReturn($SaveResult);
  }
  //endregion 添加客户

  /**
   * todo: Ajax获取Option
   */
  public function AjaxProductOptionList(){
    $nProductTypeID = RR("product_type_id");
    
    $ProductOption = $this->modOrder->clsProduct->ProductOption("type_id", $nProductTypeID);
    
    AjaxReturnCorrect("", $ProductOption);
  }

  /**
   * todo: Ajax获取产品信息
   */
  public function AjaxProductInfo(){
    $nProductID = RR("product_id");

    $modProduct = D("Product");
    $ProductInfo = $modProduct->ProductInfo($nProductID);
    
    if(!IsArray($ProductInfo)){
      return ReturnError(L("_PRODUCT_NOT_EXIST_"));
    }
    
    $Result["id"] = $ProductInfo["id"];
    $Result["product_name"] = $ProductInfo["product_name"];
    $Result["product_price"] = $ProductInfo["product_price"];
    $Result["intro"] = $ProductInfo["intro"];
    $Result["remarks"] = $ProductInfo["remarks"];
    
    AjaxReturnCorrect("", $Result);
  }

  /**
   * todo: Ajax获取产品信息
   */
  public function AjaxCustomerInfo(){
    $nProductID = RR("customer_id");

    $modCustomer = D("Customer");
    $CustomerInfo = $modCustomer->CustomerInfo($nProductID);

    if(!IsArray($CustomerInfo)){
      return ReturnError(L("_PRODUCT_NOT_EXIST_"));
    }

    AjaxReturnCorrect("", $CustomerInfo);
  }

  /**
   * todo: Ajax删除预约
   */
  public function AjaxOrderDelete(){
    $sOrderSn = RR("order_sn");

    $Result = $this->modOrder->OrderDelete($sOrderSn);

    AjaxReturn($Result);
  }

  /**
   * todo:刷新预约缓存
   */
  public function ResetOrderCache(){
    $Result = $this->modOrder->ResetOrderCache();

    AjaxReturn($Result);
  }

  /**
   * todo:获取预约列表查询参数
   */
  private function GetOrderListSearchParam(){
    $map = $this->SetOrderListParamData();

    if(
      IsN($map["search_case"])
      && IsN($map["search_key"])
      && IsN($map["search_seller_captain"])
      && IsN($map["search_seller_member"])
      && IsN($map["search_service_providers"])
      && IsN($map["search_store"])
      && IsN($map["search_review"])
      && IsN($map["search_status"])
    ){
      return array();
    }

    $this->assign("search_case", $map["search_case"]);
    $this->assign("search_key", $map["search_key"]);
    $this->assign("search_seller_captain", $map["search_seller_captain"]);
    $this->assign("search_seller_member", $map["search_seller_member"]);
    $this->assign("search_service_providers", $map["search_service_providers"]);
    $this->assign("search_store", $map["search_store"]);
    $this->assign("search_review", $map["search_review"]);
    $this->assign("search_status", $map["search_status"]);

    return $map;
  }

  private function SetOrderListParamData(){
    $Param["search_case"] = RR("search_case"); //查询条件，对应数据库字段
    $Param["search_key"] = RR("search_key"); //关键字
    $Param["search_key"] = urldecode($Param["search_key"]);
    $Param["search_seller_captain"] = RR("search_seller_captain");
    $Param["search_seller_member"] = RR("search_seller_member");
    $Param["search_service_providers"] = RR("search_service_providers");
    $Param["search_store"] = RR("search_store");
    $Param["search_review"] = RR("search_review");
    $Param["search_status"] = RR("search_status");

    return $Param;
  }
}