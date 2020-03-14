<?php
namespace CustomerRreservationSystem\Controller;

class UserController extends CommonController {
  private $modUser = null;
  
  function __construct() {
    parent::__construct();

    $this->_Model = "用户";

    $this->modUser = D("User");
  }

  //region 特殊组用户
  /**
   * todo: 先锋保险经纪信息
   */
  public function SpecialUserInfo(){
    $this->_Controller = "先锋保险经纪";
    $this->_Action = "先锋保险经纪信息";

    $nUserID = $this->modUser->clsUser->ConstInsuranceCompanyUserID();

    $SpecialUserInfo = $this->modUser->UserInfo($nUserID);

    $clsRegion = new \Org\ZhiHui\Region();
    $ProvinceList = $clsRegion->GetProvinceList();
    $CityList = array();
    $DistrictList = array();

    if(IsArray($SpecialUserInfo)){
      if(IsNum($SpecialUserInfo["province_id"], false, false)){
        $CityList = $clsRegion->GetCityList($SpecialUserInfo["province_id"]);
      }

      if(IsNum($SpecialUserInfo["city_id"], false, false)){
        $DistrictList = $clsRegion->GetDistrictList($SpecialUserInfo["city_id"]);
      }
    }

    $this->assign("SpecialUserInfo", $SpecialUserInfo);
    $this->assign("ProvinceList", $ProvinceList);
    $this->assign("CityList", $CityList);
    $this->assign("DistrictList", $DistrictList);

    $this->CustomDisplay("special_user_info");
  }

  /**
   * todo: 保存团队长信息
   */
  public function AjaxSpecialUserSave(){
    $nUserID = RR("user_id");
    $nParentUserID = 0;
    $sLoginName = RR("login_name");
    $sLoginPassword = RR("login_password");
    $nBaseSalary = 0;
    $nProvinceID = RR("province_id");
    $nCityID = RR("city_id");
    $sRealName = RR("real_name");
    $sMobilePhone = RR("mobile_phone");
    $sQQ = RR("qq");
    $sWechat = RR("wechat");
    $nDesable = RR("disable");

    $SaveResult = $this->modUser->UserSave($this->modUser->clsUser->_UserType["special"], $nUserID, $nParentUserID, $sLoginName, $sLoginPassword, $nBaseSalary, $nProvinceID, $nCityID, $sRealName, $sMobilePhone, $sQQ, $sWechat, $nDesable);

    AjaxReturn($SaveResult);
  }
  //endregion 特殊组用户

  //region 经纪公司用户
  /**
   * todo: 唯润城信息
   */
  public function BrokerCompanyUserInfo(){
    $this->_Controller = "唯润城";
    $this->_Action = "唯润城信息";

    $nUserID = $this->modUser->clsUser->ConstBrokerCompanyUserID();

    $BrokerCompanyUserInfo = $this->modUser->UserInfo($nUserID);

    $clsRegion = new \Org\ZhiHui\Region();
    $ProvinceList = $clsRegion->GetProvinceList();
    $CityList = array();
    $DistrictList = array();

    if(IsArray($BrokerCompanyUserInfo)){
      if(IsNum($BrokerCompanyUserInfo["province_id"], false, false)){
        $CityList = $clsRegion->GetCityList($BrokerCompanyUserInfo["province_id"]);
      }

      if(IsNum($BrokerCompanyUserInfo["city_id"], false, false)){
        $DistrictList = $clsRegion->GetDistrictList($BrokerCompanyUserInfo["city_id"]);
      }
    }

    $this->assign("BrokerCompanyUserInfo", $BrokerCompanyUserInfo);
    $this->assign("ProvinceList", $ProvinceList);
    $this->assign("CityList", $CityList);
    $this->assign("DistrictList", $DistrictList);

    $this->CustomDisplay("broker_company_user_info");
  }

  /**
   * todo: 保存唯润城信息
   */
  public function AjaxBrokerCompanyUserSave(){
    $nUserID = RR("user_id");
    $nParentUserID = 0;
    $sLoginName = RR("login_name");
    $sLoginPassword = RR("login_password");
    $nBaseSalary = 0;
    $nProvinceID = RR("province_id");
    $nCityID = RR("city_id");
    $sRealName = RR("real_name");
    $sMobilePhone = RR("mobile_phone");
    $sQQ = RR("qq");
    $sWechat = RR("wechat");
    $nDesable = RR("disable");

    $SaveResult = $this->modUser->UserSave($this->modUser->clsUser->_UserType["broker_company"], $nUserID, $nParentUserID, $sLoginName, $sLoginPassword, $nBaseSalary, $nProvinceID, $nCityID, $sRealName, $sMobilePhone, $sQQ, $sWechat, $nDesable);

    AjaxReturn($SaveResult);
  }
  //endregion 经纪公司用户

