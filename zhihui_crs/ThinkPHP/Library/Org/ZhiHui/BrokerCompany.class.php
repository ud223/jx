<?php
/**
 * Created by PhpStorm.
 * User: 淼
 * Date: 2015/4/29
 * Time: 11:41
 */

namespace Org\ZhiHui;

class BrokerCompany {
  private $modBrokerCompany = null;
  private $modBrokerCompanyAccount = null;
  private $modBrokerCompanyStore = null;
  
  function __construct() {
    $this->modBrokerCompany = M("broker_company");
    $this->modBrokerCompanyAccount = M("broker_company_account");
    $this->modBrokerCompanyStore = M("broker_company_store");
  }
  
  //region 缓存
  //region 经纪公司信息缓存
  /**
   * 获取经纪公司信息
   * @param $_broker_company_id 经纪公司ID
   * @return mixed
   */
  public function GetBrokerCompanyDetails($_broker_company_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetBrokerCompanyDetailsCache($_broker_company_id);

      if(empty($Info)){
        $Info = $this->GetBrokerCompanyDetailsDb($_broker_company_id);
      }
    }else{
      $Info = $this->GetBrokerCompanyDetailsDb($_broker_company_id);
    }

    return $Info;
  }

  /**
   * 设置经纪公司信息数据缓存
   * @param $_broker_company_id 经纪公司ID
   * @param array $_data 经纪公司信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetBrokerCompanyDetailsCache($_broker_company_id, $_data=NULL){
    F($this->GetBrokerCompanyDetailsCacheKey($_broker_company_id), $_data);
  }

  /**
   * 获取经纪公司信息数据缓存
   * @param $_broker_company_id 经纪公司ID
   * @return mixed
   */
  private function GetBrokerCompanyDetailsCache($_broker_company_id){
    return F($this->GetBrokerCompanyDetailsCacheKey($_broker_company_id));
  }

  /**
   * 获取经纪公司信息缓存Key
   * @param $_broker_company_id 经纪公司ID
   * @return mixed
   */
  private function GetBrokerCompanyDetailsCacheKey($_broker_company_id){
    return "BrokerCompany/Details/{$_broker_company_id}";
  }

  /**
   * todo:从数据库中获取经纪公司信息数据
   * @param $_broker_company_id 经纪公司ID
   *
   * @return mixed
   */
  private function GetBrokerCompanyDetailsDb($_broker_company_id){
    $BrokerCompanyDetails = array();

    if(!IsNum($_broker_company_id, false, false)){
      return $BrokerCompanyDetails;
    }

    $sWhere = "id={$_broker_company_id}";
    $BrokerCompanyDetails = $this->modBrokerCompany->where($sWhere)->find();

    if(!empty($BrokerCompanyDetails)){
      $this->SetBrokerCompanyDetailsCache($_broker_company_id, $BrokerCompanyDetails);
    }

    return $BrokerCompanyDetails;
  }
  //endregion 经纪公司信息缓存

  //region 经纪公司帐号信息缓存
  /**
   * 获取经纪公司帐号信息
   * @param $_broker_company_account_id 经纪公司帐号ID
   * @return mixed
   */
  public function GetBrokerCompanyAccountDetails($_broker_company_account_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetBrokerCompanyAccountDetailsCache($_broker_company_account_id);

      if(empty($Info)){
        $Info = $this->GetBrokerCompanyAccountDetailsDb($_broker_company_account_id);
      }
    }else{
      $Info = $this->GetBrokerCompanyAccountDetailsDb($_broker_company_account_id);
    }
    
    if(IsArray($Info)){
      $BrokerCompanyInfo = $this->GetBrokerCompanyDetails($Info["broker_company_id"]);
      $Info["broker_company_name"] = $BrokerCompanyInfo["broker_company_name"];
    }

    return $Info;
  }

  /**
   * 设置经纪公司帐号信息数据缓存
   * @param $_broker_company_account_id 经纪公司帐号ID
   * @param array $_data 经纪公司帐号信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetBrokerCompanyAccountDetailsCache($_broker_company_account_id, $_data=NULL){
    F($this->GetBrokerCompanyAccountDetailsCacheKey($_broker_company_account_id), $_data);
  }

  /**
   * 获取经纪公司帐号信息数据缓存
   * @param $_broker_company_account_id 经纪公司帐号ID
   * @return mixed
   */
  private function GetBrokerCompanyAccountDetailsCache($_broker_company_account_id){
    return F($this->GetBrokerCompanyAccountDetailsCacheKey($_broker_company_account_id));
  }

  /**
   * 获取经纪公司帐号信息缓存Key
   * @param $_broker_company_account_id 经纪公司帐号ID
   * @return mixed
   */
  private function GetBrokerCompanyAccountDetailsCacheKey($_broker_company_account_id){
    return "BrokerCompany/Account/{$_broker_company_account_id}";
  }

  /**
   * todo:从数据库中获取经纪公司帐号信息数据
   * @param $_broker_company_account_id 经纪公司帐号ID
   *
   * @return mixed
   */
  private function GetBrokerCompanyAccountDetailsDb($_broker_company_account_id){
    $BrokerCompanyAccountDetails = array();

    if(!IsNum($_broker_company_account_id, false, false)){
      return $BrokerCompanyAccountDetails;
    }

    $sWhere = "id={$_broker_company_account_id}";
    $BrokerCompanyAccountDetails = $this->modBrokerCompanyAccount->where($sWhere)->find();

    if(!empty($BrokerCompanyAccountDetails)){
      $this->SetBrokerCompanyAccountDetailsCache($_broker_company_account_id, $BrokerCompanyAccountDetails);
    }

    return $BrokerCompanyAccountDetails;
  }
  //endregion 经纪公司帐号信息缓存

  //region 经纪公司门店信息缓存
  /**
   * 获取经纪公司门店信息
   * @param $_broker_company_store_id 经纪公司门店ID
   * @return mixed
   */
  public function GetBrokerCompanyStoreDetails($_broker_company_store_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetBrokerCompanyStoreDetailsCache($_broker_company_store_id);

      if(empty($Info)){
        $Info = $this->GetBrokerCompanyStoreDetailsDb($_broker_company_store_id);
      }
    }else{
      $Info = $this->GetBrokerCompanyStoreDetailsDb($_broker_company_store_id);
    }

    return $Info;
  }

  /**
   * 设置经纪公司门店信息数据缓存
   * @param $_broker_company_store_id 经纪公司门店ID
   * @param array $_data 经纪公司门店信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetBrokerCompanyStoreDetailsCache($_broker_company_store_id, $_data=NULL){
    F($this->GetBrokerCompanyStoreDetailsCacheKey($_broker_company_store_id), $_data);
  }

  /**
   * 获取经纪公司门店信息数据缓存
   * @param $_broker_company_store_id 经纪公司门店ID
   * @return mixed
   */
  private function GetBrokerCompanyStoreDetailsCache($_broker_company_store_id){
    return F($this->GetBrokerCompanyStoreDetailsCacheKey($_broker_company_store_id));
  }

  /**
   * 获取经纪公司门店信息缓存Key
   * @param $_broker_company_store_id 经纪公司门店ID
   * @return mixed
   */
  private function GetBrokerCompanyStoreDetailsCacheKey($_broker_company_store_id){
    return "BrokerCompany/Store/{$_broker_company_store_id}";
  }

  /**
   * todo:从数据库中获取经纪公司门店信息数据
   * @param $_broker_company_store_id 经纪公司门店ID
   *
   * @return mixed
   */
  private function GetBrokerCompanyStoreDetailsDb($_broker_company_store_id){
    $BrokerCompanyStoreDetails = array();

    if(!IsNum($_broker_company_store_id, false, false)){
      return $BrokerCompanyStoreDetails;
    }

    $sWhere = "id={$_broker_company_store_id}";
    $BrokerCompanyStoreDetails = $this->modBrokerCompanyStore->where($sWhere)->find();

    if(!empty($BrokerCompanyStoreDetails)){
      $this->SetBrokerCompanyStoreDetailsCache($_broker_company_store_id, $BrokerCompanyStoreDetails);
    }

    return $BrokerCompanyStoreDetails;
  }
  //endregion 经纪公司门店信息缓存
  //region 缓存
  
  //region 经纪公司
  /**
   * todo: 经纪公司Option选项列表
   */
  public function BrokerCompanyOption(){
    $sField = "id";
    $sOrder = "id desc";

    $BrokerCompanyIdList = $this->modBrokerCompany->field($sField)->order($sOrder)->select();
    $BrokerCompanyOptionList = array();

    foreach($BrokerCompanyIdList as $key=>$val){
      $BrokerCompanyInfo = $this->GetBrokerCompanyDetails($val["id"]);

      $BrokerCompanyOptionInfo["key"] = $BrokerCompanyInfo["broker_company_name"];
      $BrokerCompanyOptionInfo["val"] = $BrokerCompanyInfo["id"];

      array_push($BrokerCompanyOptionList, $BrokerCompanyOptionInfo);
    }

    return $BrokerCompanyOptionList;
  }
  //endregion 经纪公司
  
  //region 门店
  /**
   * todo: 门店Option选项列表
   * @return array
   */
  public function BrokerCompanyStoreOption($_broker_company_id=0){
    $sField = "id";
    $sWhere = "1=1";
    $sOrder = "id desc";

    if(IsNum($_broker_company_id, false, false)){
      $sWhere = "broker_company_id={$_broker_company_id}";
    }
    
    $BrokerCompanyStoreIdList = $this->modBrokerCompanyStore->field($sField)->where($sWhere)->order($sOrder)->select();
    $BrokerCompanyStoreOptionList = array();

    foreach($BrokerCompanyStoreIdList as $key=>$val){
      $BrokerCompanyStoreInfo = $this->GetBrokerCompanyStoreDetails($val["id"]);

      $OptionInfo["key"] = $BrokerCompanyStoreInfo["store_name"];
      $OptionInfo["val"] = $BrokerCompanyStoreInfo["id"];

      array_push($BrokerCompanyStoreOptionList, $OptionInfo);
    }

    return $BrokerCompanyStoreOptionList;
  }
  //endregion 门店
}