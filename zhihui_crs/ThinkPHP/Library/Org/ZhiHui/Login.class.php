<?php
/**
 * Created by PhpStorm.
 * User: 淼
 * Date: 2015/4/29
 * Time: 11:41
 */

namespace Org\ZhiHui;

class Login {
  Const ConstLoginType_Admin = "admin";
  Const ConstLoginType_BrokerCompany = "broker_company";
  Const ConstLoginType_SellerManager = "seller_manager";
  
  function __construct() {
    
  }

  //region 管理员登陆状态缓存
  /**
   * 获取管理员登陆状态
   * @param $_account_id 帐号ID
   * @param $_account_type 帐号类型
   * @return mixed
   */
  public function GetAccountLoginStatus($_account_id, $_account_type){
    $Info = $this->GetAccountLoginStatusCache($_account_id, $_account_type);

    return $Info;
  }

  /**
   * 设置管理员登陆状态数据缓存
   * @param $_account_id 帐号ID
   * @param $_account_type 帐号类型
   * @param array $_data 管理员登陆状态数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetAccountLoginStatusCache($_account_id, $_account_type, $_data=NULL){
    F($this->GetAccountLoginStatusCacheKey($_account_id, $_account_type), $_data);
  }

  /**
   * 获取管理员登陆状态数据缓存
   * @param $_account_id 帐号ID
   * @param $_account_type 帐号类型
   * @return mixed
   */
  private function GetAccountLoginStatusCache($_account_id, $_account_type){
    return F($this->GetAccountLoginStatusCacheKey($_account_id, $_account_type));
  }

  /**
   * 获取管理员登陆状态缓存Key
   * @param $_account_id 帐号ID
   * @param $_account_type 帐号类型
   * @return mixed
   */
  private function GetAccountLoginStatusCacheKey($_account_id, $_account_type){
    return "Account/Login/{$_account_type}/{$_account_id}";
  }
  //endregion 管理员登陆状态缓存
  
  //region 获取登录帐号的类型
  /**
   * todo: 获取Admin登录帐号类型
   * @return string
   */
  public function GetLoginTypeAdmin(){
    return self::ConstLoginType_Admin;
  }

  /**
   * todo: 获取BrokerCompany登录帐号类型
   * @return string
   */
  public function GetLoginTypeBrokerCompany(){
    return self::ConstLoginType_BrokerCompany;
  }

  /**
   * todo: 获取SellerManager登录帐号类型
   * @return string
   */
  public function GetLoginTypeSellerManager(){
    return self::ConstLoginType_SellerManager;
  }
  //endregion 获取登录帐号的类型
}