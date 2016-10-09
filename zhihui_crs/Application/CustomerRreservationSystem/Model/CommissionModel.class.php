<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

class CommissionModel extends Model {
  Protected $autoCheckFields = false;

  private $modCommission = null;
  private $modCommissionStandard = null;

  public $clsCommission = null;
  public $clsBrokerCompany = null;
  public $clsSellerManager = null;

  function __construct() {
    $this->modCommission = M("commission");
    $this->modCommissionStandard = M("commission_standard");
    
    $this->clsCommission = new \Org\ZhiHui\Commission();
    $this->clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    $this->clsSellerManager = new \Org\ZhiHui\SellerManager();
  }
  
  //region 佣金
  /**
   * todo:佣金列表
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function CommissionList($_rownum=20, $_param=array()){
    //region 获取最后的年月
    $sField = "year, month";
    $sOrder = "year desc, month desc";
    $YearMonthInfo = $this->modCommission->field($sField)->order($sOrder)->find();
    //endregion 获取最后的年月
    
    $sField = "id";
    $sWhere = "1=1";

    if(IsArray($YearMonthInfo)){
      $sWhere  .= " and year={$YearMonthInfo["year"]}";
      $sWhere  .= " and month={$YearMonthInfo["month"]}";
    }

    $sOrder = "status asc, add_time desc";

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "login_name like '%{$_param["search_key"]}%'";
          $sWhere .= " or real_name like '%{$_param["search_key"]}%'";
          $sWhere .= " or phone like '%{$_param["search_key"]}%'";
          $sWhere .= " or qq like '%{$_param["search_key"]}%'";
          $sWhere .= " or wechat like '%{$_param["search_key"]}%'";

          if(IsNum($_param["search_key"], false, false)){
            $sWhere .= " or id = {$_param["search_key"]}";
          }

          $sWhere .= ")";
        }
      }else{
        switch($_param["search_case"]){
          case "id":
            if(IsNum($_param["search_key"], false, false)){
              $sWhere .= " and id = {$_param["search_key"]}";
            }
            break;

          case "login_name":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and login_name like '%{$_param["search_key"]}%'";
            }

            break;

          case "real_name":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and real_name like '%{$_param["search_key"]}%'";
            }
            break;

          case "phone":
            if(!IsN($_param["search_key"])){
              if(isMobile($_param["search_key"])){
                $sWhere .= " and phone like '%{$_param["search_key"]}%'";
              }
            }
            break;

          case "qq":
            if(IsNum($_param["search_key"], false, false)){
              $sWhere .= " and qq like '%{$_param["search_key"]}%'";
            }
            break;

          case "wechat":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and wechat like '%{$_param["search_key"]}%'";
            }
            break;
        }
      }

      if(IsNum($_param["search_status"], true, false)){
        if($_param["search_status"] == 0 || $_param["search_status"] == 1){
          $sWhere .= " and disable = {$_param["search_status"]}";
        }
      }

      if(IsNum($_param["search_broker_company"], false, false)){
        $sWhere .= " and broker_company_id = {$_param["search_broker_company"]}";
      }
    }

    $nTotal = $this->modCommission->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $CommissionIdList = $this->modCommission->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $CommissionList = array();

    foreach($CommissionIdList as $key=>$val){
      $CommissionInfo = $this->clsCommission->GetCommissionDetails($val["id"]);

      array_push($CommissionList, $this->FmtCommissionData($CommissionInfo));
    }

    $Result["DataList"] = $CommissionList;
    $Result["DateInfo"] = $YearMonthInfo["year"]."年".$YearMonthInfo["month"]."月";

    return array("PageInfo"=>$PageShow, $Result);
  }

  /**
   * todo: 佣金信息
   * @param $_commission_id 佣金ID
   *
   * @return array
   */
  public function CommissionInfo($_commission_id){
    if(!IsNum($_commission_id, false, false)){
      return array();
    }

    $CommissionInfo = $this->clsCommission->GetCommissionDetails($_commission_id);

    return $CommissionInfo;
  }

  /**
   * todo: 获取销售经理佣金记录列表
   *
   * @param $_seller_manage_id 销售经理ID
   *
   * @return array
   */
  public function GetSellerManageCommissionList($_commission_id, $_seller_manage_id){
    $CommissionList = $this->clsCommission->CommissionList('seller_manager_id', $_seller_manage_id);
    $Result = array();

    foreach($CommissionList as $key=>$val){
      if($val["id"] == $_commission_id){
        //continue;
      }

      $Info["id"] = $val["id"];
      $Info["year"] = $val["year"];
      $Info["month"] = $val["month"];
      $Info["achievements"] = $val["achievements"];

      //region 提成标准
      $Info["standard_name"] = "--";
      if(IsNum($val["standard_id"], false, false)){
        $CommissionStandardInfo = $this->clsCommission->GetCommissionStandardDetails($val["standard_id"]);
        $Info["standard_name"] = $CommissionStandardInfo["standard_name"];
      }
      //endregion 提成标准

      $Info["actual_delivery"] = $val["actual_delivery"];

      array_push($Result, $Info);
    }
    
    return $Result;
  }

