<?php
/**
 * Created by PhpStorm.
 * User: 淼
 * Date: 2015/4/29
 * Time: 11:41
 */

namespace Org\ZhiHui;

class Admin {
  private $modAdminUser = null;
  private $modAdminUserGroup = null;
  
  function __construct() {
    $this->modAdminUser = M("admin_user");
    $this->modAdminUserGroup = M("admin_user_group");
  }
  
  //region 缓存
  //region 管理员信息缓存
  /**
   * 获取管理员信息
   * @param $_admin_id 管理员ID
   * @return mixed
   */
  public function GetAdminDetails($_admin_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetAdminDetailsCache($_admin_id);

      if(empty($Info)){
        $Info = $this->GetAdminDetailsDb($_admin_id);
      }
    }else{
      $Info = $this->GetAdminDetailsDb($_admin_id);
    }

    $Info["group_name"] = "";
    
    if(IsNum($Info["group_id"], false, false)){
      $AdminUserGroupInfo = $this->GetAdminGroupDetails($Info["group_id"]);
      
      if(IsArray($AdminUserGroupInfo)){
        $Info["group_name"] = $AdminUserGroupInfo["group_name"];
      }
    }

    return $Info;
  }

  /**
   * 设置管理员信息数据缓存
   * @param $_admin_id 管理员ID
   * @param array $_data 管理员信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetAdminDetailsCache($_admin_id, $_data=NULL){
    F($this->GetAdminDetailsCacheKey($_admin_id), $_data);
  }

  /**
   * 获取管理员信息数据缓存
   * @param $_admin_id 管理员ID
   * @return mixed
   */
  private function GetAdminDetailsCache($_admin_id){
    return F($this->GetAdminDetailsCacheKey($_admin_id));
  }

  /**
   * 获取管理员信息缓存Key
   * @param $_admin_id 管理员ID
   * @return mixed
   */
  private function GetAdminDetailsCacheKey($_admin_id){
    return "Admin/Details/{$_admin_id}";
  }

  /**
   * todo:从数据库中获取管理员信息数据
   * @param $_admin_id 管理员ID
   *
   * @return mixed
   */
  private function GetAdminDetailsDb($_admin_id){
    $AdminDetails = array();
    
    if(!IsNum($_admin_id, false, false)){
      return $AdminDetails;
    }
    
    $sWhere = "admin_id={$_admin_id}";
    $AdminDetails = $this->modAdminUser->where($sWhere)->find();

    if(!empty($AdminDetails)){
      $this->SetAdminDetailsCache($_admin_id, $AdminDetails);
    }

    return $AdminDetails;
  }
  //endregion 管理员信息缓存
  
  //region 管理员组缓存
  /**
   * 获取管理员组信息
   * @param $_admin_group_id 管理员组ID
   * @return mixed
   */
  public function GetAdminGroupDetails($_admin_group_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetAdminGroupDetailsCache($_admin_group_id);

      if(empty($Info)){
        $Info = $this->GetAdminGroupDetailsDb($_admin_group_id);
      }
    }else{
      $Info = $this->GetAdminGroupDetailsDb($_admin_group_id);
    }

    return $Info;
  }

  /**
   * 设置管理员组信息数据缓存
   * @param $_admin_group_id 管理员组ID
   * @param array $_data 管理员组信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetAdminGroupDetailsCache($_admin_group_id, $_data=NULL){
    F($this->GetAdminGroupDetailsCacheKey($_admin_group_id), $_data);
  }

  /**
   * 获取管理员组信息数据缓存
   * @param $_admin_group_id 管理员组ID
   * @return mixed
   */
  private function GetAdminGroupDetailsCache($_admin_group_id){
    return F($this->GetAdminGroupDetailsCacheKey($_admin_group_id));
  }

  /**
   * 获取管理员组信息缓存Key
   * @param $_admin_group_id 管理员组ID
   * @return mixed
   */
  private function GetAdminGroupDetailsCacheKey($_admin_group_id){
    return "Admin/Group/{$_admin_group_id}";
  }

  /**
   * todo:从数据库中获取管理员组信息数据
   * @param $_admin_group_id 管理员组ID
   *
   * @return mixed
   */
  private function GetAdminGroupDetailsDb($_admin_group_id){
    $AdminGroupDetails = array();
    
    if(!IsNum($_admin_group_id, false, false)){
      return $AdminGroupDetails;
    }
    
    $sWhere = "id={$_admin_group_id}";
    $AdminGroupDetails = $this->modAdminUserGroup->where($sWhere)->find();

    if(!empty($AdminGroupDetails)){
      $this->SetAdminGroupDetailsCache($_admin_group_id, $AdminGroupDetails);
    }

    return $AdminGroupDetails;
  }
  //endregion 管理员组缓存

  //region 权限节点缓存
  /**
   * 获取权限节点信息
   * @param $_node_id 节点ID
   * @return mixed
   */
  public function GetAdminGroupPermissionNodeDetails($_model_name, $_controller_name, $_action_name){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetAdminGroupPermissionNodeDetailsCache($_model_name, $_controller_name, $_action_name);

      if(empty($Info)){
        $Info = $this->GetAdminGroupPermissionNodeDetailsDb($_model_name, $_controller_name, $_action_name);
      }
    }else{
      $Info = $this->GetAdminGroupPermissionNodeDetailsDb($_model_name, $_controller_name, $_action_name);
    }

    return $Info;
  }

  /**
   * 设置权限节点信息数据缓存
   * @param $_node_id 节点ID
   * @param array $_data 权限节点信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetAdminGroupPermissionNodeDetailsCache($_model_name, $_controller_name, $_action_name, $_data=NULL){
    F($this->GetAdminGroupPermissionNodeDetailsCacheKey($_model_name, $_controller_name, $_action_name), $_data);
  }

  /**
   * 获取权限节点信息数据缓存
   * @param $_node_id 节点ID
   * @return mixed
   */
  private function GetAdminGroupPermissionNodeDetailsCache($_model_name, $_controller_name, $_action_name){
    return F($this->GetAdminGroupPermissionNodeDetailsCacheKey($_model_name, $_controller_name, $_action_name));
  }

  /**
   * 获取权限节点信息缓存Key
   * @param $_node_id 节点ID
   * @return mixed
   */
  private function GetAdminGroupPermissionNodeDetailsCacheKey($_model_name, $_controller_name, $_action_name){
    
    $sFieldName = $_model_name;
    
    if(!IsN($_controller_name)){
      $sFieldName .= "_{$_controller_name}";
    }

    if(!IsN($_action_name)){
      $sFieldName .= "_{$_action_name}";
    }
    
    return "Admin/Group/Permission/Node/{$sFieldName}";
  }

  /**
   * todo:从数据库中获取权限节点信息数据
   * @param $_node_id 节点ID
   *
   * @return mixed
   */
  private function GetAdminGroupPermissionNodeDetailsDb($_model_name, $_controller_name, $_action_name){
    $sWhere = "model_name='{$_model_name}'";
    
    if(!IsN($_controller_name)){
      $sWhere .= " and controller_name='{$_controller_name}'";
    }
    
    if(!IsN($_action_name)){
      $sWhere .= " and action_name='{$_action_name}'";
    }
    
    $AdminGroupPermissionNodeDetails = $this->modAdminUserGroupPermissionNode->where($sWhere)->find();

    if(!empty($AdminGroupPermissionNodeDetails)){
      $this->SetAdminGroupPermissionNodeDetailsCache($_model_name, $_controller_name, $_action_name, $AdminGroupPermissionNodeDetails);
    }

    return $AdminGroupPermissionNodeDetails;
  }
  //endregion 权限节点缓存
  //endregion 缓存
  
  //region 管理员
  
  //endregion 管理员
}