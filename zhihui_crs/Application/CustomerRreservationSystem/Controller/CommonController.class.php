<?php
namespace CustomerRreservationSystem\Controller;
use Think\Controller;

class CommonController extends Controller {
  public $_ComName = "";
  public $_SystemName = "";
  public $_AboutLink = "";
  public $_MobileClient = false;
  
  public $_PagingRowCount = 20;
  public $_AccountID = 0;
  public $_AccountType = "";
  public $_AdminAccountType = "";
  public $_SellerCaptainAccountType = "";
  public $_SellerMemberAccountType = "";
  public $_SpecialAccountType = "";
  public $_BrokerCompanyAccountType = "";
  public $_AccountInfo = array();
  public $_AccountLoginStatus = false;

  public $_Model = "";
  public $_Controller = "";
  public $_Action = "";

  function _initialize() {
    $clsSystemConfig = new \Org\ZhiHui\SystemConfig();
    $clsSystemCategory = new \Org\ZhiHui\SystemCategory();
    $clsLogin = new \Org\ZhiHui\Login();
    $clsAdmin = new \Org\ZhiHui\Admin();
    $clsUser = new \Org\ZhiHui\User();

    $this->_ComName = $clsSystemConfig->_ComName;
    $this->_SystemName = $clsSystemConfig->_SystemName;
    $this->_AboutLink = $clsSystemConfig->_AboutLink;
    $this->_MobileClient = is_mobile_request();

    $this->_AdminAccountType = $clsUser->GetAdminTypeName();
    $this->_SellerCaptainAccountType = $clsUser->GetAccountTypeSellerCaptain();
    $this->_SellerMemberAccountType = $clsUser->GetAccountTypeSellerMember();
    $this->_SpecialAccountType = $clsUser->GetAccountTypeSpecial();
    $this->_BrokerCompanyAccountType = $clsUser->GetAccountTypeBrokerCompany();

    $nPageRowCount = RR("pagecount");

    //region 保存用户设置的每页记录数
    if(empty($nPageRowCount)){
      $this->_PagingRowCount = cookie(GetCookiePrefix()."PageRowCount");

      if(!IsNum($this->_PagingRowCount, false)){
        $this->_PagingRowCount = 20;
        cookie(GetCookiePrefix()."PageRowCount", $this->_PagingRowCount);
      }
    }else{
      $this->_PagingRowCount = $nPageRowCount;
      cookie(GetCookiePrefix()."PageRowCount", $nPageRowCount);
    }
    //endregion 保存用户设置的每页记录数
    
    $this->_AccountID = GetAccountIDCookeis();
    $this->_AccountType = GetLoginAccountTypeCookeis();

    //region 访问权限检查
    $sModuleName = MODULE_NAME;
    $sControllerName = CONTROLLER_NAME;
    $sActionName = ACTION_NAME;

    if(
      ($sControllerName !== "Index" && $sActionName !== "index")
    ){
      $HavePermission = false;

      $SystemCategoryTree = $clsSystemCategory->SystemCategoryListAll();
      $VisitUrl = "{$sControllerName}/{$sActionName}";
      foreach($SystemCategoryTree as $treeKey=>$treeVal){
        $AccountTypePermission = $treeVal["permission"][$this->_AccountType];

        if(in_array($VisitUrl, $AccountTypePermission)){
          $HavePermission = true;
          break;
        }
      }

      if(!$HavePermission){
        $sRequestType = $_SERVER['HTTP_REQUEST_TYPE'];

        switch($sRequestType){
          case "ajax";
            AjaxReturnError(L("_ACCOUNT_NOT_PERMISSION_"));
            break;

          default:
            redirect(GotoUrl("Error/NotPermission"));
            break;
        }
      }
    }
    //endregion 访问权限检查

    if(!IsNum($this->_AccountID, false, false)){
      $this->error(L('_LOGIN_AGAIN_'), GotoUrl("Login/index"), 3);
    }

    switch($this->_AccountType){
      case $clsUser->GetAdminTypeName():
        $this->_AccountInfo = $clsAdmin->GetAdminDetails($this->_AccountID);
        $this->_AccountInfo["user_type"] = $clsUser->GetAdminTypeName();
        break;

      case $clsUser->_UserType["captain"]:
        $this->_AccountInfo = $clsUser->GetUserDetails($this->_AccountID);
        $this->_AccountInfo["user_type"] = $clsUser->_UserType["captain"];
        break;

      case $clsUser->_UserType["member"]:
        $this->_AccountInfo = $clsUser->GetUserDetails($this->_AccountID);
        $this->_AccountInfo["user_type"] = $clsUser->_UserType["member"];
        break;

      case $clsUser->_UserType["special"]:
        $this->_AccountInfo = $clsUser->GetUserDetails($this->_AccountID);
        $this->_AccountInfo["user_type"] = $clsUser->_UserType["special"];
        break;

      case $clsUser->_UserType["broker_company"]:
        $this->_AccountInfo = $clsUser->GetUserDetails($this->_AccountID);
        $this->_AccountInfo["user_type"] = $clsUser->_UserType["broker_company"];
        break;
    }
    
    $this->_AccountLoginStatus = $clsLogin->GetAccountLoginStatus($this->_AccountID, $this->_AccountType);

    if($this->_AccountLoginStatus !== true){
      $this->error(L('_LOGIN_AGAIN_'), GotoUrl("Login/index"), 3);
    }else{
      if(!IsNum($this->_AccountID, false, false) || !IsArray($this->_AccountInfo)){
        $this->error(L('_LOGIN_AGAIN_'), GotoUrl("Login/index"), 3);
      }
    }
  }

  /**
   * todo:封装的输出模板，会带上每个页面固定的赋值变量
   *
   * @param $_view_name 视图文件名称
   *
   */
  public function CustomDisplay($_view_name){
    $this->assign("ComName", $this->_ComName);
    $this->assign("SystemName", $this->_SystemName);
    $this->assign("AboutLink", $this->_AboutLink);
    $this->assign("ModelName", $this->_Model);
    $this->assign("ControllerName", $this->_Controller);
    $this->assign("ActionName", $this->_Action);

    $this->assign("AccountInfo", $this->_AccountInfo);
    $this->assign("AccountType", $this->_AccountType);
    $this->assign("AdminAccount", $this->_AdminAccountType);
    $this->assign("SellerCaptainAccountType", $this->_SellerCaptainAccountType);
    $this->assign("SellerMemberAccountType", $this->_SellerMemberAccountType);
    $this->assign("SpecialAccountType", $this->_SpecialAccountType);
    $this->assign("BrokerCompanyAccountType", $this->_BrokerCompanyAccountType);

    $this->display($_view_name);
  }
}