<?php
namespace CustomerRreservationSystem\Controller;
use Think\Controller;

class ProductController extends CommonController {
  private $modProduct = null;
  
  private $clsProduct = null;
  
  function __construct() {
    parent::__construct();

    $this->_Model = "产品";

    $this->modProduct = D("Product");
    
    $this->clsProduct = new \Org\ZhiHui\Product();
  }
  
  //region 产品类型
  /**
   * todo: 产品类型列表
   */
  public function ProductTypeList(){
    $SearchParam = $this->GetProductTypeListSearchParam();
    $ProductTypeList = $this->modProduct->ProductTypeList($this->_PagingRowCount, $SearchParam);

    $this->assign("ProductTypeList", $ProductTypeList["DataList"]);
    $this->assign("Page", $ProductTypeList["PageInfo"]);

    $this->CustomDisplay("product_type_list");
  }

  /**
   * todo: 保存产品类型信息
   */
  public function AjaxProductTypeSave(){
    $sProductTypeName = RR("product_type_name");
    $nBrokerCompanyID = RR("broker_company");

    $SaveResult = $this->modProduct->ProductTypeSave($sProductTypeName, $nBrokerCompanyID);

    AjaxReturn($SaveResult);
  }

  /**
   * todo:快速修改管理员组信息的字段值
   */
  public function QuickModifyProductTypeField(){
    $sKey = RR("key"); //查询条件，对应数据库字段
    $sVal = RR("val"); //关键字
    $nID = RR("id"); //数据ID

    $Result = $this->modProduct->QuickModifyProductTypeField($nID, $sKey, $sVal);

    if($Result["error"] == 0){
      AjaxReturnCorrect($Result["info"], $sVal);
    }else{
      AjaxReturn($Result);
    }
  }

  /**
   * todo: Ajax删除产品类型
   */
  public function AjaxProductTypeDelete(){
    $sProductTypeID = RR("product_type_id");

    $Result = $this->modProduct->ProductTypeDelete($sProductTypeID);

    AjaxReturn($Result);
  }

  /**
   * todo:刷新产品类型缓存
   */
  public function ResetProductTypeCache(){
    $Result = $this->modProduct->ResetProductTypeCache();

    AjaxReturn($Result);
  }

  /**
   * todo:获取产品类型列表查询参数
   */
  private function GetProductTypeListSearchParam(){
    $map = $this->SetProductTypeListParamData();

    if(
      IsN($map["search_case"])
      && IsN($map["search_key"])
      && IsN($map["search_broker_company"])
    ){
      return array();
    }

    $this->assign("search_case", $map["search_case"]);
    $this->assign("search_key", $map["search_key"]);
    $this->assign("search_broker_company", $map["search_broker_company"]);

    return $map;
  }

  private function SetProductTypeListParamData(){
    $Param["search_case"] = RR("search_case"); //查询条件，对应数据库字段
    $Param["search_key"] = RR("search_key"); //关键字
    $Param["search_key"] = urldecode($Param["search_key"]);
    $Param["search_broker_company"] = RR("search_broker_company");

    return $Param;
  }
  //endregion 产品类型

  //region 产品
  /**
   * todo: 产品列表
   */
  public function ProductList(){
    $SearchParam = $this->GetProductListSearchParam();
    $ProductList = $this->modProduct->ProductList($this->_PagingRowCount, $SearchParam);

    //region 产品类别
    $ProductTypeOption = $this->clsProduct->ProductTypeOption();
    $this->assign("ProductTypeOption", $ProductTypeOption);
    //endregion 产品类别

    //region 服务商
    $clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    $clsServiceProviders = new \Org\ZhiHui\ServiceProviders();
    $ServiceProvidersOption = $clsServiceProviders->ServiceProvidersOption();
    $this->assign("ServiceProvidersOption", $ServiceProvidersOption);
    //endregion 服务商

    //region 门店
    $StoreOption = $clsBrokerCompany->BrokerCompanyStoreOption();
    $this->assign("StoreOption", $StoreOption);
    //endregion 门店

    $this->assign("ProductList", $ProductList["DataList"]);
    $this->assign("Page", $ProductList["PageInfo"]);

    $this->CustomDisplay("product_list");
  }

