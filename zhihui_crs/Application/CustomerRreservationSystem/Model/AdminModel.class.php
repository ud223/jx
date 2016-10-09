<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

class AdminModel extends Model {
//  Protected $autoCheckFields = false;

  private $_AdminLoginNameLength = 12;
  private $_AdminRealNameLength = 10;
  private $_AdminGroupNameLength = 20;
  
  private $modAdminUser = null;
  private $modAdminUserGroup = null;
  
  public $clsAdmin = null;
  
  function __construct() {
    $this->modAdminUser = M("admin_user");
    $this->modAdminUserGroup = M("admin_user_group");
    
    $this->clsAdmin = new \Org\ZhiHui\Admin();
  }

  //region 管理员

  /**
   * todo:管理员列表
   *
   * @param int   $_rownum 页记录数
   * @param array $_param  查询参数
   *
   * @return array
   */
  public function AdminUserList($_rownum=20, $_param=array()){
    $sField = "admin_id";
    $sWhere = "1=1";
    $sOrder = "disable asc, add_time desc";

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "login_name like '%{$_param["search_key"]}%'";
          $sWhere .= " or real_name like '%{$_param["search_key"]}%'";
          $sWhere .= " or email like '%{$_param["search_key"]}%'";
          $sWhere .= " or phone like '%{$_param["search_key"]}%'";
          $sWhere .= " or qq like '%{$_param["search_key"]}%'";
          $sWhere .= " or wechat like '%{$_param["search_key"]}%'";

          if(IsNum($_param["search_key"], false, false)){
            $sWhere .= " or admin_id = {$_param["search_key"]}";
          }

          $sWhere .= ")";
        }
      }else{
        switch($_param["search_case"]){
          case "admin_id":
            if(IsNum($_param["search_key"], false, false)){
              $sWhere .= " and admin_id = {$_param["search_key"]}";
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

          case "email":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and email like '%{$_param["search_key"]}%'";
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

      if(IsNum($_param["search_group"], false, false)){
        $sWhere .= " and group_id = {$_param["search_group"]}";
      }

      if(IsNum($_param["search_broker_company"], false, false)){
        $sWhere .= " and broker_company_id = {$_param["search_broker_company"]}";
      }
    }

    $nTotal = $this->modAdminUser->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $AdminUserIdList = $this->modAdminUser->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $AdminUserList = array();

    foreach($AdminUserIdList as $key=>$val){
      $AdminUserInfo = $this->clsAdmin->GetAdminDetails($val["admin_id"]);

      $AdminUserInfo["disable_style"] = $this->FmtStatusStyle("disable", $AdminUserInfo["disable"]);

      $AdminUserInfo["group_name"] = "--";
      if(IsNum($AdminUserInfo["group_id"], false, false)){
        $AdminUserGroupInfo = $this->clsAdmin->GetAdminGroupDetails($AdminUserInfo["group_id"]);
        $AdminUserInfo["group_name"] = $AdminUserGroupInfo["group_name"];
      }

      array_push($AdminUserList, $AdminUserInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$AdminUserList);
  }

  /**
   * todo: 管理员信息
   * @param $_admin_id
   *
   * @return array|mixed
   */
  public function AdminUserInfo($_admin_id){
    $Result = array();

    if(!IsNum($_admin_id, false, false)){
      $Result["admin_user_info"] = array();
    }else{
      $Result["admin_user_info"] = $this->clsAdmin->GetAdminDetails($_admin_id);
    }
    
    $Result["admin_group_option"] = $this->AdminUserGroupOption();

    return $Result;
  }

  /**
   * todo: 保存管理员数据
   *
   * @param $_admin_user_id  管理员ID
   * @param $_login_name     登陆账号
   * @param $_login_password 登陆密码
   * @param $_real_name      真实姓名
   * @param $_email          电子邮件
   * @param $_mobile_phone   联系电话
   * @param $_qq             QQ号
   * @param $_wechat         微信
   * @param $_desable        禁用状态
   *
   * @return array
   */
  public function AdminUserSave($_admin_user_id, $_group_id, $_login_name, $_login_password, $_real_name, $_email, $_mobile_phone, $_qq, $_wechat, $_desable){
    $AdminUserInfo = array();

    //region 数据检查,并赋值保存的数据
    //检查管理员信息
    if(IsNum($_admin_user_id, false, false)){
      $AdminUserInfo = $this->clsAdmin->GetAdminDetails($_admin_user_id);

      if(!IsArray($AdminUserInfo)){
        return ReturnError(L("_ADMIN_NOT_EXIST_"));
      }

      $SaveBaseData["admin_id"] = $_admin_user_id;
    }

    //region 检查管理员组
    if(!IsNum($_group_id, false, false)){
      return ReturnError(L("_ADMIN_GROUP_ID_ERROR_"));
    }else{
      $AdminUserGroupInfo = $this->clsAdmin->GetAdminGroupDetails($_group_id);

      if(!IsArray($AdminUserGroupInfo)){
        return ReturnError(L("_ADMIN_GROUP_DATA_NOT_EXIST_"));
      }

      $SaveBaseData["group_id"] = $_group_id;
    }
    //endregion 检查管理员组
    

    //如果是新建管理员，则密码是必须的
    if(!IsArray($AdminUserInfo)){
      //检查登陆名称
      if(IsN($_login_name)){
        return ReturnError(L("_ADMIN_ACCOUNT_NULL_"));
      }else{
        $nStrLen = strLength($_login_name);

        if($nStrLen > $this->_AdminLoginNameLength){
          $sError = sprintf(L("_ADMIN_ACCOUNT_MAX_LENGTH_ERROR_"), $this->_AdminLoginNameLength);
          return ReturnError($sError);
        }

        $sWhere = "login_name='{$_login_name}'";
        $nAdminNumber = $this->modAdminUser->where($sWhere)->count();

        if(IsNum($nAdminNumber, false, false)){
          return ReturnError(L("_ADMIN_ACCOUNT_EXIST_"));
        }

        $SaveBaseData["login_name"] = $_login_name;
      }

      //检查登陆密码
      if(IsN($_login_password)){
        return ReturnError(L("_ADMIN_PASSWORD_NULL_"));
      }
    }

    if(!IsN($_login_password)){
      $NewPassword = PasswordMd5($_login_password);

      $SaveBaseData["password"] = $NewPassword["pwd"];
      $SaveBaseData["salt"] = $NewPassword["salt"];
    }

    //检查真实姓名
    if(!IsN($_real_name)){
      $nStrLen = strLength($_real_name);

      if($nStrLen > $this->_AdminRealNameLength){
        $sError = sprintf(L("_ADMIN_REAL_NAME_MAX_LENGTH_ERROR_"), $this->_AdminRealNameLength);
        return ReturnError($sError);
      }

      $SaveBaseData["real_name"] = $_real_name;
    }

    //检查邮件地址
    if(!IsN($_email)){
      if(!isEMail($_email)){
        return ReturnError(L("_EMIAL_ERROR_"));
      }

      $SaveBaseData["email"] = $_email;
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

    if(IsArray($AdminUserInfo)){
      $SaveBaseResult = $this->modAdminUser->save($SaveBaseData);

      $nAdminUserID = $AdminUserInfo["admin_id"];
    }else{
      $SaveBaseData["headpic"] = 0;
      $SaveBaseData["agency_id"] = 0;
      $SaveBaseData["suppliers_id"] = 0;
      $SaveBaseData["last_login"] = 0;
      $SaveBaseData["last_ip"] = "0.0.0.0";
      $SaveBaseData["add_time"] = $nTime;

      $SaveBaseResult = $this->modAdminUser->add($SaveBaseData);

      $nAdminUserID = $SaveBaseResult;
    }

    if($SaveBaseResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    //endregion 添加基本数据

    $this->clsAdmin->SetAdminDetailsCache($nAdminUserID);

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo: 删除管理员
   *
   * @param $_id_str id字符串，逗号分割id
   *
   * @return array
   */
  public function AdminUserDelete($_id_str){
    if(IsN($_id_str)){
      return ReturnError(L("_ADMIN_ID_NULL_"));
    }else{
      $IdList = explode(",", $_id_str);

      foreach($IdList as $key=>$val){
        if(!IsNum($val, false, false)){
          return ReturnError(L("_ADMIN_ID_ERROR_"));
        }
      }
    }

    $sWhere = "admin_id in ({$_id_str})";
    $Result = $this->modAdminUser->where($sWhere)->delete();

    if($Result === false){
      return ReturnError(L("_DELETE_DATA_FAILURE_"));
    }

    foreach($IdList as $key=>$val){
      $this->clsAdmin->SetAdminDetailsCache($val);
    }

    return ReturnCorrect(L("_DELETE_DATA_SUCCEED_"));
  }

  /**
   * todo: 刷新管理员缓存
   */
  public function ResetAdminUserCache(){
    $sField = "admin_id";
    $AdminUserIdList = $this->modAdminUser->field($sField)->select();

    foreach($AdminUserIdList as $key=>$val){
      $this->clsAdmin->SetAdminDetailsCache($val["admin_id"]);
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

  /**
   * todo:注销
   * @return array
   */
  public function DoLogout(){
//    $nAdminID = $this->clsAdmin->GetAdminIDCookeis();
//
//    $this->clsAdmin->SetAdminStatusCache($nAdminID);
//    $this->clsAdmin->ClearAdminIDCookies($nAdminID);
//    $this->clsAdmin->SetAdminDetailsCache($nAdminID);

    return ReturnCorrect();
  }
  //endregion 管理员
  
  //region 管理员组
  /**
   * todo:管理员组列表
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function AdminGroupList($_rownum=20, $_param=array()){
    $sField = "id";

    $nTotal = $this->modAdminUserGroup->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $AdminGroupIdList = $this->modAdminUserGroup->field($sField)->limit($Page->firstRow.','.$Page->listRows)->select();
    $AdminGroupList = array();

    foreach($AdminGroupIdList as $key=>$val){
      $GroupInfo = $this->clsAdmin->GetAdminGroupDetails($val["id"]);

      array_push($AdminGroupList, $GroupInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$AdminGroupList);
  }

  /**
   * todo:保存管理员组信息
   *
   * @param $_group_id
   * @param $_group_name
   *
   * @return array
   */
  public function AdminGroupSave($_group_id, $_group_name){
    $AdminGroupInfo = array();

    if(IsNum($_group_id, false, false)){
      $AdminGroupInfo = $this->clsAdmin->GetAdminGroupDetails();
    }

    if(IsN($_group_name)){
      return ReturnError(L("_ADMIN_GROUP_NAME_NULL_"));
    }else{
      $nStrLen = strLength($_group_name);

      if($nStrLen > $this->_AdminGroupNameLength){
        $sError = sprintf(L("_ADMIN_GROUP_NAME_MAX_LENGTH_ERROR_"), $this->_AdminGroupNameLength);
        return ReturnError($sError);
      }
    }

    if(IsArray($AdminGroupInfo)){
      //修改
      if($AdminGroupInfo["group_name"] == $_group_name){
        return ReturnError(L("_ADMIN_GROUP_NAME_EQ_OLD_NAME_").",无需保存数据。");
      }else{
        $CheckResult = $this->CheckAdminGroupNameExist($_group_name);

        if($CheckResult === true){
          return ReturnError(L("_ADMIN_GROUP_NAME_EXIST_"));
        }
      }

      $nAdminGroupID = $_group_id;

      $SaveData["id"] = $_group_id;
      $SaveData["group_name"] = $_group_name;

      $Result = $this->modAdminUserGroup->save($SaveData);
    }else{
      //添加
      $CheckResult = $this->CheckAdminGroupNameExist($_group_name);

      if($CheckResult === true){
        return ReturnError(L("_ADMIN_GROUP_NAME_EXIST_"));
      }

      $SaveData["group_name"] = $_group_name;

      $Result = $this->modAdminUserGroup->add($SaveData);

      $nAdminGroupID = $Result;
    }

    if($Result === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }

    $this->clsAdmin->SetAdminGroupDetailsCache($nAdminGroupID);
    
    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo:快速保存管理员组信息
   */
  public function QuickAdminGroupSave($_id, $_key, $_val){
    if(!IsNum($_id, false) || IsN($_key) || IsN($_val)){
      return ReturnError(L('_PARAM_ERROR_'));
    }

    $map["id"] = $_id;
    $map[$_key] = $_val;

    $Result = $this->modAdminUserGroup->save($map);

    if($Result !== false){
      $this->clsAdmin->SetAdminGroupDetailsCache($_id);
      return ReturnCorrect(L('_SAVE_DATA_SUCCEED_'));
    }else{
      return ReturnError(L('_SAVE_DATA_FAILURE_'));
    }
  }

  /**
   * todo:刷新管理员组缓存
   */
  public function ResetAdminGroupCache(){
    $sField = "id";
    $AdminGroupIdList = $this->modAdminUserGroup->field($sField)->select();

    foreach($AdminGroupIdList as $key=>$val){
      $this->clsAdmin->SetAdminGroupDetailsCache($val["id"]);
    }

    return ReturnCorrect(L("_CACHE_REFRESH_SUCCEED_"));
  }

  /**
   * todo: 删除管理员组
   *
   * @param $_id_str id字符串，逗号分割id
   *
   * @return array
   */
  public function AdminGroupDelete($_id_str){
    if(IsN($_id_str)){
      return ReturnError(L("_ADMIN_GROUP_ID_NULL_"));
    }else{
      $IdList = explode(",", $_id_str);

      foreach($IdList as $key=>$val){
        if(!IsNum($val, false, false)){
          return ReturnError(L("_ADMIN_GROUP_ID_ERROR_"));
        }
      }
    }

    $sWhere = "id in ({$_id_str})";
    $Result = $this->modAdminUserGroup->where($sWhere)->delete();

    if($Result === false){
      return ReturnError(L("_DELETE_DATA_FAILURE_"));
    }

    foreach($IdList as $key=>$val){
      $this->clsAdmin->SetAdminGroupDetailsCache($val);
    }

    return ReturnCorrect(L("_DELETE_DATA_SUCCEED_"));
  }

  /**
   * todo:管理员组Option
   * @return array
   */
  public function AdminUserGroupOption(){
    $sField = "id";

    $AdminUserGroupIdList = $this->modAdminUserGroup->field($sField)->select();
    $AdminUserGroupOption = array();

    foreach($AdminUserGroupIdList as $key=>$val){
      $GroupInfo = $this->clsAdmin->GetAdminGroupDetails($val["id"]);

      $Option["key"] = $GroupInfo["group_name"];
      $Option["val"] = $GroupInfo["id"];

      array_push($AdminUserGroupOption, $Option);
    }

    return $AdminUserGroupOption;
  }

  /**
   * todo: 检查管理员组名称是否存在
   * @param $_group_name 管理员组名称
   *
   * @return bool false:不存在, true:存在
   */
  private function CheckAdminGroupNameExist($_group_name){
    if(IsN($_group_name)){
      return false;
    }

    $sWhere = "group_name='{$_group_name}'";

    $GroupNameNumber = $this->modAdminUserGroup->where($sWhere)->count();

    if(IsNum($GroupNameNumber, false, false)){
      return true;
    }

    return false;
  }
  //endregion 管理员组
  
  //region 管理员组权限
  /**
   * todo: 管理员组权限管理
   * @param $_group_id
   *
   * @return array
   */
  public function AdminGroupPermissionManage($_group_id){
    if(!IsNum($_group_id, false, false)){
      return ReturnError(L("_ADMIN_GROUP_ID_ERROR_"));
    }

    //region 管理员组信息
    $AdminGroupInfo = $this->clsAdmin->GetAdminGroupDetails($_group_id);
    $Result["group_info"] = $AdminGroupInfo;
    //region 管理员组信息

    //region 获取栏目对应的权限项
    $clsSystemCategory = new \Org\ZhiHui\SystemCategory();
    $SystemCategoryListAll = $clsSystemCategory->SystemCategoryListAll();
    $PermissionItemList = array();

    foreach($SystemCategoryListAll as $Lv1Key=>$Lv1Val){
      $Lv1Info["menu_name"] = $Lv1Val["menu_name"];
      $Lv1Info["model_name"] = $Lv1Val["model_name"];
      $Lv1Info["controller_name"] = $Lv1Val["controller_name"];
      $Lv1Info["action_name"] = $Lv1Val["action_name"];
      $Lv1Info["status"] = 0;
      $Lv1Info["sub"] = array();

      foreach($Lv1Val["sub"] as $Lv2Key=>$Lv2Val){
        $Lv2Info["menu_name"] = $Lv2Val["menu_name"];
        $Lv2Info["model_name"] = $Lv2Val["model_name"];
        $Lv2Info["controller_name"] = $Lv2Val["controller_name"];
        $Lv2Info["action_name"] = $Lv2Val["action_name"];
        $Lv2Info["status"] = 0;
        $Lv2Info["sub"] = array();
        
        array_push($Lv1Info["sub"], $Lv2Info);
      }
      
      array_push($PermissionItemList, $Lv1Info);
    }

    $Result["permission_node"] = $PermissionItemList;
    //endregion 获取栏目对应的权限项

    return ReturnCorrect("", $Result);
  }
  //endregion 管理员组权限
}
