<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

class CommissionModel extends Model {
  Protected $autoCheckFields = false;

  private $modCommission = null;
  private $modCommissionDetail = null;
  private $modCommissionStandard = null;
  private $modOrder = null;

  public $clsCommission = null;
  public $clsOrder = null;
  public $clsUser = null;
  public $clsProduct = null;

  function __construct() {
    $this->modCommission = M("commission");
    $this->modCommissionDetail = M("commission_detail");
    $this->modCommissionStandard = M("commission_standard");
    $this->modOrder = M("order");
    
    $this->clsCommission = new \Org\ZhiHui\Commission();
    $this->clsOrder = new \Org\ZhiHui\Order();
    $this->clsUser = new \Org\ZhiHui\User();
    $this->clsProduct = new \Org\ZhiHui\Product();
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
    $LastDateInfo = $this->GetLastDate();
    $DateInfo["Year"] = $LastDateInfo[0];
    $DateInfo["Month"] = $LastDateInfo[1];

    $sField = "id, (rake+member_rake) as total";
    $sWhere = "year={$DateInfo["Year"]}";
    $sWhere .= " and month={$DateInfo["Month"]}";
    $sOrder = "achievements desc, total desc, status asc, add_time desc";

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

    return array("PageInfo"=>$PageShow, "DataList"=>$CommissionList, "DateInfo"=>$DateInfo);
  }

