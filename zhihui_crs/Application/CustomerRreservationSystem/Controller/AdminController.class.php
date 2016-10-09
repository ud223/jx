<?php
namespace CustomerRreservationSystem\Controller;
use Think\Controller;

class AdminController extends CommonController {
  private $modAdmin = null;

  function __construct() {
    parent::__construct();

    $this->_Model = "管理员";

    $this->modAdmin = D("Admin");
  }
  
  //region 管理员
  /**
   * todo: 管理员列表
   */
  public function AdminUserList(){
    $SearchParam = $this->GetAdminUserListSearchParam();
    $AdminUserList = $this->modAdmin->AdminUserList($this->_PagingRowCount, $SearchParam);
    
    $AdminUserGroupOption = $this->modAdmin->AdminUserGroupOption();

    $this->assign("AdminUserGroupOption", $AdminUserGroupOption);
    $this->assign("AdminUserList", $AdminUserList["DataList"]);
    $this->assign("Page", $AdminUserList["PageInfo"]);
    
    $this->CustomDisplay("admin_user_list");
  }

  /**
   * todo: 管理员信息
   */
  public function AdminUserInfo(){
    $nAdminUserID = RR("admin_user_id");
    
    $AdminUserInfo = $this->modAdmin->AdminUserInfo($nAdminUserID);

    $this->assign("AdminUserInfo", $AdminUserInfo["admin_user_info"]);
    $this->assign("AdminUserGroupOption", $AdminUserInfo["admin_group_option"]);

    $this->CustomDisplay("admin_user_info");
  }

  /**
   * todo: 保存管理员信息
   */
  public function AjaxAdminUserSave(){
    $nAdminUserID = RR("admin_user_id");
    $nGroupID = RR("admin_group");
    $sLoginName = RR("login_name");
    $sLoginPassword = RR("login_password");
    $sRealName = RR("real_name");
    $sEmail = RR("email");
    $sMobilePhone = RR("mobile_phone");
    $sQQ = RR("qq");
    $sWechat = RR("wechat");
    $nDesable = RR("disable");

    $SaveResult = $this->modAdmin->AdminUserSave($nAdminUserID, $nGroupID, $sLoginName, $sLoginPassword, $sRealName, $sEmail, $sMobilePhone, $sQQ, $sWechat, $nDesable);

    AjaxReturn($SaveResult);
  }

  /**
   * todo: Ajax删除管理员
   */
  public function AjaxAdminUserDelete(){
    $sAdminUserID = RR("admin_user_id");

    $Result = $this->modAdmin->AdminUserDelete($sAdminUserID);

    AjaxReturn($Result);
  }

  /**
   * todo:刷新管理员缓存
   */
  public function ResetAdminUserCache(){
    $Result = $this->modAdmin->ResetAdminUserCache();

    AjaxReturn($Result);
  }

  /**
   * todo:获取商品元列表查询参数
   */
  private function GetAdminUserListSearchParam(){
    $map = $this->SetAdminUserListParamData();

    if(
      IsN($map["search_case"])
      && IsN($map["search_key"])
      && IsN($map["search_status"])
      && IsN($map["search_group"])
    ){
      return array();
    }

    $this->assign("search_case", $map["search_case"]);
    $this->assign("search_key", $map["search_key"]);
    $this->assign("search_status", $map["search_status"]);
    $this->assign("search_group", $map["search_group"]);

    return $map;
  }

  private function SetAdminUserListParamData(){
    $Param["search_case"] = RR("search_case"); //查询条件，对应数据库字段
    $Param["search_key"] = RR("search_key"); //关键字
    $Param["search_key"] = urldecode($Param["search_key"]);
    $Param["search_status"] = RR("search_status");
    $Param["search_group"] = RR("search_group");

    return $Param;
  }
  //endregion 管理员
  
  //region 管理员组
  /**
   * todo: 管理员组列表
   */
  public function AdminGroupList(){
    $AdminGroupList = $this->modAdmin->AdminGroupList($this->_PagingRowCount, array());

    $this->assign("AdminGroupList", $AdminGroupList["DataList"]);
    $this->assign("Page", $AdminGroupList["PageInfo"]);

    $this->CustomDisplay("admin_group_list");
  }

  /**
   * todo:Ajax保存管理员组信息
   */
  public function AjaxAdminGroupSave(){
    $nGroupID = RR("group_id");
    $sGroupName = RR("group_name");

    $SaveResult = $this->modAdmin->AdminGroupSave($nGroupID, $sGroupName);

    AjaxReturn($SaveResult);
  }

  /**
   * todo:快速修改管理员组信息的字段值
   */
  public function QuickModifyGroupField(){
    $sKey = RR("key"); //查询条件，对应数据库字段
    $sVal = RR("val"); //关键字
    $nID = RR("id"); //数据ID

    $Result = $this->modAdmin->QuickAdminGroupSave($nID, $sKey, $sVal);

    if($Result["error"] == 0){
      AjaxReturnCorrect($Result["info"], $sVal);
    }else{
      AjaxReturn($Result);
    }
  }

  /**
   * todo:刷新管理员缓存
   */
  public function ResetAdminGroupCache(){
    $Result = $this->modAdmin->ResetAdminGroupCache();

    AjaxReturn($Result);
  }

  /**
   * todo: Ajax删除管理员组
   */
  public function AjaxAdminGroupDelete(){
    $sAdminGroupID = RR("admin_group_id");

    $Result = $this->modAdmin->AdminGroupDelete($sAdminGroupID);

    AjaxReturn($Result);
  }
  //endregion 管理员组
}