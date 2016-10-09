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
    $SearchParam = $this->GetCustomerListSearchParam();
    
    if($this->_AccountType != $this->_AdminAccountType){
      $SearchParam["search_broker_company"] = $this->_AccountInfo["broker_company_id"];
    }
    
    $CustomerList = $this->modCustomer->CustomerList($this->_PagingRowCount, $SearchParam);

    $clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    $BrokerCompanyOption = $clsBrokerCompany->BrokerCompanyOption();
    
    $this->assign("BrokerCompanyOption", $BrokerCompanyOption);
    $this->assign("CustomerList", $CustomerList["DataList"]);
    $this->assign("Page", $CustomerList["PageInfo"]);

    $this->CustomDisplay("customer_list");
  }

  /**
   * todo:客户信息
   */
  public function CustomerInfo(){
    $nCustomerID = RR("customer_id");

    $clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    $clsCustomer = new \Org\ZhiHui\Customer();
    
    $CustomerInfo = $this->modCustomer->CustomerInfo($nCustomerID);
    $BrokerCompanyOption = $clsBrokerCompany->BrokerCompanyOption();

    if($this->_AccountType != $this->_AdminAccountType){
      $BrokerCompanyInfo = $clsBrokerCompany->GetBrokerCompanyDetails($this->_AccountInfo["broker_company_id"]);
    }
    
    $clsRegion = new \Org\ZhiHui\Region();
    $ProvinceList = $clsRegion->GetProvinceList();
    $CityList = array();
    $DistrictList = array();
    
    if(IsArray($CustomerInfo)){
      if(IsNum($CustomerInfo["province_id"], false, false)){
        $CityList = $clsRegion->GetCityList($CustomerInfo["province_id"]);
      }

      if(IsNum($CustomerInfo["city_id"], false, false)){
        $DistrictList = $clsRegion->GetDistrictList($CustomerInfo["city_id"]);
      }
    }

    $this->assign("IdCardType", $clsCustomer->_IdCardType);
    $this->assign("GenderOption", $clsCustomer->_Gender);
    $this->assign("CustomerInfo", $CustomerInfo);
    $this->assign("BrokerCompanyInfo", $BrokerCompanyInfo);
    $this->assign("BrokerCompanyOption", $BrokerCompanyOption);
    $this->assign("ProvinceList", $ProvinceList);
    $this->assign("CityList", $CityList);
    $this->assign("DistrictList", $DistrictList);

    $this->CustomDisplay("customer_info");
  }

  /**
   * todo: 保存客户信息
   */
  public function AjaxCustomerSave(){
    $nCustomerID = RR("customer_id");
    $nBrokerCompanyID = RR("broker_company");
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

    $SaveResult = $this->modCustomer->CustomerSave($nCustomerID, $nBrokerCompanyID, $sRealName, $sGender, $sNationality, $sIdCardType, $sIdCardNo, $fileIdCardImgA, $nIdCardImgA, $sBirthday, $nProvinceID, $nCityID, $nDistrictID, $sAddress, $sPhone, $sWechat);

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

  /**
   * todo:刷新客户缓存
   */
  public function ResetCustomerCache(){
    $Result = $this->modCustomer->ResetCustomerCache();

    AjaxReturn($Result);
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