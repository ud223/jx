<?php
namespace CustomerRreservationSystem\Controller;
use Think\Controller;

class CustomerController extends CommonController {
  private $modCustomer = null;
  
  function __construct() {
    parent::__construct();

    $this->_Model = "客户";

    $this->modCustomer = D("Customer");
  }
  
  //region 客户
  /**
   * todo: 客户列表
   */
  public function CustomerList(){
    $nSellerCaptainID = 0;
    $nSellerMemberID = 0;

    if($this->_AccountInfo["user_type"] == $this->_SellerCaptainAccountType){
      $nSellerCaptainID = $this->_AccountInfo["id"];
    }elseif($this->_AccountInfo["user_type"] == $this->_SellerMemberAccountType){
      $nSellerCaptainID = $this->_AccountInfo["parent_user_id"];
      $nSellerMemberID = $this->_AccountInfo["id"];
    }

    $SearchParam = $this->GetCustomerListSearchParam();
    
    if($this->_AccountType != $this->_AdminAccountType){
      $SearchParam["search_broker_company"] = $this->_AccountInfo["broker_company_id"];
    }
    
    $CustomerList = $this->modCustomer->CustomerList($nSellerCaptainID, $nSellerMemberID, $this->_PagingRowCount, $SearchParam);

    $this->assign("CustomerList", $CustomerList["DataList"]);
    $this->assign("Page", $CustomerList["PageInfo"]);

    $this->CustomDisplay("customer_list");
  }

  /**
   * todo:客户信息
   */
  public function CustomerInfo(){
    $nCustomerID = RR("customer_id");

    $CustomerInfo = $this->modCustomer->CustomerInfo($nCustomerID);
    $SellerCaptainOption = $this->modCustomer->clsUser->SellerCaptainOption();

    if(IsArray($CustomerInfo)){
      $SellerMemberOption = $this->modCustomer->clsUser->SellerMemberOption($CustomerInfo["seller_captain_id"]);
    }else{
      if($this->_AccountInfo["user_type"] == $this->_SellerMemberAccountType){

      }
    }

    if($this->_AccountInfo["user_type"] == $this->_SellerCaptainAccountType){
      $SellerCaptainInfo = $this->_AccountInfo;
      $SellerMemberOption = $this->modCustomer->clsUser->SellerMemberOption($this->_AccountInfo["id"]);
    }elseif($this->_AccountInfo["user_type"] == $this->_SellerMemberAccountType){
      $SellerCaptainInfo = $this->modCustomer->clsUser->GetUserDetails($this->_AccountInfo["parent_user_id"]);
      $SellerMemberInfo = $this->_AccountInfo;
    }
    
//    $ProvinceList = $this->modCustomer->clsRegion->GetProvinceList();
//    $CityList = array();
//    $DistrictList = array();
//
//    if(IsArray($CustomerInfo)){
//      if(IsNum($CustomerInfo["province_id"], false, false)){
//        $CityList = $this->modCustomer->clsRegion->GetCityList($CustomerInfo["province_id"]);
//      }
//
//      if(IsNum($CustomerInfo["city_id"], false, false)){
//        $DistrictList = $this->modCustomer->clsRegion->GetDistrictList($CustomerInfo["city_id"]);
//      }
//    }

    $this->assign("IdCardType", $this->modCustomer->clsCustomer->_IdCardType);
    $this->assign("GenderOption", $this->modCustomer->clsCustomer->_Gender);
    $this->assign("CustomerInfo", $CustomerInfo);
    $this->assign("SellerCaptainInfo", $SellerCaptainInfo);
    $this->assign("SellerMemberInfo", $SellerMemberInfo);
    $this->assign("SellerCaptainOption", $SellerCaptainOption);
    $this->assign("SellerMemberOption", $SellerMemberOption);
    $this->assign("NationalityOption", $this->modCustomer->clsCustomer->_Nationality);
//    $this->assign("ProvinceList", $ProvinceList);
//    $this->assign("CityList", $CityList);
//    $this->assign("DistrictList", $DistrictList);

    $this->CustomDisplay("customer_info");
  }

  /**
   * todo: 保存客户信息
   */
  public function AjaxCustomerSave(){
    $nCustomerID = RR("customer_id");
    $nSellerCaptainID = RR("seller_captain_id");
    $nSellerMemberID = RR("seller_member_id");
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

    $SaveResult = $this->modCustomer->CustomerSave($nCustomerID, $nSellerCaptainID, $nSellerMemberID, $sRealName, $sGender, $sNationality, $sIdCardType, $sIdCardNo, $fileIdCardImgA, $nIdCardImgA, $sBirthday, $nProvinceID, $nCityID, $nDistrictID, $sAddress, $sPhone, $sWechat);

    AjaxReturn($SaveResult);
  }

  /**
   * todo: Ajax删除客户
   */
  public function AjaxCustomerDelete(){
    $sCustomerID = RR("customer_id");

    $Result = $this->modCustomer->CustomerDelete($sCustomerID);

    AjaxReturn($Result);
  }

  public function AjaxCustomerInfo(){
    $nCustomerID = RR("customer_id");

    $CustomerInfo = $this->modCustomer->CustomerInfo($nCustomerID);

    $clsUser = new \Org\ZhiHui\User();

    if(IsArray($CustomerInfo)){
      $SellerCaptainInfo = $clsUser->GetUserDetails($CustomerInfo["seller_captain_id"]);
      $SellerMemberInfo = $clsUser->GetUserDetails($CustomerInfo["seller_member_id"]);

      $CustomerInfo["seller_captain_name"] = $SellerCaptainInfo["real_name"];
      $CustomerInfo["seller_member_name"] = $SellerMemberInfo["real_name"];
    }

    AjaxReturnCorrect("", $CustomerInfo);
  }

  public function AjaxCustomerPayList(){
    $nCustomerID = RR("customer_id");

    $CustomerPayList = $this->modCustomer->CustomerPayList($nCustomerID);

    AjaxReturnCorrect("", $CustomerPayList);
  }

  /**
   * todo:刷新客户缓存
   */
  public function ResetCustomerCache(){
    $Result = $this->modCustomer->ResetCustomerCache();

    AjaxReturn($Result);
  }

  /**
   * todo: Ajax获取客户经理option
   */
  public function AjaxGetSellerMemberOption(){
    $nSellerCaptainID = RR("seller_captain_id");
    
    $SellMemberOption = $this->modCustomer->clsUser->SellerMemberOption($nSellerCaptainID);

    AjaxReturnCorrect("", $SellMemberOption);
  }

  /**
   * todo:获取客户列表查询参数
   */
  private function GetCustomerListSearchParam(){
    $map = $this->SetCustomerListParamData();

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

  private function SetCustomerListParamData(){
    $Param["search_case"] = RR("search_case"); //查询条件，对应数据库字段
    $Param["search_key"] = RR("search_key"); //关键字
    $Param["search_key"] = urldecode($Param["search_key"]);
    $Param["search_broker_company"] = RR("search_broker_company");

    return $Param;
  }
  //endregion 客户
}