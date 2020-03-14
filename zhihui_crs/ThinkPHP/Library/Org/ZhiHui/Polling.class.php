<?php
/**
 * Created by PhpStorm.
 * User: Time_Lover
 * Date: 2016/11/2
 * Time: 22:32
 */

namespace Org\ZhiHui;


class Polling
{
  //region 预约状态统计缓存
  /**
   * 获取预约状态统计
   * @param $_user_id 用户ID
   * @param $_status 预约状态
   * @return mixed
   */
  public function GetOrderStatusCountDetails($_user_id, $_status){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetOrderStatusCountDetailsCache($_user_id, $_status);

      if(empty($Info)){
        $Info = $this->GetOrderStatusCountDetailsDb($_user_id, $_status);
      }
    }else{
      $Info = $this->GetOrderStatusCountDetailsDb($_user_id, $_status);
    }

    return $Info;
  }

  /**
   * 设置预约状态统计数据缓存
   * @param $_user_id 用户ID
   * @param $_status 预约状态
   * @param array $_data 预约状态统计数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetOrderStatusCountDetailsCache($_user_id, $_status, $_data=NULL){
    F($this->GetOrderStatusCountDetailsCacheKey($_user_id, $_status), $_data);
  }

  /**
   * 获取预约状态统计数据缓存
   * @param $_user_id 用户ID
   * @param $_status 预约状态
   * @return mixed
   */
  private function GetOrderStatusCountDetailsCache($_user_id, $_status){
    return F($this->GetOrderStatusCountDetailsCacheKey($_user_id, $_status));
  }

  /**
   * 获取预约状态统计缓存Key
   * @param $_user_id 用户ID
   * @param $_status 预约状态
   * @return mixed
   */
  private function GetOrderStatusCountDetailsCacheKey($_user_id, $_status){
    return "Order/Count/{$_user_id}/{$_status}";
  }

  /**
   * todo:从数据库中获取预约状态统计数据
   * @param $_user_id 用户ID
   * @param $_status 预约状态
   *
   * @return mixed
   */
  private function GetOrderStatusCountDetailsDb($_user_id, $_status){
    $OrderStatusCountDetails = array();

    if(!IsNum($_user_id, false, false)){
      return $OrderStatusCountDetails;
    }

    if(!IsNum($_status, false, false)){
      return $OrderStatusCountDetails;
    }

    $sWhere = "user_id={$_user_id} and status={$_status}";
    $modOrderStatusCount = M("order_status_count");
    $OrderStatusCountDetails = $modOrderStatusCount->where($sWhere)->find();

    if(IsArray($OrderStatusCountDetails)){
      $this->SetOrderStatusCountDetailsCache($_user_id, $_status, $OrderStatusCountDetails);
    }

    return $OrderStatusCountDetails;
  }
  //endregion 预约状态统计缓存

  /**
   * todo: 获取客户经理新状态预约统计
   * @param $_user_id
   *
   * @return mixed
   */
  public function GetSellerMemberOrderCount($_user_id){
    $Result["total"] = 0;
    $Result["signatory_approve"] = 0;
    $Result["signatory_reject"] = 0;
    $Result["subscribe_approve"] = 0;
    $Result["subscribe_reject"] = 0;

    if(IsNum($_user_id, false, false)){
      $clsOrder = new \Org\ZhiHui\Order();
      $modOrder = M("order");
      
      $sWhere = "seller_member_id={$_user_id}";
      
      //region 计划书已回复
      $nSignatoryApproveStatus = $clsOrder->OrderStatusSignatoryApproveStatus();
      $sSignatoryApproveWhere = "{$sWhere} and status={$nSignatoryApproveStatus}";
      $nSignatoryApproveCount = $modOrder->where($sSignatoryApproveWhere)->count();
      
      $Result["signatory_approve"] = $nSignatoryApproveCount;
      $Result["total"] += $Result["signatory_approve"];
      //endregion 计划书已回复

      //region 计划书已拒绝
      $nSignatoryRejectStatus = $clsOrder->OrderStatusSignatoryRejectStatus();
      $sSignatoryRejectWhere = "{$sWhere} and status={$nSignatoryRejectStatus}";
      $nSignatoryRejectCount = $modOrder->where($sSignatoryRejectWhere)->count();
      $Result["signatory_reject"] = $nSignatoryRejectCount;
      $Result["total"] += $Result["signatory_reject"];
      //endregion 计划书已拒绝

      //region 预约审批通过
      $nSubscribeApproveStatus = $clsOrder->OrderStatusSubscribeApproveStatus();
      $sSubscribeApproveWhere = "{$sWhere} and status={$nSubscribeApproveStatus}";
      $SubscribeApproveCount = $modOrder->where($sSubscribeApproveWhere)->count();
      $Result["subscribe_approve"] = $SubscribeApproveCount;
      $Result["total"] += $Result["subscribe_approve"];
      //endregion 预约审批通过

      //region 预约审批拒绝
      $nSubscribeRejectStatus = $clsOrder->OrderStatusSubscribeRejectStatus();
      $sSubscribeRejectWhere = "{$sWhere} and status={$nSubscribeRejectStatus}";
      $SubscribeRejectCount = $modOrder->where($sSubscribeRejectWhere)->count();
      $Result["subscribe_reject"] = $SubscribeRejectCount;
      $Result["total"] += $Result["subscribe_reject"];
      //endregion 预约审批拒绝
    }

    return $Result;
  }

  /**
   * todo: 获取保险公司新状态预约统计
   * @return mixed
   */
  public function GetSpecialOrderCount(){
    $Result["total"] = 0;
    $Result["signatory"] = 0;
    $Result["subscribe"] = 0;

    $clsUser = new \Org\ZhiHui\User();
    $nUserID = $clsUser->ConstInsuranceCompanyUserID();

    $clsOrder = new \Org\ZhiHui\Order();
    $modOrder = M("order");
    
    //region 计划书申请
    $nSignatoryStatus = $clsOrder->OrderStatusSignatoryStatus();
    $sSignatoryWhere = "status={$nSignatoryStatus}";
    $SignatoryCount = $modOrder->where($sSignatoryWhere)->count();
    
    $Result["signatory"] = $SignatoryCount;
    $Result["total"] += $Result["signatory"];
    //endregion 计划书申请

    //region 预约申请
    $nSubscribeStatus = $clsOrder->OrderStatusSubscribeStatus();
    $sSubscribeWhere = "status={$nSubscribeStatus}";
    $SubscribeCount = $modOrder->where($sSubscribeWhere)->count();
    
    $Result["subscribe"] = $SubscribeCount;
    $Result["total"] += $Result["subscribe"];
    //endregion 预约申请

    return $Result;
  }

  /**
   * todo: 添加订单状态统计数据
   * @param     $_user_id
   * @param     $_status
   * @param int $_number
   *
   * @return bool|mixed
   */
  public function AddOrderStatusCount($_user_id, $_status, $_number=1){
    if(!IsNum($_user_id, false, false)){
      return false;
    }

    $clsOrder = new \Org\ZhiHui\Order();
    $StatusConfigInfo = $clsOrder->OrderStatusGetOrderStatusConfig($_status);
    
    if(!IsArray($StatusConfigInfo)){
      return false;
    }

    if(!IsNum($_number, false, false)){
      return false;
    }
    
    $StatusCountInfo = $this->GetOrderStatusCountDetails($_user_id, $_status);
    $modOrderStatusCount = M("order_status_count");

    if(IsArray($StatusCountInfo)){
      $SaveData["id"] = $StatusCountInfo["id"];
      $SaveData["number"] = array("exp", "number+{$_number}");
      $SaveData["update_time"] = time();

      $Result = $modOrderStatusCount->save($SaveData);

      if($Result !== false){
        $this->SetOrderStatusCountDetailsCache($StatusConfigInfo["user_id"], $StatusConfigInfo["$_status"]);
      }
    }else{
      $AddData["user_id"] = $_user_id;
      $AddData["status"] = $_status;
      $AddData["number"] = $_number;
      $AddData["update_time"] = time();

      $Result = $modOrderStatusCount->add($AddData);
    }

    return $Result;
  }

  /**
   * todo: 删除订单状态统计数据
   * @param     $_user_id
   * @param     $_status
   * @param int $_number
   *
   * @return bool|mixed
   */
  public function DelOrderStatusCount($_user_id, $_status, $_number=1){
    if(!IsNum($_user_id, false, false)){
      return false;
    }

    $clsOrder = new \Org\ZhiHui\Order();
    $StatusConfigInfo = $clsOrder->OrderStatusGetOrderStatusConfig($_status);

    if(!IsArray($StatusConfigInfo)){
      return false;
    }

    if(!IsNum($_number, false, false)){
      return false;
    }

    $StatusCountInfo = $this->GetOrderStatusCountDetails($_user_id, $_status);

    $modOrderStatusCount = M("order_status_count");

    if(IsArray($StatusCountInfo)){
      if(IsNum($StatusCountInfo["number"], false, false)){
        $SaveData["id"] = $StatusCountInfo["id"];
        $SaveData["number"] = array("exp", "number-{$_number}");
        $SaveData["update_time"] = time();

        $Result = $modOrderStatusCount->save($SaveData);

        if($Result !== false){
          $this->SetOrderStatusCountDetailsCache($StatusConfigInfo["user_id"], $StatusConfigInfo["$_status"]);
        }
      }

      return false;
    }else{
      return false;
    }

    return $Result;
  }
}