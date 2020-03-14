<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

//管理员模型
class LoginModel extends Model {
  private $modAdmin = null;
  private $modUser = null;

  private $clsLogin = null;
  private $clsAdmin = null;
  private $clsUser = null;

  function __construct() {
    $this->modAdmin = M("admin_user");
    $this->modUser = M("users");

    $this->clsLogin = new \Org\ZhiHui\Login();
    $this->clsAdmin = new \Org\ZhiHui\Admin();
    $this->clsUser = new \Org\ZhiHui\User();
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
      case $this->clsUser->GetAdminTypeName():
        $sField = "admin_id";
        $sWhere = "login_name='{$_name}'";
        $sWhere .= " and disable=0";
        $nAdminUserId = $this->modAdmin->where($sWhere)->getField($sField);

        if(!IsNum($nAdminUserId, false, false)){
          return ReturnError(L("_LOGIN_PASSWORD_ERROR_"));
        }

        $AccountInfo = $this->clsAdmin->GetAdminDetails($nAdminUserId);
        break;

      default:
        $sField = "id";
        $sWhere = "login_name='{$_name}'";
        $sWhere .= " and disable=0";
        $nUserId = $this->modUser->where($sWhere)->getField($sField);

        if(!IsNum($nUserId, false, false)){
          return ReturnError(L("_LOGIN_PASSWORD_ERROR_"));
        }

        $AccountInfo = $this->clsUser->GetUserDetails($nUserId);
        $UserGroupInfo = $this->clsUser->GroupIdGetGroupInfo($AccountInfo["group_id"]);

        if($AccountInfo["group_id"] != $UserGroupInfo["group_id"]){
          return ReturnError(L("_LOGIN_TYPE_ERROR_"));
        }
        break;
    }

    if(!IsArray($AccountInfo)){
      return ReturnError(L("_LOGIN_PASSWORD_ERROR_"));
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
      case $this->clsUser->GetAdminTypeName():
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
        $sAccountType = $this->clsUser->GetAdminTypeName();
        break;

      default:
        $SaveData = array(
          "id" => $AccountInfo["id"],
          "password" => $NewPassword["pwd"],
          "salt" => $NewPassword["salt"],
          "last_login" => time(),
          "last_ip" => get_client_ip(),
        );

        $this->modUser->save($SaveData);

        //清除登录前的缓存
        $this->clsUser->SetUserDetailsCache($AccountInfo["id"]);

        //缓存新的管理员数据
        $this->clsUser->GetUserDetails($AccountInfo["id"]);

        $nAccountID = $AccountInfo["id"];
        $sAccountType = $this->clsUser->UserGroupGetAccountTypeName($AccountInfo["group_id"]);
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
   *
   * @param $_name 登陆账号
   * @param $_type
   * @param $_pwd  登录密码
   *
   * @param $_code
   *
   * @return array
   */
  private function CheckLogin($_name, $_type, $_pwd, $_code){
    if(IsN($_name)){
      return ReturnError(L('_ADMIN_ACCOUNT_NULL_'));
    }
    
    if(IsN($_type)){
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
      case $this->clsUser->GetAdminTypeName():
        //清除登录前的缓存
        $this->clsAdmin->SetAdminDetailsCache($nAccountID);
        break;

      default:
        //清除登录前的缓存
        $this->clsUser->SetUserDetailsCache($nAccountID);
        break;
    }
    
    return ReturnCorrect(L("_LOGOUT_SUCCEED_"));
  }
  //endregion 注销
}