  public function HistoryCommissionList($_user_id, $_rownum=20, $_param=array()){
    if(!IsNum($_user_id, false, false)){
      return array("PageInfo"=>array(), "DataList"=>array());
    }

    $sField = "cd.commission_id, cd.order_sn, cd.status";
    $sJoin = "";
    $sWhere = "cd.user_id={$_user_id}";
    $sOrder = "cd.year desc, cd.month asc, cd.order_time desc";

    if(!empty($_param)){
      //region 组合日期
      $nBeginTime = isDateTime($_param["serarch_begin_date"]." 00:00:00");
      $nEndTime = isDateTime($_param["serarch_end_date"]." 23:59:59");
      
      if($nBeginTime !== false && $nEndTime !== false){
        if($nBeginTime > $nEndTime){
          $nTemp = $nEndTime;
          
          $nEndTime =$nBeginTime;
          $nBeginTime = $nTemp;
        }
        
        $sWhere .= " and cd.order_time>={$nBeginTime} and cd.order_time<={$nEndTime}";
      }else{
        if($nBeginTime !== false){
          $sWhere .= " and cd.order_time>={$nBeginTime}";
        }
  
        if($nEndTime !== false){
          $sWhere .= " and cd.order_time<={$nEndTime}";
        }
      }
      //endregion 组合日期
      
      //region 组合名称
      if(!IsN($_param["serarch_product_name"])){
        $sJoin .= " left join ". tn("order") ." as o on o.order_sn=cd.order_sn";
        $sJoin .= " left join ". tn("product_snapshots") ." as ps on ps.id=cd.product_snapshots_id";
  
        $sWhere .= " and ps.product_name like '%{$_param["serarch_product_name"]}%'";
      }
      //endregion 组合名称
      
      if(IsNum($_param["search_send_status"], true, false)){
        if($_param["search_send_status"] == 0 || $_param["search_send_status"] == 1){
          $sWhere .= " and cd.status = {$_param["search_send_status"]}";
        }
      }
    }

    $modCommission = M("commission_detail as cd");
    $nTotal = $modCommission->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $CommissionIdList = $modCommission->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $CommissionList = array();

    
    foreach($CommissionIdList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);
      $ProductSnapshotsInfo = $this->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
      
      $Info["order_sn"] = $OrderInfo["order_sn"];
      $Info["product_type_name"] = $ProductSnapshotsInfo["type_name"];
      $Info["product_name"] = $ProductSnapshotsInfo["product_name"];
      $Info["customer_name"] = $OrderInfo["customer"]["real_name"];
      $Info["pay_date"] = $OrderInfo["pay_date"];
      $Info["order_amoount"] = $OrderInfo["year_premium_amount"];
      $Info["payment_years"] = $OrderInfo["payment_years"];
      $Info["status"] = $val["status"];
      $Info["status_style"] = $this->FmtStatusStyle("status", $Info["status"]);

      array_push($CommissionList, $Info);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$CommissionList);
  }

  /**
   * todo: 计算上个月的提成
   * @return array
   */
  public function CalcLastMonthCommission(){
    $LastDateInfo = $this->GetLastDate();
    $DateInfo["Year"] = $LastDateInfo[0];
    $DateInfo["Month"] = $LastDateInfo[1];

    $nBeginTime = strtotime($LastDateInfo[2]." 00:00:00");
    $nEndTime = strtotime($LastDateInfo[3]." 23:59:59");

    //region 检查佣金数据是否存在
    $sWhere = "year={$LastDateInfo[0]} and month={$LastDateInfo[1]}";
    $nDataCount = $this->modCommission->where($sWhere)->count();

    if($nDataCount > 0){
      return ReturnError(L("上月佣金提成数据已存在"));
    }
    //endregion 检查佣金数据是否存在

    //region 统计上月预约
    $sField = "`order_sn`";
    $sWhere = "`status`>={$this->clsOrder->OrderStatusPayStatus()}";
    $sWhere .= " and `pay_time`>={$nBeginTime}";
    $sWhere .= " and `pay_time`<={$nEndTime}";

    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->select();
    //endregion 统计上月预约

    //region 创建佣金数据
    $CommissionData = array();
    $CommissionDetailData = array();
    
    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);
      $ProductSnapshotsInfo = $this->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);

      //region 佣金数据
      if(IsNum($OrderInfo["seller_member_id"], false, false)){
        //region 客户经理佣金数据
        $nUserID = $OrderInfo["seller_member_id"];

        $SellerMemberAmount = $this->clsCommission->CalcCommissionAmount($OrderInfo["year_premium_amount"], $ProductSnapshotsInfo["member_first_rate"], $ProductSnapshotsInfo["member_next_rate"]);

        if(IsArray($CommissionData["u_{$nUserID}"])){
          $CommissionData["u_{$nUserID}"]["achievements"] += $OrderInfo["year_premium_amount"];
          $CommissionData["u_{$nUserID}"]["rake"] += $SellerMemberAmount;
  
          //region 明细
          $CommissionDetail["commission_id"] = 0;
          $CommissionDetail["order_sn"] = $OrderInfo["order_sn"];
          $CommissionDetail["user_id"] = $nUserID;
          $CommissionDetail["year"] = $DateInfo["Year"];
          $CommissionDetail["month"] = $DateInfo["Month"];
          $CommissionDetail["status"] = 0;
          $CommissionDetail["order_time"] = $OrderInfo["pay_time"];
          $CommissionDetail["update_time"] = time();
  
          array_push($CommissionDetailData, $CommissionDetail);
          //endregion 明细
        }else{
          $CommissionData["u_{$nUserID}"]["user_id"] = $nUserID;
          $CommissionData["u_{$nUserID}"]["year"] = $DateInfo["Year"];
          $CommissionData["u_{$nUserID}"]["month"] = $DateInfo["Month"];
          $CommissionData["u_{$nUserID}"]["achievements"] += $OrderInfo["year_premium_amount"];
          $CommissionData["u_{$nUserID}"]["rake"] = $SellerMemberAmount;
          $CommissionData["u_{$nUserID}"]["member_rake"] = 0;
          $CommissionData["u_{$nUserID}"]["status"] = 0;
          $CommissionData["u_{$nUserID}"]["remarks"] = "";
          $CommissionData["u_{$nUserID}"]["add_time"] = time();
          $CommissionData["u_{$nUserID}"]["update_time"] = time();
  
          //region 明细
          $CommissionDetail["commission_id"] = 0;
          $CommissionDetail["order_sn"] = $OrderInfo["order_sn"];
          $CommissionDetail["user_id"] = $nUserID;
          $CommissionDetail["year"] = $DateInfo["Year"];
          $CommissionDetail["month"] = $DateInfo["Month"];
          $CommissionDetail["status"] = 0;
          $CommissionDetail["order_time"] = $OrderInfo["pay_time"];
          $CommissionDetail["update_time"] = time();
  
          array_push($CommissionDetailData, $CommissionDetail);
          //endregion 明细
        }
        //endregion 客户经理佣金数据

        //region 团队长的下级分成佣金数据
        $nUserID = $OrderInfo["seller_captain_id"];
        $SellerCaptainAmount = $this->clsCommission->CalcCommissionAmount($OrderInfo["year_premium_amount"], $ProductSnapshotsInfo["captain_first_rate"], $ProductSnapshotsInfo["captain_next_rate"]);
        
        $RebateAmount = $SellerCaptainAmount - $SellerMemberAmount;

        if(IsArray($CommissionData["u_{$nUserID}"])){
          $CommissionData["u_{$nUserID}"]["achievements"] += 0;
          $CommissionData["u_{$nUserID}"]["member_rake"] += $RebateAmount;
  
          //region 明细
          $CommissionDetail["commission_id"] = 0;
          $CommissionDetail["order_sn"] = $OrderInfo["order_sn"];
          $CommissionDetail["user_id"] = $nUserID;
          $CommissionDetail["year"] = $DateInfo["Year"];
          $CommissionDetail["month"] = $DateInfo["Month"];
          $CommissionDetail["status"] = 0;
          $CommissionDetail["order_time"] = $OrderInfo["pay_time"];
          $CommissionDetail["update_time"] = time();
  
          array_push($CommissionDetailData, $CommissionDetail);
          //endregion 明细
        }else{
          $CommissionData["u_{$nUserID}"]["user_id"] = $nUserID;
          $CommissionData["u_{$nUserID}"]["year"] = $DateInfo["Year"];
          $CommissionData["u_{$nUserID}"]["month"] = $DateInfo["Month"];
          $CommissionData["u_{$nUserID}"]["achievements"] += 0;
          $CommissionData["u_{$nUserID}"]["rake"] = 0;
          $CommissionData["u_{$nUserID}"]["member_rake"] = $RebateAmount;
          $CommissionData["u_{$nUserID}"]["status"] = 0;
          $CommissionData["u_{$nUserID}"]["remarks"] = "";
          $CommissionData["u_{$nUserID}"]["add_time"] = time();
          $CommissionData["u_{$nUserID}"]["update_time"] = time();
  
          //region 明细
          $CommissionDetail["commission_id"] = 0;
          $CommissionDetail["order_sn"] = $OrderInfo["order_sn"];
          $CommissionDetail["user_id"] = $nUserID;
          $CommissionDetail["year"] = $DateInfo["Year"];
          $CommissionDetail["month"] = $DateInfo["Month"];
          $CommissionDetail["status"] = 0;
          $CommissionDetail["order_time"] = $OrderInfo["pay_time"];
          $CommissionDetail["update_time"] = time();
  
          array_push($CommissionDetailData, $CommissionDetail);
          //endregion 明细
        }
        //endregion 团队长的下级分成佣金数据
      }else{
        $nUserID = $OrderInfo["seller_captain_id"];

        $SellerCaptainAmount = $this->clsCommission->CalcCommissionAmount($OrderInfo["year_premium_amount"], $ProductSnapshotsInfo["captain_first_rate"], $ProductSnapshotsInfo["captain_next_rate"]);

        if(IsArray($CommissionData["u_{$nUserID}"])){
          $CommissionData["u_{$nUserID}"]["achievements"] += $OrderInfo["year_premium_amount"];
          $CommissionData["u_{$nUserID}"]["rake"] += $SellerCaptainAmount;
  
          //region 明细
          $CommissionDetail["commission_id"] = 0;
          $CommissionDetail["order_sn"] = $OrderInfo["order_sn"];
          $CommissionDetail["user_id"] = $nUserID;
          $CommissionDetail["year"] = $DateInfo["Year"];
          $CommissionDetail["month"] = $DateInfo["Month"];
          $CommissionDetail["status"] = 0;
          $CommissionDetail["order_time"] = $OrderInfo["pay_time"];
          $CommissionDetail["update_time"] = time();
  
          array_push($CommissionDetailData, $CommissionDetail);
          //endregion 明细
        }else{
          $CommissionData["u_{$nUserID}"]["user_id"] = $nUserID;
          $CommissionData["u_{$nUserID}"]["year"] = $DateInfo["Year"];
          $CommissionData["u_{$nUserID}"]["month"] = $DateInfo["Month"];
          $CommissionData["u_{$nUserID}"]["achievements"] += $OrderInfo["year_premium_amount"];
          $CommissionData["u_{$nUserID}"]["rake"] = $SellerCaptainAmount;
          $CommissionData["u_{$nUserID}"]["member_rake"] = 0;
          $CommissionData["u_{$nUserID}"]["status"] = 0;
          $CommissionData["u_{$nUserID}"]["remarks"] = "";
          $CommissionData["u_{$nUserID}"]["add_time"] = time();
          $CommissionData["u_{$nUserID}"]["update_time"] = time();
  
          //region 明细
          $CommissionDetail["commission_id"] = 0;
          $CommissionDetail["order_sn"] = $OrderInfo["order_sn"];
          $CommissionDetail["user_id"] = $nUserID;
          $CommissionDetail["year"] = $DateInfo["Year"];
          $CommissionDetail["month"] = $DateInfo["Month"];
          $CommissionDetail["status"] = 0;
          $CommissionDetail["order_time"] = $OrderInfo["pay_time"];
          $CommissionDetail["update_time"] = time();
  
          array_push($CommissionDetailData, $CommissionDetail);
          //endregion 明细
        }
      }
      //endregion 佣金数据
    }
    //endregion 创建佣金数据
    
    
    if(!IsArray($CommissionDetailData)){
      return ReturnError("没有可添加的佣金明细数据");
    }
  
    $model = M();
    $model->startTrans();
  
    //region 添加佣金明细数据
    $AddCommissionDetailResult = $model->table(tn("commission_detail"))->addAll($CommissionDetailData);
    //endregion 添加佣金明细数据
    
    //region 添加佣金数据并更新佣金明细
    foreach($CommissionData as $key=>$val){
      $AddCommissionResult = $model->table(tn("commission"))->add($val);
  
      if($AddCommissionResult === false){
        break;
      }
  
      $SaveData = array();
      
      $SaveData["commission_id"] = $AddCommissionResult;
      $sWhere = "user_id={$val["user_id"]}";
      $sWhere .= " and year={$val["year"]}";
      $sWhere .= " and month={$val["month"]}";
      $sWhere .= " and status=0";
  
      $SaveCommissionDetailResult = $model->table(tn("commission_detail"))->where($sWhere)->save($SaveData);
  
      if($SaveCommissionDetailResult === false){
        break;
      }
    }
    //endregion 添加佣金数据
    
    if($AddCommissionDetailResult === false || $AddCommissionResult === false || $SaveCommissionDetailResult === false){
      $model->rollback();
  
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }else{
      $model->commit();
  
      return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
    }
  }

  /**
   * todo: 计算当月的提成
   * @return array
   */
  public function CalcCurrMonthCommission($_user_id){
    $CommissionData = array();

    if(!IsNum($_user_id, false, false)){
      return ReturnCorrect("", $CommissionData);
    }

    $LastDateInfo = $this->GetCurrDate(time());
    $DateInfo["Year"] = $LastDateInfo[0];
    $DateInfo["Month"] = $LastDateInfo[1];

    $nBeginTime = strtotime($LastDateInfo[2]." 00:00:00");
    $nEndTime = strtotime($LastDateInfo[3]." 23:59:59");

    //region 统计当月月预约
    $sField = "`order_sn`";
    $sWhere = "`status`>={$this->clsOrder->OrderStatusPayStatus()}";
    $sWhere .= " and (`seller_captain_id`={$_user_id} or `seller_member_id`={$_user_id})";
    $sWhere .= " and `pay_time`>={$nBeginTime}";
    $sWhere .= " and `pay_time`<={$nEndTime}";

    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->select();
    //endregion 统计当月月预约

    //region 创建佣金数据
    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);
      $ProductSnapshotsInfo = $this->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);

      if(IsNum($OrderInfo["seller_member_id"], false, false)){
        //region 客户经理佣金数据
        $SellerMemberAmount = $this->clsCommission->CalcCommissionAmount($OrderInfo["year_premium_amount"], $ProductSnapshotsInfo["member_first_rate"], $ProductSnapshotsInfo["member_next_rate"]);

        if(IsArray($CommissionData)){
          $CommissionData["achievements"] += $OrderInfo["year_premium_amount"];
          $CommissionData["rake"] += $SellerMemberAmount;
        }else{
          $CommissionData["year"] = $DateInfo["Year"];
          $CommissionData["month"] = $DateInfo["Month"];
          $CommissionData["achievements"] += $OrderInfo["year_premium_amount"];
          $CommissionData["rake"] = $SellerMemberAmount;
          $CommissionData["member_rake"] = 0;
        }
        //endregion 客户经理佣金数据

        //region 团队长的下级分成佣金数据
        $SellerCaptainAmount = $this->clsCommission->CalcCommissionAmount($OrderInfo["year_premium_amount"], $ProductSnapshotsInfo["captain_first_rate"], $ProductSnapshotsInfo["captain_next_rate"]);

        $RebateAmount = $SellerCaptainAmount - $SellerMemberAmount;

        if(IsArray($CommissionData)){
          $CommissionData["achievements"] += 0;
          $CommissionData["member_rake"] += $RebateAmount;
        }else{
          $CommissionData["year"] = $DateInfo["Year"];
          $CommissionData["month"] = $DateInfo["Month"];
          $CommissionData["achievements"] += 0;
          $CommissionData["rake"] = 0;
          $CommissionData["member_rake"] = $RebateAmount;
        }
        //endregion 团队长的下级分成佣金数据
      }else{
        $SellerCaptainAmount = $this->clsCommission->CalcCommissionAmount($OrderInfo["year_premium_amount"], $ProductSnapshotsInfo["captain_first_rate"], $ProductSnapshotsInfo["captain_next_rate"]);

        if(IsArray($CommissionData)){
          $CommissionData["achievements"] += $OrderInfo["year_premium_amount"];
          $CommissionData["rake"] += $SellerCaptainAmount;
        }else{
          $CommissionData["year"] = $DateInfo["Year"];
          $CommissionData["month"] = $DateInfo["Month"];
          $CommissionData["achievements"] += $OrderInfo["year_premium_amount"];
          $CommissionData["rake"] = $SellerCaptainAmount;
          $CommissionData["member_rake"] = 0;
        }
      }
    }
    //endregion 创建佣金数据

    return ReturnCorrect("", $CommissionData);
  }
  
  /**
   * todo: 计算上个月的提成
   * @return array
   */
  public function CalcLastMonthCommissionBak(){
    $LastDateInfo = $this->GetLastDate();
    $DateInfo["Year"] = $LastDateInfo[0];
    $DateInfo["Month"] = $LastDateInfo[1];

    $nBeginTime = strtotime($LastDateInfo[2]." 00:00:00");
    $nEndTime = strtotime($LastDateInfo[3]." 23:59:59");

    //region 检查佣金数据是否存在
    $sWhere = "year={$LastDateInfo[0]} and month={$LastDateInfo[1]}";
    $nDataCount = $this->modCommission->where($sWhere)->count();

    if($nDataCount > 0){
      return ReturnError(L("上月佣金提成数据已存在"));
    }
    //endregion 检查佣金数据是否存在

    //region 统计10月预约
    //region 统计客户经理
    $sField = "`seller_member_id`";
    $sField .= ", `seller_captain_id`";
    $sField .= ", sum(`pay_amount`) as `sell_amount`";
    $sWhere = "(`status`={$this->clsOrder->OrderStatusPayStatus()} or `status`={$this->clsOrder->OrderStatusReturnVisitStatus()})";
    $sWhere .= " and `pay_time`>={$nBeginTime}";
    $sWhere .= " and `pay_time`<={$nEndTime}";
    $sWhere .= " and `seller_member_id`>0";
    $sGroupBy = "`seller_member_id`";

    $SellerMemberSellAmountList = $this->modOrder->field($sField)->where($sWhere)->group($sGroupBy)->select();
    //endregion 统计客户经理

    //region 统计团队长
    $sField = "`seller_captain_id`";
    $sField .= ", sum(`pay_amount`) as `sell_amount`";
    $sWhere = "(`status`={$this->clsOrder->OrderStatusPayStatus()} or `status`={$this->clsOrder->OrderStatusReturnVisitStatus()})";
    $sWhere .= " and `pay_time`>={$nBeginTime}";
    $sWhere .= " and `pay_time`<={$nEndTime}";
    $sWhere .= " and `seller_member_id`=0";
    $sGroupBy = "`seller_captain_id`";

    $SellerCaptainSellAmountList = $this->modOrder->field($sField)->where($sWhere)->group($sGroupBy)->select();
    //endregion 统计团队长
    //endregion 统计10月预约

    //region 创建佣金数据
    //region 客户经理
    $CommissionData = array();
    foreach($SellerMemberSellAmountList as $key=>$val){
      $nRake = ($val["sell_amount"] * $this->clsCommission->CommissionSellerMemberRate());

      $CommissionData["u_{$val["seller_member_id"]}"]["user_id"] = $val["seller_member_id"];
      $CommissionData["u_{$val["seller_member_id"]}"]["year"] = $DateInfo["Year"];
      $CommissionData["u_{$val["seller_member_id"]}"]["month"] = $DateInfo["Month"];
      $CommissionData["u_{$val["seller_member_id"]}"]["achievements"] = $val["sell_amount"];
      $CommissionData["u_{$val["seller_member_id"]}"]["rake"] = $nRake;
      $CommissionData["u_{$val["seller_member_id"]}"]["member_rake"] = 0;
      $CommissionData["u_{$val["seller_member_id"]}"]["status"] = 0;
      $CommissionData["u_{$val["seller_member_id"]}"]["remarks"] = "";
      $CommissionData["u_{$val["seller_member_id"]}"]["add_time"] = time();
      $CommissionData["u_{$val["seller_member_id"]}"]["update_time"] = time();

      //region 队长提成
      if(IsArray($CommissionData["u_{$val["seller_captain_id"]}"])){
        $nRake = ($val["sell_amount"] * $this->clsCommission->CommissionSellerCaptainRate());

        $CommissionData["u_{$val["seller_captain_id"]}"]["member_rake"] += $nRake;
      }else{
        $nRake = ($val["sell_amount"] * $this->clsCommission->CommissionSellerCaptainRate());

        $CommissionData["u_{$val["seller_captain_id"]}"]["user_id"] = $val["seller_captain_id"];
        $CommissionData["u_{$val["seller_captain_id"]}"]["year"] = $DateInfo["Year"];
        $CommissionData["u_{$val["seller_captain_id"]}"]["month"] = $DateInfo["Month"];
        $CommissionData["u_{$val["seller_captain_id"]}"]["achievements"] = 0;
        $CommissionData["u_{$val["seller_captain_id"]}"]["rake"] = 0;
        $CommissionData["u_{$val["seller_captain_id"]}"]["member_rake"] = $nRake;
        $CommissionData["u_{$val["seller_captain_id"]}"]["status"] = 0;
        $CommissionData["u_{$val["seller_captain_id"]}"]["remarks"] = "";
        $CommissionData["u_{$val["seller_captain_id"]}"]["add_time"] = time();
        $CommissionData["u_{$val["seller_captain_id"]}"]["update_time"] = time();
      }
      //endregion 队长提成
    }
    //endregion 客户经理

    //region 团队长
    foreach($SellerCaptainSellAmountList as $key=>$val){
      $nRake = ($val["sell_amount"] * $this->clsCommission->CommissionTotalRate());

      if(IsArray($CommissionData["u_{$val["seller_captain_id"]}"])){
        $CommissionData["u_{$val["seller_captain_id"]}"]["rake"] += $nRake;
        $CommissionData["u_{$val["seller_captain_id"]}"]["achievements"] += $val["sell_amount"];
      }else{
        $CommissionData["u_{$val["seller_captain_id"]}"]["user_id"] = $val["seller_captain_id"];
        $CommissionData["u_{$val["seller_captain_id"]}"]["year"] = $DateInfo["Year"];
        $CommissionData["u_{$val["seller_captain_id"]}"]["month"] = $DateInfo["Month"];
        $CommissionData["u_{$val["seller_captain_id"]}"]["achievements"] = $val["sell_amount"];
        $CommissionData["u_{$val["seller_captain_id"]}"]["rake"] = $nRake;
        $CommissionData["u_{$val["seller_captain_id"]}"]["member_rake"] = 0;
        $CommissionData["u_{$val["seller_captain_id"]}"]["status"] = 0;
        $CommissionData["u_{$val["seller_captain_id"]}"]["remarks"] = "";
        $CommissionData["u_{$val["seller_captain_id"]}"]["add_time"] = time();
        $CommissionData["u_{$val["seller_captain_id"]}"]["update_time"] = time();
      }
    }
    //endregion 团队长
    //endregion 创建佣金数据

    $AddData = array();
    foreach($CommissionData as $key=>$val){
      array_push($AddData, $val);
    }

    if(!IsArray($AddData)){
      return ReturnError("没有可添加的佣金提成数据");
    }

    $Result = $this->modCommission->addAll($AddData);

    if($Result === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }
  
  public function SendCommission($_commission_id, $_remark){
    if(!IsNum($_commission_id, false, false)){
      return ReturnError(L("_COMMISSION_ID_ERROR_"));
    }else{
      $CommissionInfo = $this->clsCommission->GetCommissionDetails($_commission_id);

      if(!IsArray($CommissionInfo)){
        return ReturnError(L("_COMMISSION_NOT_EXIST_"));
      }
    }
    
    $SaveData["id"] = $CommissionInfo["id"];
    $SaveData["status"] = 1;
    $SaveData["update_time"] = time();
    
    if(!IsN($_remark)){
      $SaveData["remarks"] = $_remark;
    }

    $Result = $this->modCommission->save($SaveData);

    if($Result === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    
    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo:佣金列表
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function CommissionListBak($_rownum=20, $_param=array()){
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
   * @param $_commission_id
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

  /**
   * todo: 获取上月佣金明细
   * @param $_commission_id
   *
   * @return array
   */
  public function CommissionOrderList($_commission_id){
    if(!IsNum($_commission_id, false, false)){
      return ReturnError(L("_COMMISSION_ID_ERROR_"));
    }else{
      $CommissionInfo = $this->clsCommission->GetCommissionDetails($_commission_id);

      if(!IsArray($CommissionInfo)){
        return ReturnError(L("_COMMISSION_NOT_EXIST_"));
      }
    }

    $LastDateInfo = $this->GetLastDate($CommissionInfo["add_time"]);
    $nBeginTime = strtotime($LastDateInfo[2]." 00:00:00");
    $nEndTime = strtotime($LastDateInfo[3]." 23:59:59");

    $sField = "order_sn";
    $sWhere = "(seller_captain_id={$CommissionInfo["user_id"]} or seller_member_id={$CommissionInfo["user_id"]})";
    $sWhere .= " and `pay_time`>={$nBeginTime}";
    $sWhere .= " and `pay_time`<={$nEndTime}";

    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->select();
    $clsUser = new \Org\ZhiHui\User();

    $Result["achievements"] = $CommissionInfo["achievements"];
    $Result["rake"] = $CommissionInfo["rake"]+$CommissionInfo["member_rake"];
    //$Result["member_rake"] = $CommissionInfo["member_rake"];
    //$Result["total_rake"] = ($Result["rake"]+$Result["member_rake"]);
    $Result["order_list"] = array();
    
    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);
      $ProductSnapshotsInfo = $this->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);

      $Info["order_sn"] = $OrderInfo["order_sn"];
      $Info["product_type_name"] = $ProductSnapshotsInfo["type_name"];
      $Info["product_name"] = $ProductSnapshotsInfo["product_name"];
      $Info["year_premium_amount"] = $OrderInfo["year_premium_amount"];
      
      if($OrderInfo["seller_captain_id"] == $CommissionInfo["user_id"]){
        if(IsNum($OrderInfo["seller_member_id"], false, false)){
          $UserInfo = $clsUser->GetUserDetails($OrderInfo["seller_member_id"]);
          $Info["user_name"] = $UserInfo["real_name"];

          $Info["first_rate"] = $ProductSnapshotsInfo["captain_first_rate"]-$ProductSnapshotsInfo["member_first_rate"];
          $Info["next_rate"] = $ProductSnapshotsInfo["captain_next_rate"]-$ProductSnapshotsInfo["member_next_rate"];
        }else{
          $UserInfo = $clsUser->GetUserDetails($OrderInfo["seller_captain_id"]);
          $Info["user_name"] = $UserInfo["real_name"];
          $Info["first_rate"] = $ProductSnapshotsInfo["captain_first_rate"];
          $Info["next_rate"] = $ProductSnapshotsInfo["captain_next_rate"];
        }
      }else{
        $UserInfo = $clsUser->GetUserDetails($OrderInfo["seller_member_id"]);
        $Info["user_name"] = $UserInfo["real_name"];
        $Info["first_rate"] = $ProductSnapshotsInfo["member_first_rate"];
        $Info["next_rate"] = $ProductSnapshotsInfo["member_next_rate"];
      }

      $Info["first_amount"] = ($OrderInfo["year_premium_amount"]*$Info["first_rate"]);
      $Info["next_amount"] = ($OrderInfo["year_premium_amount"]*$Info["next_rate"]);

      $Info["first_rate"] *= 100;
      $Info["next_rate"] *= 100;
      $Info["order_date"] = Time2FullDate($OrderInfo["pay_time"], "Y-m-d");

      array_push($Result["order_list"], $Info);
    }

    return ReturnCorrect("", $Result);
  }
  
  /**
   * todo: 获取当月佣金明细
   *
   * @param $_user_id
   *
   * @return array
   * @internal param $_commission_id
   *
   */
  public function CurrCommissionOrderList($_user_id){
    $LastDateInfo = $this->GetCurrDate(time());
    $nBeginTime = strtotime($LastDateInfo[2]." 00:00:00");
    $nEndTime = strtotime($LastDateInfo[3]." 23:59:59");

    $sField = "order_sn";
    $sWhere = "(seller_captain_id={$_user_id} or seller_member_id={$_user_id})";
    $sWhere .= " and `pay_time`>={$nBeginTime}";
    $sWhere .= " and `pay_time`<={$nEndTime}";

    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->select();
    $clsUser = new \Org\ZhiHui\User();

    $Result["achievements"] = 0;
    $Result["rake"] = 0;
    $Result["total_rake"] = 0;
    $Result["order_list"] = array();

    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);
      $ProductSnapshotsInfo = $this->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);

      $Result["achievements"] += $OrderInfo["year_premium_amount"];

      $Info["order_sn"] = $OrderInfo["order_sn"];
      $Info["product_type_name"] = $ProductSnapshotsInfo["type_name"];
      $Info["product_name"] = $ProductSnapshotsInfo["product_name"];
      $Info["year_premium_amount"] = $OrderInfo["year_premium_amount"];

      if($OrderInfo["seller_captain_id"] == $_user_id){
        if(IsNum($OrderInfo["seller_member_id"], false, false)){
          $UserInfo = $clsUser->GetUserDetails($OrderInfo["seller_member_id"]);
          $Info["user_name"] = $UserInfo["real_name"];

          $Info["first_rate"] = $ProductSnapshotsInfo["captain_first_rate"]-$ProductSnapshotsInfo["member_first_rate"];
          $Info["next_rate"] = $ProductSnapshotsInfo["captain_next_rate"]-$ProductSnapshotsInfo["member_next_rate"];
        }else{
          $UserInfo = $clsUser->GetUserDetails($OrderInfo["seller_captain_id"]);
          $Info["user_name"] = $UserInfo["real_name"];
          $Info["first_rate"] = $ProductSnapshotsInfo["captain_first_rate"];
          $Info["next_rate"] = $ProductSnapshotsInfo["captain_next_rate"];
        }
      }else{
        $UserInfo = $clsUser->GetUserDetails($OrderInfo["seller_member_id"]);
        $Info["user_name"] = $UserInfo["real_name"];
        $Info["first_rate"] = $ProductSnapshotsInfo["member_first_rate"];
        $Info["next_rate"] = $ProductSnapshotsInfo["member_next_rate"];
      }

      $Info["first_amount"] = ($OrderInfo["year_premium_amount"]*$Info["first_rate"]);
      $Info["next_amount"] = ($OrderInfo["year_premium_amount"]*$Info["next_rate"]);

      $Result["rake"] += ($Info["first_amount"]+$Info["next_amount"]);

      $Info["first_rate"] *= 100;
      $Info["next_rate"] *= 100;
      $Info["order_date"] = Time2FullDate($OrderInfo["pay_time"], "Y-m-d");

      array_push($Result["order_list"], $Info);
    }

    return ReturnCorrect("", $Result);
  }

  public function FmtCommissionData($_data){
    if(!IsArray($_data)){
      return $_data;
    }

    $_data["status_style"] = $this->FmtStatusStyle("status", $_data["status"]);

    $UserInfo = $this->clsUser->GetUserDetails($_data["user_id"]);
    $_data["real_name"] = $UserInfo["real_name"];
    $_data["group_name"] = $UserInfo["group_name"];
    $_data["rake_total"] = ($_data["rake"]+$_data["member_rake"]);

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
   *
   * @param $_field
   * @param $_val
   *
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

  public function GetLastDate($_timestamp=0){
    if(IsNum($_timestamp, false, false)){
      $timestamp = $_timestamp;
    }else{
      $timestamp=time();
    }

    $sFirstTimestamp = strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-01');

    $nYear = date('Y', $sFirstTimestamp);
    $nMonth = date('m', $sFirstTimestamp);
    $sFirstDate = date('Y-m-01', $sFirstTimestamp);
    $sLastDate =date('Y-m-d',strtotime("$sFirstDate +1 month -1 day"));

    return array($nYear, $nMonth, $sFirstDate, $sLastDate);
  }

  public function GetCurrDate($_timestamp=0){
    if(IsNum($_timestamp, false, false)){
      $timestamp = $_timestamp;
    }else{
      $timestamp=time();
    }

    $nFirstTimestamp = strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)).'-01');

    $nYear = date('Y', $nFirstTimestamp);
    $nMonth = date('m', $nFirstTimestamp);
    $sFirstDate = date('Y-m-01', $nFirstTimestamp);
    $sLastDate = date('Y-m-d', strtotime("{$sFirstDate} +1 month -1 day"));

    return array($nYear, $nMonth, $sFirstDate, $sLastDate);
  }
}
