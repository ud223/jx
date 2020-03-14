<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

class UserModel extends Model {
  Protected $autoCheckFields = false;

  private $_UserLoginNameLength = 12;
  private $_UserRealNameLength = 20;

  private $modUser = null;
  
  public $clsUser = null;

  function __construct() {
    $this->modUser = M("users");
    
    $this->clsUser = new \Org\ZhiHui\User();
  }
  
  /**
   * todo: 用户列表
   *
   * @param       $_user_type
   * @param int   $_user_id
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function UserList($_user_type, $_user_id=0, $_rownum=20, $_param=array()){
    $sField = "id";
    $sOrder = "disable asc, add_time desc";

    switch($_user_type){
      case $this->clsUser->_UserType["captain"]:
        $UserGroupInfo = $this->clsUser->SellerCaptainUserGroup();

        $sWhere = "group_id={$UserGroupInfo["group_id"]}";
        break;

      case $this->clsUser->_UserType["member"]:
        $UserGroupInfo = $this->clsUser->SellereMemberUserGroup();

        $sWhere = "group_id={$UserGroupInfo["group_id"]}";

        if(IsNum($_user_id, false, false)){
          $sWhere .= " and parent_user_id={$_user_id}";
        }

        break;

      default:
        $sWhere = "group_id=-1";
        break;
    }

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

      if(IsNum($_param["search_captain"], false, false)){
        $sWhere .= " and captain_id = {$_param["search_captain"]}";
      }
    }

    $nTotal = $this->modUser->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $SellerCaptainIdList = $this->modUser->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $AccountList = array();

    foreach($SellerCaptainIdList as $key=>$val){
      $AccountInfo = $this->clsUser->GetUserDetails($val["id"]);

      $AccountInfo["disable_style"] = $this->FmtStatusStyle("disable", $AccountInfo["disable"]);

//      $AccountInfo["broker_company_name"] = "--";
//      if(IsNum($AccountInfo["broker_company_id"], false, false)){
//        $BrokerCompanyInfo = $this->clsBrokerCompany->GetBrokerCompanyDetails($AccountInfo["broker_company_id"]);
//        $AccountInfo["broker_company_name"] = $BrokerCompanyInfo["broker_company_name"];
//      }

      array_push($AccountList, $AccountInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$AccountList);
  }

  /**
   * todo: 团队长信息
   * @param $_seller_captain_id 团队长ID
   *
   * @return array
   */
  public function UserInfo($_seller_captain_id){
    $Result = array();

    if(IsNum($_seller_captain_id, false, false)){
      $Result = $this->clsUser->GetUserDetails($_seller_captain_id);
    }

    return $Result;
  }