  /**
   * todo: 产品列表
   */
  public function ProductInfo(){
    $nProductID = RR("product_id");

    $ProductInfo = $this->modProduct->ProductInfo($nProductID);
    
    //region 经纪公司
    $clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    if($this->_AccountType !== $this->_AdminAccountType){
      $BrokerCompanyInfo = $clsBrokerCompany->GetBrokerCompanyDetails($this->_AccountInfo["broker_company_id"]);
      $this->assign("BrokerCompanyInfo", $BrokerCompanyInfo);
    }else{
      $BrokerCompanyOption = $clsBrokerCompany->BrokerCompanyOption();
      $this->assign("BrokerCompanyOption", $BrokerCompanyOption);
    }
    //endregion 经纪公司
    
    //region 产品类别
    $ProductTypeOption = $this->clsProduct->ProductTypeOption();
    $this->assign("ProductTypeOption", $ProductTypeOption);
    //endregion 产品类别

    //region 服务商
    $clsServiceProviders = new \Org\ZhiHui\ServiceProviders();
    $ServiceProvidersOption = $clsServiceProviders->ServiceProvidersOption();
    $this->assign("ServiceProvidersOption", $ServiceProvidersOption);
    //endregion 服务商
    
    //region 门店
    $StoreOption = $clsBrokerCompany->BrokerCompanyStoreOption();
    
    if(IsArray($ProductInfo)){
      $StoreIdList = explode(",", $ProductInfo["store_id"]);
      
      foreach($StoreOption as $key=>$val){
        if(in_array($val["val"], $StoreIdList)){
          $StoreOption[$key]["status"] = "checked";
        }else{
          $StoreOption[$key]["status"] = "";
        }
      }
    }
    
    $this->assign("StoreOption", $StoreOption);
    //endregion 门店

    $this->assign("ProductInfo", $ProductInfo);
    
    $this->CustomDisplay("product_info");
  }

  /**
   * todo: 保存产品信息
   */
  public function AjaxProductSave(){
    $nProductID = RR("product_id");
    $nBrokerCompanyID = RR("broker_company");
    $nProductType = RR("product_type");
    $nServiceProvidersID = RR("service_providers");
    $sProductName = RR("product_name");
    $nProductPrice = RR("product_price");
    $sStoreStr = RR("store");
    $sProductIntro = RR("product_intro");
    $sProductContent = $_POST["product_content"];
    $sProductRemarks = RR("product_remarks");
    $nProductStatus = RR("product_status");
    
    $SaveResult = $this->modProduct->ProductSave($nProductID, $nBrokerCompanyID, $nProductType, $nServiceProvidersID, $sProductName, $nProductPrice, $sStoreStr, $sProductIntro, $sProductContent, $sProductRemarks, $nProductStatus);

    AjaxReturn($SaveResult);
  }
  
  /**
   * todo: Ajax删除产品
   */
  public function AjaxProductDelete(){
    $sProductID = RR("product_id");

    $Result = $this->modProduct->ProductDelete($sProductID);

    AjaxReturn($Result);
  }

  /**
   * todo:刷新产品缓存
   */
  public function ResetProductCache(){
    $Result = $this->modProduct->ResetProductCache();

    AjaxReturn($Result);
  }

  /**
   * todo: 上传产品详情图片
   */
  public function UploadProductContentImage(){
    $UploadFileInfo = $_FILES["ProductContentImage"];
    
    $Result = $this->modProduct->UploadProductContentImage($UploadFileInfo);
    
    AjaxReturn($Result);
  }

  /**
   * todo:获取产品列表查询参数
   */
  private function GetProductListSearchParam(){
    $map = $this->SetProductListParamData();

    if(
      IsN($map["search_case"])
      && IsN($map["search_key"])
      && IsN($map["search_product_type"])
      && IsN($map["search_service"])
      && IsN($map["search_store"])
      && IsN($map["search_status"])
    ){
      return array();
    }

    $this->assign("search_case", $map["search_case"]);
    $this->assign("search_key", $map["search_key"]);
    $this->assign("search_product_type", $map["search_product_type"]);
    $this->assign("search_service", $map["search_service"]);
    $this->assign("search_store", $map["search_store"]);
    $this->assign("search_status", $map["search_status"]);

    return $map;
  }

  private function SetProductListParamData(){
    $Param["search_case"] = RR("search_case"); //查询条件，对应数据库字段
    $Param["search_key"] = RR("search_key"); //关键字
    $Param["search_key"] = urldecode($Param["search_key"]);
    $Param["search_product_type"] = RR("search_product_type");
    $Param["search_service"] = RR("search_service");
    $Param["search_store"] = RR("search_store");
    $Param["search_status"] = RR("search_status");

    return $Param;
  }
  //endregion 产品
}