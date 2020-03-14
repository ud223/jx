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

    $sGroupName = $this->_AccountInfo["group_name"];

    $this->assign("GroupName", $sGroupName);
    $this->assign("AccountInfo", $this->_AccountInfo);
    $this->assign("SystemCategoryList", $SystemCategoryList);
  
//    if($this->_MobileClient){
//      $this->CustomDisplay("WapPage/index");
//    }else{
//      $this->CustomDisplay();
//    }
  
    $this->CustomDisplay();
  }

  public function AjaxPollingUntreatedNumber($_is_ajax=true){
    $clsUser = new \Org\ZhiHui\User();
    $clsPolling = new \Org\ZhiHui\Polling();

    if($this->_AccountID == $clsUser->ConstInsuranceCompanyUserID()){
      $OrderStatusCount = $clsPolling->GetSpecialOrderCount();
    }else{
      $OrderStatusCount = $clsPolling->GetSellerMemberOrderCount($this->_AccountID);
    }

    $OrderStatusCount["user_type"] = $this->_AccountType;

    if($_is_ajax){
      AjaxReturnCorrect("", $OrderStatusCount);
    }else{
      return $OrderStatusCount;
    }

  }

  /**
   * todo: 默认的主选项卡
   */
  public function DefaultMainTab(){
    $this->CustomDisplay("default_main_tab");
  }
}