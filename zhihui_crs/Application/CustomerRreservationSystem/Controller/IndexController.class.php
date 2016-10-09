<?php
namespace CustomerRreservationSystem\Controller;
use Think\Controller;

class IndexController extends CommonController {
  function __construct() {
    parent::__construct();

    $this->_Model = "首页";
  }
  
  public function index(){
    //region 系统栏目菜单
    $clsSystemCategory = new \Org\ZhiHui\SystemCategory();
    $SystemCategoryList = $clsSystemCategory->GetSystemCategoryTree();
    //endregion 系统栏目菜单
//p($this->_AccountInfo);
    
    $clsLogin = new \Org\ZhiHui\Login();
    $sAccountType = GetLoginAccountTypeCookeis();
    $sGroupName = "";
    
    switch($sAccountType){
      case $clsLogin->GetLoginTypeAdmin():
        $sGroupName = $this->_AccountInfo["group_name"];
        break;
      
      case $clsLogin->GetLoginTypeBrokerCompany():
        $sGroupName = "经纪公司";
        break;
      
      case $clsLogin->GetLoginTypeSellerManager():
        $sGroupName = "销售经理";
        break;
    }
    
    $this->assign("GroupName", $sGroupName);
    $this->assign("AccountInfo", $this->_AccountInfo);
    $this->assign("SystemCategoryList", $SystemCategoryList);
    
    $this->CustomDisplay();
  }

  /**
   * todo: 默认的主选项卡
   */
  public function DefaultMainTab(){
    $this->CustomDisplay("default_main_tab");
  }
}