  //region 团队长
  /**
   * todo: 团队长列表
   */
  public function SellerCaptainList(){
    $this->_Controller = "团队长";
    $this->_Action = "团队长列表";

    $sUserType = $this->modUser->clsUser->_UserType["captain"];
    $nParentUserId = 0;
    $SearchParam = $this->GetSellerCaptainListSearchParam();

    $SellerCaptainList = $this->modUser->UserList($sUserType, $nParentUserId, $this->_PagingRowCount, $SearchParam);

    $this->assign("SellerCaptainList", $SellerCaptainList["DataList"]);
    $this->assign("Page", $SellerCaptainList["PageInfo"]);

    $this->CustomDisplay("seller_captain_list");
  }

  /**
   * todo:保存团队长信息
   */
  public function SellerCaptainInfo(){
    $this->_Controller = "团队长";
    $this->_Action = "团队长信息";

    $nUserID = RR("user_id");

    $SellerCaptainInfo = $this->modUser->UserInfo($nUserID);

    $clsRegion = new \Org\ZhiHui\Region();
    $ProvinceList = $clsRegion->GetProvinceList();
    $CityList = array();
    $DistrictList = array();

    if(IsArray($SellerCaptainInfo)){
      if(IsNum($SellerCaptainInfo["province_id"], false, false)){
        $CityList = $clsRegion->GetCityList($SellerCaptainInfo["province_id"]);
      }

      if(IsNum($SellerCaptainInfo["city_id"], false, false)){
        $DistrictList = $clsRegion->GetDistrictList($SellerCaptainInfo["city_id"]);
      }
    }

    $this->assign("SellerCaptainInfo", $SellerCaptainInfo);
    $this->assign("ProvinceList", $ProvinceList);
    $this->assign("CityList", $CityList);
    $this->assign("DistrictList", $DistrictList);

    $this->CustomDisplay("seller_captain_info");
  }

  /**
   * todo: 保存团队长信息
   */
  public function AjaxSellerCaptainSave(){
    $nUserID = RR("user_id");
    $nParentUserID = RR("parent_user_id");
    $sLoginName = RR("login_name");
    $sLoginPassword = RR("login_password");
    $nBaseSalary = RR("base_salary");
    $nProvinceID = RR("province_id");
    $nCityID = RR("city_id");
    $sRealName = RR("real_name");
    $sMobilePhone = RR("mobile_phone");
    $sQQ = RR("qq");
    $sWechat = RR("wechat");
    $nDesable = RR("disable");

    $SaveResult = $this->modUser->UserSave($this->modUser->clsUser->_UserType["captain"], $nUserID, $nParentUserID, $sLoginName, $sLoginPassword, $nBaseSalary, $nProvinceID, $nCityID, $sRealName, $sMobilePhone, $sQQ, $sWechat, $nDesable);

    AjaxReturn($SaveResult);
  }

  /**
   * todo:刷新团队长缓存
   */
  public function ResetSellerCaptainCache(){
    $Result = $this->modUser->ResetUserCache($this->modUser->clsUser->_UserType["captain"]);

    AjaxReturn($Result);
  }

  /**
   * todo:获取团队长列表查询参数
   */
  private function GetSellerCaptainListSearchParam(){
    $map = $this->SetSellerCaptainListParamData();

    if(
      IsN($map["search_case"])
      && IsN($map["search_key"])
      && IsN($map["search_status"])
      && IsN($map["search_broker_company"])
    ){
      return array();
    }

    $this->assign("search_case", $map["search_case"]);
    $this->assign("search_key", $map["search_key"]);
    $this->assign("search_status", $map["search_status"]);
    $this->assign("search_broker_company", $map["search_broker_company"]);

    return $map;
  }

  private function SetSellerCaptainListParamData(){
    $Param["search_case"] = RR("search_case"); //查询条件，对应数据库字段
    $Param["search_key"] = RR("search_key"); //关键字
    $Param["search_key"] = urldecode($Param["search_key"]);
    $Param["search_status"] = RR("search_status");
    $Param["search_broker_company"] = RR("search_broker_company");

    return $Param;
  }
  //endregion 团队长

  //region 客户经理
  /**
   * todo: 客户经理列表
   */
  public function SellerMemberList(){
    $this->_Controller = "客户经理";
    $this->_Action = "客户经理列表";

    $sUserType = $this->modUser->clsUser->_UserType["member"];
    $nParentUserId = 0;

    if($this->_AccountInfo["user_type"] == $this->_SellerCaptainAccountType){
      $nParentUserId = $this->_AccountInfo["id"];
    }

    $SearchParam = $this->GetSellerMemberListSearchParam();

    $SellerMemberList = $this->modUser->UserList($sUserType, $nParentUserId, $this->_PagingRowCount, $SearchParam);

    $this->assign("SellerMemberList", $SellerMemberList["DataList"]);
    $this->assign("Page", $SellerMemberList["PageInfo"]);

    $this->CustomDisplay("seller_member_list");
  }

