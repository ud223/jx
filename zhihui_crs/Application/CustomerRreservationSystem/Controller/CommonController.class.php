<?php
namespace CustomerRreservationSystem\Controller;
use Think\Controller;

class CommonController extends Controller {
  public $_ComName = "";
  public $_SystemName = "";
  public $_AboutLink = "";
  
  public $_PagingRowCount = 20;
  public $_AccountID = 0;
  public $_AccountType = "";
  public $_AdminAccountType = "";
  public $_BrokerCompanyAccountType = "";
  public $_SellerManagerAccountType = "";
  public $_AccountInfo = array();
  public $_AccountLoginStatus = false;

  public $_Model = "";
  
  function _initialize() {
    $clsSystemConfig = new \Org\ZhiHui\SystemConfig();
    $clsSystemCategory = new \Org\ZhiHui\SystemCategory();
    $clsLogin = new \Org\ZhiHui\Login();
    $clsAdmin = new \Org\ZhiHui\Admin();
    $clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    $clsSellerManager = new \Org\ZhiHui\SellerManager();

    
    $this->_ComName = $clsSystemConfig->_ComName;
    $this->_SystemName = $clsSystemConfig->_SystemName;
    $this->_AboutLink = $clsSystemConfig->_AboutLink;
    
    $this->_AdminAccountType = $clsLogin->GetLoginTypeAdmin();
    $this->_BrokerCompanyAccountType = $clsLogin->GetLoginTypeBrokerCompany();
    $this->_SellerManagerAccountType = $clsLogin->GetLoginTypeSellerManager();
    
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
      case $clsLogin->GetLoginTypeAdmin():
        $this->_AccountInfo = $clsAdmin->GetAdminDetails($this->_AccountID);
        break;
      
      case $clsLogin->GetLoginTypeBrokerCompany():
        $this->_AccountInfo = $clsBrokerCompany->GetBrokerCompanyAccountDetails($this->_AccountID);
        break;

      case $clsLogin->GetLoginTypeSellerManager():
        $this->_AccountInfo = $clsSellerManager->GetSellerManagerDetails($this->_AccountID);
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
   * @param $_template 视图文件名称
   */
  public function CustomDisplay($_view_name){
    $this->assign("ComName", $this->_ComName);
    $this->assign("SystemName", $this->_SystemName);
    $this->assign("AboutLink", $this->_AboutLink);
    $this->assign("ModelName", $this->_Model);

    $this->assign("AccountInfo", $this->_AccountInfo);
    $this->assign("AccountType", $this->_AccountType);
    $this->assign("AdminAccount", $this->_AdminAccountType);
    $this->assign("BrokerCompanyAccount", $this->_BrokerCompanyAccountType);
    $this->assign("SellerManagerAccount", $this->_SellerManagerAccountType);

    $this->display($_view_name);
  }
}