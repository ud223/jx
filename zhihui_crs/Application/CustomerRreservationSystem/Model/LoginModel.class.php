<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

//管理员模型
class LoginModel extends Model {
  private $modAdmin = null;
  private $modBrokerCompanyAccount = null;
  private $modSellerManager = null;

  private $clsLogin = null;
  private $clsAdmin = null;
  private $clsBrokerCompany = null;
  private $clsSellerManager = null;
  
  function __construct() {
    $this->modAdmin = M("admin_user");
    $this->modBrokerCompanyAccount = M("broker_company_account");
    $this->modSellerManager = M("seller_manager");
    
    $this->clsLogin = new \Org\ZhiHui\Login();
    $this->clsAdmin = new \Org\ZhiHui\Admin();
    $this->clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    $this->clsSellerManager = new \Org\ZhiHui\SellerManager();
  }

  //region 登陆
  /**
   * todo:登陆
   *
   * @param $_name 账号
   * @param $_type 账号类型。admin、broker_company、sell_manager
   * @param $_pwd  密码
   * @param $_code 验证码
   *
   * @return array
   */
  public function DoLogin($_name, $_type, $_pwd, $_code){
    //检查参数
    $LoginStatus = $this->CheckLogin($_name, $_type, $_pwd, $_code);

    if($LoginStatus["error"] == 1){
      return ReturnError($LoginStatus["info"]);
    }

    //region 获取账号信息
    switch($_type){
      case $this->clsLogin->GetLoginTypeAdmin():
        $sField = "admin_id";
        $sWhere = "login_name='{$_name}'";
        $sWhere .= " and disable=0";
        $nAdminUserId = $this->modAdmin->where($sWhere)->getField($sField);

        if(!IsNum($nAdminUserId, false, false)){
          return ReturnError(L("_LOGIN_PASSWORD_ERROR_"));
        }

        $AccountInfo = $this->clsAdmin->GetAdminDetails($nAdminUserId);

        if(!IsArray($AccountInfo)){
          return ReturnError(L("_LOGIN_PASSWORD_ERROR_"));
        }
        break;
      
      case $this->clsLogin->GetLoginTypeBrokerCompany():
        $sField = "id";
        $sWhere = "login_name='{$_name}'";
        $sWhere .= " and disable=0";
        $nBrokerCompanyAccountId = $this->modBrokerCompanyAccount->where($sWhere)->getField($sField);

        if(!IsNum($nBrokerCompanyAccountId, false, false)){
          return ReturnError(L("_LOGIN_PASSWORD_ERROR_"));
        }

        $AccountInfo = $this->clsBrokerCompany->GetBrokerCompanyAccountDetails($nBrokerCompanyAccountId);

        if(!IsArray($AccountInfo)){
          return ReturnError(L("_LOGIN_PASSWORD_ERROR_"));
        }
        
        break;
      
      case $this->clsLogin->GetLoginTypeSellerManager():
        $sField = "id";
        $sWhere = "login_name='{$_name}'";
        $sWhere .= " and disable=0";
        $nSellerManagerId = $this->modSellerManager->where($sWhere)->getField($sField);

        if(!IsNum($nSellerManagerId, false, false)){
          return ReturnError(L("_LOGIN_PASSWORD_ERROR_"));
        }

        $AccountInfo = $this->clsSellerManager->GetSellerManagerDetails($nSellerManagerId);

        if(!IsArray($AccountInfo)){
          return ReturnError(L("_LOGIN_PASSWORD_ERROR_"));
        }
        break;
    }
    //endregion 获取账号信息

    //region 校验密码
    $PasswordInfo = PasswordMd5($_pwd, $AccountInfo["salt"]);

    if($PasswordInfo["pwd"] !== $AccountInfo["password"]){
      return ReturnError(L('_LOGIN_PASSWORD_ERROR_'));
    }
    //endregion 校验密码

    //更新管理员的安全码
    $NewPassword = PasswordMd5($_pwd);

    switch($_type){
      case $this->clsLogin->GetLoginTypeAdmin():
        $SaveData = array(
          "user_id" => $AccountInfo["admin_id"],
          "password" => $NewPassword["pwd"],
          "salt" => $NewPassword["salt"],
          "last_login" => time(),
          "last_ip" => get_client_ip(),
        );

        $this->modAdmin->save($SaveData);

        //清除登录前的缓存
        $this->clsAdmin->SetAdminDetailsCache($AccountInfo["admin_id"]);

        //缓存新的管理员数据
        $this->clsAdmin->GetAdminDetails($AccountInfo["admin_id"]);
        
        $nAccountID = $AccountInfo["admin_id"];
        $sAccountType = $this->clsLogin->GetLoginTypeAdmin();
        break;

      case $this->clsLogin->GetLoginTypeBrokerCompany():
        $SaveData = array(
          "id" => $AccountInfo["id"],
          "password" => $NewPassword["pwd"],
          "salt" => $NewPassword["salt"],
          "last_login" => time(),
          "last_ip" => get_client_ip(),
        );

        $this->modBrokerCompanyAccount->save($SaveData);

        //清除登录前的缓存
        $this->clsBrokerCompany->SetBrokerCompanyAccountDetailsCache($AccountInfo["id"]);

        //缓存新的管理员数据
        $this->clsBrokerCompany->GetBrokerCompanyAccountDetails($AccountInfo["id"]);

        $nAccountID = $AccountInfo["id"];
        $sAccountType = $this->clsLogin->GetLoginTypeBrokerCompany();
        break;

      case $this->clsLogin->GetLoginTypeSellerManager():
        $SaveData = array(
          "id" => $AccountInfo["id"],
          "password" => $NewPassword["pwd"],
          "salt" => $NewPassword["salt"],
          "last_login" => time(),
          "last_ip" => get_client_ip(),
        );

        $this->modSellerManager->save($SaveData);
        
        $this->clsSellerManager->SetSellerManagerDetailsCache($AccountInfo["id"]);
        $this->clsSellerManager->GetSellerManagerDetails($AccountInfo["id"]);

        $nAccountID = $AccountInfo["id"];
        $sAccountType = $this->clsLogin->GetLoginTypeSellerManager();
        break;
    }

    //设置管理员ID
    SetAccountIDCookies($nAccountID);
    
    //设置登录帐号类型
    SetLoginAccountTypeCookies($sAccountType);

    //设置登陆状态
    $this->clsLogin->SetAccountLoginStatusCache($nAccountID, $sAccountType, true);

    return ReturnCorrect(L("_LOGIN_SUCCEED_"));
  }

