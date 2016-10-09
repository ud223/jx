<?php
namespace CustomerRreservationSystem\Controller;
use Think\Controller;

class BrokerCompanyController extends CommonController {
  private $modBrokerCompany = null;
  
  function __construct() {
    parent::__construct();

    $this->_Model = "经纪公司";

    $this->modBrokerCompany = D("BrokerCompany");
  }

  //region 经济公司
  /**
   * todo: 经纪公司列表
   */
  public function BrokerCompanyList(){
    $SearchParam = $this->GetBrokerCompanyListSearchParam();
    $BrokerCompanyList = $this->modBrokerCompany->BrokerCompanyList($this->_PagingRowCount, $SearchParam);

    $this->assign("BrokerCompanyList", $BrokerCompanyList["DataList"]);
    $this->assign("Page", $BrokerCompanyList["PageInfo"]);

    $this->CustomDisplay("broker_company_list");
  }

  /**
   * todo: 经纪公司列表
   */
  public function BrokerCompanyInfo(){
    $nBrokerCompanyID = RR("broker_company_id");
    
    $BrokerCompanyInfo = $this->modBrokerCompany->BrokerCompanyInfo($nBrokerCompanyID);

    $this->assign("BrokerCompanyInfo", $BrokerCompanyInfo);

    $this->CustomDisplay("broker_company_info");
  }

  /**
   * todo: 经纪公司列表
   */
  public function AjaxBrokerCompanySave(){
    $nBrokerCompanyID = RR("broker_company_id");
    $nBrokerCompanyName = RR("broker_company_name");

    $Result = $this->modBrokerCompany->BrokerCompanySave($nBrokerCompanyID, $nBrokerCompanyName);

    AjaxReturn($Result);
  }

  /**
   * todo:快速修改经济公司信息的字段值
   */
  public function QuickModifyBrokerCompanyField(){
    $sKey = RR("key"); //查询条件，对应数据库字段
    $sVal = RR("val"); //关键字
    $nID = RR("id"); //数据ID

    $Result = $this->modBrokerCompany->QuickBrokerCompanySave($nID, $sKey, $sVal);

    if($Result["error"] == 0){
      AjaxReturnCorrect($Result["info"], $sVal);
    }else{
      AjaxReturn($Result);
    }
  }

  /**
   * todo:刷新经济公司缓存
   */
  public function ResetBrokerCompanyCache(){
    $Result = $this->modBrokerCompany->ResetBrokerCompanyCache();

    AjaxReturn($Result);
  }

  /**
   * todo: Ajax删除经纪公司
   */
  public function AjaxBrokerCompanyDelete(){
    $sBrokerCompanyID = RR("broker_company_id");
    
    $Result = $this->modBrokerCompany->BrokerCompanyDelete($sBrokerCompanyID);
    
    AjaxReturn($Result);
  }

  /**
   * todo:获取经济公司列表查询参数
   */
  private function GetBrokerCompanyListSearchParam(){
    $map = $this->SetBrokerCompanyListParamData();

    if(
      IsN($map["search_case"])
      && IsN($map["search_key"])
      && IsN($map["search_status"])
    ){
      return array();
    }

    $this->assign("search_case", $map["search_case"]);
    $this->assign("search_key", $map["search_key"]);
    $this->assign("search_status", $map["search_status"]);
    $this->assign("sort_value", $map["sort_value"]);

    return $map;
  }

  private function SetBrokerCompanyListParamData(){
    $Param["search_case"] = RR("search_case"); //查询条件，对应数据库字段
    $Param["search_key"] = RR("search_key"); //关键字
    $Param["search_key"] = urldecode($Param["search_key"]);
    $Param["search_status"] = RR("search_status");

    return $Param;
  }
  //endregion 经纪公司
  
  //region 经纪公司帐号
  /**
   * todo: 经纪公司帐号列表
   */
  public function BrokerCompanyAccountList(){
    $SearchParam = $this->GetBrokerCompanyAccountListSearchParam();
    
    if($this->_AccountType != $this->_AdminAccountType){
      $SearchParam["search_broker_company"] = $this->_AccountInfo["broker_company_id"];
    }
    
    $BrokerCompanyAccountList = $this->modBrokerCompany->BrokerCompanyAccountList($this->_PagingRowCount, $SearchParam);

    $BrokerCompanyOption = $this->modBrokerCompany->clsBrokerCompany->BrokerCompanyOption();
    
    $this->assign("BrokerCompanyOption", $BrokerCompanyOption);
    $this->assign("BrokerCompanyAccountList", $BrokerCompanyAccountList["DataList"]);
    $this->assign("Page", $BrokerCompanyAccountList["PageInfo"]);

    $this->CustomDisplay("broker_company_user_list");
  }

