<?php
/**
 * Created by PhpStorm.
 * User: 淼
 * Date: 2015/4/29
 * Time: 11:41
 */

namespace Org\ZhiHui;

class SellerManager {
  private $modSellerManager = null;
  
  function __construct() {
    $this->modSellerManager = M("seller_manager");
  }
  
  //region 缓存
  //region 销售经理信息缓存
  /**
   * 获取销售经理信息
   * @param $_seller_manager_id 销售经理ID
   * @return mixed
   */
  public function GetSellerManagerDetails($_seller_manager_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetSellerManagerDetailsCache($_seller_manager_id);

      if(empty($Info)){
        $Info = $this->GetSellerManagerDetailsDb($_seller_manager_id);
      }
    }else{
      $Info = $this->GetSellerManagerDetailsDb($_seller_manager_id);
    }
    
    if(IsArray($Info)){
      $clsRegion = new \Org\ZhiHui\Region();

      $Info["province_name"] = "";
      if(IsNum($Info["province_id"], false, false)){
        $ProvinceInfo = $clsRegion->GetProvinceDetails($Info["province_id"]);
        $Info["province_name"] = $ProvinceInfo["province_name"];
      }

      $Info["city_name"] = "";
      if(IsNum($Info["city_id"], false, false)){
        $ProvinceInfo = $clsRegion->GetCityDetails($Info["city_id"]);
        $Info["city_name"] = $ProvinceInfo["city_name"];
      }
    }

    return $Info;
  }

  /**
   * 设置销售经理信息数据缓存
   * @param $_seller_manager_id 销售经理ID
   * @param array $_data 销售经理信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetSellerManagerDetailsCache($_seller_manager_id, $_data=NULL){
    F($this->GetSellerManagerDetailsCacheKey($_seller_manager_id), $_data);
  }

  /**
   * 获取销售经理信息数据缓存
   * @param $_seller_manager_id 销售经理ID
   * @return mixed
   */
  private function GetSellerManagerDetailsCache($_seller_manager_id){
    return F($this->GetSellerManagerDetailsCacheKey($_seller_manager_id));
  }

  /**
   * 获取销售经理信息缓存Key
   * @param $_seller_manager_id 销售经理ID
   * @return mixed
   */
  private function GetSellerManagerDetailsCacheKey($_seller_manager_id){
    return "SellerManager/Details/{$_seller_manager_id}";
  }

  /**
   * todo:从数据库中获取销售经理信息数据
   * @param $_seller_manager_id 销售经理ID
   *
   * @return mixed
   */
  private function GetSellerManagerDetailsDb($_seller_manager_id){
    $SellerManagerDetails = array();

    if(!IsNum($_seller_manager_id, false, false)){
      return $SellerManagerDetails;
    }

    $sWhere = "id={$_seller_manager_id}";
    $SellerManagerDetails = $this->modSellerManager->where($sWhere)->find();

    if(!empty($SellerManagerDetails)){
      $this->SetSellerManagerDetailsCache($_seller_manager_id, $SellerManagerDetails);
    }

    return $SellerManagerDetails;
  }
  //endregion 销售经理信息缓存
  //endregion 缓存

  /**
   * todo: 销售经理Option列表
   * @param $_broker_company_id
   */
  public function SellerManagerOption($_broker_company_id){
    $sField = "id";
    $sWhere = "disable=0";
    
    if(IsNum($_broker_company_id, false, false)){
      $sWhere .= " and broker_company_id={$_broker_company_id}";
    }
    
    $SellerManagerIdList = $this->modSellerManager->field($sField)->where($sWhere)->select();
    $SellerManagerOption = array();
    
    foreach($SellerManagerIdList as $key=>$val){
      $SellerManagerInfo = $this->GetSellerManagerDetails($val["id"]);
      
      $OptionInfo["key"] = $SellerManagerInfo["login_name"];
      
      if(!IsN($SellerManagerInfo["real_name"])){
        $OptionInfo["key"] .= "/".$SellerManagerInfo["real_name"];
      }

      $OptionInfo["val"] = $SellerManagerInfo["id"];
      
      array_push($SellerManagerOption, $OptionInfo);
    }
    
    return $SellerManagerOption;
  }
  
}