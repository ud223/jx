<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

class SellerManagerModel extends Model {
  Protected $autoCheckFields = false;

  private $_SellerManagerLoginNameLength = 12;
  private $_SellerManagerRealNameLength = 10;

  private $modSellerManager = null;
  
  public $clsSellerManager = null;
  public $clsBrokerCompany = null;
  
  function __construct() {
    $this->modSellerManager = M("seller_manager");
    
    $this->clsSellerManager = new \Org\ZhiHui\SellerManager();
    $this->clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
  }
  
  //region 销售经理
  /**
   * todo:销售经理列表
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function SellerManagerList($_rownum=20, $_param=array()){
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

    $nTotal = $this->modSellerManager->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $SellerManagerIdList = $this->modSellerManager->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $AccountList = array();

    foreach($SellerManagerIdList as $key=>$val){
      $AccountInfo = $this->clsSellerManager->GetSellerManagerDetails($val["id"]);

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
   * todo: 销售经理信息
   * @param $_seller_manager_id 销售经理ID
   *
   * @return array
   */
  public function SellerManagerInfo($_seller_manager_id){
    $Result = array();

    if(!IsNum($_seller_manager_id, false, false)){
      $Result["seller_manager_info"] = array();
    }else{
      $Result["seller_manager_info"] = $this->clsSellerManager->GetSellerManagerDetails($_seller_manager_id);
    }

    $Result["broker_company_option"] = $this->clsBrokerCompany->BrokerCompanyOption();

    return $Result;
  }

  /**
   * todo: 保存销售经理
   * @param $_seller_manager_id 销售经理ID
   * @param $_broker_company_id 经纪公司ID
   * @param $_login_name 登录帐号名
   * @param $_login_password 登录密码
   * @param $_base_salary 基本薪资
   * @param $_province_id 省份ID
   * @param $_city_id 城市ID
   * @param $_real_name 真实姓名
   * @param $_mobile_phone 手机号吗
   * @param $_qq QQ号
   * @param $_wechat 微信号
   * @param $_desable 帐号状态
   *
   * @return array
   */
  public function SellerManagerSave($_seller_manager_id, $_broker_company_id, $_login_name, $_login_password, $_base_salary, $_province_id, $_city_id, $_real_name, $_mobile_phone, $_qq, $_wechat, $_desable){
    $SellerManagerInfo = array();

    //region 数据检查,并赋值保存的数据
    //region 检查销售经理信息
    if(IsNum($_seller_manager_id, false, false)){
      $SellerManagerInfo = $this->clsSellerManager->GetSellerManagerDetails($_seller_manager_id);

      if(!IsArray($SellerManagerInfo)){
        return ReturnError(L("_SELLER_MANAGER_NOT_EXIST_"));
      }

      $SaveBaseData["id"] = $_seller_manager_id;
    }
    //endregion 检查销售经理信息

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
    
    //如果是新建销售经理，则密码是必须的
    if(!IsArray($SellerManagerInfo)){
      //检查登陆密码
      if(IsN($_login_password)){
        return ReturnError(L("_SELLER_MANAGER_PASSWORD_NULL_"));
      }
    }

    //region 检查登陆名称
    if(IsN($_login_name)){
      return ReturnError(L("_SELLER_MANAGER_ACCOUNT_NAME_NULL_"));
    }else{
      $nStrLen = strLength($_login_name);

      if($nStrLen > $this->_SellerManagerLoginNameLength){
        $sError = sprintf(L("_SELLER_MANAGER_ACCOUNT_MAX_LENGTH_ERROR_"), $this->_SellerManagerLoginNameLength);
        return ReturnError($sError);
      }

      $sWhere = "login_name='{$_login_name}'";
      
      if(IsArray($SellerManagerInfo)){
        $sWhere .= " and id != '{$SellerManagerInfo["id"]}'";
      }
      
      $nSellerManagerNumber = $this->modSellerManager->where($sWhere)->count();

      if(IsNum($nSellerManagerNumber, false, false)){
        return ReturnError(L("_SELLER_MANAGER_ACCOUNT_NAME_EXIST_"));
      }

      $SaveBaseData["login_name"] = $_login_name;
    }
    //endregion 检查登陆名称
    
    if(!IsFloat($_base_salary, 2, false, false)){
      return ReturnError(L("_SELLER_MANAGER_BASE_SALARY_ERROR_"));
    }else{
      $SaveBaseData["base_salary"] = $_base_salary;
    }
    
    if(!IsNum($_province_id, false, false)){
      return ReturnError(L("_REGION_PROVINCE_ID_ERROR_"));
    }else{
      $SaveBaseData["province_id"] = $_province_id;
    }

    if(!IsNum($_city_id, false, false)){
      return ReturnError(L("_REGION_CITY_ID_ERROR_"));
    }else{
      $SaveBaseData["city_id"] = $_city_id;
    }
    
    if(!IsN($_login_password)){
      $NewPassword = PasswordMd5($_login_password);

      $SaveBaseData["password"] = $NewPassword["pwd"];
      $SaveBaseData["salt"] = $NewPassword["salt"];
    }
    
    //region 检查真实姓名
    if(IsN($_real_name)){
      return ReturnError(L("_SELLER_MANAGER_REAL_NAME_NULL_"));
    }else{
      $nStrLen = strLength($_real_name);

      if($nStrLen > $this->_SellerManagerRealNameLength){
        $sError = sprintf(L("_SELLER_MANAGER_REAL_NAME_MAX_LENGTH_ERROR_"), $this->_SellerManagerRealNameLength);
        return ReturnError($sError);
      }

      $SaveBaseData["real_name"] = $_real_name;
    }
    //endregion 检查真实姓名

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

    if(IsArray($SellerManagerInfo)){
      $SaveBaseResult = $this->modSellerManager->save($SaveBaseData);

      $nSellerManagerID = $SellerManagerInfo["id"];
    }else{
      $SaveBaseData["last_login"] = 0;
      $SaveBaseData["last_ip"] = "0.0.0.0";
      $SaveBaseData["add_time"] = $nTime;
      $SaveBaseData["update_time"] = $nTime;

      $SaveBaseResult = $this->modSellerManager->add($SaveBaseData);

      $nSellerManagerID = $SaveBaseResult;
    }

    if($SaveBaseResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    //endregion 添加基本数据

    $this->clsSellerManager->SetSellerManagerDetailsCache($nSellerManagerID);

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo: 删除销售经理
   *
   * @param $_id_str id字符串，逗号分割id
   *
   * @return array
   */
  public function SellerManagerDelete($_id_str){
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
    $Result = $this->modSellerManager->where($sWhere)->delete();

    if($Result === false){
      return ReturnError(L("_DELETE_DATA_FAILURE_"));
    }

    foreach($IdList as $key=>$val){
      $this->clsSellerManager->SetSellerManagerDetailsCache($val);
    }

    return ReturnCorrect(L("_DELETE_DATA_SUCCEED_"));
  }

  /**
   * todo: 刷新销售经理缓存
   */
  public function ResetSellerManagerCache(){
    $sField = "id";
    $SellerManagerIdList = $this->modSellerManager->field($sField)->select();

    foreach($SellerManagerIdList as $key=>$val){
      $this->clsSellerManager->SetSellerManagerDetailsCache($val["id"]);
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
  //endregion 销售经理
}
