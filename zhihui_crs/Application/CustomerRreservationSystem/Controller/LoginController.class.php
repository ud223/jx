<?php
namespace CustomerRreservationSystem\Controller;
use Think\Controller;

class LoginController extends Controller {
  private $modLogin = null;
  
  function __construct() {
    parent::__construct();
    
    $this->modLogin = D("Login");
  }

  /**
   * todo: 登陆页
   */
  public function index(){
    $clsSystemConfig = new \Org\ZhiHui\SystemConfig();
    $clsLogin = new \Org\ZhiHui\Login();
    
    $this->assign("AdminType", $clsLogin->GetLoginTypeAdmin());
    $this->assign("BrokerCompanyType", $clsLogin->GetLoginTypeBrokerCompany());
    $this->assign("SellerManagerType", $clsLogin->GetLoginTypeSellerManager());
    
    $this->assign("EnAbbrName", $clsSystemConfig->_EnAbbrName);
    $this->assign("SystemName", $clsSystemConfig->_SystemName);
    
    $this->display();
  }
    
  /**
  * 执行登陆
  */
  public function DoLogin(){
    $sLoginName = RR('login_name');
    $sLoginType = RR('login_type');
    $sPassword = RR('login_pwd');
    $sCode = RR('login_code');

    $Result = $this->modLogin->DoLogin($sLoginName, $sLoginType, $sPassword, $sCode);
    
    AjaxReturn($Result);
  }
  
  public function Logout(){
    $Result = $this->modLogin->Logout();
    
    redirect(U("Login/index"));
  }

  /**
   * todo: 创建验证码
   */
  public function CreateVerify(){
    $arrVerifyConfig = C('VERIFY');
    $Verify = new \Think\Verify($arrVerifyConfig);

    //不加这个，验证码可能会显示不了
    ob_clean();

    $Verify->entry();
  }
}