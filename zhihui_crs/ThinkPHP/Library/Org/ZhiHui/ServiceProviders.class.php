<?php
/**
 * Created by PhpStorm.
 * User: 淼
 * Date: 2015/4/29
 * Time: 11:41
 */

namespace Org\ZhiHui;

class ServiceProviders {
  private $modServiceProviders = null;
  
  function __construct() {
    $this->modServiceProviders = M("service_providers");
  }
  
  //region 缓存
  //region 服务商信息缓存
  /**
   * 获取服务商信息
   * @param $_service_providers_id 服务商ID
   * @return mixed
   */
  public function GetServiceProvidersDetails($_service_providers_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetServiceProvidersDetailsCache($_service_providers_id);

      if(empty($Info)){
        $Info = $this->GetServiceProvidersDetailsDb($_service_providers_id);
      }
    }else{
      $Info = $this->GetServiceProvidersDetailsDb($_service_providers_id);
    }

    return $Info;
  }

  /**
   * 设置服务商信息数据缓存
   * @param $_service_providers_id 服务商ID
   * @param array $_data 服务商信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetServiceProvidersDetailsCache($_service_providers_id, $_data=NULL){
    F($this->GetServiceProvidersDetailsCacheKey($_service_providers_id), $_data);
  }

  /**
   * 获取服务商信息数据缓存
   * @param $_service_providers_id 服务商ID
   * @return mixed
   */
  private function GetServiceProvidersDetailsCache($_service_providers_id){
    return F($this->GetServiceProvidersDetailsCacheKey($_service_providers_id));
  }

  /**
   * 获取服务商信息缓存Key
   * @param $_service_providers_id 服务商ID
   * @return mixed
   */
  private function GetServiceProvidersDetailsCacheKey($_service_providers_id){
    return "ServiceProviders/Details/{$_service_providers_id}";
  }

  /**
   * todo:从数据库中获取服务商信息数据
   * @param $_service_providers_id 服务商ID
   *
   * @return mixed
   */
  private function GetServiceProvidersDetailsDb($_service_providers_id){
    $ServiceProvidersDetails = array();

    if(!IsNum($_service_providers_id, false, false)){
      return $ServiceProvidersDetails;
    }

    $sWhere = "id={$_service_providers_id}";
    $ServiceProvidersDetails = $this->modServiceProviders->where($sWhere)->find();

    if(!empty($ServiceProvidersDetails)){
      $this->SetServiceProvidersDetailsCache($_service_providers_id, $ServiceProvidersDetails);
    }

    return $ServiceProvidersDetails;
  }
  //endregion 服务商信息缓存
  //endregion 缓存

  /**
   * todo: 门店Option选项列表
   * @return array
   */
  public function ServiceProvidersOption(){
    $sField = "id";
    $sOrder = "id desc";

    $ServiceProvidersIdList = $this->modServiceProviders->field($sField)->order($sOrder)->select();
    $ServiceProvidersOptionList = array();

    foreach($ServiceProvidersIdList as $key=>$val){
      $ServiceProvidersInfo = $this->GetServiceProvidersDetails($val["id"]);

      $OptionInfo["key"] = $ServiceProvidersInfo["service_providers_name"];
      $OptionInfo["val"] = $ServiceProvidersInfo["id"];

      array_push($ServiceProvidersOptionList, $OptionInfo);
    }

    return $ServiceProvidersOptionList;
  }
}