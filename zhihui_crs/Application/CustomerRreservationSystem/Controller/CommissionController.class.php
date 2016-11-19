<?php
namespace CustomerRreservationSystem\Controller;
use Think\Controller;

class CommissionController extends CommonController {
  private $modCommission = null;
  
  function __construct() {
    parent::__construct();

    $this->_Model = "佣金";

    $this->modCommission = D("Commission");
  }
  
  //region 佣金
  /**
   * todo: 佣金列表
   */
  public function CommissionList(){
    $SearchParam = $this->GetCommissionListSearchParam();

    if($this->_AccountType != $this->_AdminAccountType){
      $SearchParam["search_broker_company"] = $this->_AccountInfo["broker_company_id"];
    }

    $CommissionList = $this->modCommission->CommissionList($this->_PagingRowCount, $SearchParam);

    $this->assign("CommissionList", $CommissionList[0]["DataList"]);
    $this->assign("DateInfo", $CommissionList[0]["DateInfo"]);
    $this->assign("Page", $CommissionList["PageInfo"]);

    $this->CustomDisplay("commission_list");
  }

  /**
   * todo:保存佣金信息
   */
  public function CommissionInfo(){
    $nCommissionID = RR("commission_id");
    $CommissionInfo = $this->modCommission->CommissionInfo($nCommissionID);

    //region 经纪公司
    switch($this->_AccountType){
      case $this->_AdminAccountType:
        $BrokerCompanyOption = $this->modCommission->clsBrokerCompany->BrokerCompanyOption();
        $this->assign("BrokerCompanyOption", $BrokerCompanyOption);
        break;

      case $this->_BrokerCompanyAccountType:
        $BrokerCompanyInfo = $this->modCommission->clsBrokerCompany->GetBrokerCompanyDetails($this->_AccountInfo["broker_company_id"]);
        $this->assign("BrokerCompanyInfo", $BrokerCompanyInfo);

        $SellerManagerOption = $this->modCommission->clsSellerManager->SellerManagerOption($this->_AccountInfo["broker_company_id"]);
        $this->assign("SellerManagerOption", $SellerManagerOption);
        break;
    }
    //endregion 经纪公司

    //region 提成标准
    $CommissionStandardOption = $this->modCommission->clsCommission->CommissionStandardOption();
    $this->assign("CommissionStandardOption", $CommissionStandardOption);
    //endregion 提成标准

    //region 编辑时加载的数据
    if(IsArray($CommissionInfo)){
      //region 销售经理
      if(IsNum($CommissionInfo["broker_company_id"], false, false)){
        $SellerManagerOption = $this->modCommission->clsSellerManager->SellerManagerOption($CommissionInfo["broker_company_id"]);
        $this->assign("SellerManagerOption", $SellerManagerOption);
      }

      if(IsNum($CommissionInfo["seller_manager_id"], false, false)){
        $SellerManagerInfo = $this->modCommission->clsSellerManager->GetSellerManagerDetails($CommissionInfo["seller_manager_id"]);
        $this->assign("SellerManagerInfo", $SellerManagerInfo);
      }
      //endregion 销售经理

      //region 获取销售经理的佣金记录
      $SellerManagerCommissionList = $this->modCommission->GetSellerManageCommissionList($CommissionInfo["id"], $CommissionInfo["seller_manager_id"]);
      $this->assign("SellerManagerCommissionList", $SellerManagerCommissionList);
      //endregion 获取销售经理的佣金记录
    }
    //region 编辑时加载的数据

    //没有数据时默认的日期
    $sDefaultDate = date("Y")."-".(date("m")-1);
    
    //region 获取最后一次添加的佣金
    $LastCommissionInfo = $this->modCommission->clsCommission->LastCommissionInfo($this->_AccountInfo["broker_company_id"]);
    //endregion 获取最后一次添加的佣金

    $this->assign("CommissionInfo", $CommissionInfo);
    $this->assign("DefaultDate", $sDefaultDate);
    $this->assign("LastCommissionInfo", $LastCommissionInfo);

    $this->CustomDisplay("commission_info");
  }

  /**
   * todo: Ajax获取销售经理Option
   */
  public function AjaxSellerManagerOption(){
    $nBrokerCompanyID = RR("broker_company_id");

    $clsSellerManager = new \Org\ZhiHui\SellerManager();
    $Result = $clsSellerManager->SellerManagerOption($nBrokerCompanyID);

    AjaxReturnCorrect("", $Result);
  }

  /**
   * todo: 保存佣金信息
   */
  public function AjaxCommissionSave(){
    $nCommissionID = RR("commission_id");
    $nBrokerCompany= RR("broker_company");
    $nSellerManagerID = RR("seller_manager");
    $nCommissionDate = RR("commission_date");
    $nStandardID = RR("standard");
    $nRank = RR("rank");
    $nAchievements = RR("achievements");
    $nRake = RR("rake");
    $nBonus = RR("bonus");
    $nSubsidy = RR("subsidy");
    $nTax = RR("tax");
    $nOtherMinus = RR("other_minus");
    $nShouldPay = RR("should_pay");
    $nActualDelivery = RR("actual_delivery");
    $nStatus = RR("status");
    $sRemarks = RR("remarks");

    $SaveResult = $this->modCommission->CommissionSave($nCommissionID, $nBrokerCompany, $nSellerManagerID, $nCommissionDate, $nStandardID, $nRank, $nAchievements, $nRake, $nBonus, $nSubsidy, $nTax, $nOtherMinus, $nShouldPay, $nActualDelivery, $nStatus, $sRemarks);

    AjaxReturn($SaveResult);
  }

  /**
   * todo: Ajax获取佣金记录数据
   */
  public function AjaxCommissionInfo(){
    $nCommissionID = RR("commission_id");
    $CommissionInfo = $this->modCommission->CommissionInfo($nCommissionID);
    $Result = "";

    if(IsArray($CommissionInfo)){
      $Result = $this->modCommission->FmtCommissionData($CommissionInfo);
    }

    AjaxReturnCorrect("", $Result);
  }

  /**
   * todo:刷新佣金缓存
   */
  public function ResetCommissionCache(){
    $Result = $this->modCommission->ResetCommissionCache();

    AjaxReturn($Result);
  }

  /**
   * todo:获取佣金列表查询参数
   */
  private function GetCommissionListSearchParam(){
    $map = $this->SetCommissionListParamData();

    if(
      IsN($map["search_case"])
      && IsN($map["search_key"])
      && IsN($map["search_status"])
      && IsN($map["search_broker_company"])
    ){
      return array();
    }

    $this->assign("search_case", $map["search_case"]);
    $this->assign("search_key", $map["search_key"]);
    $this->assign("search_status", $map["search_status"]);
    $this->assign("search_broker_company", $map["search_broker_company"]);

    return $map;
  }

  private function SetCommissionListParamData(){
    $Param["search_case"] = RR("search_case"); //查询条件，对应数据库字段
    $Param["search_key"] = RR("search_key"); //关键字
    $Param["search_key"] = urldecode($Param["search_key"]);
    $Param["search_status"] = RR("search_status");
    $Param["search_broker_company"] = RR("search_broker_company");

    return $Param;
  }
  //endregion 佣金

  //region 提成标准
  public function AjaxStandardAdd(){
    $sStandardID = RR("standard_id");
    $sStandardName = RR("standard_name");

    $Result = $this->modCommission->CommissionStandardSave($sStandardID, $sStandardName);

    AjaxReturn($Result);
  }
  //endregion 提成标准
}