<?php
namespace CustomerRreservationSystem\Controller;
use Think\Controller;

class SellerManagerController extends CommonController {
  private $modSellerManager = null;
  
  function __construct() {
    parent::__construct();

    $this->_Model = "经纪公司";

    $this->modSellerManager = D("SellerManager");
  }
  
  //region 销售经理
  /**
   * todo: 销售经理列表
   */
  public function SellerManagerList(){
    $SearchParam = $this->GetSellerManagerListSearchParam();

    if($this->_AccountType != $this->_AdminAccountType){
      $SearchParam["search_broker_company"] = $this->_AccountInfo["broker_company_id"];
    }
    
    $SellerManagerList = $this->modSellerManager->SellerManagerList($this->_PagingRowCount, $SearchParam);

    $SellerManagerOption = $this->modSellerManager->clsBrokerCompany->BrokerCompanyOption();
    
    $this->assign("SellerManagerOption", $SellerManagerOption);
    $this->assign("SellerManagerList", $SellerManagerList["DataList"]);
    $this->assign("Page", $SellerManagerList["PageInfo"]);

    $this->CustomDisplay("seller_manager_list");
  }

  /**
   * todo:保存销售经理信息
   */
  public function SellerManagerInfo(){
    $nSellerManagerID = RR("seller_manager_id");

    $SellerManagerInfo = $this->modSellerManager->SellerManagerInfo($nSellerManagerID);

    $clsRegion = new \Org\ZhiHui\Region();
    $ProvinceList = $clsRegion->GetProvinceList();
    $CityList = array();
    $DistrictList = array();

    if(IsArray($SellerManagerInfo["seller_manager_info"])){
      if(IsNum($SellerManagerInfo["seller_manager_info"]["province_id"], false, false)){
        $CityList = $clsRegion->GetCityList($SellerManagerInfo["seller_manager_info"]["province_id"]);
      }

      if(IsNum($SellerManagerInfo["seller_manager_info"]["city_id"], false, false)){
        $DistrictList = $clsRegion->GetDistrictList($SellerManagerInfo["seller_manager_info"]["city_id"]);
      }
    }
    
    $this->assign("SellerManagerInfo", $SellerManagerInfo["seller_manager_info"]);
    $this->assign("BrokerCompanyOption", $SellerManagerInfo["broker_company_option"]);
    $this->assign("ProvinceList", $ProvinceList);
    $this->assign("CityList", $CityList);
    $this->assign("DistrictList", $DistrictList);
    
    $this->CustomDisplay("seller_manager_info");
  }

  /**
   * todo: 保存销售经理信息
   */
  public function AjaxSellerManagerSave(){
    $nSellerManagerID = RR("seller_manager_id");
    $nBrokerCompanyID = RR("broker_company");
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

    $SaveResult = $this->modSellerManager->SellerManagerSave($nSellerManagerID, $nBrokerCompanyID, $sLoginName, $sLoginPassword, $nBaseSalary, $nProvinceID, $nCityID, $sRealName, $sMobilePhone, $sQQ, $sWechat, $nDesable);

    AjaxReturn($SaveResult);
  }

  /**
   * todo: Ajax删除销售经理
   */
  public function AjaxSellerManagerDelete(){
    $sSellerManagerID = RR("seller_manager_id");

    $Result = $this->modSellerManager->SellerManagerDelete($sSellerManagerID);

    AjaxReturn($Result);
  }

  /**
   * todo:刷新销售经理缓存
   */
  public function ResetSellerManagerCache(){
    $Result = $this->modSellerManager->ResetSellerManagerCache();

    AjaxReturn($Result);
  }

  /**
   * todo:获取销售经理列表查询参数
   */
  private function GetSellerManagerListSearchParam(){
    $map = $this->SetSellerManagerListParamData();

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

  private function SetSellerManagerListParamData(){
    $Param["search_case"] = RR("search_case"); //查询条件，对应数据库字段
    $Param["search_key"] = RR("search_key"); //关键字
    $Param["search_key"] = urldecode($Param["search_key"]);
    $Param["search_status"] = RR("search_status");
    $Param["search_broker_company"] = RR("search_broker_company");

    return $Param;
  }
  //endregion 销售经理
}