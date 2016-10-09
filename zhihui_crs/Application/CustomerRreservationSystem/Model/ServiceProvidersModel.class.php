<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

class ServiceProvidersModel extends Model {
  Protected $autoCheckFields = false;

  private $modServiceProviders = null;
  
  private $clsServiceProviders = null;
  private $clsBrokerCompany = null;
  
  
  function __construct() {
    $this->modServiceProviders = M("service_providers");
    
    $this->clsServiceProviders = new \Org\ZhiHui\ServiceProviders();
    $this->clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
  }
  
  //region 服务商
  /**
   * todo:服务商列表
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function ServiceProvidersList($_rownum=20, $_param=array()){
    $sField = "id";
    $sWhere = "1=1";
    $sOrder = "id desc";

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "service_providers_name like '%{$_param["search_key"]}%'";
          $sWhere .= ")";
        }
      }
    }

    $nTotal = $this->modServiceProviders->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $ServiceProvidersIdList = $this->modServiceProviders->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $ServiceProvidersList = array();

    foreach($ServiceProvidersIdList as $key=>$val){
      $ServiceProvidersInfo = $this->clsServiceProviders->GetServiceProvidersDetails($val["id"]);

      array_push($ServiceProvidersList, $ServiceProvidersInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$ServiceProvidersList);
  }

  /**
   * todo: 保存服务商
   * 
   * @param $_service_providers_name 服务商名称
   * @param $_broker_company_id 经纪公司ID
   *
   * @return array
   */
  public function ServiceProvidersSave($_service_providers_name, $_broker_company_id){
    //region 数据检查,并赋值保存的数据
    if(IsN($_service_providers_name)){
      return ReturnError(L("_SERVICE_PROVIDERS_NAME_NULL_"));
    }else{
      $sWhere = "service_providers_name='{$_service_providers_name}'";
      $ServiceProvidersNumber = $this->modServiceProviders->where($sWhere)->count();
      
      if(IsNum($ServiceProvidersNumber, false, false)){
        return ReturnError(L("_SERVICE_PROVIDERS_NAME_EXIST_"));
      }

      $SaveBaseData["service_providers_name"] = $_service_providers_name;
    }

    //region 检查经纪公司
    if(!IsNum($_broker_company_id, false, false)){
      $_broker_company_id = 0;
      //return ReturnError(L("_BROKER_COMPANY_NEED_SELECT_"));
    }else{
      $BrokerCompanyInfo = $this->clsBrokerCompany->GetBrokerCompanyDetails($_broker_company_id);

      if(!IsArray($BrokerCompanyInfo)){
        return ReturnError(L("_BROKER_COMPANY_NOT_EXIST_"));
      }
    }
    $SaveBaseData["broker_company_id"] = $_broker_company_id;
    //endregion 检查经纪公司
    
    //endregion 数据检查,并赋值保存的数据

    //region 添加基本数据
    $SaveBaseResult = $this->modServiceProviders->add($SaveBaseData);

    $nServiceProvidersID = $SaveBaseResult;

    if($SaveBaseResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    //endregion 添加基本数据

    $this->clsServiceProviders->SetServiceProvidersDetailsCache($nServiceProvidersID);

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo:快速保存服务商信息
   */
  public function QuickModifyServiceProvidersField($_id, $_key, $_val){
    if(!IsNum($_id, false) || IsN($_key) || IsN($_val)){
      return ReturnError(L('_PARAM_ERROR_'));
    }

    $map["id"] = $_id;
    $map[$_key] = $_val;

    $Result = $this->modServiceProviders->save($map);

    if($Result !== false){
      $this->clsServiceProviders->SetServiceProvidersDetailsCache($_id);
      return ReturnCorrect(L('_SAVE_DATA_SUCCEED_'));
    }else{
      return ReturnError(L('_SAVE_DATA_FAILURE_'));
    }
  }
  
  /**
   * todo: 删除服务商
   *
   * @param $_id_str id字符串，逗号分割id
   *
   * @return array
   */
  public function ServiceProvidersDelete($_id_str){
    if(IsN($_id_str)){
      return ReturnError(L("_SERVICE_PROVIDERS_ID_NULL_"));
    }else{
      $IdList = explode(",", $_id_str);

      foreach($IdList as $key=>$val){
        if(!IsNum($val, false, false)){
          return ReturnError(L("_SERVICE_PROVIDERS_ID_ERROR_"));
        }
      }
    }

    $sWhere = "id in ({$_id_str})";
    $Result = $this->modServiceProviders->where($sWhere)->delete();

    if($Result === false){
      return ReturnError(L("_DELETE_DATA_FAILURE_"));
    }

    foreach($IdList as $key=>$val){
      $this->clsServiceProviders->SetServiceProvidersDetailsCache($val);
    }

    return ReturnCorrect(L("_DELETE_DATA_SUCCEED_"));
  }

  /**
   * todo: 刷新服务商缓存
   */
  public function ResetServiceProvidersCache(){
    $sField = "id";
    $ServiceProvidersIdList = $this->modServiceProviders->field($sField)->select();

    foreach($ServiceProvidersIdList as $key=>$val){
      $this->clsServiceProviders->SetServiceProvidersDetailsCache($val["id"]);
    }

    return ReturnCorrect(L("_CACHE_REFRESH_SUCCEED_"));
  }
  //endregion 服务商
}