  /**
   * todo:保存客户经理信息
   */
  public function SellerMemberInfo(){
    $this->_Controller = "客户经理";
    $this->_Action = "客户经理信息";

    $nUserID = RR("user_id");

    $SellerMemberInfo = $this->modUser->UserInfo($nUserID);

    $SellerCaptainOption = $this->modUser->clsUser->SellerCaptainOption();

    $clsRegion = new \Org\ZhiHui\Region();
    $ProvinceList = $clsRegion->GetProvinceList();
    $CityList = array();
    $DistrictList = array();

    if(IsArray($SellerMemberInfo)){
      if(IsNum($SellerMemberInfo["province_id"], false, false)){
        $CityList = $clsRegion->GetCityList($SellerMemberInfo["province_id"]);
      }

      if(IsNum($SellerMemberInfo["city_id"], false, false)){
        $DistrictList = $clsRegion->GetDistrictList($SellerMemberInfo["city_id"]);
      }
    }

    $this->assign("SellerMemberInfo", $SellerMemberInfo);
    $this->assign("SellerCaptainOption", $SellerCaptainOption);
    $this->assign("ProvinceList", $ProvinceList);
    $this->assign("CityList", $CityList);
    $this->assign("DistrictList", $DistrictList);


    $this->CustomDisplay("seller_member_info");
  }

  /**
   * todo: 保存客户经理信息
   */
  public function AjaxSellerMemberSave(){
    $nUserID = RR("user_id");
    $nParentUserID = RR("parent_user_id");
    $sLoginName = RR("login_name");
    $sLoginPassword = RR("login_password");
    $nBaseSalary = RR("base_salary");
    $nProvinceID = RR("province_id");
    $nCityID = RR("city_id");
    $sRealName = RR("real_name");
    $sMobilePhone = RR("mobile_phone");
    $sQQ = RR("qq");
    $sWechat = RR("wechat");
    $nDesable = RR("disable");

    $SaveResult = $this->modUser->UserSave($this->modUser->clsUser->_UserType["member"], $nUserID, $nParentUserID, $sLoginName, $sLoginPassword, $nBaseSalary, $nProvinceID, $nCityID, $sRealName, $sMobilePhone, $sQQ, $sWechat, $nDesable);

    AjaxReturn($SaveResult);
  }

  /**
   * todo:刷新客户经理缓存
   */
  public function ResetSellerMemberCache(){
    $Result = $this->modUser->ResetUserCache($this->modUser->clsUser->_UserType["member"]);

    AjaxReturn($Result);
  }

  /**
   * todo:获取客户经理列表查询参数
   */
  private function GetSellerMemberListSearchParam(){
    $map = $this->SetSellerMemberListParamData();

    if(
      IsN($map["search_case"])
      && IsN($map["search_key"])
      && IsN($map["search_status"])
      && IsN($map["search_broker_company"])
    ){
      return array();
    }

    $this->assign("search_case", $map["search_case"]);
    $this->assign("search_key", $map["search_key"]);
    $this->assign("search_status", $map["search_status"]);
    $this->assign("search_broker_company", $map["search_broker_company"]);

    return $map;
  }

  private function SetSellerMemberListParamData(){
    $Param["search_case"] = RR("search_case"); //查询条件，对应数据库字段
    $Param["search_key"] = RR("search_key"); //关键字
    $Param["search_key"] = urldecode($Param["search_key"]);
    $Param["search_status"] = RR("search_status");
    $Param["search_broker_company"] = RR("search_broker_company");

    return $Param;
  }
  //endregion 客户经理

  /**
   * todo: Ajax获取用户信息
   */
  public function AjaxUserInfo(){
    $nUserID = RR("user_id");

    $UserInfo = $this->modUser->UserInfo($nUserID);

    AjaxReturnCorrect("", $UserInfo);
  }

  /**
   * todo: Ajax获取客户列表
   */
  public function AjaxCustomerList(){
    $nUserID = RR("user_id");

    $modCustomer = D("Customer");
    $Result = $modCustomer->SellerMemberCustomerList($nUserID);

    AjaxReturnCorrect("", $Result);
  }

  /**
   * todo: Ajax删除用户
   */
  public function AjaxUserDelete(){
    $sSellerCaptainID = RR("user_id");

    $Result = $this->modUser->UserDelete($sSellerCaptainID);

    AjaxReturn($Result);
  }

  public function CustomerPayList(){
    $nCustomerID = RR("customer_id");

    $modCustomer = D("Customer");
    $CustomerPayList = $modCustomer->CustomerPayList($nCustomerID);

    $this->assign("CustomerPayList", $CustomerPayList);

    $this->CustomDisplay("Customer/customer_pay_list");
  }
}