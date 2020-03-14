<?php
namespace CustomerRreservationSystem\Controller;

class CommissionController extends CommonController {
  private $modCommission = null;
  
  function __construct() {
    parent::__construct();

    $this->_Model = "佣金";

    $this->modCommission = D("Commission");
  }
  
  //region 佣金
  public function CommissionDateList(){
    $SearchParam = $this->GetCommissionListSearchParam();
    $CommissionList = $this->modCommission->CommissionList($this->_PagingRowCount, $SearchParam);

    $this->assign("CommissionList", $CommissionList["DataList"]);
    $this->assign("DateInfo", $CommissionList["DateInfo"]);
    $this->assign("Page", $CommissionList["PageInfo"]);

    $this->CustomDisplay("commission_date_list");
  }
  
  public function UserHistoryCommissionList(){
    $SearchParam = $this->GetHistoryCommissionListSearchParam();
    $CommissionList = $this->modCommission->HistoryCommissionList($this->_AccountID, $this->_PagingRowCount, $SearchParam);

    $this->assign("CommissionList", $CommissionList["DataList"]);
    $this->assign("Page", $CommissionList["PageInfo"]);

    $this->CustomDisplay("history_commission_date_list");
  }

  /**
   * todo: 计算上月提成金额
   */
  public function AjaxCalcLastMonthCommission(){
    $Result = $this->modCommission->CalcLastMonthCommission();

    AjaxReturn($Result);
  }

  /**
   * todo: 发放佣金
   */
  public function AjaxSendCommission(){
    $nCommissionID = RR("commission_id");
    $sRemark = RR("remark");

    $Result = $this->modCommission->SendCommission($nCommissionID, $sRemark);

    AjaxReturn($Result);
  }

  /**
   * todo:刷新佣金缓存
   */
  public function ResetCommissionCache(){
    $Result = $this->modCommission->ResetCommissionCache();

    AjaxReturn($Result);
  }

  /**
   * todo: 获取佣金来源的订单列表
   */
  public function AjaxCommissionOrderList(){
    $nCommissionID = RR("commission_id");

    if(IsNum($nCommissionID, false, false)){
      $Result = $this->modCommission->CommissionOrderList($nCommissionID);
    }else{
      $Result = $this->modCommission->CurrCommissionOrderList($this->_AccountID);
    }

    AjaxReturn($Result);
  }

  public function AjaxUserCurrMonthCommissionInfo(){
    $Result = $this->modCommission->CalcCurrMonthCommission($this->_AccountID);

    AjaxReturn($Result);
  }
  //endregion 佣金

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
  
  /**
   * todo:获取佣金列表查询参数
   */
  private function GetHistoryCommissionListSearchParam(){
    $map = $this->SetHistoryCommissionListParamData();
    
    if(
      IsN($map["serarch_begin_date"])
      && IsN($map["serarch_end_date"])
      && IsN($map["serarch_product_name"])
      && IsN($map["search_send_status"])
    ){
      return array();
    }
    
    $this->assign("serarch_begin_date", $map["serarch_begin_date"]);
    $this->assign("serarch_end_date", $map["serarch_end_date"]);
    $this->assign("serarch_product_name", $map["serarch_product_name"]);
    $this->assign("search_send_status", $map["search_send_status"]);
    
    return $map;
  }
  
  private function SetHistoryCommissionListParamData(){
    $Param["serarch_begin_date"] = RR("serarch_begin_date");
    $Param["serarch_end_date"] = RR("serarch_end_date");
    $Param["serarch_product_name"] = urldecode(RR("serarch_product_name"));
    $Param["search_send_status"] = RR("search_send_status");
    
    return $Param;
  }
}