  /**
   * todo: 保存佣金
   * @param $_commission_id 佣金ID
   * @param $_broker_company_id 经纪公司ID
   * @param $_seller_manager_id 销售经理ID
   * @param $_commission_date 佣金日期
   * @param $_standard_id 提成标准ID
   * @param $_rank 本月排名
   * @param $_achievements 本月业绩
   * @param $_rake 本月提成
   * @param $_bonus 本月奖金
   * @param $_subsidy 其它补贴
   * @param $_tax 扣税金额
   * @param $_other_minus 其它扣除
   * @param $_should_pay 应发工资
   * @param $_actual_delivery 实发工资
   * @param $_status 发放状态
   * @param $_remarks 备注信息
   *
   * @return array
   */
  public function CommissionSave($_commission_id, $_broker_company_id, $_seller_manager_id, $_commission_date, $_standard_id, $_rank, $_achievements, $_rake, $_bonus, $_subsidy, $_tax, $_other_minus, $_should_pay, $_actual_delivery, $_status, $_remarks){
    $CommissionInfo = array();

    //region 数据检查,并赋值保存的数据
    //region 检查佣金信息
    if(IsNum($_commission_id, false, false)){
      $CommissionInfo = $this->clsCommission->GetCommissionDetails($_commission_id);

      if(!IsArray($CommissionInfo)){
        return ReturnError(L("_COMMISSION_NOT_EXIST_"));
      }
    }
    //endregion 检查佣金信息

    //region 检查经纪公司
    if(!IsNum($_broker_company_id, false, false)){
      return ReturnError(L("_BROKER_COMPANY_NEED_SELECT_"));
    }else{
      $BrokerCompanyInfo = $this->clsBrokerCompany->GetBrokerCompanyDetails($_broker_company_id);

      if(!IsArray($BrokerCompanyInfo)){
        return ReturnError(L("_BROKER_COMPANY_NOT_EXIST_"));
      }

      $SaveData["broker_company_id"] = $_broker_company_id;
    }
    //endregion 检查经纪公司

    //region 检查佣金日期
    $CommissionDate = isDateTime($_commission_date);
    if($CommissionDate === false){
      return ReturnError(L("_COMMISSION_DATE_ERROR_"));
    }else{
      $SaveData["year"] = date("Y", $CommissionDate);
      $SaveData["month"] = date("m", $CommissionDate);
    }
    //endregion 检查佣金日期

    //region 检查销售经理信息
    if(IsNum($_seller_manager_id, false, false)){
      $SellerManagerInfo = $this->clsSellerManager->GetSellerManagerDetails($_seller_manager_id);

      if(!IsArray($SellerManagerInfo)){
        return ReturnError(L("_SELLER_MANAGER_NOT_EXIST_"));
      }

      $SaveData["seller_manager_id"] = $_seller_manager_id;
    }else{
      return ReturnError(L("_SELLER_MANAGER_ID_ERROR_"));
    }
    //endregion 检查销售经理信息

    //region 检查提成标准信息
    if(IsNum($_standard_id, false, false)){
      $CommissionStandardInfo = $this->clsCommission->GetCommissionStandardDetails($_standard_id);

      if(!IsArray($CommissionStandardInfo)){
        return ReturnError(L("_COMMISSION_STANDARD_NOT_EXIST_"));
      }

      $SaveData["standard_id"] = $_standard_id;
    }else{
      return ReturnError(L("_COMMISSION_STANDARD_ID_ERROR_"));
    }
    //endregion 检查提成标准信息

    //检查排名
    if(!IsNum($_rank, false, false)){
      return ReturnError(L("_COMMISSION_STANDARD_RANK_ERROR_"));
    }else{
      $SaveData["rank"] = $_rank;
    }

    //检查业绩
    if(!IsNum($_achievements, true, false)){
      return ReturnError(L("_COMMISSION_ACHIEVEMENTS_ERROR_"));
    }else{
      $SaveData["achievements"] = $_achievements;
    }

    //检查提成
    if(!IsNum($_rake, true, false)){
      return ReturnError(L("_COMMISSION_STANDARD_RANK_ERROR_"));
    }else{
      $SaveData["rake"] = $_rake;
    }

    //检查奖金
    if(!IsNum($_bonus, true, false)){
      return ReturnError(L("_COMMISSION_BONUS_ERROR_"));
    }else{
      $SaveData["bonus"] = $_bonus;
    }

    //检查补贴
    if(!IsNum($_subsidy, true, false)){
      return ReturnError(L("_COMMISSION_SUBSIDY_ERROR_"));
    }else{
      $SaveData["subsidy"] = $_subsidy;
    }

    //检查扣税
    if(!IsNum($_tax, true, false)){
      return ReturnError(L("_COMMISSION_TAX_ERROR_"));
    }else{
      $SaveData["tax"] = $_tax;
    }

    //检查其他扣除
    if(!IsNum($_other_minus, true, false)){
      return ReturnError(L("_COMMISSION_OTHER_MINUS_ERROR_"));
    }else{
      $SaveData["other_minus"] = $_other_minus;
    }

    //检查应发工资
    if(!IsNum($_should_pay, true, false)){
      return ReturnError(L("_COMMISSION_SHOULD_PAY_ERROR_"));
    }else{
      $SaveData["should_pay"] = $_should_pay;
    }

    //检查实发工资
    if(!IsNum($_actual_delivery, true, false)){
      return ReturnError(L("_COMMISSION_ACTUAL_DELIVERY_ERROR_"));
    }else{
      $SaveData["actual_delivery"] = $_actual_delivery;
    }

    //检查发放状态
    if($_status !=0 && $_status != 1){
      $SaveData["rank"] = 0;
    }else{
      $SaveData["rank"] = $_rank;
    }
    //endregion 数据检查,并赋值保存的数据

    $nTime = time();
    $SaveData["update_time"] = $nTime;
    
    if(!IsN($_remarks)){
      $SaveData["remarks"] = $_remarks;
    }
    
    if(IsArray($CommissionInfo)){
      $SaveData["id"] = $_commission_id;

      $Result = $this->modCommission->save($SaveData);

      if($Result !== false){
        $this->clsCommission->SetCommissionDetailsCache($_commission_id);
      }
    }else{
      $SaveData["add_time"] = $nTime;

      $Result = $this->modCommission->add($SaveData);

      $_commission_id = $Result;
    }

    if($Result === false){
      return ReturnCorrect(L("_SAVE_DATA_FAILURE_"));
    }

    $this->clsCommission->GetCommissionDetails($_commission_id);

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo: 刷新佣金缓存
   */
  public function ResetCommissionCache(){
    $sField = "id";
    $CommissionIdList = $this->modCommission->field($sField)->select();

    foreach($CommissionIdList as $key=>$val){
      $this->clsCommission->SetCommissionDetailsCache($val["id"]);
    }

    return ReturnCorrect(L("_CACHE_REFRESH_SUCCEED_"));
  }

  public function FmtCommissionData($_data){
    if(!IsArray($_data)){
      return $_data;
    }

    $_data["status_style"] = $this->FmtStatusStyle("status", $_data["status"]);

    //region 销售经理
    $_data["seller_manager_name"] = "--";
    if(IsNum($_data["seller_manager_id"], false, false)){
      $SellerManagerInfo = $this->clsSellerManager->GetSellerManagerDetails($_data["seller_manager_id"]);

      $_data["seller_manager_name"] = $SellerManagerInfo["real_name"];
      $_data["base_salary"] = $SellerManagerInfo["base_salary"];
      $_data["region"] = $SellerManagerInfo["province_name"] . "-" . $SellerManagerInfo["city_name"];
    }
    //endregion 销售经理

    //region 提成标准
    $_data["standard_name"] = "--";
    if(IsNum($_data["standard_id"], false, false)){
      $CommissionStandardInfo = $this->clsCommission->GetCommissionStandardDetails($_data["standard_id"]);
      $_data["standard_name"] = $CommissionStandardInfo["standard_name"];
    }
    //endregion 提成标准

    return $_data;
  }

  /**
   * 格式化状态样式
   * @return array
   */
  private function FmtStatusStyle($_field, $_val){
    if($_field == 'status'){
      switch($_val){
        case "0" :
          return FmtValueStyle(4, "未发放");
          break;

        case "1" :
          return FmtValueStyle(1, "已发放");
          break;
      }
    }
  }
  //endregion 佣金
  
  //region 提成标准
  /**
   * todo: 保存提成标准
   * @param $_standard_id
   * @param $_standard_name
   */
  public function CommissionStandardSave($_standard_id, $_standard_name){
    $CommissionStandardInfo = array();
    
    if(IsNum($_standard_id, false, false)){
      $CommissionStandardInfo = $this->clsCommission->GetCommissionStandardDetails($_standard_id);
    }

    if(IsN($_standard_name)){
      return ReturnError(L("_COMMISSION_STANDARD_NAME_NULL_"));
    }else{
      $sWhere = "standard_name='{$_standard_name}'";

      if(IsArray($CommissionStandardInfo)){
        $sWhere .= " and id!={$CommissionStandardInfo["id"]}";
      }

      $nCommissionStandardCount = $this->modCommissionStandard->where($sWhere)->count();

      if(IsNum($nCommissionStandardCount, false, false)){
        return ReturnError(L("_COMMISSION_STANDARD_NAME_EXIST_"));
      }
    }

    $SaveData["standard_name"] = $_standard_name;

    if(IsArray($CommissionStandardInfo)){
      $SaveData["id"] = $_standard_id;
      $Result = $this->modCommissionStandard->save($SaveData);

      if($Result !== false){
        $this->clsCommission->SetCommissionStandardDetailsCache($_standard_id);
      }
    }else{
      $Result = $this->modCommissionStandard->add($SaveData);
      $_standard_id = $Result;
    }

    if($Result === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }

    $CommissionStandardInfo = $this->clsCommission->GetCommissionStandardDetails($_standard_id);

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"), $CommissionStandardInfo);
  }
  //endregion 提成标准
}