  /**
   * todo:经济公司帐号信息
   */
  public function BrokerCompanyAccountInfo(){
    $nBrokerCompanyAccountID = RR("broker_company_account_id");

    $BrokerCompanyAccountInfo = $this->modBrokerCompany->BrokerCompanyAccountInfo($nBrokerCompanyAccountID);

    $this->assign("BrokerCompanyAccountInfo", $BrokerCompanyAccountInfo["broker_company_account_info"]);

    if($this->_AccountType === $this->_AdminAccountType){
      $this->assign("BrokerCompanyOption", $BrokerCompanyAccountInfo["broker_company_option"]);
    }
    
    $this->CustomDisplay("broker_company_user_info");
  }

  /**
   * todo: 保存经纪公司帐号信息
   */
  public function AjaxBrokerCompanyAccountSave(){
    $nBrokerCompanyAccountID = RR("broker_company_account_id");
    $nBrokerCompanyID = RR("broker_company");
    $sLoginName = RR("login_name");
    $sLoginPassword = RR("login_password");
    $sRealName = RR("real_name");
    $sMobilePhone = RR("mobile_phone");
    $sQQ = RR("qq");
    $sWechat = RR("wechat");
    $nDesable = RR("disable");

    $SaveResult = $this->modBrokerCompany->BrokerCompanyAccountSave($nBrokerCompanyAccountID, $nBrokerCompanyID, $sLoginName, $sLoginPassword, $sRealName, $sMobilePhone, $sQQ, $sWechat, $nDesable);

    AjaxReturn($SaveResult);
  }

  /**
   * todo: Ajax删除经纪公司帐号
   */
  public function AjaxBrokerCompanyAccountDelete(){
    $sBrokerCompanyAccountID = RR("broker_company_account_id");

    $Result = $this->modBrokerCompany->BrokerCompanyAccountDelete($sBrokerCompanyAccountID);

    AjaxReturn($Result);
  }

  /**
   * todo:刷新经纪公司帐号缓存
   */
  public function ResetBrokerCompanyAccountCache(){
    $Result = $this->modBrokerCompany->ResetBrokerCompanyAccountCache();

    AjaxReturn($Result);
  }