  /**
   * todo:检查登陆参数
   * @param $_name 登陆账号
   * @param $_pwd 登录密码
   *
   * @return array
   */
  private function CheckLogin($_name, $_type, $_pwd, $_code){
    if(IsN($_name)){
      return ReturnError(L('_ADMIN_ACCOUNT_NULL_'));
    }
    
    if($_type != $this->clsLogin->GetLoginTypeAdmin() && $_type != $this->clsLogin->GetLoginTypeBrokerCompany() && $_type != $this->clsLogin->GetLoginTypeSellerManager()){
      return ReturnError(L('_LOGIN_TYPE_ERROR_'));
    }

    if(IsN($_pwd)){
      return ReturnError(L('_LOGIN_PASSWORD_NULL_'));
    }

    if(IsN($_code)){
      return ReturnError(L('_VERIFY_CODE_NULL_'));
    }else{
      $arrVerifyConfig = C('VERIFY');
      $Verify = new \Think\Verify($arrVerifyConfig);
      $VerifyResult = $Verify->check($_code);

      if($VerifyResult === false){
        return ReturnError(L("_VERIFY_CODE_ERROR_"));
      }
    }

    return ReturnCorrect();
  }
  //endregion 登陆
  
  //region 注销
  public function Logout(){
    $nAccountID = GetAccountIDCookeis();
    $nAccoutType = GetLoginAccountTypeCookeis();
    
    ClearAccountIDCookies();
    ClearLoginAccountTypeCookies();

    $this->clsLogin->SetAccountLoginStatusCache($nAccountID, $nAccoutType);

    switch($nAccoutType){
      case $this->clsLogin->GetLoginTypeAdmin():
        //清除登录前的缓存
        $this->clsAdmin->SetAdminDetailsCache($nAccountID);
        break;

      case $this->clsLogin->GetLoginTypeBrokerCompany():
        //清除登录前的缓存
        $this->clsBrokerCompany->SetBrokerCompanyAccountDetailsCache($nAccountID);
        break;

      case $this->clsLogin->GetLoginTypeSellerManager():
        $this->clsSellerManager->SetSellerManagerDetailsCache($nAccountID);
        break;
    }
    
    return ReturnCorrect(L("_LOGOUT_SUCCEED_"));
  }
  //endregion 注销
}
