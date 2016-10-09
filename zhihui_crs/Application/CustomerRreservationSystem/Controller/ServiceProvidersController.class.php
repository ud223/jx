<?php
namespace CustomerRreservationSystem\Controller;
use Think\Controller;

class ServiceProvidersController extends CommonController {
  private $modServiceProviders = null;
  
  function __construct() {
    parent::__construct();

    $this->_Model = "服务商";

    $this->modServiceProviders = D("ServiceProviders");
  }
  
  //region 服务商
  /**
   * todo: 服务商列表
   */
  public function ServiceProvidersList(){
    $SearchParam = $this->GetServiceProvidersListSearchParam();
    $ServiceProvidersList = $this->modServiceProviders->ServiceProvidersList($this->_PagingRowCount, $SearchParam);

    $this->assign("ServiceProvidersList", $ServiceProvidersList["DataList"]);
    $this->assign("Page", $ServiceProvidersList["PageInfo"]);

    $this->CustomDisplay("service_providers_list");
  }

  /**
   * todo: 保存服务商信息
   */
  public function AjaxServiceProvidersSave(){
    $sServiceProvidersName = RR("service_providers_name");
    $nBrokerCompanyID = RR("broker_company");

    $SaveResult = $this->modServiceProviders->ServiceProvidersSave($sServiceProvidersName, $nBrokerCompanyID);

    AjaxReturn($SaveResult);
  }

  /**
   * todo:快速修改服务商信息的字段值
   */
  public function QuickModifyServiceProvidersField(){
    $sKey = RR("key"); //查询条件，对应数据库字段
    $sVal = RR("val"); //关键字
    $nID = RR("id"); //数据ID

    $Result = $this->modServiceProviders->QuickModifyServiceProvidersField($nID, $sKey, $sVal);

    if($Result["error"] == 0){
      AjaxReturnCorrect($Result["info"], $sVal);
    }else{
      AjaxReturn($Result);
    }
  }

  /**
   * todo: Ajax删除服务商
   */
  public function AjaxServiceProvidersDelete(){
    $sServiceProvidersID = RR("service_providers_id");

    $Result = $this->modServiceProviders->ServiceProvidersDelete($sServiceProvidersID);

    AjaxReturn($Result);
  }

  /**
   * todo:刷新服务商缓存
   */
  public function ResetServiceProvidersCache(){
    $Result = $this->modServiceProviders->ResetServiceProvidersCache();

    AjaxReturn($Result);
  }

  /**
   * todo:获取服务商列表查询参数
   */
  private function GetServiceProvidersListSearchParam(){
    $map = $this->SetServiceProvidersListParamData();

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

  private function SetServiceProvidersListParamData(){
    $Param["search_case"] = RR("search_case"); //查询条件，对应数据库字段
    $Param["search_key"] = RR("search_key"); //关键字
    $Param["search_key"] = urldecode($Param["search_key"]);
    $Param["search_broker_company"] = RR("search_broker_company");

    return $Param;
  }
  //endregion 服务商
}