  /**
   * todo: 保存用户信息
   *
   * @param $_user_type      用户类型
   * @param $_user_id        用户ID
   * @param $_parent_id      父级用户ID
   * @param $_login_name     登录帐号名
   * @param $_login_password 登录密码
   * @param $_base_salary    基本薪资
   * @param $_province_id    省份ID
   * @param $_city_id        城市ID
   * @param $_real_name      真实姓名
   * @param $_mobile_phone   手机号吗
   * @param $_qq             QQ号
   * @param $_wechat         微信号
   * @param $_desable        帐号状态
   *
   * @return array
   */
  public function UserSave($_user_type, $_user_id, $_parent_id, $_login_name, $_login_password, $_base_salary, $_province_id, $_city_id, $_real_name, $_mobile_phone, $_qq, $_wechat, $_desable){
    $UserInfo = array();

    //region 数据检查,并赋值保存的数据

    //region 检查用户信息
    if(IsNum($_user_id, false, false)){
      $UserInfo = $this->clsUser->GetUserDetails($_user_id);

      if(!IsArray($UserInfo)){
        return ReturnError(L("_USER_NOT_EXIST_"));
      }

      $SaveBaseData["id"] = $_user_id;
    }
    //endregion 检查用户信息

    switch($_user_type){
      case $this->clsUser->_UserType["captain"]:
        $UserGroupInfo = $this->clsUser->SellerCaptainUserGroup();
        $SaveBaseData["group_id"] = $UserGroupInfo["group_id"];
        $SaveBaseData["parent_user_id"] = $this->clsUser->ConstBrokerCompanyUserID();

        if(!IsFloat($_base_salary, 2, true, false)){
          return ReturnError(L("_USER_BASE_SALARY_ERROR_"));
        }else{
          $SaveBaseData["base_salary"] = $_base_salary;
        }
        break;

      case $this->clsUser->_UserType["member"]:
        if(!IsNum($_parent_id, false, false)){
          return ReturnError(L("_USER_PARENT_ID_ERROR_"));
        }

        $ParentUserInfo = $this->clsUser->GetUserDetails($_parent_id);
        $UserGroupInfo = $this->clsUser->SellereMemberUserGroup();

        $SaveBaseData["group_id"] = $UserGroupInfo["group_id"];
        $SaveBaseData["parent_user_id"] = $ParentUserInfo["id"];

        if(!IsFloat($_base_salary, 2, true, false)){
          return ReturnError(L("_USER_BASE_SALARY_ERROR_"));
        }else{
          $SaveBaseData["base_salary"] = $_base_salary;
        }
        break;

      case $this->clsUser->_UserType["special"]:
        $UserGroupInfo = $this->clsUser->SpecialUserGroup();
        $SaveBaseData["group_id"] = $UserGroupInfo["group_id"];
        $SaveBaseData["parent_user_id"] = 0;
        $SaveBaseData["base_salary"] = 0;
        break;

      case $this->clsUser->_UserType["broker_company"]:
        $UserGroupInfo = $this->clsUser->BrokerCompanyUserGroup();
        $SaveBaseData["group_id"] = $UserGroupInfo["group_id"];
        $SaveBaseData["parent_user_id"] = 0;
        $SaveBaseData["base_salary"] = 0;
        break;

      default:
        return ReturnError(L("_USER_NOT_EXIST_"));
        break;
    }
    
    //如果是新建团队长，则密码是必须的
    if(!IsArray($UserInfo)){
      //检查登陆密码
      if(IsN($_login_password)){
        return ReturnError(L("_USER_PASSWORD_NULL_"));
      }
    }

    //region 检查登陆名称
    if(IsN($_login_name)){
      return ReturnError(L("_USER_ACCOUNT_NAME_NULL_"));
    }else{
      $nStrLen = strLength($_login_name);

      if($nStrLen > $this->_UserLoginNameLength){
        $sError = sprintf(L("_USER_ACCOUNT_MAX_LENGTH_ERROR_"), $this->_UserLoginNameLength);
        return ReturnError($sError);
      }

      $sWhere = "login_name='{$_login_name}'";
      
      if(IsArray($UserInfo)){
        $sWhere .= " and id != '{$UserInfo["id"]}'";
      }
      
      $nUserNumber = $this->modUser->where($sWhere)->count();

      if(IsNum($nUserNumber, false, false)){
        return ReturnError(L("_USER_ACCOUNT_NAME_EXIST_"));
      }

      $SaveBaseData["login_name"] = $_login_name;
    }
    //endregion 检查登陆名称
    
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
      return ReturnError(L("_USER_REAL_NAME_NULL_"));
    }else{
      $nStrLen = strLength($_real_name);

      if($nStrLen > $this->_UserRealNameLength){
        $sError = sprintf(L("_USER_REAL_NAME_MAX_LENGTH_ERROR_"), $this->_UserRealNameLength);
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

    if(IsArray($UserInfo)){
      $SaveBaseResult = $this->modUser->save($SaveBaseData);

      $nUserID = $UserInfo["id"];
    }else{
      $SaveBaseData["last_login"] = 0;
      $SaveBaseData["last_ip"] = "0.0.0.0";
      $SaveBaseData["add_time"] = $nTime;
      $SaveBaseData["update_time"] = $nTime;

      $SaveBaseResult = $this->modUser->add($SaveBaseData);

      $nUserID = $SaveBaseResult;
    }

    if($SaveBaseResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    //endregion 添加基本数据

    $this->clsUser->SetUserDetailsCache($nUserID);

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo: 删除用户
   *
   * @param $_id_str id字符串，逗号分割id
   *
   * @return array
   */
  public function UserDelete($_id_str){
    if(IsN($_id_str)){
      return ReturnError(L("_USER_ID_NULL_"));
    }else{
      $IdList = explode(",", $_id_str);

      foreach($IdList as $key=>$val){
        if(!IsNum($val, false, false)){
          return ReturnError(L("_USER_ID_ERROR_"));
        }
      }
    }

    $sWhere = "id in ({$_id_str})";
    $Result = $this->modUser->where($sWhere)->delete();

    if($Result === false){
      return ReturnError(L("_DELETE_DATA_FAILURE_"));
    }

    foreach($IdList as $key=>$val){
      $this->clsUser->SetUserDetailsCache($val);
    }

    return ReturnCorrect(L("_DELETE_DATA_SUCCEED_"));
  }

  /**
   * todo: 刷新团队长缓存
   *
   * @param $_user_type
   * @param $_user_id
   *
   * @return array
   */
  public function ResetUserCache($_user_type, $_user_id){
    $sField = "id";

    switch($_user_type){
      case $this->clsUser->_UserType["captain"]:
        $UserGroupInfo = $this->clsUser->SellerCaptainUserGroup();

        $sWhere = "group_id={$UserGroupInfo["group_id"]}";
        
        if(IsNum($_user_id, false, false)){
          $sWhere = "parent_user_id={$_user_id}";
        }
        
        break;

      default:
        $sWhere = "1=1";
        break;
    }

    $UserIdList = $this->modUser->field($sField)->where($sWhere)->select();

    foreach($UserIdList as $key=>$val){
      $this->clsUser->SetUserDetailsCache($val["id"]);
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
}
