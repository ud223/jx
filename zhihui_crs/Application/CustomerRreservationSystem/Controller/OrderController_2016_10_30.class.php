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
  
  //region 预约
  /**
   * todo: 预约列表
   */
  public function OrderList(){
    $SearchParam = $this->GetOrderListSearchParam();

    if($this->_AccountType != $this->_AdminAccountType){
      $SearchParam["search_broker_company"] = $this->_AccountInfo["broker_company_id"];
    }
    
    $OrderList = $this->modOrder->OrderList($this->_PagingRowCount, $SearchParam);

    $clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    $clsServiceProviders = new \Org\ZhiHui\ServiceProviders();
    $clsSellerManager = new \Org\ZhiHui\SellerManager();
    
    //region 经纪公司
    if($this->_AccountType === $this->_AdminAccountType){
      $BrokerCompanyOption = $clsBrokerCompany->BrokerCompanyOption();
      $this->assign("BrokerCompanyOption", $BrokerCompanyOption);
    }
    //endregion 经纪公司
    
    switch($this->_AccountType){
      case $this->_AdminAccountType:
        $SellerManagerOption = $clsSellerManager->SellerManagerOption();
        break;
      
      case $this->_BrokerCompanyAccountType:
        $SellerManagerOption = $clsSellerManager->SellerManagerOption($this->_AccountInfo["broker_company_id"]);

        $StoreOption = $clsBrokerCompany->BrokerCompanyStoreOption($this->_AccountInfo["broker_company_id"]);
        break;
    }

    //region 销售经理
    $this->assign("SellerManagerOption", $SellerManagerOption);
    //endregion 销售经理
    
    //region 服务商
    $ServiceProvidersOption = $clsServiceProviders->ServiceProvidersOption();
    $this->assign("ServiceProvidersOption", $ServiceProvidersOption);
    //endregion 服务商

    //region 门店
    
    $this->assign("StoreOption", $StoreOption);
    //endregion 门店
    
    $this->assign("OrderList", $OrderList["DataList"]);
    $this->assign("Page", $OrderList["PageInfo"]);

    $this->CustomDisplay("order_list");
  }

  /**
   * todo: 创建预约
   */
  public function OrderAddInfo(){
    $clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    $clsProduct = new \Org\ZhiHui\Product();
    $clsServiceProviders = new \Org\ZhiHui\ServiceProviders();
    $clsSellerManager = new \Org\ZhiHui\SellerManager();
    $clsCustomer = new \Org\ZhiHui\Customer();
    
    switch($this->_AccountType){
      case $this->_AdminAccountType:
        $BrokerCompanyOption = $clsBrokerCompany->BrokerCompanyOption();
        
        $SellerManagerOption = $clsSellerManager->SellerManagerOption($this->_AccountInfo["broker_company_id"]);
        
        $StoreOption = $clsBrokerCompany->BrokerCompanyStoreOption();

        $CustomerOption = $clsCustomer->CustomerOption();
        break;

      case $this->_BrokerCompanyAccountType:
        $BrokerCompanyInfo = $clsBrokerCompany->GetBrokerCompanyDetails($this->_AccountInfo["broker_company_id"]);
        
        $SellerManagerOption = $clsSellerManager->SellerManagerOption($this->_AccountInfo["broker_company_id"]);

        $StoreOption = $clsBrokerCompany->BrokerCompanyStoreOption($this->_AccountInfo["broker_company_id"]);

        $CustomerOption = $clsCustomer->CustomerOption($this->_AccountInfo["broker_company_id"]);
        break;

      case $this->_SellerManagerAccountType:
        $BrokerCompanyInfo = $clsBrokerCompany->GetBrokerCompanyDetails($this->_AccountInfo["broker_company_id"]);

        $SellerManagerInfo = $this->_AccountInfo;

        $StoreOption = $clsBrokerCompany->BrokerCompanyStoreOption($this->_AccountInfo["broker_company_id"]);

        $CustomerOption = $clsCustomer->CustomerOption($this->_AccountInfo["broker_company_id"]);
        break;
    }

    //region 经纪公司
    $this->assign("BrokerCompanyInfo", $BrokerCompanyInfo);
    $this->assign("BrokerCompanyOption", $BrokerCompanyOption);
    //endregion 经纪公司

    //region 销售经理
    $this->assign("SellerManagerInfo", $SellerManagerInfo);
    $this->assign("SellerManagerOption", $SellerManagerOption);
    //endregion 销售经理

    //region 门店
    $this->assign("StoreOption", $StoreOption);
    //endregion 门店

    //region 服务商
    $ServiceProvidersOption = $clsServiceProviders->ServiceProvidersOption();
    $this->assign("ServiceProvidersOption", $ServiceProvidersOption);
    //endregion 服务商
    
    //region 产品类别
    $ProductTypeOption = $clsProduct->ProductTypeOption();
    $this->assign("ProductTypeOption", $ProductTypeOption);
    //endregion 产品类别

    //region 客户
    $this->assign("CustomerOption", $CustomerOption);
    //endregion 客户
    
    $this->CustomDisplay("order_info_add");
  }

  /**
   * todo: 编辑/审核预约
   */
  public function OrderInfoEditReview(){
    $sOrderSn = RR("order_sn");

    $OrderInfo = $this->modOrder->OrderInfo($sOrderSn);

    $clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    $clsProduct = new \Org\ZhiHui\Product();
    $clsServiceProviders = new \Org\ZhiHui\ServiceProviders();
    $clsSellerManager = new \Org\ZhiHui\SellerManager();
    $clsCustomer = new \Org\ZhiHui\Customer();

    //region 经纪公司
    if($this->_AccountType !== $this->_AdminAccountType){
      $BrokerCompanyInfo = $clsBrokerCompany->GetBrokerCompanyDetails($this->_AccountInfo["broker_company_id"]);
      $this->assign("BrokerCompanyInfo", $BrokerCompanyInfo);
    }else{
      $BrokerCompanyOption = $clsBrokerCompany->BrokerCompanyOption();
      $this->assign("BrokerCompanyOption", $BrokerCompanyOption);
    }
    //endregion 经纪公司

    if(IsArray($OrderInfo)){
      //region 销售经理
      if(IsNum($OrderInfo["broker_company_id"], false, false)){
        $SellerManagerOption = $clsSellerManager->SellerManagerOption($OrderInfo["broker_company_id"]);
        $this->assign("SellerManagerOption", $SellerManagerOption);
      }
      //endregion 销售经理
      
      //region 服务商
      if(IsNum($OrderInfo["service_providers_id"], false, false)){
        $ServiceProvidersInfo = $clsServiceProviders->GetServiceProvidersDetails($OrderInfo["service_providers_id"]);
        $this->assign("ServiceProvidersInfo", $ServiceProvidersInfo);
      }
      //endregion 服务商

      //region 门店
      if(IsNum($OrderInfo["store_id"], false, false)){
        $StoreInfo = $clsBrokerCompany->GetBrokerCompanyStoreDetails($OrderInfo["store_id"]);
        $this->assign("StoreInfo", $StoreInfo);
      }
      //endregion 门店
      
      //region 产品
      if(IsNum($OrderInfo["service_providers_id"], false, false)){
        $ProductOption = $clsProduct->ProductOption('service_providers_id', $OrderInfo["service_providers_id"]);
        $this->assign("ProductOption", $ProductOption);
      }

      if(IsNum($OrderInfo["product_id"], false, false)){
        $ProductInfo = $clsProduct->GetProductDetails($OrderInfo["product_id"]);
        $this->assign("ProductInfo", $ProductInfo);
      }
      //endregion 产品

      //region 客户
      if(IsNum($OrderInfo["broker_company_id"], false, false)){
        $CustomerOption = $clsCustomer->CustomerOption($OrderInfo["broker_company_id"]);
        $this->assign("CustomerOption", $CustomerOption);
      }

      if(IsNum($OrderInfo["customer_id"], false, false)){
        $CustomerInfo = $clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
        $CustomerInfo["birthday"] = Time2FullDate($CustomerInfo["birthday"], "Y年m月d日");
        $this->assign("CustomerInfo", $CustomerInfo);
      }
      //endregion 客户
    }
    
    //region 门店
    $StoreOption = $clsBrokerCompany->BrokerCompanyStoreOption();
    $this->assign("StoreOption", $StoreOption);
    //endregion 门店

    //region 服务商
    $ServiceProvidersOption = $clsServiceProviders->ServiceProvidersOption();
    $this->assign("ServiceProvidersOption", $ServiceProvidersOption);
    //endregion 服务商

    //region 产品类别
    $ProductTypeOption = $clsProduct->ProductTypeOption();
    $this->assign("ProductTypeOption", $ProductTypeOption);
    //endregion 产品类别
    
    //region 审批信息
    $ReviewInfo = $this->modOrder->OrderLastReviewInfo($OrderInfo["order_sn"], $OrderInfo["status"]);
    //endregion 审批信息

    $this->assign("OrderInfo", $OrderInfo);
    $this->assign("ReviewInfo", $ReviewInfo);

    $this->CustomDisplay("order_info_edit_review");
  }

  /**
   * todo: 保存预约审核
   */
  public function OrderInfoReviewSave(){
    $sOrderSn = RR("order_sn");
    $sReviewRemarks = RR("review_remarks");
    $nReviewStatus = RR("review_status");
    
    $Result = $this->modOrder->OrderInfoReviewSave($this->_AccountID, $this->_AccountType, $sOrderSn, $sReviewRemarks, $nReviewStatus);
    
    AjaxReturn($Result);
  }

  /**
   * todo: 审核预约
   */
  public function OrderInfoReview(){
    $sOrderSn = RR("order_sn");

    $OrderInfo = $this->modOrder->OrderInfo($sOrderSn);

    $clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    $clsProduct = new \Org\ZhiHui\Product();
    $clsServiceProviders = new \Org\ZhiHui\ServiceProviders();
    $clsCustomer = new \Org\ZhiHui\Customer();

    if(IsArray($OrderInfo)){
      //region 服务商
      if(IsNum($OrderInfo["service_providers_id"], false, false)){
        $ServiceProvidersInfo = $clsServiceProviders->GetServiceProvidersDetails($OrderInfo["service_providers_id"]);
        $this->assign("ServiceProvidersInfo", $ServiceProvidersInfo);
      }
      //endregion 服务商

      //region 门店
      if(IsNum($OrderInfo["store_id"], false, false)){
        $StoreInfo = $clsBrokerCompany->GetBrokerCompanyStoreDetails($OrderInfo["store_id"]);
        $this->assign("StoreInfo", $StoreInfo);
      }
      //endregion 门店

      //region 产品
      if(IsNum($OrderInfo["product_id"], false, false)){
        $ProductInfo = $clsProduct->GetProductDetails($OrderInfo["product_id"]);
        $this->assign("ProductInfo", $ProductInfo);
      }
      //endregion 产品

      //region 客户
      if(IsNum($OrderInfo["customer_id"], false, false)){
        $CustomerInfo = $clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
        $CustomerInfo["birthday"] = Time2FullDate($CustomerInfo["birthday"], "Y年m月d日");
        $this->assign("CustomerInfo", $CustomerInfo);
      }
      //endregion 客户
    }

    $this->assign("OrderInfo", $OrderInfo);

    $this->CustomDisplay("order_info_review");
  }

  /**
   * todo: 销售经理编辑预约信息
   */
  public function OrderInfoEdit(){
    $sOrderSn = RR("order_sn");
    $OrderInfo = $this->modOrder->OrderInfo($sOrderSn);

    $clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    $clsSellerManager = new \Org\ZhiHui\SellerManager();
    $clsProduct = new \Org\ZhiHui\Product();
    $clsServiceProviders = new \Org\ZhiHui\ServiceProviders();
    $clsCustomer = new \Org\ZhiHui\Customer();

    if(IsArray($OrderInfo)){
      //region 经纪公司
      $BrokerCompanyInfo = $clsBrokerCompany->GetBrokerCompanyDetails($OrderInfo["broker_company_id"]);
      $this->assign("BrokerCompanyInfo", $BrokerCompanyInfo);
      //endregion 经纪公司
      
      //region 销售经理
      $SellerManagerInfo = $clsSellerManager->GetSellerManagerDetails($OrderInfo["seller_manager_id"]);
      $this->assign("SellerManagerInfo", $SellerManagerInfo);
      //endregion 销售经理

      //region 门店
      $StoreOption = $clsBrokerCompany->BrokerCompanyStoreOption($OrderInfo["broker_company_id"]);
      $this->assign("StoreOption", $StoreOption);
      
      if(IsNum($OrderInfo["store_id"], false, false)){
        $StoreInfo = $clsBrokerCompany->GetBrokerCompanyStoreDetails($OrderInfo["store_id"]);
        $this->assign("StoreInfo", $StoreInfo);
      }

      $StoreOption = $clsBrokerCompany->BrokerCompanyStoreOption();
      $this->assign("StoreOption", $StoreOption);
      //endregion 门店

      //region 服务商
      if(IsNum($OrderInfo["service_providers_id"], false, false)){
        $ServiceProvidersInfo = $clsServiceProviders->GetServiceProvidersDetails($OrderInfo["service_providers_id"]);
        $this->assign("ServiceProvidersInfo", $ServiceProvidersInfo);
      }
      //endregion 服务商

      //region 产品
      if(IsNum($OrderInfo["product_id"], false, false)){
        $ProductInfo = $clsProduct->GetProductDetails($OrderInfo["product_id"]);
        $this->assign("ProductInfo", $ProductInfo);
      }

      if(IsNum($OrderInfo["service_providers_id"], false, false)){
        $ProductOption = $clsProduct->ProductOption('service_providers_id', $OrderInfo["service_providers_id"]);
        $this->assign("ProductOption", $ProductOption);
      }
      //endregion 产品

      //region 客户
      if(IsNum($OrderInfo["customer_id"], false, false)){
        $CustomerInfo = $clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
        $CustomerInfo["birthday"] = Time2FullDate($CustomerInfo["birthday"], "Y年m月d日");
        $this->assign("CustomerInfo", $CustomerInfo);
      }

      if(IsNum($OrderInfo["broker_company_id"], false, false)){
        $CustomerOption = $clsCustomer->CustomerOption($OrderInfo["broker_company_id"]);
        $this->assign("CustomerOption", $CustomerOption);
      }
      //endregion 客户

      //region 审批信息
      $ReviewInfo = $this->modOrder->OrderLastReviewInfo($OrderInfo["order_sn"], $OrderInfo["status"]);
      $ReviewList = $this->modOrder->OrderReviewList($OrderInfo["order_sn"]);
      //endregion 审批信息
    }

    $this->assign("OrderInfo", $OrderInfo);
    $this->assign("ReviewInfo", $ReviewInfo);
    $this->assign("ReviewList", $ReviewList);

    if(($OrderInfo["status"] == 0 || $OrderInfo["status"] == 1) && $OrderInfo["review"] == 3){
      $this->CustomDisplay("order_info_edit2");
    }else{
      $this->CustomDisplay("order_info_edit");
    }
  }

  /**
   * todo: 保存销售经理编辑的预约信息
   */
  public function AjaxOrderEditSave(){
    $OrderSn = RR("order_sn");
    $sSubmitAction = RR("submit_action");
    $nStoreID = RR("store");
    $sReservationTime = RR("reservation_time");
    $sPayTime = RR("pay_time");
    $nPayAmount = RR("pay_amount");
    $sVisitTime = RR("visit_time");
    $sSellerManagerRemarks = RR("seller_manager_remarks");
    
    $Result = $this->modOrder->OrderEditSave($OrderSn, $sSubmitAction, $nStoreID, $sReservationTime, $sPayTime, $nPayAmount, $sVisitTime, $sSellerManagerRemarks);
    
    AjaxReturn($Result);
  }

  /**
   * todo: Ajax获取Option
   */
  public function AjaxOptionList(){
    $nBrokerCompanyID = RR("broker_company_id");
    
    $clsSellerManager = new \Org\ZhiHui\SellerManager();
    $Result["seller_manage_option"] = $clsSellerManager->SellerManagerOption($nBrokerCompanyID);
    
    $clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    $Result["store_option"] = $clsBrokerCompany->BrokerCompanyStoreOption($nBrokerCompanyID);

    $clsCustomer = new \Org\ZhiHui\Customer();
    $Result["customer_option"] = $clsCustomer->CustomerOption($nBrokerCompanyID);
    
    AjaxReturnCorrect("", $Result);
  }
  
  /**
   * todo: Ajax获取Option
   */
  public function AjaxProductOptionList(){
    $nServiceProvidersID = RR("service_providers_id");
    
    $clsProduct = new \Org\ZhiHui\Product();
    $ProductOption = $clsProduct->ProductOption("service_providers_id", $nServiceProvidersID);
    
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
   * todo: 添加预约信息
   */
  public function AjaxOrderAdd(){
    $sOrderSn = RR("order_sn");
    $nBrokerCompanyID = RR("broker_company");
    $nSellerManagerID = RR("seller_manager");
    $nStoreID = RR("store");
    $nServiceProviders = RR("service_providers");
    $nCustomerID = RR("customer");
    $nProductID = RR("product");
    $fileAttachment = $_FILES["attachment"];
    $nAttachmentID = RR("attachment_id");
    $nSellerManagerRemarks = RR("seller_manager_remarks");

    $SaveResult = $this->modOrder->OrderAdd($sOrderSn, $this->_AccountType, $nBrokerCompanyID, $nSellerManagerID, $nStoreID, $nServiceProviders, $nCustomerID, $nProductID, $fileAttachment, $nAttachmentID, $nSellerManagerRemarks);

    AjaxReturn($SaveResult);
  }

  /**
   * todo:快速修改预约信息的字段值
   */
  public function QuickModifyOrderField(){
    $sKey = RR("key"); //查询条件，对应数据库字段
    $sVal = RR("val"); //关键字
    $nID = RR("id"); //数据ID

    $Result = $this->modOrder->QuickModifyOrderField($nID, $sKey, $sVal);

    if($Result["error"] == 0){
      AjaxReturnCorrect($Result["info"], $sVal);
    }else{
      AjaxReturn($Result);
    }
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
      && IsN($map["search_broker_company"])
      && IsN($map["search_seller_manager"])
      && IsN($map["search_service_providers"])
      && IsN($map["search_store"])
      && IsN($map["search_review"])
      && IsN($map["search_status"])
    ){
      return array();
    }

    $this->assign("search_case", $map["search_case"]);
    $this->assign("search_key", $map["search_key"]);
    $this->assign("search_broker_company", $map["search_broker_company"]);
    $this->assign("search_seller_manager", $map["search_seller_manager"]);
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
    $Param["search_broker_company"] = RR("search_broker_company");
    $Param["search_seller_manager"] = RR("search_seller_manager");
    $Param["search_service_providers"] = RR("search_service_providers");
    $Param["search_store"] = RR("search_store");
    $Param["search_review"] = RR("search_review");
    $Param["search_status"] = RR("search_status");

    return $Param;
  }
  //endregion 预约
}