  /**
   * todo:获取经纪公司帐号列表查询参数
   */
  private function GetBrokerCompanyAccountListSearchParam(){
    $map = $this->SetBrokerCompanyAccountListParamData();

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

  private function SetBrokerCompanyAccountListParamData(){
    $Param["search_case"] = RR("search_case"); //查询条件，对应数据库字段
    $Param["search_key"] = RR("search_key"); //关键字
    $Param["search_key"] = urldecode($Param["search_key"]);
    $Param["search_status"] = RR("search_status");
    $Param["search_broker_company"] = RR("search_broker_company");

    return $Param;
  }
  //endregion 经纪公司帐号
  
  //region 门店
  /**
   * todo: 经纪公司门店列表
   */
  public function BrokerCompanyStoreList(){
    $SearchParam = $this->GetBrokerCompanyStoreListSearchParam();

    if($this->_AccountType != $this->_AdminAccountType){
      $SearchParam["search_broker_company"] = $this->_AccountInfo["broker_company_id"];
    }
    
    $BrokerCompanyStoreList = $this->modBrokerCompany->BrokerCompanyStoreList($this->_PagingRowCount, $SearchParam);

    $BrokerCompanyOption = $this->modBrokerCompany->clsBrokerCompany->BrokerCompanyOption();

    $clsRegion = new \Org\ZhiHui\Region();
    $ProvinceList = $clsRegion->GetProvinceList();
    $CityList = array();
    $DistrictList = array();

    if(IsNum($SearchParam["search_province"], false, false)){
      $CityList = $clsRegion->GetCityList($SearchParam["search_province"]);
    }

    if(IsNum($SearchParam["search_city"], false, false)){
      $DistrictList = $clsRegion->GetDistrictList($SearchParam["search_city"]);
    }
    
    $this->assign("BrokerCompanyOption", $BrokerCompanyOption);
    $this->assign("BrokerCompanyStoreList", $BrokerCompanyStoreList["DataList"]);
    $this->assign("Page", $BrokerCompanyStoreList["PageInfo"]);
    $this->assign("ProvinceList", $ProvinceList);
    $this->assign("CityList", $CityList);
    $this->assign("DistrictList", $DistrictList);
    
    $this->CustomDisplay("broker_company_store_list");
  }

  /**
   * todo:经纪公司门店信息
   */
  public function BrokerCompanyStoreInfo(){
    $nBrokerCompanyStoreID = RR("broker_company_store_id");

    $BrokerCompanyStoreInfo = $this->modBrokerCompany->BrokerCompanyStoreInfo($nBrokerCompanyStoreID);

    $clsRegion = new \Org\ZhiHui\Region();
    $ProvinceList = $clsRegion->GetProvinceList();
    $CityList = array();
    $DistrictList = array();

    if(IsNum($BrokerCompanyStoreInfo["broker_company_store_info"]["province_id"], false, false)){
      $CityList = $clsRegion->GetCityList($BrokerCompanyStoreInfo["broker_company_store_info"]["province_id"]);
    }

    if(IsNum($BrokerCompanyStoreInfo["broker_company_store_info"]["city_id"], false, false)){
      $DistrictList = $clsRegion->GetDistrictList($BrokerCompanyStoreInfo["broker_company_store_info"]["city_id"]);
    }

    $this->assign("BrokerCompanyStoreInfo", $BrokerCompanyStoreInfo["broker_company_store_info"]);
    $this->assign("BrokerCompanyOption", $BrokerCompanyStoreInfo["broker_company_option"]);
    $this->assign("ProvinceList", $ProvinceList);
    $this->assign("CityList", $CityList);
    $this->assign("DistrictList", $DistrictList);
    
    $this->CustomDisplay("broker_company_store_info");
  }

  /**
   * todo: 保存经纪公司门店信息
   */
  public function AjaxBrokerCompanyStoreSave(){
    $nBrokerCompanyStoreID = RR("broker_company_store_id");
    $nBrokerCompanyID = RR("broker_company");
    $sStoreName = RR("store_name");
    $sPhone = RR("phone");
    $nProvinceID = RR("province_id");
    $nCityID = RR("city_id");
    $nDistrictID = RR("district_id");
    $sAddress = RR("address");

    $SaveResult = $this->modBrokerCompany->BrokerCompanyStoreSave($nBrokerCompanyStoreID, $nBrokerCompanyID, $sStoreName, $sPhone, $nProvinceID, $nCityID, $nDistrictID, $sAddress);

    AjaxReturn($SaveResult);
  }

  /**
   * todo: Ajax删除经纪公司门店
   */
  public function AjaxBrokerCompanyStoreDelete(){
    $sBrokerCompanyStoreID = RR("broker_company_store_id");

    $Result = $this->modBrokerCompany->BrokerCompanyStoreDelete($sBrokerCompanyStoreID);

    AjaxReturn($Result);
  }

  /**
   * todo:刷新经纪公司门店缓存
   */
  public function ResetBrokerCompanyStoreCache(){
    $Result = $this->modBrokerCompany->ResetBrokerCompanyStoreCache();

    AjaxReturn($Result);
  }

  /**
   * todo:获取门店列表查询参数
   */
  private function GetBrokerCompanyStoreListSearchParam(){
    $map = $this->SetBrokerCompanyStoreListParamData();

    if(
      IsN($map["search_province"])
      && IsN($map["search_city"])
      && IsN($map["search_district"])
      && IsN($map["search_broker_company"])
    ){
      return array();
    }

    $this->assign("search_province", $map["search_province"]);
    $this->assign("search_city", $map["search_city"]);
    $this->assign("search_district", $map["search_district"]);
    $this->assign("search_broker_company", $map["search_broker_company"]);

    return $map;
  }

  private function SetBrokerCompanyStoreListParamData(){
    $Param["search_province"] = RR("search_province"); //查询条件，对应数据库字段
    $Param["search_city"] = RR("search_city"); //关键字
    $Param["search_district"] = RR("search_district"); //关键字
    $Param["search_broker_company"] = RR("search_broker_company");

    return $Param;
  }
  //endregion 门店
}