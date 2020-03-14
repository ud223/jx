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
   * todo: 产品浏览列表
   */
  public function ProductViewList(){
    if($this->_MobileClient){
      $this->CustomDisplay("wap_product_view_list");
      exit();
    }
    
    $SearchParam = $this->GetProductListSearchParam();
    $SearchParam["search_status"] = 1;
    $ProductList = $this->modProduct->ProductList($this->_PagingRowCount, $SearchParam);

    //region 产品类别
    $ProductTypeOption = $this->clsProduct->ProductTypeOption();
    $this->assign("ProductTypeOption", $ProductTypeOption);
    //endregion 产品类别

    $this->assign("ProductList", $ProductList["DataList"]);
    $this->assign("Page", $ProductList["PageInfo"]);
    
    $this->CustomDisplay("product_view_list");
  }
  
  /**
   * todo: 产品浏览列表
   */
  public function AjaxProductViewList(){
    $nPage = RR("page");
    
    $ProductList = $this->modProduct->AjaxProductList($nPage, 10);
    
    if($this->_AccountType == $this->_SellerMemberAccountType || $this->_AccountType == $this->_SpecialAccountType){
      foreach($ProductList["DataList"] as $key=>$val){
        $ProductList["DataList"][$key]["captain_first_rate"] = "";
        $ProductList["DataList"][$key]["captain_next_rate"] = "";
      }
    }
    
    AjaxReturnCorrect("", $ProductList);
  }

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
    
    //region 产品类别
    $ProductTypeOption = $this->clsProduct->ProductTypeOption();
    $this->assign("ProductTypeOption", $ProductTypeOption);
    //endregion 产品类别

    //region 支付方式
    $PaymentType = $this->modProduct->clsProduct->_PaymentType;
    $this->assign("PaymentType", $PaymentType);
    //endregion 支付方式

    //region 缴费方式
    $PayType = $this->modProduct->clsProduct->_PayType;
    $this->assign("PayType", $PayType);
    //endregion 缴费方式

    //region 附件上传配置
    $ProductAttachmentImgCfg = GetProductAttachmentImgCfg();
    $this->assign("ProductAttachmentImgCfg", $ProductAttachmentImgCfg);

    $ProductAttachmentFileCfg = GetProductAttachmentFileCfg();
    $this->assign("ProductAttachmentFileCfg", $ProductAttachmentFileCfg);
    //endregion 附件上传配置

    $this->assign("AttachmentFileType", $this->modProduct->clsProduct->_AttachmentType["file"]);
    $this->assign("AttachmentImageType", $this->modProduct->clsProduct->_AttachmentType["image"]);
    $this->assign("ProductInfo", $ProductInfo);
  
    $this->CustomDisplay("product_info");
  }
  
  public function ProductViewInfo(){
    $nProductID = RR("product_id");
  
    $ProductInfo = $this->modProduct->ProductInfo($nProductID);
    $Result = array();
  
    if(IsArray($ProductInfo)){
      $Result["type_name"] = $ProductInfo["type_name"];
      $Result["product_name"] = $ProductInfo["product_name"];
      $Result["payment_type"] = $ProductInfo["payment_type"];
      $Result["pay_type"] = $ProductInfo["pay_type"];
      $Result["min_age"] = $ProductInfo["min_age"];
      $Result["validity_year"] = $ProductInfo["validity_year"];
    
      if($this->_AccountType != $this->_SellerMemberAccountType && $this->_AccountType != $this->_SpecialAccountType){
        $Result["captain_first_rate"] = $ProductInfo["captain_first_rate"];
        $Result["captain_next_rate"] = $ProductInfo["captain_next_rate"];
      }else{
        $Result["captain_first_rate"] = "";
        $Result["captain_next_rate"] = "";
      }
    
      $Result["member_first_rate"] = $ProductInfo["member_first_rate"];
      $Result["member_next_rate"] = $ProductInfo["member_next_rate"];
      $Result["status"] = $ProductInfo["status"];
      $Result["intro"] = $ProductInfo["intro"];
      $Result["content"] = $ProductInfo["content"];
      $Result["remarks"] = $ProductInfo["remarks"];
      $Result["attachment_type"] = $ProductInfo["attachment_type"];
      $Result["attachment"] = $ProductInfo["attachment"];
    }

    $this->assign("ProductInfo", $ProductInfo);
    $this->CustomDisplay("wap_product_view_info");
  }

  /**
   * todo: 保存产品信息
   */
  public function AjaxProductSave(){
    $nProductID = RR("product_id");
    $nProductType = RR("product_type");
    $sProductName = RR("product_name");
    $sPayType = RR("pay_type");
    $sPaymentType = RR("payment_type");
    $nProductPrice = RR("product_price");
    $nMinAge = RR("min_age");
    $nMaxAge = RR("max_age");
    $nCaptainFirstRate = RR("captain_first_rate");
    $nCaptainNextRate = RR("captain_next_rate");
    $nMemberFirstRate = RR("member_first_rate");
    $nMemberNextRate = RR("member_next_rate");
    $nValidityYear = RR("validity_year");
    $sProductIntro = RR("product_intro");
    $sProductContent = $_POST["product_content"];
    $sProductRemarks = RR("product_remarks");
    $nProductStatus = RR("product_status");
    $Attachment = $_FILES["attachment"];
    
    $SaveResult = $this->modProduct->ProductSave($nProductID, $nProductType, $sProductName, $sPayType, $sPaymentType, $nProductPrice, $nMinAge, $nMaxAge, $nCaptainFirstRate, $nCaptainNextRate, $nMemberFirstRate, $nMemberNextRate, $nValidityYear, $sProductIntro, $sProductContent, $sProductRemarks, $nProductStatus, $Attachment);

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

  public function AjaxProductInfo(){
    $nProductID = RR("product_id");

    $ProductInfo = $this->modProduct->ProductInfo($nProductID);
    $Result = array();

    if(IsArray($ProductInfo)){
      $Result["type_name"] = $ProductInfo["type_name"];
      $Result["product_name"] = $ProductInfo["product_name"];
      $Result["payment_type"] = $ProductInfo["payment_type"];
      $Result["pay_type"] = $ProductInfo["pay_type"];
      $Result["min_age"] = $ProductInfo["min_age"];
      $Result["validity_year"] = $ProductInfo["validity_year"];

      if($this->_AccountType != $this->_SellerMemberAccountType && $this->_AccountType != $this->_SpecialAccountType){
        $Result["captain_first_rate"] = $ProductInfo["captain_first_rate"];
        $Result["captain_next_rate"] = $ProductInfo["captain_next_rate"];
      }else{
        $Result["captain_first_rate"] = "";
        $Result["captain_next_rate"] = "";
      }

      $Result["member_first_rate"] = $ProductInfo["member_first_rate"];
      $Result["member_next_rate"] = $ProductInfo["member_next_rate"];
      $Result["status"] = $ProductInfo["status"];
      $Result["intro"] = $ProductInfo["intro"];
      $Result["content"] = $ProductInfo["content"];
      $Result["remarks"] = $ProductInfo["remarks"];
      $Result["attachment_type"] = $ProductInfo["attachment_type"];
      $Result["attachment"] = $ProductInfo["attachment"];
    }

    AjaxReturnCorrect("", $Result);
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