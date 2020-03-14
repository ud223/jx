<?php
/**
 * Created by PhpStorm.
 * User: 淼
 * Date: 2015/4/29
 * Time: 11:41
 */

namespace Org\ZhiHui;

class User {
  const ConstInsuranceCompanyUserID = 1;
  const ConstBrokerCompanyUserID = 2;

  Const ConstAdminTypeName = "admin";

  /**
   * @var array
   */
  public $_UserType = array(
    "captain"=>"captain",
    "member"=>"member",
    "special"=>"special",
    "broker_company"=>"broker_company",
  );
  
  private $modUser = null;
  
  function __construct() {
    $this->modUser = M("users");
  }

  //region 缓存
  //region 用户信息缓存
  /**
   * 获取用户信息
   * @param $_user_id 用户ID
   * @return mixed
   */
  public function GetUserDetails($_user_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetUserDetailsCache($_user_id);

      if(empty($Info)){
        $Info = $this->GetUserDetailsDb($_user_id);
      }
    }else{
      $Info = $this->GetUserDetailsDb($_user_id);
    }

    if(IsArray($Info)){
      $clsRegion = new \Org\ZhiHui\Region();

      $Info["group_name"] = "";
      if(IsNum($Info["group_id"], false, false)){
        $Info["group_name"] = $this->GroupIdGetGroupName($Info["group_id"]);
      }

      $Info["parent_user_name"] = "";
      if(IsNum($Info["parent_user_id"], false, false)){
        $ParentUserInfo = $this->GetUserDetails($Info["parent_user_id"]);
        $Info["parent_user_name"] = $ParentUserInfo["real_name"];
      }

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
   * 设置用户信息数据缓存
   * @param $_user_id 用户ID
   * @param array $_data 用户信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetUserDetailsCache($_user_id, $_data=NULL){
    F($this->GetUserDetailsCacheKey($_user_id), $_data);
  }

  /**
   * 获取用户信息数据缓存
   * @param $_user_id 用户ID
   * @return mixed
   */
  private function GetUserDetailsCache($_user_id){
    return F($this->GetUserDetailsCacheKey($_user_id));
  }

  /**
   * 获取用户信息缓存Key
   * @param $_user_id 用户ID
   * @return mixed
   */
  private function GetUserDetailsCacheKey($_user_id){
    return "User/Details/{$_user_id}";
  }

  /**
   * todo:从数据库中获取用户信息数据
   * @param $_user_id 用户ID
   *
   * @return mixed
   */
  private function GetUserDetailsDb($_user_id){
    $UserDetails = array();

    if(!IsNum($_user_id, false, false)){
      return $UserDetails;
    }

    $sWhere = "id={$_user_id}";
    $UserDetails = $this->modUser->where($sWhere)->find();

    if(!empty($UserDetails)){
      $this->SetUserDetailsCache($_user_id, $UserDetails);
    }

    return $UserDetails;
  }
  //endregion 用户信息缓存
  //endregion 缓存
  
  //region 获取常量
  /**
   * todo: 获取保险公司用户ID
   * @return int
   */
  public function ConstInsuranceCompanyUserID(){
    return self::ConstInsuranceCompanyUserID;
  }

  /**
   * todo: 获取经纪公司用户ID
   * @return int
   */
  public function ConstBrokerCompanyUserID(){
    return self::ConstBrokerCompanyUserID;
  }
  //endregion 获取常量

  //region 获取用户帐号的类型
  /**
   * todo: 获取管理员帐号类型
   * @return string
   */
  public function GetAdminTypeName(){
    return self::ConstAdminTypeName;
  }

  /**
   * todo: 获取团队长帐号类型
   * @return string
   */
  public function GetAccountTypeSellerCaptain(){
    return $this->_UserType["captain"];
  }

  /**
   * todo: 获取客户经理帐号类型
   * @return string
   */
  public function GetAccountTypeSellerMember(){
    return $this->_UserType["member"];
  }

  /**
   * todo: 获取特殊帐号类型
   * @return string
   */
  public function GetAccountTypeSpecial(){
    return $this->_UserType["special"];
  }

  /**
   * todo: 获取经纪公司类型
   * @return string
   */
  public function GetAccountTypeBrokerCompany(){
    return $this->_UserType["broker_company"];
  }

  /**
   * todo: 用户组获取账号类型名称
   * @param $_gourp_id
   *
   * @return mixed|string
   */
  public function UserGroupGetAccountTypeName($_gourp_id){
    $sAccountTypeName = "";

    switch($_gourp_id){
      case 1:
        $sAccountTypeName = $this->_UserType["special"];
        break;

      case 2:
        $sAccountTypeName = $this->_UserType["broker_company"];
        break;

      case 100:
        $sAccountTypeName = $this->_UserType["captain"];
        break;

      case 101:
        $sAccountTypeName = $this->_UserType["member"];
        break;
    }

    return $sAccountTypeName;
  }
  //endregion 获取用户帐号的类型

  //region 用户组
  /**
   * todo: 用户组列表
   * @return array
   */
  public function UserGroupList(){
    $GroupList = array(
      array(
        "group_id"=>1,
        "group_name"=>"特殊组",
        "is_show"=>true,
      ),
      array(
        "group_id"=>2,
        "group_name"=>"经纪公司",
        "is_show"=>true,
      ),
      array(
        "group_id"=>100,
        "group_name"=>"团队长",
        "is_show"=>true,
      ),
      array(
        "group_id"=>101,
        "group_name"=>"客户经理",
        "is_show"=>true,
      ),
    );

    return $GroupList;
  }

  /**
   * todo: 获取特殊用户组
   * @return mixed
   */
  public function SpecialUserGroup(){
    $GroupList = $this->UserGroupList();
    
    return $GroupList[0];
  }

  /**
   * todo: 获取经纪公司组
   * @return mixed
   */
  public function BrokerCompanyUserGroup(){
    $GroupList = $this->UserGroupList();

    return $GroupList[1];
  }

  /**
   * todo: 获取团队长组
   * @return mixed
   */
  public function SellerCaptainUserGroup(){
    $GroupList = $this->UserGroupList();

    return $GroupList[2];
  }

  /**
   * todo: 获取客户经理组
   * @return mixed
   */
  public function SellereMemberUserGroup(){
    $GroupList = $this->UserGroupList();

    return $GroupList[3];
  }

  /**
   * todo: 用户组Option
   * @return array
   */
  public function UserGroupOption(){
    $GroupList = $this->UserGroupList();
    $OptionList = array();

    foreach($GroupList as $key=>$val){
      if(!$val["is_show"]){
        continue;
      }

      $Option["key"] = $val["group_name"];
      $Option["val"] = $val["group_id"];

      array_push($OptionList, $Option);
    }

    return $OptionList;
  }

  /**
   * todo: 根据用户组ID获取用户组信息
   * @param $_group_id
   *
   * @return mixed|string
   */
  public function GroupIdGetGroupInfo($_group_id){
    if(!IsNum($_group_id, false, false)){
      return array();
    }

    $GroupList = $this->UserGroupList();
    $GroupInfo = array();

    foreach($GroupList as $key=>$val){
      if($val["group_id"] == $_group_id){
        $GroupInfo = $val;
        breka;
      }
    }

    return $GroupInfo;
  }

  /**
   * todo: 根据用户组ID获取用户组名称
   * @param $_group_id
   *
   * @return mixed|string
   */
  public function GroupIdGetGroupName($_group_id){
    if(!IsNum($_group_id, false, false)){
      return "";
    }

    $GroupInfo = $this->GroupIdGetGroupInfo($_group_id);

    return $GroupInfo["group_name"];
  }
  //endregion 用户组

  /**
   * todo: 团队长Option列表
   *
   * @return array
   */
  public function SellerCaptainOption(){
    $UserGroupInfo = $this->SellerCaptainUserGroup();

    $sField = "id";
    $sWhere = "disable=0 and group_id={$UserGroupInfo["group_id"]}";

    $SellerCaptainIdList = $this->modUser->field($sField)->where($sWhere)->select();
    $SellerCaptainOption = array();
    
    foreach($SellerCaptainIdList as $key=>$val){
      $SellerCaptainInfo = $this->GetUserDetails($val["id"]);
      
      $OptionInfo["key"] = $SellerCaptainInfo["real_name"];
      $OptionInfo["val"] = $SellerCaptainInfo["id"];
      
      array_push($SellerCaptainOption, $OptionInfo);
    }
    
    return $SellerCaptainOption;
  }

  /**
   * todo: 团队长Option列表
   *
   * @param $_seller_captain_id
   *
   * @return array
   */
  public function SellerMemberOption($_seller_captain_id){
    if(!IsNum($_seller_captain_id, false, false)){
      return array();
    }

    $UserGroupInfo = $this->SellereMemberUserGroup();

    $sField = "id";
    $sWhere = "disable=0 and group_id={$UserGroupInfo["group_id"]} and parent_user_id={$_seller_captain_id}";

    $SellerMemberIdList = $this->modUser->field($sField)->where($sWhere)->select();
    $SellerMemberOption = array();

    foreach($SellerMemberIdList as $key=>$val){
      $SellerMemberInfo = $this->GetUserDetails($val["id"]);

      $OptionInfo["key"] = $SellerMemberInfo["real_name"];
      $OptionInfo["val"] = $SellerMemberInfo["id"];

      array_push($SellerMemberOption, $OptionInfo);
    }

    return $SellerMemberOption;
  }
}