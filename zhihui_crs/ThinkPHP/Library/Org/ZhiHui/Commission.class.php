<?php
/**
 * Created by PhpStorm.
 * User: 淼
 * Date: 2015/4/29
 * Time: 11:41
 */

namespace Org\ZhiHui;

class Commission {
  private $modCommission = null;
  private $modCommissionStandard = null;

  const ConstMaxRate = 0.1;
  const ConstSellMemberRate = 0.06;

  function __construct() {
    $this->modCommission = M("commission");
    $this->modCommissionStandard = M("commission_standard");
  }
  
  //region 缓存
  //region 佣金信息缓存
  /**
   * 获取佣金信息
   * @param $_commission_id 佣金ID
   * @return mixed
   */
  public function GetCommissionDetails($_commission_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetCommissionDetailsCache($_commission_id);

      if(empty($Info)){
        $Info = $this->GetCommissionDetailsDb($_commission_id);
      }
    }else{
      $Info = $this->GetCommissionDetailsDb($_commission_id);
    }

    return $Info;
  }

  /**
   * 设置佣金信息数据缓存
   * @param $_commission_id 佣金ID
   * @param array $_data 佣金信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetCommissionDetailsCache($_commission_id, $_data=NULL){
    F($this->GetCommissionDetailsCacheKey($_commission_id), $_data);
  }

  /**
   * 获取佣金信息数据缓存
   * @param $_commission_id 佣金ID
   * @return mixed
   */
  private function GetCommissionDetailsCache($_commission_id){
    return F($this->GetCommissionDetailsCacheKey($_commission_id));
  }

  /**
   * 获取佣金信息缓存Key
   * @param $_commission_id 佣金ID
   * @return mixed
   */
  private function GetCommissionDetailsCacheKey($_commission_id){
    return "Commission/Details/{$_commission_id}";
  }

  /**
   * todo:从数据库中获取佣金信息数据
   * @param $_commission_id 佣金ID
   *
   * @return mixed
   */
  private function GetCommissionDetailsDb($_commission_id){
    $CommissionDetails = array();

    if(!IsNum($_commission_id, false, false)){
      return $CommissionDetails;
    }

    $sWhere = "id={$_commission_id}";
    $CommissionDetails = $this->modCommission->where($sWhere)->find();

    if(!empty($CommissionDetails)){
      $this->SetCommissionDetailsCache($_commission_id, $CommissionDetails);
    }

    return $CommissionDetails;
  }
  //endregion 佣金信息缓存

  //region 提成标准信息缓存
  /**
   * 获取提成标准信息
   * @param $_standard_id 提成标准ID
   * @return mixed
   */
  public function GetCommissionStandardDetails($_standard_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetCommissionStandardDetailsCache($_standard_id);

      if(empty($Info)){
        $Info = $this->GetCommissionStandardDetailsDb($_standard_id);
      }
    }else{
      $Info = $this->GetCommissionStandardDetailsDb($_standard_id);
    }

    return $Info;
  }

  /**
   * 设置提成标准信息数据缓存
   * @param $_standard_id 提成标准ID
   * @param array $_data 提成标准信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetCommissionStandardDetailsCache($_standard_id, $_data=NULL){
    F($this->GetCommissionStandardDetailsCacheKey($_standard_id), $_data);
  }

  /**
   * 获取提成标准信息数据缓存
   * @param $_standard_id 提成标准ID
   * @return mixed
   */
  private function GetCommissionStandardDetailsCache($_standard_id){
    return F($this->GetCommissionStandardDetailsCacheKey($_standard_id));
  }

  /**
   * 获取提成标准信息缓存Key
   * @param $_standard_id 提成标准ID
   * @return mixed
   */
  private function GetCommissionStandardDetailsCacheKey($_standard_id){
    return "Commission/Standard/Details/{$_standard_id}";
  }

  /**
   * todo:从数据库中获取提成标准信息数据
   * @param $_standard_id 提成标准ID
   *
   * @return mixed
   */
  private function GetCommissionStandardDetailsDb($_standard_id){
    $CommissionStandardDetails = array();

    if(!IsNum($_standard_id, false, false)){
      return $CommissionStandardDetails;
    }

    $sWhere = "id={$_standard_id}";
    $CommissionStandardDetails = $this->modCommissionStandard->where($sWhere)->find();

    if(!empty($CommissionStandardDetails)){
      $this->SetCommissionStandardDetailsCache($_standard_id, $CommissionStandardDetails);
    }

    return $CommissionStandardDetails;
  }
  //endregion 提成标准信息缓存
  //endregion 缓存

  //region 佣金
  /**
   * todo: 获取佣金列表
   *
   * @param $_where_key 查询条件Key
   * @param $_where_val 查询条件Val
   *
   * @return array
   */
  public function CommissionList($_where_key, $_where_val){
    $sField = "id";
    $sWhere = "1=1";
    $sOrder = "year desc, month desc";

    switch($_where_key){
      case "seller_manager_id":
        if(IsNum($_where_val, false, false)){
          $sWhere .= " and seller_manager_id={$_where_val}";
        }
        break;
    }

    $CommissionIdList = $this->modCommission->field($sField)->where($sWhere)->order($sOrder)->select();
    $CommissionList = array();

    foreach($CommissionIdList as $key=>$val){
      $CommissionInfo = $this->GetCommissionDetails($val["id"]);

      array_push($CommissionList, $CommissionInfo);
    }

    return $CommissionList;
  }

  /**
   * todo: 获取最后一条佣金信息
   * @param $_broker_company_id 经纪公司ID
   */
  public function LastCommissionInfo($_broker_company_id){
    $sField = "id";
    $sWhere = "1=1";
    $sOrder = "add_time desc";
    
    if(IsNum($_broker_company_id, false, false)){
      $sWhere .= " and broker_company_id={$_broker_company_id}";
    }

    $CommissionID = $this->modCommission->where($sWhere)->order($sOrder)->getField($sField);
    $CommissionInfo = array();

    if(IsNum($CommissionID, false, false)){
      $CommissionInfo = $this->GetCommissionDetails($CommissionID);
    }

    return $CommissionInfo;
  }
  //endregion 佣金

  //region 提成标准
  /**
   * todo: 销售提成最大比例
   * @return float
   */
  public function CommissionTotalRate(){
    return self::ConstMaxRate;
  }

  /**
   * todo: 客户经理提成比例
   * @return float
   */
  public function CommissionSellerMemberRate(){
    return self::ConstSellMemberRate;
  }
  /**
   * todo: 团队长提成比例
   * @return float
   */
  public function CommissionSellerCaptainRate(){
    $nRate = (self::ConstMaxRate - self::ConstSellMemberRate);

    return $nRate;
  }
  
  /**
   * todo: 提成标准Option
   * @return array
   */
  public function CommissionStandardOption(){
    $sField = "id";
    $CommissionStandardIdList = $this->modCommissionStandard->field($sField)->select();
    $OptionList = array();

    foreach($CommissionStandardIdList as $key=>$val){
      $CommissionStandardInfo = $this->GetCommissionStandardDetails($val["id"]);

      $OptionInfo["key"] = $CommissionStandardInfo["standard_name"];
      $OptionInfo["val"] = $CommissionStandardInfo["id"];

      array_push($OptionList, $OptionInfo);
    }

    return $OptionList;
  }
  //endregion 提成标准

  //region 提成金额
  /**
   * todo: 客户经理提成比例
   * @return float
   */
  public function CalcCommissionAmount($_year_premium_amount, $_first_rate, $_last_rate){
    //客户经理佣金 =首年保费*客户经理首年佣金+次年保费*客户经理次年佣金
    $Amount = (($_year_premium_amount * $_first_rate) + ($_year_premium_amount * $_last_rate));
    return $Amount;
  }
  //endregion 提成金额
}