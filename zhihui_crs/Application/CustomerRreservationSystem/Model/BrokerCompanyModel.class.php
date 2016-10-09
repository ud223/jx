<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

class BrokerCompanyModel extends Model {
  Protected $autoCheckFields = false;

  private $_BrokerCompanyLoginNameLength = 12;
  private $_BrokerCompanyRealNameLength = 10;

  private $modBrokerCompany = null;
  private $modBrokerCompanyAccount = null;
  private $modBrokerCompanyStore = null;
  
  public $clsBrokerCompany = null;
  private $clsRegion = null;
  
  function __construct() {
    $this->modBrokerCompany = M("broker_company");
    $this->modBrokerCompanyAccount = M("broker_company_account");
    $this->modBrokerCompanyStore = M("broker_company_store");
    
    $this->clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    $this->clsRegion = new \Org\ZhiHui\Region();
  }
  
  //region 经纪公司
  public function BrokerCompanyList($_rownum=20, $_param=array()){
    $sField = "id";
    $sWhere = "1=1";
    $sOrder = "id desc";

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "broker_company_name like '%{$_param["search_key"]}%'";
          
          if(IsNum($_param["search_key"], false, false)){
            $sWhere .= " or id = {$_param["search_key"]}";
          }

          $sWhere .= ")";
        }
      }else{
        switch($_param["search_case"]){
          case "id":
            if(IsNum($_param["search_key"], false, false)){
              $sWhere .= " and id = {$_param["search_key"]}";
            }
            break;

          case "broker_company_name":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and broker_company_name like '%{$_param["search_key"]}%'";
            }

            break;
        }
      }
    }

    $nTotal = $this->modBrokerCompany->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $BrokerCompanyIdList = $this->modBrokerCompany->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $BrokerCompanyList = array();

    foreach($BrokerCompanyIdList as $key=>$val){
      $BrokerCompanyInfo = $this->clsBrokerCompany->GetBrokerCompanyDetails($val["id"]);
      array_push($BrokerCompanyList, $BrokerCompanyInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$BrokerCompanyList);
  }

  /**
   * todo: 经纪公司信息
   *
   * @param $_broker_company_id 经济公司ID
   *
   * @return array|mixed
   */
  public function BrokerCompanyInfo($_broker_company_id){
    $BrokerCompanyInfo = array();
    
    if(IsNum($_broker_company_id, false, false)){
      $BrokerCompanyInfo = $this->clsBrokerCompany->GetBrokerCompanyDetails($_broker_company_id);
    }
    
    return $BrokerCompanyInfo;
  }

  /**
   * todo: 保存经纪公司信息
   *
   * @param $_broker_company_id 经济公司ID
   * @param $_broker_company_name 经济公司名称
   *
   * @return array|mixed
   */
  public function BrokerCompanySave($_broker_company_id, $_broker_company_name){
    if(IsN($_broker_company_name)){
      return ReturnError(L("BROKER_COMPANY_NAME_NULL_"));
    }else{
      $sWhere = "broker_company_name='{$_broker_company_name}'";
      $nCompanyNumber = $this->modBrokerCompany->where($sWhere)->count();
      
      if(IsNum($nCompanyNumber, false, false)){
        return ReturnError(L("BROKER_COMPANY_NAME_EXIST_"));
      }
    }
    
    $SaveData["broker_company_name"] = $_broker_company_name;
    
    if(IsNum($_broker_company_id, false, false)){
      $SaveData["id"] = $_broker_company_id;
      
      $Result = $this->modBrokerCompany->save($SaveData);
      
      if($Result !== false){
        $this->clsBrokerCompany->SetBrokerCompanyDetailsCache($_broker_company_id);
      }
    }else{
      $Result = $this->modBrokerCompany->add($SaveData);
    }
    
    if($Result === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    
    $this->clsBrokerCompany->GetBrokerCompanyDetails($_broker_company_id);
    
    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo:快速保存经纪公司信息
   */
  public function QuickBrokerCompanySave($_id, $_key, $_val){
    if(!IsNum($_id, false) || IsN($_key) || IsN($_val)){
      return ReturnError(L('_PARAM_ERROR_'));
    }

    $map["id"] = $_id;
    $map[$_key] = $_val;

    $Result = $this->modBrokerCompany->save($map);

    if($Result !== false){
      $this->clsBrokerCompany->SetBrokerCompanyDetailsCache($_id);
      return ReturnCorrect(L('_SAVE_DATA_SUCCEED_'));
    }else{
      return ReturnError(L('_SAVE_DATA_FAILURE_'));
    }
  }

  /**
   * todo: 删除经纪公司
   *
   * @param $_id_str id字符串，逗号分割id
   *
   * @return array
   */
  public function BrokerCompanyDelete($_id_str){
    if(IsN($_id_str)){
      return ReturnError(L("_ADMIN_ID_NULL_"));
    }else{
      $IdList = explode(",", $_id_str);
      
      foreach($IdList as $key=>$val){
        if(!IsNum($val, false, false)){
          return ReturnError(L("BROKER_COMPANY_ID_ERROR_"));
        }
      }
    }
    
    $sWhere = "id in ({$_id_str})";
    $Result = $this->modBrokerCompany->where($sWhere)->delete();
    
    if($Result === false){
      return ReturnError(L("_DELETE_DATA_FAILURE_"));
    }

    foreach($IdList as $key=>$val){
      $this->clsBrokerCompany->SetBrokerCompanyDetailsCache($val);
    }
    
    return ReturnCorrect(L("_DELETE_DATA_SUCCEED_"));
  }

  /**
   * todo:刷新经纪公司缓存
   */
  public function ResetBrokerCompanyCache(){
    $sField = "id";
    $BrokerCompanyIdList = $this->modBrokerCompany->field($sField)->select();

    foreach($BrokerCompanyIdList as $key=>$val){
      $this->clsBrokerCompany->SetBrokerCompanyDetailsCache($val["id"]);
    }

    return ReturnCorrect(L("_CACHE_REFRESH_SUCCEED_"));
  }
  //endregion 经纪公司
  
  //region 经纪公司帐号
  /**
   * todo:经纪公司帐号列表
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function BrokerCompanyAccountList($_rownum=20, $_param=array()){
    $sField = "id";
    $sWhere = "1=1";
    $sOrder = "disable asc, add_time desc";

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "login_name like '%{$_param["search_key"]}%'";
          $sWhere .= " or real_name like '%{$_param["search_key"]}%'";
          $sWhere .= " or phone like '%{$_param["search_key"]}%'";
          $sWhere .= " or qq like '%{$_param["search_key"]}%'";
          $sWhere .= " or wechat like '%{$_param["search_key"]}%'";

          if(IsNum($_param["search_key"], false, false)){
            $sWhere .= " or id = {$_param["search_key"]}";
          }

          $sWhere .= ")";
        }
      }else{
        switch($_param["search_case"]){
          case "id":
            if(IsNum($_param["search_key"], false, false)){
              $sWhere .= " and id = {$_param["search_key"]}";
            }
            break;

          case "login_name":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and login_name like '%{$_param["search_key"]}%'";
            }

            break;

          case "real_name":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and real_name like '%{$_param["search_key"]}%'";
            }
            break;

          case "phone":
            if(!IsN($_param["search_key"])){
              if(isMobile($_param["search_key"])){
                $sWhere .= " and phone like '%{$_param["search_key"]}%'";
              }
            }
            break;

          case "qq":
            if(IsNum($_param["search_key"], false, false)){
              $sWhere .= " and qq like '%{$_param["search_key"]}%'";
            }
            break;

          case "wechat":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and wechat like '%{$_param["search_key"]}%'";
            }
            break;
        }
      }

      if(IsNum($_param["search_status"], true, false)){
        if($_param["search_status"] == 0 || $_param["search_status"] == 1){
          $sWhere .= " and disable = {$_param["search_status"]}";
        }
      }

      if(IsNum($_param["search_broker_company"], false, false)){
        $sWhere .= " and broker_company_id = {$_param["search_broker_company"]}";
      }
    }

    $nTotal = $this->modBrokerCompanyAccount->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $BrokerCompanyAccountIdList = $this->modBrokerCompanyAccount->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $AccountList = array();

    foreach($BrokerCompanyAccountIdList as $key=>$val){
      $AccountInfo = $this->clsBrokerCompany->GetBrokerCompanyAccountDetails($val["id"]);

      $AccountInfo["disable_style"] = $this->FmtStatusStyle("disable", $AccountInfo["disable"]);

      $AccountInfo["broker_company_name"] = "--";
      if(IsNum($AccountInfo["broker_company_id"], false, false)){
        $BrokerCompanyInfo = $this->clsBrokerCompany->GetBrokerCompanyDetails($AccountInfo["broker_company_id"]);
        $AccountInfo["broker_company_name"] = $BrokerCompanyInfo["broker_company_name"];
      }

      array_push($AccountList, $AccountInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$AccountList);
  }

  /**
   * todo: 经纪公司帐号信息
   * @param $_broker_company_account_id 经纪公司帐号ID
   *
   * @return array
   */
  public function BrokerCompanyAccountInfo($_broker_company_account_id){
    $Result = array();

    if(!IsNum($_broker_company_account_id, false, false)){
      $Result["broker_company_account_info"] = array();
    }else{
      $Result["broker_company_account_info"] = $this->clsBrokerCompany->GetBrokerCompanyAccountDetails($_broker_company_account_id);
    }

    $Result["broker_company_option"] = $this->clsBrokerCompany->BrokerCompanyOption();

    return $Result;
  }

  /**
   * todo: 保存经纪公司帐号
   * @param $_broker_company_account_id 帐号ID
   * @param $_broker_company_id 经纪公司ID
   * @param $_login_name 登录名称
   * @param $_login_password 登录密码
   * @param $_real_name 真实姓名
   * @param $_mobile_phone 手机号
   * @param $_qq qq号
   * @param $_wechat 微信号
   * @param $_desable 状态
   *
   * @return array
   */
  public function BrokerCompanyAccountSave($_broker_company_account_id, $_broker_company_id, $_login_name, $_login_password, $_real_name, $_mobile_phone, $_qq, $_wechat, $_desable){
    $BrokerCompanyAccountInfo = array();

    //region 数据检查,并赋值保存的数据
    //检查经纪公司帐号信息
    if(IsNum($_broker_company_account_id, false, false)){
      $BrokerCompanyAccountInfo = $this->clsBrokerCompany->GetBrokerCompanyAccountDetails($_broker_company_account_id);

      if(!IsArray($BrokerCompanyAccountInfo)){
        return ReturnError(L("_BROKER_COMPANY_ACCOUNT_NOT_EXIST_"));
      }

      $SaveBaseData["id"] = $_broker_company_account_id;
    }

    //region 检查经纪公司
    if(!IsNum($_broker_company_id, false, false)){
      return ReturnError(L("_BROKER_COMPANY_NEED_SELECT_"));
    }else{
      $BrokerCompanyInfo = $this->clsBrokerCompany->GetBrokerCompanyDetails($_broker_company_id);

      if(!IsArray($BrokerCompanyInfo)){
        return ReturnError(L("_BROKER_COMPANY_NOT_EXIST_"));
      }

      $SaveBaseData["broker_company_id"] = $_broker_company_id;
    }
    //endregion 检查经纪公司


    //如果是新建经纪公司帐号，则密码是必须的
    if(!IsArray($BrokerCompanyAccountInfo)){
      //检查登陆密码
      if(IsN($_login_password)){
        return ReturnError(L("_BROKER_COMPANY_PASSWORD_NULL_"));
      }
    }

    //检查登陆名称
    if(IsN($_login_name)){
      return ReturnError(L("_BROKER_COMPANY_ACCOUNT_NAME_NULL_"));
    }else{
      $nStrLen = strLength($_login_name);

      if($nStrLen > $this->_BrokerCompanyLoginNameLength){
        $sError = sprintf(L("_BROKER_COMPANY_ACCOUNT_MAX_LENGTH_ERROR_"), $this->_BrokerCompanyLoginNameLength);
        return ReturnError($sError);
      }

      $sWhere = "login_name='{$_login_name}'";
      
      if(IsArray($BrokerCompanyAccountInfo)){
        $sWhere .= " and id != '{$BrokerCompanyAccountInfo["id"]}'";
      }
      
      $nBrokerCompanyAccountNumber = $this->modBrokerCompanyAccount->where($sWhere)->count();

      if(IsNum($nBrokerCompanyAccountNumber, false, false)){
        return ReturnError(L("_BROKER_COMPANY_ACCOUNT_NAME_EXIST_"));
      }

      $SaveBaseData["login_name"] = $_login_name;
    }

    if(!IsN($_login_password)){
      $NewPassword = PasswordMd5($_login_password);

      $SaveBaseData["password"] = $NewPassword["pwd"];
      $SaveBaseData["salt"] = $NewPassword["salt"];
    }

    //检查真实姓名
    if(IsN($_real_name)){
      return ReturnError(L("_BROKER_COMPANY_REAL_NAME_NULL_"));
    }else{
      $nStrLen = strLength($_real_name);

      if($nStrLen > $this->_BrokerCompanyRealNameLength){
        $sError = sprintf(L("_BROKER_COMPANY_REAL_NAME_MAX_LENGTH_ERROR_"), $this->_BrokerCompanyRealNameLength);
        return ReturnError($sError);
      }

      $SaveBaseData["real_name"] = $_real_name;
    }

    //检查手机号
    if(!IsN($_mobile_phone)){
      if(!isMobile($_mobile_phone)){
        return ReturnError(L("_MOBILE_PHONE_ERROR_"));
      }

      $SaveBaseData["phone"] = $_mobile_phone;
    }

    //检查QQ号
    if(!IsN($_qq)){
      if(!IsNum($_qq, false, false)){
        return ReturnError(L("_QQ_ERROR_"));
      }

      $SaveBaseData["qq"] = $_qq;
    }

    //检查禁用状态
    if($_desable != 0 && $_desable != 1){
      $SaveBaseData["disable"] = 0;
    }else{
      $SaveBaseData["disable"] = $_desable;
    }

    if(!IsN($_wechat)){
      $SaveBaseData["wechat"] = $_wechat;
    }
    //endregion 数据检查,并赋值保存的数据

    //region 添加基本数据
    $nTime = time();

    if(IsArray($BrokerCompanyAccountInfo)){
      $SaveBaseResult = $this->modBrokerCompanyAccount->save($SaveBaseData);

      $nBrokerCompanyAccountID = $BrokerCompanyAccountInfo["id"];
    }else{
      $SaveBaseData["last_login"] = 0;
      $SaveBaseData["last_ip"] = "0.0.0.0";
      $SaveBaseData["add_time"] = $nTime;
      $SaveBaseData["update_time"] = $nTime;

      $SaveBaseResult = $this->modBrokerCompanyAccount->add($SaveBaseData);

      $nBrokerCompanyAccountID = $SaveBaseResult;
    }

    if($SaveBaseResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    //endregion 添加基本数据

    $this->clsBrokerCompany->SetBrokerCompanyAccountDetailsCache($nBrokerCompanyAccountID);

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo: 删除经纪公司帐号
   *
   * @param $_id_str id字符串，逗号分割id
   *
   * @return array
   */
  public function BrokerCompanyAccountDelete($_id_str){
    if(IsN($_id_str)){
      return ReturnError(L("_BROKER_COMPANY_ACCOUNT_ID_NULL_"));
    }else{
      $IdList = explode(",", $_id_str);

      foreach($IdList as $key=>$val){
        if(!IsNum($val, false, false)){
          return ReturnError(L("_BROKER_COMPANY_ACCOUNT_ID_ERROR_"));
        }
      }
    }

    $sWhere = "id in ({$_id_str})";
    $Result = $this->modBrokerCompanyAccount->where($sWhere)->delete();

    if($Result === false){
      return ReturnError(L("_DELETE_DATA_FAILURE_"));
    }

    foreach($IdList as $key=>$val){
      $this->clsBrokerCompany->SetBrokerCompanyAccountDetailsCache($val);
    }

    return ReturnCorrect(L("_DELETE_DATA_SUCCEED_"));
  }

  /**
   * todo: 刷新经纪公司帐号缓存
   */
  public function ResetBrokerCompanyAccountCache(){
    $sField = "id";
    $BrokerCompanyAccountIdList = $this->modBrokerCompanyAccount->field($sField)->select();

    foreach($BrokerCompanyAccountIdList as $key=>$val){
      $this->clsBrokerCompany->SetBrokerCompanyAccountDetailsCache($val["id"]);
    }

    return ReturnCorrect(L("_CACHE_REFRESH_SUCCEED_"));
  }

  /**
   * 格式化状态样式
   * @return array
   */
  private function FmtStatusStyle($_field, $_val){
    if($_field == 'disable'){
      switch($_val){
        case "0" :
          return FmtValueStyle(3, "启用");
          break;

        case "1" :
          return FmtValueStyle(5, "禁用");
          break;
      }
    }
  }
  //endregion 经纪公司帐号

  //region 经纪公司门店
  /**
   * todo:经纪公司门店列表
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function BrokerCompanyStoreList($_rownum=20, $_param=array()){
    $sField = "id";
    $sWhere = "1=1";
    $sOrder = "add_time desc";

    if(!empty($_param)){
      if(IsNum($_param["search_province"], false, false)){
        $sWhere .= " and province_id = {$_param["search_province"]}";
      }

      if(IsNum($_param["search_city"], false, false)){
        $sWhere .= " and city_id = {$_param["search_city"]}";
      }

      if(IsNum($_param["search_district"], false, false)){
        $sWhere .= " and district_id = {$_param["search_district"]}";
      }

      if(IsNum($_param["search_broker_company"], false, false)){
        $sWhere .= " and broker_company_id = {$_param["search_broker_company"]}";
      }
    }

    $nTotal = $this->modBrokerCompanyStore->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $BrokerCompanyStoreIdList = $this->modBrokerCompanyStore->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $StoreList = array();

    foreach($BrokerCompanyStoreIdList as $key=>$val){
      $StoreInfo = $this->clsBrokerCompany->GetBrokerCompanyStoreDetails($val["id"]);

      $StoreInfo["broker_company_name"] = "--";
      if(IsNum($StoreInfo["broker_company_id"], false, false)){
        $BrokerCompanyInfo = $this->clsBrokerCompany->GetBrokerCompanyDetails($StoreInfo["broker_company_id"]);
        $StoreInfo["broker_company_name"] = $BrokerCompanyInfo["broker_company_name"];
      }

      $StoreInfo["province_name"] = "";
      if(IsNum($StoreInfo["province_id"], false, false)){
        $ProvinceInfo = $this->clsRegion->GetProvinceDetails($StoreInfo["province_id"]);
        $StoreInfo["province_name"] = $ProvinceInfo["province_name"];
      }

      $StoreInfo["city_name"] = "";
      if(IsNum($StoreInfo["city_id"], false, false)){
        $CityInfo = $this->clsRegion->GetCityDetails($StoreInfo["city_id"]);
        $StoreInfo["city_name"] = $CityInfo["city_name"];
      }

      $StoreInfo["district_name"] = "";
      if(IsNum($StoreInfo["district_id"], false, false)){
        $DistrictInfo = $this->clsRegion->GetDistrictDetails($StoreInfo["district_id"]);
        $StoreInfo["district_name"] = $DistrictInfo["district_name"];
      }

      array_push($StoreList, $StoreInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$StoreList);
  }

  /**
   * todo: 经纪公司门店信息
   * @param $_broker_company_store_id 经纪公司门店ID
   *
   * @return array
   */
  public function BrokerCompanyStoreInfo($_broker_company_store_id){
    $Result = array();

    if(!IsNum($_broker_company_store_id, false, false)){
      $Result["broker_company_store_info"] = array();
    }else{
      $Result["broker_company_store_info"] = $this->clsBrokerCompany->GetBrokerCompanyStoreDetails($_broker_company_store_id);
    }

    $Result["broker_company_option"] = $this->clsBrokerCompany->BrokerCompanyOption();

    return $Result;
  }

  /**
   * todo: 保存经纪公司门店
   * @param $_broker_company_account_id 帐号ID
   * @param $_broker_company_id 经纪公司ID
   * @param $_login_name 登录名称
   * @param $_login_password 登录密码
   * @param $_real_name 真实姓名
   * @param $_mobile_phone 手机号
   * @param $_qq qq号
   * @param $_wechat 微信号
   * @param $_desable 状态
   *
   * @return array
   */
  public function BrokerCompanyStoreSave($_broker_company_store_id, $_broker_company_id, $_store_name, $_phone, $_province_id, $_city_id, $_district_id, $_address){
    $BrokerCompanyStoreInfo = array();

    //region 数据检查,并赋值保存的数据
    //region 检查门店信息
    if(IsNum($_broker_company_store_id, false, false)){
      $BrokerCompanyStoreInfo = $this->clsBrokerCompany->GetBrokerCompanyStoreDetails($_broker_company_store_id);

      if(!IsArray($BrokerCompanyStoreInfo)){
        return ReturnError(L("_BROKER_COMPANY_STORE_NOT_EXIST_"));
      }

      $SaveBaseData["id"] = $_broker_company_store_id;
    }
    //endregion 检查门店信息

    //region 检查经纪公司
    if(!IsNum($_broker_company_id, false, false)){
      return ReturnError(L("_BROKER_COMPANY_NEED_SELECT_"));
    }else{
      $BrokerCompanyInfo = $this->clsBrokerCompany->GetBrokerCompanyDetails($_broker_company_id);

      if(!IsArray($BrokerCompanyInfo)){
        return ReturnError(L("_BROKER_COMPANY_NOT_EXIST_"));
      }

      $SaveBaseData["broker_company_id"] = $_broker_company_id;
    }
    //endregion 检查经纪公司
    
    //region 检查门店名称
    if(IsN($_store_name)){
      return ReturnError(L("_BROKER_COMPANY_STORE_NAME_NULL_"));
    }else{
      $sWhere = "store_name='{$_store_name}'";

      if(IsArray($BrokerCompanyStoreInfo)){
        $sWhere .= " and id != '{$BrokerCompanyStoreInfo["id"]}'";
      }

      $nBrokerCompanyStoreNumber = $this->modBrokerCompanyStore->where($sWhere)->count();

      if(IsNum($nBrokerCompanyStoreNumber, false, false)){
        return ReturnError(L("_BROKER_COMPANY_STORE_NAME_EXIST_"));
      }

      $SaveBaseData["store_name"] = $_store_name;
    }
    //endregion 检查门店名称

    //检查联系电话
    if(!IsN($_phone)){
      $SaveBaseData["phone"] = $_phone;
    }

    //region 检查地址
    if(!IsNum($_province_id, false, false)){
      $_province_id = 0;
    }

    if(!IsNum($_city_id, false, false)){
      $_city_id = 0;
    }

    if(!IsNum($_district_id, false, false)){
      $_district_id = 0;
    }

    if(IsN($_address, false, false)){
      $_address = "";
    }

    $SaveBaseData["province_id"] = $_province_id;
    $SaveBaseData["city_id"] = $_city_id;
    $SaveBaseData["district_id"] = $_district_id;
    $SaveBaseData["address"] = $_address;
    //endregion 检查地址

    //endregion 数据检查,并赋值保存的数据

    //region 添加基本数据
    $nTime = time();

    if(IsArray($BrokerCompanyStoreInfo)){
      $SaveBaseData["update_time"] = $nTime;
      
      $SaveBaseResult = $this->modBrokerCompanyStore->save($SaveBaseData);

      $nBrokerCompanyStoreID = $BrokerCompanyStoreInfo["id"];
    }else{
      $SaveBaseData["add_time"] = $nTime;
      $SaveBaseData["update_time"] = $nTime;

      $SaveBaseResult = $this->modBrokerCompanyStore->add($SaveBaseData);
      $nBrokerCompanyStoreID = $SaveBaseResult;
    }

    if($SaveBaseResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    //endregion 添加基本数据

    $this->clsBrokerCompany->SetBrokerCompanyStoreDetailsCache($nBrokerCompanyStoreID);

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo: 删除经纪公司门店
   *
   * @param $_id_str id字符串，逗号分割id
   *
   * @return array
   */
  public function BrokerCompanyStoreDelete($_id_str){
    if(IsN($_id_str)){
      return ReturnError(L("_BROKER_COMPANY_STORE_NEED_SELECT_"));
    }else{
      $IdList = explode(",", $_id_str);

      foreach($IdList as $key=>$val){
        if(!IsNum($val, false, false)){
          return ReturnError(L("_BROKER_COMPANY_STORE_ID_ERROR_"));
        }
      }
    }

    $sWhere = "id in ({$_id_str})";
    $Result = $this->modBrokerCompanyStore->where($sWhere)->delete();

    if($Result === false){
      return ReturnError(L("_DELETE_DATA_FAILURE_"));
    }

    foreach($IdList as $key=>$val){
      $this->clsBrokerCompany->SetBrokerCompanyStoreDetailsCache($val);
    }

    return ReturnCorrect(L("_DELETE_DATA_SUCCEED_"));
  }

  /**
   * todo: 刷新门店缓存
   */
  public function ResetBrokerCompanyStoreCache(){
    $sField = "id";
    $BrokerCompanyStoreIdList = $this->modBrokerCompanyStore->field($sField)->select();

    foreach($BrokerCompanyStoreIdList as $key=>$val){
      $this->clsBrokerCompany->SetBrokerCompanyStoreDetailsCache($val["id"]);
    }

    return ReturnCorrect(L("_CACHE_REFRESH_SUCCEED_"));
  }
  //endregion 经纪公司门店
}
