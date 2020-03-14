<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

class OrderModel extends Model {
  Protected $autoCheckFields = false;

  private $modOrder = null;
  private $modOrderReview = null;

  public $clsOrder = null;
  public $clsUser = null;
  public $clsCustomer = null;
  public $clsProduct = null;

  private $clsPolling = null;

  function __construct() {
    $this->modOrder = M("order");
    $this->modOrderReview = M("order_review");
    
    $this->clsOrder = new \Org\ZhiHui\Order();
    $this->clsUser = new \Org\ZhiHui\User();
    $this->clsCustomer = new \Org\ZhiHui\Customer();
    $this->clsProduct = new \Org\ZhiHui\Product();
    $this->clsPolling = new \Org\ZhiHui\Polling();
  }

  //region 申请
  //region 签约预约
  /**
   * todo:预约列表
   *
   * @param       $_seller_captain_id
   * @param       $_seller_member_id
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function SignatoryOrderList($_seller_captain_id, $_seller_member_id, $_rownum=20, $_param=array()){

    $sField = "order_sn";
    $sWhere = "(status={$this->clsOrder->OrderStatusSignatoryStatus()} or status={$this->clsOrder->OrderStatusSignatoryRejectStatus()})";
    $sOrder = "id desc";

    if(IsNum($_seller_member_id, false, false)){
      $sWhere .= " and seller_member_id={$_seller_member_id}";
    }else{
      if(IsNum($_seller_captain_id, false, false)){
        $sWhere .= " and seller_captain_id={$_seller_captain_id}";
      }
    }

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "order_sn like '%{$_param["search_key"]}%'";
          $sWhere .= " or product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
          $sWhere .= " or customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
          $sWhere .= ")";
        }
      }else{
        switch($_param["search_case"]){
          case "order_sn":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and order_sn like '%{$_param["search_key"]}%'";
            }
            break;

          case "produc":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
            }
            break;

          case "customer":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
            }
            break;
        }
      }
    }

    $nTotal = $this->modOrder->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $OrderList = array();

    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);

      //region 产品快照信息
      $ProductInfo = $this->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
      $OrderInfo["product_name"] = $ProductInfo["product_name"];
      //endregion 产品快照信息

      //region 客户信息
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
      $OrderInfo["customer_name"] = $CustomerInfo["real_name"];
      //endregion 客户信息

      if(IsNum($OrderInfo["seller_member_id"], false, false)){
        //region 客户经理信息
        $SellerMemberInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_member_id"]);
        $OrderInfo["seller_manager_name"] = $SellerMemberInfo["real_name"];
        //endregion 客户经理信息
      }else{
        //region 团队长信息
        $SellerCaptainInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_captain_id"]);
        $OrderInfo["seller_manager_name"] = $SellerCaptainInfo["real_name"];
        //endregion 团队长信息
      }

      //region 时间信息
      if(IsNum($OrderInfo["signed_time"], false, false)){
        $OrderInfo["signed_time"] = Time2FullDate($OrderInfo["signed_time"]);
      }

      if(IsNum($OrderInfo["reservation_time"], false, false)){
        $OrderInfo["reservation_time"] = Time2FullDate($OrderInfo["reservation_time"]);
      }else{
        $OrderInfo["reservation_time"] = "预约中";
      }

      if(IsNum($OrderInfo["pay_time"], false, false)){
        $OrderInfo["pay_time"] = Time2FullDate($OrderInfo["pay_time"]);
      }else{
        $OrderInfo["pay_time"] = "未消费";
      }

      if(IsNum($OrderInfo["visit_time"], false, false)){
        $OrderInfo["visit_time"] = Time2FullDate($OrderInfo["visit_time"]);
      }else{
        $OrderInfo["visit_time"] = "未回访";
      }
      //endregion 时间信息

      //region 预约状态
      $OrderStatusInfo = $this->clsOrder->OrderStatusGetOrderStatusConfig($OrderInfo["status"]);
      $OrderInfo["status_desc"] = $OrderStatusInfo["desc_style"];
      //endregion 预约状态

      //region 审批信息
      $sField = "id";
      $sWhere = "order_sn='{$OrderInfo["order_sn"]}'";
      $sWhere .= " and status>=10";
      $sWhere .= " and status<20";
      $sWhere .= " and add_time>{$OrderInfo["update_time"]}";
      $sOrder = "id desc";

      $OrderReviewID = $this->modOrderReview->where($sWhere)->order($sOrder)->getField($sField);

      if(IsNum($OrderReviewID, false, false)){
        $OrderReviewInfo = $this->clsOrder->GetOrderReviewDetails($OrderReviewID);
        $ReviewUserInfo = $this->clsUser->GetUserDetails($OrderReviewInfo["account_id"]);

        $OrderInfo["review_remarks"] = $OrderReviewInfo["review_remarks"];
        $OrderInfo["review_account"] = $ReviewUserInfo["real_name"];
      }

      //endregion 审批信息

      array_push($OrderList, $OrderInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$OrderList);
  }

  /**
   * todo: 创建预约
   *
   * @param $_order_sn
   * @param $_seller_captain_id
   * @param $_seller_member_id
   * @param $_customer_id
   * @param $_product_id
   * @param $_guarantee_amount
   * @param $_year_premium_amount
   * @param $_seller_manager_remarks
   *
   * @param $_attachment
   * @param $_seller_manager_remarks
   *
   * @return array
   */
  public function OrderInfoSave($_order_sn, $_seller_captain_id, $_seller_member_id, $_customer_id, $_product_id, $_payment_years, $_guarantee_amount, $_year_premium_amount, $_seller_manager_remarks, $_attachment, $_seller_manager_remarks){
    //region 参数检查
    
    //region 检查团队长
    if(!IsNum($_seller_captain_id, false, false)){
      return ReturnError(L("_SELLER_CAPTAIN_ID_ERROR_"));
    }else{
      $SellerCaptainInfo = $this->clsUser->GetUserDetails($_seller_captain_id);

      if(!IsArray($SellerCaptainInfo)){
        return ReturnError(L("_SELLER_CAPTAIN_NOT_EXIST_"));
      }else{
        $UserGroupInfo = $this->clsUser->SellerCaptainUserGroup();

        if($SellerCaptainInfo["group_id"] != $UserGroupInfo["group_id"]){
          return ReturnError(L("_USER_GROUP_NOT_SELLER_CAPTAIN_"));
        }
      }

      $AddData["broker_company_id"] = $SellerCaptainInfo["parent_user_id"];
      $AddData["seller_captain_id"] = $SellerCaptainInfo["id"];
    }
    //endregion 检查团队长

    //region 检查客户经理
    if(!IsNum($_seller_member_id, false, false)){
      $AddData["seller_member_id"] = 0;
    }else{
      $SellerMemberInfo = $this->clsUser->GetUserDetails($_seller_member_id);

      if(!IsArray($SellerMemberInfo)){
        return ReturnError(L("_SELLER_CAPTAIN_NOT_EXIST_"));
      }else{
        $UserGroupInfo = $this->clsUser->SellereMemberUserGroup();

        if($SellerMemberInfo["group_id"] != $UserGroupInfo["group_id"]){
          return ReturnError(L("_USER_GROUP_NOT_SELLER_MEMBER_"));
        }

        if($SellerMemberInfo["parent_user_id"] != $SellerCaptainInfo["id"]){
          return ReturnError(L("_SELLER_MEMBER_NOT_SELLER_CAPTAIN_USER_"));
        }
      }

      $AddData["seller_member_id"] = $SellerMemberInfo["id"];
    }
    //endregion 检查客户经理

    //region 检查客户
    if(!IsNum($_customer_id, false, false)){
      return ReturnError(L("_CUSTOMER_ID_NULL_"));
    }else{
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($_customer_id);

      if(!IsArray($CustomerInfo)){
        return ReturnError(L("_CUSTOMER_NOT_EXIST_"));
      }

      $AddData["customer_id"] = $_customer_id;
    }
    //endregion 检查客户

    //region 检查产品
    if(!IsNum($_product_id, false, false)){
      return ReturnError(L("_PRODUCT_ID_NULL_"));
    }else{
      $ProductInfo = $this->clsProduct->GetProductDetails($_product_id);

      if(!IsArray($ProductInfo)){
        return ReturnError(L("_PRODUCT_NOT_EXIST_"));
      }

      $AddData["product_id"] = $_product_id;
    }
    //endregion 检查产品
    
    //region 检查缴费年限
    if(!IsN($_payment_years)){
      if(!IsNum($_payment_years, false, false)){
        return ReturnError(L("_ORDER_PAYMENT_YEARS_ERROR_"));
      }else{
        $AddData["payment_years"] = $_payment_years;
      }
    }
    //endregion 检查缴费年限

    //region 检查保障额度
    if(!IsN($_guarantee_amount)){
      if(!IsNum($_guarantee_amount, false, false)){
        return ReturnError(L("_ORDER_GUARANTEE_AMOUNT_ERROR_"));
      }else{
        $AddData["guarantee_amount"] = $_guarantee_amount;
      }
    }
    //endregion 检查保障额度

    //region 检查保障额度
    if(!IsN($_year_premium_amount)){
      if(!IsNum($_year_premium_amount, false, false)){
        return ReturnError(L("_ORDER_YEAR_PREMIUM_AMOUNT_ERROR_"));
      }else{
        $AddData["year_premium_amount"] = $_year_premium_amount;
      }
    }
    //endregion 检查保障额度

    //region 保存证件照图片
    if(IsArray($_attachment)){
      $FileInfo = $this->UploadAttachmentImage($_attachment);
      if($FileInfo["error"] != 0){
        return $FileInfo;
      }

      $AddData["attachment"] = $FileInfo["data"];
    }
    //endregion 保存证件照图片

    //endregion 参数检查

    $AddData["seller_manager_remarks"] = $_seller_manager_remarks;

    $OrderInfo = $this->clsOrder->GetOrderDetails($_order_sn);
    $nOrderStatus = $this->clsOrder->OrderStatusSignatoryStatus();
    $AddData["status"] = $nOrderStatus;
    $AddData["update_time"] = time();
    if(IsArray($OrderInfo)){
      if(
        $OrderInfo["status"] != $this->clsOrder->OrderStatusSignatoryStatus()
        && $OrderInfo["status"] != $this->clsOrder->OrderStatusSignatoryRejectStatus()
      ){
        return ReturnError("_ORDER_SIGNATORY_STATUS_CHNAGED_");
      }
      
      $sOrderSn = $OrderInfo["order_sn"];

      $AddData["id"] = $OrderInfo["id"];

      $SaveResult = $this->modOrder->save($AddData);

      if($SaveResult === false){
        return ReturnError(L("_SAVE_DATA_FAILURE_"));
      }

      $this->clsOrder->SetOrderDetailsCache($sOrderSn);
    }else{
      //region 添加基本数据
      $AddData["broker_company_remarks"] = "";

      $sOrderSn = $this->clsOrder->CreateOrderSn();
      $AddData["order_sn"] = $sOrderSn;
      $AddData["signed_time"] = time();
      $AddData["reservation_time"] = 0;
      $AddData["pay_time"] = 0;
      $AddData["visit_time"] = 0;

      $AddResult = $this->modOrder->add($AddData);

      if($AddResult === false){
        return ReturnError(L("_SAVE_DATA_FAILURE_"));
      }
      //endregion 添加基本数据
      
      //region 保存产品快照
      $BrokerCompanyInfo = $this->clsUser->GetUserDetails($ProductInfo["broker_company_id"]);
      $InsuranceCompanyInfo = $this->clsUser->GetUserDetails($ProductInfo["insurance_company_id"]);

      $ProductSnapshots["order_sn"] = $sOrderSn;
      $ProductSnapshots["product_id"] = $ProductInfo["id"];
      $ProductSnapshots["type_id"] = $ProductInfo["type_id"];
      $ProductSnapshots["type_name"] = $ProductInfo["type_name"];
      $ProductSnapshots["broker_company_user"] = $BrokerCompanyInfo["real_name"];
      $ProductSnapshots["insurance_company_user"] = $InsuranceCompanyInfo["real_name"];
      $ProductSnapshots["product_name"] = $ProductInfo["product_name"];
      $ProductSnapshots["payment_type"] = $ProductInfo["payment_type"];
      $ProductSnapshots["pay_type"] = $ProductInfo["pay_type"];
      $ProductSnapshots["min_age"] = $ProductInfo["min_age"];
      $ProductSnapshots["max_age"] = $ProductInfo["max_age"];
      $ProductSnapshots["validity_year"] = $ProductInfo["validity_year"];
      $ProductSnapshots["captain_first_rate"] = $ProductInfo["captain_first_rate"];
      $ProductSnapshots["captain_next_rate"] = $ProductInfo["captain_next_rate"];
      $ProductSnapshots["member_first_rate"] = $ProductInfo["member_first_rate"];
      $ProductSnapshots["member_next_rate"] = $ProductInfo["member_next_rate"];
      $ProductSnapshots["attachment_type"] = $ProductInfo["attachment_type"];
      $ProductSnapshots["attachment_id"] = $ProductInfo["attachment_id"];

      $modProductSnapshots = M("product_snapshots");
      $ProductSnapshotsResult = $modProductSnapshots->add($ProductSnapshots);
      
      if($ProductSnapshotsResult !== false){
        $SaveData["id"] = $AddResult;
        $SaveData["product_snapshots_id"] = $ProductSnapshotsResult;

        $SaveResult = $this->modOrder->save($SaveData);
      }
      //endregion 保存产品快照
    }

    $this->clsOrder->SetOrderDetailsCache($sOrderSn);

    $nUserID = $this->clsUser->ConstInsuranceCompanyUserID();
    $this->clsPolling->AddOrderStatusCount($nUserID, $nOrderStatus);

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo: 预约信息
   *
   * @param $_order_sn
   *
   * @return array
   *
   */
  public function OrderInfo($_order_sn){
    $OrderInfo = array();

    if(!IsN($_order_sn)){
      $OrderInfo = $this->clsOrder->GetOrderDetails($_order_sn);

      $OrderInfo["signed_time"] = Time2FullDate($OrderInfo["signed_time"], "Y-m-d H:i");
      $OrderInfo["reservation_time"] = Time2FullDate($OrderInfo["reservation_time"], "Y-m-d H:i");
      $OrderInfo["pay_time"] = Time2FullDate($OrderInfo["pay_time"], "Y-m-d H:i");
      $OrderInfo["visit_time"] = Time2FullDate($OrderInfo["visit_time"], "Y-m-d H:i");

      //region 预约状态
      $OrderStatusInfo = $this->clsOrder->OrderStatusGetOrderStatusConfig($OrderInfo["status"]);
      $OrderInfo["status_desc"] = $OrderStatusInfo["desc_style"];
      //endregion 预约状态
    }

    return $OrderInfo;
  }

  /**
   * todo: 上传图片
   *
   * @param $_file
   *
   * @return array
   */
  private function UploadAttachmentImage($_file){
    if(!IsArray($_file)){
      return ReturnError(L("_UPLOAD_FILE_NOT_EXIST_"));
    }

    $clsUploadFile = new \Org\ZhiHui\UploadFile();
    $clsUploadFile->FilePathType = "order_attachment";
    $clsUploadFile->FileNameType = "time";

    $UploadResult = $clsUploadFile->UploadImage($_file);

    if($UploadResult["error"] !=0){
      return $UploadResult;
    }

    $UploadImageInfo = $UploadResult["data"]["attachment"];
    $nTime = time();

    $SaveImageData["original_name"] = $UploadImageInfo["name"];
    $SaveImageData["name"] = $UploadImageInfo["savename"];
    $SaveImageData["path"] = $UploadImageInfo["savepath"];
    $SaveImageData["file"] = $UploadImageInfo["file"];
    $SaveImageData["ext"] = $UploadImageInfo["ext"];
    $SaveImageData["size"] = $UploadImageInfo["size"];
    $SaveImageData["width"] = $UploadImageInfo["width"];
    $SaveImageData["height"] = $UploadImageInfo["height"];
    $SaveImageData["mime"] = $UploadImageInfo["mime"];
    $SaveImageData["type"] = $UploadImageInfo["type"];
    $SaveImageData["add_time"] = $nTime;
    $SaveImageData["update_time"] = $nTime;

    $SaveResult = $clsUploadFile->SaveImgInfoToDb($SaveImageData);

    if($SaveResult["error"] == 1){
      return ReturnError(L("_UPLOAD_FAILURE_"));
    }else{
      if(!IsNum($SaveResult["data"], false)){
        return ReturnError(L("_UPLOAD_FAILURE_"));
      }
    }

    return $SaveResult;
  }

  /**
   * todo: 上传附件
   * @param $_file
   *
   * @return array
   */
  private function UploadAttachmentFile($_file){
    if(!IsArray($_file)){
      return ReturnError(L("_UPLOAD_FILE_NOT_EXIST_"));
    }

    $clsUploadFile = new \Org\ZhiHui\UploadFile();
    $clsUploadFile->FilePathType = "product_attachment_file";
    $clsUploadFile->FileNameType = "time";

    $UploadResult = $clsUploadFile->UploadFile($_file);

    if($UploadResult["error"] !=0){
      return $UploadResult;
    }

    $UploadImageInfo = $UploadResult["data"]["attachment"];

    $SaveFileData["original_name"] = $UploadImageInfo["name"];
    $SaveFileData["name"] = $UploadImageInfo["savename"];
    $SaveFileData["path"] = $UploadImageInfo["savepath"];
    $SaveFileData["file"] = $UploadImageInfo["file"];
    $SaveFileData["ext"] = $UploadImageInfo["ext"];
    $SaveFileData["size"] = $UploadImageInfo["size"];
    $SaveFileData["type"] = $UploadImageInfo["type"];

    $SaveResult = $clsUploadFile->SaveFileInfoToDb($SaveFileData);

    if($SaveResult["error"] == 1){
      return ReturnError(L("_UPLOAD_FAILURE_"));
    }else{
      if(!IsNum($SaveResult["data"], false)){
        return ReturnError(L("_UPLOAD_FAILURE_"));
      }
    }

    return $SaveResult;
  }
  //endregion 签约预约

  //region 预约预约
  /**
   * todo: 预约预约列表
   * @param       $_seller_captain_id
   * @param       $_seller_member_id
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function SubscribeOrderList($_seller_captain_id, $_seller_member_id, $_rownum=20, $_param=array()){
    $sField = "order_sn";
    $sWhere = "(status={$this->clsOrder->OrderStatusSubscribeStatus()} or status={$this->clsOrder->OrderStatusSubscribeRejectStatus()} or status={$this->clsOrder->OrderStatusSubscribeApproveStatus()} or status={$this->clsOrder->OrderStatusPayStatus()})";
    $sOrder = "status, signed_time desc";

    if(IsNum($_seller_member_id, false, false)){
      $sWhere .= " and seller_member_id={$_seller_member_id}";
    }else{
      if(IsNum($_seller_captain_id, false, false)){
        $sWhere .= " and seller_captain_id={$_seller_captain_id}";
      }
    }

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "order_sn like '%{$_param["search_key"]}%'";
          $sWhere .= " or product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
          $sWhere .= " or customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
          $sWhere .= ")";
        }
      }else{
        switch($_param["search_case"]){
          case "order_sn":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and order_sn like '%{$_param["search_key"]}%'";
            }
            break;

          case "produc":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
            }
            break;

          case "customer":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
            }
            break;
        }
      }
      
      if(IsNum($_param["search_status"], false, false)){
        $sWhere .= " and status={$_param["search_status"]}";
      }
    }

    $nTotal = $this->modOrder->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $OrderList = array();

    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);
      
      array_push($OrderList, $OrderInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$OrderList);
  }
  
  /**
   * todo: 预约预约列表
   * @param       $_seller_captain_id
   * @param       $_seller_member_id
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function AjaxSubscribeOrderList($_seller_captain_id, $_seller_member_id, $_page=1, $_rownum=20){
    $sField = "order_sn";
    $sWhere = "(status={$this->clsOrder->OrderStatusSubscribeStatus()} or status={$this->clsOrder->OrderStatusSubscribeRejectStatus()} or status={$this->clsOrder->OrderStatusSubscribeApproveStatus()} or status={$this->clsOrder->OrderStatusPayStatus()})";
    $sOrder = "status, signed_time desc";
    
    if(IsNum($_seller_member_id, false, false)){
      $sWhere .= " and seller_member_id={$_seller_member_id}";
    }else{
      if(IsNum($_seller_captain_id, false, false)){
        $sWhere .= " and seller_captain_id={$_seller_captain_id}";
      }
    }
    
    $nTotal = $this->modOrder->where($sWhere)->count();
  
    if(!IsNum($_page, false, false)){
      $_page = 1;
    }
  
    $nStartRows = ($_page-1)*$_rownum;
    
    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->order($sOrder)->limit($nStartRows.','.$_rownum)->select();
    $OrderList = array();
    
    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);
      
      array_push($OrderList, $OrderInfo);
    }
    
    return array("PageInfo"=>FmtPagin($nTotal, $_page, $_rownum), "DataList"=>$OrderList);
  }
  
  /**
   * todo: 预约信息
   *
   * @param $_order_sn
   *
   * @return array
   *
   */
  public function SubscribeOrderInfo($_order_sn){
    $OrderInfo = array();
    
    if(!IsN($_order_sn)){
      $OrderInfo = $this->clsOrder->GetOrderDetails($_order_sn);
      
      $OrderInfo["signed_time"] = Time2FullDate($OrderInfo["signed_time"], "Y-m-d H:i");
      $OrderInfo["reservation_time"] = Time2FullDate($OrderInfo["reservation_time"], "Y-m-d H:i");
      $OrderInfo["pay_time"] = Time2FullDate($OrderInfo["pay_time"], "Y-m-d H:i");
      $OrderInfo["visit_time"] = Time2FullDate($OrderInfo["visit_time"], "Y-m-d H:i");
      
      //region 预约状态
      $OrderStatusInfo = $this->clsOrder->OrderStatusGetOrderStatusConfig($OrderInfo["status"]);
      $OrderInfo["status_desc"] = $OrderStatusInfo["desc_style"];
      //endregion 预约状态
      
      //region 审批信息
      $sField = "id";
      $sWhere = "order_sn='{$OrderInfo["order_sn"]}'";
      $sWhere .= " and ((status>=20 and status<30) or status={$this->clsOrder->OrderStatusSignatoryApproveStatus()})";
      $sOrder = "id desc";
      
      $OrderReviewID = $this->modOrderReview->where($sWhere)->order($sOrder)->getField($sField);
      
      if(IsNum($OrderReviewID, false, false)){
        $OrderReviewInfo = $this->clsOrder->GetOrderReviewDetails($OrderReviewID);
        $ReviewUserInfo = $this->clsUser->GetUserDetails($OrderReviewInfo["account_id"]);
        
        $OrderInfo["review_remarks"] = $OrderReviewInfo["review_remarks"];
        $OrderInfo["review_account"] = $ReviewUserInfo["real_name"];
      }else{
        $OrderInfo["review_remarks"] = "--";
        $OrderInfo["review_account"] = "--";
      }
      
      //endregion 审批信息
    }
    
    return $OrderInfo;
  }
  
  /**
   * todo: 保存预约预约信息
   *
   * @param $_order_sn
   * @param $_seller_captain_id
   * @param $_seller_member_id
   * @param $_customer_id
   * @param $_product_id
   * @param $_reservation_time
   * @param $_payment_years
   * @param $_guarantee_amount
   * @param $_year_premium_amount
   * @param $_seller_manager_remarks
   * @param $_attachment
   *
   * @return array
   */
  public function ChangeReservationTime($_order_sn, $_reservation_time){
    //region 检查参数
    //region 检查预约编号
    if(IsN($_order_sn)){
      return ReturnError(L("_ORDER_SN_NULL_"));
    }else{
      $OrderInfo = $this->clsOrder->GetOrderDetails($_order_sn);
      
      if(!IsArray($OrderInfo)){
        return ReturnError(L("_ORDER_NOT_EXIST_"));
      }
      
      if($OrderInfo["status"] == $this->clsOrder->OrderStatusSubscribeApproveInfo()){
        return ReturnError("已预约的预约不能修改内容");
      }
      
      $SaveData["id"] = $OrderInfo["id"];
    }
    //endregion 检查预约编号
    
    //region 检查预约时间
    $nCurrTime = time();
    $nReservationTime = isDateTime($_reservation_time);
    
    if($nReservationTime === false){
      return ReturnError(L("_ORDER_RESERVATION_TIME_ERROR_"));
    }else{
      if($nReservationTime <= $nCurrTime){
        return ReturnError(L("_ORDER_RESERVATION_TIME_MIN_ERROR_"));
      }
      
      $SaveData["reservation_time"] = $nReservationTime;
    }
    //endregion 检查预约时间
    //endregion 参数检查
  
    $SaveData["update_time"] = time();
    $SaveResult = $this->modOrder->save($SaveData);
  
    if($SaveResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    
    $this->clsOrder->SetOrderDetailsCache($OrderInfo["order_sn"]);
    $this->clsOrder->GetOrderDetails($OrderInfo["order_sn"]);
    
    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"), $SaveResult);
  }
  
  /**
   * todo: 保存预约预约信息
   *
   * @param $_order_sn
   * @param $_seller_captain_id
   * @param $_seller_member_id
   * @param $_customer_id
   * @param $_product_id
   * @param $_reservation_time
   * @param $_payment_years
   * @param $_guarantee_amount
   * @param $_year_premium_amount
   * @param $_seller_manager_remarks
   * @param $_attachment
   *
   * @return array
   */
  public function SubscribeOrderInfoSave($_order_sn, $_seller_captain_id, $_seller_member_id, $_customer_id, $_product_id, $_reservation_time, $_payment_years, $_guarantee_amount, $_year_premium_amount, $_seller_manager_remarks, $_attachment){
    //region 检查参数
    //region 检查预约编号
    if(!IsN($_order_sn)){
      $OrderInfo = $this->clsOrder->GetOrderDetails($_order_sn);

      if(!IsArray($OrderInfo)){
        return ReturnError(L("_ORDER_NOT_EXIST_"));
      }
      
      if($OrderInfo["status"] == $this->clsOrder->OrderStatusSubscribeApproveInfo()){
        return ReturnError("已预约的预约不能修改内容");
      }
      
      $SaveData["id"] = $OrderInfo["id"];
    }else{
      $sOrderSn = $this->clsOrder->CreateOrderSn();
    }
    //endregion 检查预约编号
  
    //region 检查团队长
    if(!IsNum($_seller_captain_id, false, false)){
      return ReturnError(L("_SELLER_CAPTAIN_ID_ERROR_"));
    }else{
      $SellerCaptainInfo = $this->clsUser->GetUserDetails($_seller_captain_id);
    
      if(!IsArray($SellerCaptainInfo)){
        return ReturnError(L("_SELLER_CAPTAIN_NOT_EXIST_"));
      }else{
        $UserGroupInfo = $this->clsUser->SellerCaptainUserGroup();
      
        if($SellerCaptainInfo["group_id"] != $UserGroupInfo["group_id"]){
          return ReturnError(L("_USER_GROUP_NOT_SELLER_CAPTAIN_"));
        }
      }
    
      $SaveData["broker_company_id"] = $SellerCaptainInfo["parent_user_id"];
      $SaveData["seller_captain_id"] = $SellerCaptainInfo["id"];
    }
    //endregion 检查团队长
  
    //region 检查客户经理
    if(!IsNum($_seller_member_id, false, false)){
      $SaveData["seller_member_id"] = 0;
    }else{
      $SellerMemberInfo = $this->clsUser->GetUserDetails($_seller_member_id);
    
      if(!IsArray($SellerMemberInfo)){
        return ReturnError(L("_SELLER_CAPTAIN_NOT_EXIST_"));
      }else{
        $UserGroupInfo = $this->clsUser->SellereMemberUserGroup();
      
        if($SellerMemberInfo["group_id"] != $UserGroupInfo["group_id"]){
          return ReturnError(L("_USER_GROUP_NOT_SELLER_MEMBER_"));
        }
      
        if($SellerMemberInfo["parent_user_id"] != $SellerCaptainInfo["id"]){
          return ReturnError(L("_SELLER_MEMBER_NOT_SELLER_CAPTAIN_USER_"));
        }
      }
    
      $SaveData["seller_member_id"] = $SellerMemberInfo["id"];
    }
    //endregion 检查客户经理
  
    //region 检查客户
    if(!IsNum($_customer_id, false, false)){
      return ReturnError(L("_CUSTOMER_ID_NULL_"));
    }else{
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($_customer_id);
    
      if(!IsArray($CustomerInfo)){
        return ReturnError(L("_CUSTOMER_NOT_EXIST_"));
      }
    
      $SaveData["customer_id"] = $_customer_id;
    }
    //endregion 检查客户
    
    if(!IsArray($OrderInfo)){
      //region 检查产品
      if(!IsNum($_product_id, false, false)){
        //return ReturnError(L("_PRODUCT_ID_NULL_"));
      }else{
        $ProductInfo = $this->clsProduct->GetProductDetails($_product_id);
    
        if(!IsArray($ProductInfo)){
          return ReturnError(L("_PRODUCT_NOT_EXIST_"));
        }
  
        //region 保存产品快照
        $BrokerCompanyInfo = $this->clsUser->GetUserDetails($ProductInfo["broker_company_id"]);
        $InsuranceCompanyInfo = $this->clsUser->GetUserDetails($ProductInfo["insurance_company_id"]);
        
        $ProductSnapshots["order_sn"] = $sOrderSn;
        $ProductSnapshots["product_id"] = $ProductInfo["id"];
        $ProductSnapshots["type_id"] = $ProductInfo["type_id"];
        $ProductSnapshots["type_name"] = $ProductInfo["type_name"];
        $ProductSnapshots["broker_company_user"] = $BrokerCompanyInfo["real_name"];
        $ProductSnapshots["insurance_company_user"] = $InsuranceCompanyInfo["real_name"];
        $ProductSnapshots["product_name"] = $ProductInfo["product_name"];
        $ProductSnapshots["payment_type"] = $ProductInfo["payment_type"];
        $ProductSnapshots["pay_type"] = $ProductInfo["pay_type"];
        $ProductSnapshots["min_age"] = $ProductInfo["min_age"];
        $ProductSnapshots["max_age"] = $ProductInfo["max_age"];
        $ProductSnapshots["validity_year"] = $ProductInfo["validity_year"];
        $ProductSnapshots["captain_first_rate"] = $ProductInfo["captain_first_rate"];
        $ProductSnapshots["captain_next_rate"] = $ProductInfo["captain_next_rate"];
        $ProductSnapshots["member_first_rate"] = $ProductInfo["member_first_rate"];
        $ProductSnapshots["member_next_rate"] = $ProductInfo["member_next_rate"];
        $ProductSnapshots["attachment_type"] = $ProductInfo["attachment_type"];
        $ProductSnapshots["attachment_id"] = $ProductInfo["attachment_id"];
  
        $modProductSnapshots = M("product_snapshots");
        $ProductSnapshotsResult = $modProductSnapshots->add($ProductSnapshots);
  
        if($ProductSnapshotsResult === false){
          return ReturnError(L("_ORDER_PRODUCT_SNAPSHOTS_SAVE_FAILURE_"));
        }
  
        $SaveData["product_snapshots_id"] = $ProductSnapshotsResult;
        //endregion 保存产品快照
      }
      //endregion 检查产品
    }
    
    //region 检查预约时间
    $nCurrTime = time();
    $nReservationTime = isDateTime($_reservation_time);
  
    if($nReservationTime === false){
      return ReturnError(L("_ORDER_RESERVATION_TIME_ERROR_"));
    }else{
      if($nReservationTime <= $nCurrTime){
        return ReturnError(L("_ORDER_RESERVATION_TIME_MIN_ERROR_"));
      }
  
      $SaveData["reservation_time"] = $nReservationTime;
    }
    //endregion 检查预约时间
  
    //region 检查缴费年限
    if(!IsN($_payment_years)){
      if(!IsNum($_payment_years, false, false)){
        //return ReturnError(L("_ORDER_PAYMENT_YEARS_ERROR_"));
      }else{
        $SaveData["payment_years"] = $_payment_years;
      }
    }
    //endregion 检查缴费年限
  
    //region 检查保障额度
    if(!IsN($_guarantee_amount)){
      if(!IsNum($_guarantee_amount, false, false)){
        //return ReturnError(L("_ORDER_GUARANTEE_AMOUNT_ERROR_"));
      }else{
        $SaveData["guarantee_amount"] = $_guarantee_amount;
      }
    }
    //endregion 检查保障额度
  
    //region 检查年缴保费
    if(!IsN($_year_premium_amount)){
      if(!IsNum($_year_premium_amount, false, false)){
        //return ReturnError(L("_ORDER_YEAR_PREMIUM_AMOUNT_ERROR_"));
      }else{
        $SaveData["year_premium_amount"] = $_year_premium_amount;
      }
    }
    //endregion 检查年缴保费
  
    //region 保存证件照图片
    if(IsArray($_attachment)){
      $FileInfo = $this->UploadAttachmentImage($_attachment);
      if($FileInfo["error"] != 0){
        return $FileInfo;
      }
    
      $SaveData["attachment"] = $FileInfo["data"];
    }
    //endregion 保存证件照图片
    //endregion 参数检查
  
    if(IsArray($OrderInfo)){
      switch($OrderInfo["status"]){
        case $this->clsOrder->OrderStatusSignatoryStatus():
          $SaveData["status"] = $this->clsOrder->OrderStatusSubscribeStatus();
          break;

        case $this->clsOrder->OrderStatusSignatoryApproveStatus():
          //$SaveData["status"] = $this->clsOrder->OrderStatusSubscribeApproveStatus();
          $SaveData["status"] = $this->clsOrder->OrderStatusSubscribeStatus();
          break;
      }
      
      if(!IsNum($OrderInfo["status"], false, false)){
        $SaveData["signed_time"] = time();
      }
      
      $SaveData["update_time"] = time();
      $SaveResult = $this->modOrder->save($SaveData);
  
      if($SaveResult === false){
        return ReturnError(L("_SAVE_DATA_FAILURE_"));
      }
    }else{
      $SaveData["order_sn"] = $sOrderSn;
      $SaveData["signed_time"] = time();
      $SaveData["seller_manager_remarks"] = $_seller_manager_remarks;
      $SaveData["status"] = $this->clsOrder->OrderStatusSubscribeStatus();
  
      $SaveResult = $this->modOrder->add($SaveData);
  
      if($SaveResult === false){
        return ReturnError(L("_SAVE_DATA_FAILURE_"));
      }
    }
    
    $this->clsOrder->SetOrderDetailsCache($OrderInfo["order_sn"]);

    $NewOrderInfo = $this->clsOrder->GetOrderDetails($OrderInfo["order_sn"]);

    if(IsNum($NewOrderInfo["seller_member_id"], false, false)){
      $nUserID = $NewOrderInfo["seller_member_id"];
    }else{
      if(IsNum($NewOrderInfo["seller_captain_id"], false, false)){
        $nUserID = $NewOrderInfo["seller_captain_id"];
      }
    }

    if(IsNum($nUserID, false, false)){
      $nInsuranceCompanyUserID = $this->clsUser->ConstInsuranceCompanyUserID();
      $this->clsPolling->AddOrderStatusCount($nInsuranceCompanyUserID, $NewOrderInfo["status"]);
      $this->clsPolling->DelOrderStatusCount($nUserID, $OrderInfo["status"]);
    }

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"), $SaveResult);
  }
  
  public function AuditedPlanList($_user_id){
    $sField = "order_sn";
    $sWhere = "(seller_captain_id={$_user_id} or seller_member_id={$_user_id})";
    $sWhere .= " and (status={$this->clsOrder->OrderStatusSignatoryStatus()} or status={$this->clsOrder->OrderStatusSignatoryApproveStatus()})";
    $sWhere .= " and (seller_captain_id={$_user_id} or seller_member_id={$_user_id})";
    $sOrder = "id desc";
    
    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->order($sOrder)->select();
    $OrderList = array();
  
    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);
    
      $Info["order_sn"] = $OrderInfo["order_sn"];
      $Info["product_snapshots_id"] = $OrderInfo["product_snapshots_id"];
      $Info["customer_id"] = $OrderInfo["customer_id"];
      $Info["attachment"] = $OrderInfo["attachment"];
      $Info["seller_manager_remarks"] = $OrderInfo["seller_manager_remarks"];
      $Info["xf_attachment"] = $OrderInfo["xf_attachment"];
      
      array_push($OrderList, $OrderInfo);
    }
    
    return $OrderList;
  }
  //endregion 预约预约

  //region 消费预约
  /**
   * todo: 消费预约列表
   * @param       $_seller_captain_id
   * @param       $_seller_member_id
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function PayOrderList($_seller_captain_id, $_seller_member_id, $_rownum=20, $_param=array()){

    $sField = "order_sn";
    $sWhere = "status={$this->clsOrder->OrderStatusSubscribeApproveStatus()}";
    $sOrder = "id desc";

    if(IsNum($_seller_member_id, false, false)){
      $sWhere .= " and seller_member_id={$_seller_member_id}";
    }else{
      if(IsNum($_seller_captain_id, false, false)){
        $sWhere .= " and seller_captain_id={$_seller_captain_id}";
      }
    }

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "order_sn like '%{$_param["search_key"]}%'";
          $sWhere .= " or product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
          $sWhere .= " or customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
          $sWhere .= ")";
        }
      }else{
        switch($_param["search_case"]){
          case "order_sn":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and order_sn like '%{$_param["search_key"]}%'";
            }
            break;

          case "produc":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
            }
            break;

          case "customer":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
            }
            break;
        }
      }
    }

    $nTotal = $this->modOrder->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $OrderList = array();

    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);

      //region 产品信息
      $ProductInfo = $this->clsProduct->GetProductDetails($OrderInfo["product_id"]);
      $OrderInfo["product_name"] = $ProductInfo["product_name"];
      $OrderInfo["product_price"] = $ProductInfo["product_price"];
      //endregion 产品信息

      //region 客户信息
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
      $OrderInfo["customer_name"] = $CustomerInfo["real_name"];
      //endregion 客户信息

      if(IsNum($OrderInfo["seller_member_id"], false, false)){
        //region 客户经理信息
        $SellerMemberInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_member_id"]);
        $OrderInfo["seller_manager_name"] = $SellerMemberInfo["real_name"];
        //endregion 客户经理信息
      }else{
        //region 团队长信息
        $SellerCaptainInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_captain_id"]);
        $OrderInfo["seller_manager_name"] = $SellerCaptainInfo["real_name"];
        //endregion 团队长信息
      }

      //region 时间信息
      if(IsNum($OrderInfo["signed_time"], false, false)){
        $OrderInfo["signed_time"] = Time2FullDate($OrderInfo["signed_time"]);
      }

      if(IsNum($OrderInfo["reservation_time"], false, false)){
        $OrderInfo["reservation_time"] = Time2FullDate($OrderInfo["reservation_time"]);
      }else{
        $OrderInfo["reservation_time"] = "预约中";
      }

      if(IsNum($OrderInfo["pay_time"], false, false)){
        $OrderInfo["pay_time"] = Time2FullDate($OrderInfo["pay_time"]);
      }else{
        $OrderInfo["pay_time"] = "未消费";
      }

      if(IsNum($OrderInfo["visit_time"], false, false)){
        $OrderInfo["visit_time"] = Time2FullDate($OrderInfo["visit_time"]);
      }else{
        $OrderInfo["visit_time"] = "未回访";
      }
      //endregion 时间信息

      //region 预约状态
      $OrderStatusInfo = $this->clsOrder->OrderStatusGetOrderStatusConfig($OrderInfo["status"]);
      $OrderInfo["status_desc"] = $OrderStatusInfo["desc_style"];
      //endregion 预约状态

      array_push($OrderList, $OrderInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$OrderList);
  }

  /**
   * todo: 保存消费金额
   * @param $_order_sn
   * @param $_pay_amount
   *
   * @return array
   */
  public function PayOrderInfoSave($_order_sn, $_pay_amount, $_pay_time){
    //region 检查参数
    //region 检查预约编号
    if(IsN($_order_sn)){
      return ReturnError(L("_ORDER_SN_NULL_"));
    }else{
      $OrderInfo = $this->clsOrder->GetOrderDetails($_order_sn);

      if(!IsArray($OrderInfo)){
        return ReturnError(L("_ORDER_NOT_EXIST_"));
      }

      if($OrderInfo["status"] != $this->clsOrder->OrderStatusSubscribeApproveStatus()){
        return ReturnError(L("_ORDER_STATUS_ERROR_"));
      }

      $SaveData["id"] = $OrderInfo["id"];
    }
    //endregion 检查预约编号

    if(!IsNum($_pay_amount, false, false)){
      return ReturnError(L("_ORDER_PAY_AMOUNT_ERROR_"));
    }

    if(IsN($_pay_time)){
      return ReturnError(L("_ORDER_PAY_TIME_ERROR_"));
    }else{
      if(!isDateTime($_pay_time)){
        return ReturnError(L("_ORDER_PAY_TIME_ERROR_"));
      }

      $nPayTime = strtotime($_pay_time);
    }

    $SaveData["pay_amount"] = $_pay_amount;
    $SaveData["status"] = $this->clsOrder->OrderStatusPayStatus();
    $SaveData["pay_time"] = $nPayTime;
    $SaveData["update_time"] = time();
    //endregion 检查参数

    $SaveResult = $this->modOrder->save($SaveData);

    if($SaveResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }

    $this->clsOrder->SetOrderDetailsCache($OrderInfo["order_sn"]);
    $NewOrderInfo = $this->clsOrder->GetOrderDetails($OrderInfo["order_sn"]);

    if(IsNum($NewOrderInfo["seller_member_id"], false, false)){
      $nUserID = $NewOrderInfo["seller_member_id"];
    }else{
      if(IsNum($NewOrderInfo["seller_captain_id"], false, false)){
        $nUserID = $NewOrderInfo["seller_captain_id"];
      }
    }

    if(IsNum($nUserID, false, false)){
      $this->clsPolling->DelOrderStatusCount($nUserID, $OrderInfo["status"]);
    }

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"), $SaveResult);
  }
  
  /**
   * todo: 确认订单已消费
   *
   * @param $_order_sn
   * @param $_year_premium_amount
   * @param $_payment_years
   * @param $_guarantee_amount
   * @param $_product_id
   *
   * @param $_attachment
   *
   * @return array
   */
  public function ConfirmPaymentOrderInfoSave($_order_sn, $_year_premium_amount, $_payment_years, $_guarantee_amount, $_product_id, $_attachment){
    //region 检查参数
    //region 检查预约编号
    if(IsN($_order_sn)){
      return ReturnError(L("_ORDER_SN_NULL_"));
    }else{
      $OrderInfo = $this->clsOrder->GetOrderDetails($_order_sn);

      if(!IsArray($OrderInfo)){
        return ReturnError(L("_ORDER_NOT_EXIST_"));
      }

      if($OrderInfo["status"] != $this->clsOrder->OrderStatusSubscribeApproveStatus()){
        return ReturnError(L("_ORDER_STATUS_ERROR_"));
      }

      $SaveData["id"] = $OrderInfo["id"];
      
      if(!IsFloat($OrderInfo["year_premium_amount"], false, false)){
        if(!IsFloat($_year_premium_amount, false, false)){
          return ReturnError(L("_ORDER_YEAR_PREMIUM_AMOUNT_ERROR_"));
        }else{
          $SaveData["year_premium_amount"] = $_year_premium_amount;
        }
      }
  
      if(!IsFloat($OrderInfo["payment_years"], false, false)){
        if(!IsNum($_payment_years, false, false)){
          return ReturnError(L("_ORDER_PAYMENT_YEARS_ERROR_"));
        }else{
          $SaveData["payment_years"] = $_payment_years;
        }
      }
  
      if(!IsFloat($OrderInfo["guarantee_amount"], false, false)){
        if(!IsFloat($_guarantee_amount, true, false)){
          //return ReturnError(L("_ORDER_GUARANTEE_AMOUNT_ERROR_"));
        }else{
          $SaveData["guarantee_amount"] = $_guarantee_amount;
        }
      }
  
      //region 检查产品
      if(!IsNum($OrderInfo["product_snapshots_id"], false, false)){
        if(!IsNum($_product_id, false, false)){
          return ReturnError("请选择产品");
        }
  
        $ProductInfo = $this->clsProduct->GetProductDetails($_product_id);
  
        if(!IsArray($ProductInfo)){
          return ReturnError(L("_PRODUCT_NOT_EXIST_"));
        }
  
        //region 保存产品快照
        $BrokerCompanyInfo = $this->clsUser->GetUserDetails($ProductInfo["broker_company_id"]);
        $InsuranceCompanyInfo = $this->clsUser->GetUserDetails($ProductInfo["insurance_company_id"]);
        
        $ProductSnapshots["order_sn"] = $OrderInfo["order_sn"];
        $ProductSnapshots["product_id"] = $ProductInfo["id"];
        $ProductSnapshots["type_id"] = $ProductInfo["type_id"];
        $ProductSnapshots["type_name"] = $ProductInfo["type_name"];
        $ProductSnapshots["broker_company_user"] = $BrokerCompanyInfo["real_name"];
        $ProductSnapshots["insurance_company_user"] = $InsuranceCompanyInfo["real_name"];
        $ProductSnapshots["product_name"] = $ProductInfo["product_name"];
        $ProductSnapshots["payment_type"] = $ProductInfo["payment_type"];
        $ProductSnapshots["pay_type"] = $ProductInfo["pay_type"];
        $ProductSnapshots["min_age"] = $ProductInfo["min_age"];
        $ProductSnapshots["max_age"] = $ProductInfo["max_age"];
        $ProductSnapshots["validity_year"] = $ProductInfo["validity_year"];
        $ProductSnapshots["captain_first_rate"] = $ProductInfo["captain_first_rate"];
        $ProductSnapshots["captain_next_rate"] = $ProductInfo["captain_next_rate"];
        $ProductSnapshots["member_first_rate"] = $ProductInfo["member_first_rate"];
        $ProductSnapshots["member_next_rate"] = $ProductInfo["member_next_rate"];
        $ProductSnapshots["attachment_type"] = $ProductInfo["attachment_type"];
        $ProductSnapshots["attachment_id"] = $ProductInfo["attachment_id"];
  
        $modProductSnapshots = M("product_snapshots");
        $ProductSnapshotsResult = $modProductSnapshots->add($ProductSnapshots);
  
        if($ProductSnapshotsResult === false){
          return ReturnError(L("_ORDER_PRODUCT_SNAPSHOTS_SAVE_FAILURE_"));
        }
  
        $SaveData["product_snapshots_id"] = $ProductSnapshotsResult;
        //endregion 保存产品快照
      }
      //endregion 检查产品
  
      //region 处理附件
      if(!IsArray($OrderInfo["xf_attachment"])){
        if(!IsArray($_attachment)){
          return ReturnError(L("_ORDER_PLAN_ATTACHMENT_NULL_"));
        }else{
          if($_attachment["type"] == "image/jpeg" || $_attachment["type"] == "image/png"){
            $sFileType = "image";
          }else{
            $sFileType = $_attachment["type"];
          }
  
          switch($sFileType){
            case "image":
              $UploadResult = $this->UploadAttachmentImage($_attachment);
              $SaveData["attachment_type"] = $this->clsProduct->_AttachmentType["image"];
      
              break;
    
            default:
              $UploadResult = $this->UploadAttachmentFile($_attachment);
              $SaveData["attachment_type"] = $this->clsProduct->_AttachmentType["file"];
              break;
          }
  
          if($UploadResult["error"] != 0){
            return $UploadResult;
          }
          
          $SaveData["attachment_id"] = $UploadResult["data"];
        }
      }
      //endregion 处理附件
    }
    //endregion 检查预约编号

    $SaveData["status"] = $this->clsOrder->OrderStatusPayStatus();
    $SaveData["pay_time"] = time();
    $SaveData["update_time"] = time();
    //endregion 检查参数

    $SaveResult = $this->modOrder->save($SaveData);

    if($SaveResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }

    $this->clsOrder->SetOrderDetailsCache($OrderInfo["order_sn"]);
    $NewOrderInfo = $this->clsOrder->GetOrderDetails($OrderInfo["order_sn"]);

    if(IsNum($NewOrderInfo["seller_member_id"], false, false)){
      $nUserID = $NewOrderInfo["seller_member_id"];
    }else{
      if(IsNum($NewOrderInfo["seller_captain_id"], false, false)){
        $nUserID = $NewOrderInfo["seller_captain_id"];
      }
    }

    if(IsNum($nUserID, false, false)){
      $this->clsPolling->DelOrderStatusCount($nUserID, $OrderInfo["status"]);
    }
    
    //region 增加产品销售数量
    $ProductSnapshotsInfo = $this->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
    $this->clsProduct->PlusProductSellNumber($ProductSnapshotsInfo["product_id"]);
    //region 增加产品销售数量

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"), $SaveResult);
  }
  //endregion 消费预约

  //region 回访预约
  /**
   * todo: 回访预约列表
   * @param       $_seller_captain_id
   * @param       $_seller_member_id
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function ReturnVisitOrderList($_seller_captain_id, $_seller_member_id, $_rownum=20, $_param=array()){

    $sField = "order_sn";
    $sWhere = "status={$this->clsOrder->OrderStatusPayStatus()}";
    $sOrder = "id desc";

    if(IsNum($_seller_member_id, false, false)){
      $sWhere .= " and seller_member_id={$_seller_member_id}";
    }else{
      if(IsNum($_seller_captain_id, false, false)){
        $sWhere .= " and seller_captain_id={$_seller_captain_id}";
      }
    }

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "order_sn like '%{$_param["search_key"]}%'";
          $sWhere .= " or product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
          $sWhere .= " or customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
          $sWhere .= ")";
        }
      }else{
        switch($_param["search_case"]){
          case "order_sn":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and order_sn like '%{$_param["search_key"]}%'";
            }
            break;

          case "produc":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
            }
            break;

          case "customer":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
            }
            break;
        }
      }
    }

    $nTotal = $this->modOrder->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $OrderList = array();

    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);

      //region 产品信息
      $ProductInfo = $this->clsProduct->GetProductDetails($OrderInfo["product_id"]);
      $OrderInfo["product_name"] = $ProductInfo["product_name"];
      $OrderInfo["product_price"] = $ProductInfo["product_price"];
      //endregion 产品信息

      //region 客户信息
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
      $OrderInfo["customer_name"] = $CustomerInfo["real_name"];
      //endregion 客户信息

      if(IsNum($OrderInfo["seller_member_id"], false, false)){
        //region 客户经理信息
        $SellerMemberInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_member_id"]);
        $OrderInfo["seller_manager_name"] = $SellerMemberInfo["real_name"];
        //endregion 客户经理信息
      }else{
        //region 团队长信息
        $SellerCaptainInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_captain_id"]);
        $OrderInfo["seller_manager_name"] = $SellerCaptainInfo["real_name"];
        //endregion 团队长信息
      }

      //region 时间信息
      if(IsNum($OrderInfo["signed_time"], false, false)){
        $OrderInfo["signed_time"] = Time2FullDate($OrderInfo["signed_time"]);
      }

      if(IsNum($OrderInfo["reservation_time"], false, false)){
        $OrderInfo["reservation_time"] = Time2FullDate($OrderInfo["reservation_time"]);
      }else{
        $OrderInfo["reservation_time"] = "预约中";
      }

      if(IsNum($OrderInfo["pay_time"], false, false)){
        $OrderInfo["pay_time"] = Time2FullDate($OrderInfo["pay_time"]);
      }else{
        $OrderInfo["pay_time"] = "未消费";
      }

      if(IsNum($OrderInfo["visit_time"], false, false)){
        $OrderInfo["visit_time"] = Time2FullDate($OrderInfo["visit_time"]);
      }else{
        $OrderInfo["visit_time"] = "未回访";
      }
      //endregion 时间信息

      //region 预约状态
      $OrderStatusInfo = $this->clsOrder->OrderStatusGetOrderStatusConfig($OrderInfo["status"]);
      $OrderInfo["status_desc"] = $OrderStatusInfo["desc_style"];
      //endregion 预约状态

      //region 审批信息
      $sField = "id";
      $sWhere = "order_sn='{$val["order_sn"]}'";
      $sWhere .= " and status={$OrderInfo["status"]}";
      $sWhere .= " and add_time>{$OrderInfo["update_time"]}";
      $sOrder = "id desc";

      $OrderReviewID = $this->modOrderReview->where($sWhere)->order($sOrder)->getField($sField);

      if(IsNum($OrderReviewID, false, false)){
        $OrderReviewInfo = $this->clsOrder->GetOrderReviewDetails($OrderReviewID);
        $ReviewUserInfo = $this->clsUser->GetUserDetails($OrderReviewInfo["account_id"]);

        $OrderInfo["review_remarks"] = $OrderReviewInfo["review_remarks"];
        $OrderInfo["review_account"] = $ReviewUserInfo["real_name"];
      }

      //endregion 审批信息

      array_push($OrderList, $OrderInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$OrderList);
  }

  /**
   * todo: 保存回访预约
   *
   * @param $_order_sn
   * @param $_visit_time
   *
   * @return array
   *
   */
  public function ReturnVisitOrderInfoSave($_order_sn, $_visit_time){
    //region 检查参数
    //region 检查预约编号
    if(IsN($_order_sn)){
      return ReturnError(L("_ORDER_SN_NULL_"));
    }else{
      $OrderInfo = $this->clsOrder->GetOrderDetails($_order_sn);

      if(!IsArray($OrderInfo)){
        return ReturnError(L("_ORDER_NOT_EXIST_"));
      }

      if($OrderInfo["status"] != $this->clsOrder->OrderStatusPayStatus()){
        return ReturnError(L("_ORDER_STATUS_ERROR_"));
      }

      $SaveData["id"] = $OrderInfo["id"];
    }
    //endregion 检查预约编号

    if(IsN($_visit_time)){
      return ReturnError(L("_ORDER_VISIT_TIME_ERROR_"));
    }else{
      if(!isDateTime($_visit_time)){
        return ReturnError(L("_ORDER_VISIT_TIME_ERROR_"));
      }

      $nVisitTime = strtotime($_visit_time);
    }

    $SaveData["status"] = $this->clsOrder->OrderStatusReturnVisitStatus();
    $SaveData["visit_time"] = $nVisitTime;
    $SaveData["update_time"] = time();
    //endregion 检查参数

    $SaveResult = $this->modOrder->save($SaveData);

    if($SaveResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }

    $this->clsOrder->SetOrderDetailsCache($OrderInfo["order_sn"]);

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"), $SaveResult);
  }
  //endregion 消费预约

  //region 保险计划书
  /**
   * todo:保险计划书列表
   *
   * @param       $_seller_captain_id
   * @param       $_seller_member_id
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function PlanList($_seller_captain_id, $_seller_member_id, $_rownum=20, $_param=array()){

    $sField = "order_sn";
    $sWhere = "(status={$this->clsOrder->OrderStatusSignatoryStatus()} or status={$this->clsOrder->OrderStatusSignatoryApproveStatus()} or status={$this->clsOrder->OrderStatusSignatoryRejectStatus()})";
    $sOrder = "id desc";

    if(IsNum($_seller_member_id, false, false)){
      $sWhere .= " and seller_member_id={$_seller_member_id}";
    }else{
      if(IsNum($_seller_captain_id, false, false)){
        $sWhere .= " and seller_captain_id={$_seller_captain_id}";
      }
    }

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "order_sn like '%{$_param["search_key"]}%'";
          $sWhere .= " or product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
          $sWhere .= " or customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
          $sWhere .= ")";
        }
      }else{
        switch($_param["search_case"]){
          case "order_sn":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and order_sn like '%{$_param["search_key"]}%'";
            }
            break;

          case "produc":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
            }
            break;

          case "customer":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
            }
            break;
        }
      }
    }

    $nTotal = $this->modOrder->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $OrderList = array();

    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);

      //region 产品快照信息
      $ProductInfo = $this->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
      $OrderInfo["product_name"] = $ProductInfo["product_name"];
      //endregion 产品快照信息

      //region 客户信息
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
      $OrderInfo["customer_name"] = $CustomerInfo["real_name"];
      //endregion 客户信息

      if(IsNum($OrderInfo["seller_member_id"], false, false)){
        //region 客户经理信息
        $SellerMemberInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_member_id"]);
        $OrderInfo["seller_manager_name"] = $SellerMemberInfo["real_name"];
        //endregion 客户经理信息
      }else{
        //region 团队长信息
        $SellerCaptainInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_captain_id"]);
        $OrderInfo["seller_manager_name"] = $SellerCaptainInfo["real_name"];
        //endregion 团队长信息
      }

      //region 时间信息
      if(IsNum($OrderInfo["signed_time"], false, false)){
        $OrderInfo["signed_time"] = Time2FullDate($OrderInfo["signed_time"]);
      }

      if(IsNum($OrderInfo["reservation_time"], false, false)){
        $OrderInfo["reservation_time"] = Time2FullDate($OrderInfo["reservation_time"]);
      }else{
        $OrderInfo["reservation_time"] = "预约中";
      }

      if(IsNum($OrderInfo["pay_time"], false, false)){
        $OrderInfo["pay_time"] = Time2FullDate($OrderInfo["pay_time"]);
      }else{
        $OrderInfo["pay_time"] = "未消费";
      }

      if(IsNum($OrderInfo["visit_time"], false, false)){
        $OrderInfo["visit_time"] = Time2FullDate($OrderInfo["visit_time"]);
      }else{
        $OrderInfo["visit_time"] = "未回访";
      }
      //endregion 时间信息

      //region 预约状态
      $OrderStatusInfo = $this->clsOrder->OrderStatusGetOrderStatusConfig($OrderInfo["status"]);
      $OrderInfo["status_desc"] = $OrderStatusInfo["desc_style"];
      //endregion 预约状态

      //region 审批信息
      $sField = "id";
      $sWhere = "order_sn='{$OrderInfo["order_sn"]}'";
      $sWhere .= " and status>=10";
      $sWhere .= " and status<20";
      $sWhere .= " and add_time>{$OrderInfo["update_time"]}";
      $sOrder = "id desc";

      $OrderReviewID = $this->modOrderReview->where($sWhere)->order($sOrder)->getField($sField);

      if(IsNum($OrderReviewID, false, false)){
        $OrderReviewInfo = $this->clsOrder->GetOrderReviewDetails($OrderReviewID);
        $ReviewUserInfo = $this->clsUser->GetUserDetails($OrderReviewInfo["account_id"]);

        $OrderInfo["review_remarks"] = $OrderReviewInfo["review_remarks"];
        $OrderInfo["review_account"] = $ReviewUserInfo["real_name"];
      }

      //endregion 审批信息

      array_push($OrderList, $OrderInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$OrderList);
  }
  
  /**
   * todo: 保险计划书信息
   *
   * @param $_order_sn
   *
   * @return array
   *
   */
  public function PlanInfo($_order_sn){
    $OrderInfo = array();
    
    if(!IsN($_order_sn)){
      $OrderInfo = $this->clsOrder->GetOrderDetails($_order_sn);
      
      $OrderInfo["signed_time"] = Time2FullDate($OrderInfo["signed_time"], "Y-m-d H:i");
      $OrderInfo["reservation_time"] = Time2FullDate($OrderInfo["reservation_time"], "Y-m-d H:i");
      $OrderInfo["pay_time"] = Time2FullDate($OrderInfo["pay_time"], "Y-m-d H:i");
      $OrderInfo["visit_time"] = Time2FullDate($OrderInfo["visit_time"], "Y-m-d H:i");
      
      //region 预约状态
      $OrderStatusInfo = $this->clsOrder->OrderStatusGetOrderStatusConfig($OrderInfo["status"]);
      $OrderInfo["status_desc"] = $OrderStatusInfo["desc_style"];
      //endregion 预约状态
      
      //region 审批信息
      $sField = "id";
      $sWhere = "order_sn='{$OrderInfo["order_sn"]}'";
      $sWhere .= " and ((status>=20 and status<30) or status={$this->clsOrder->OrderStatusSignatoryApproveStatus()})";
      $sOrder = "id desc";
      
      $OrderReviewID = $this->modOrderReview->where($sWhere)->order($sOrder)->getField($sField);
      
      if(IsNum($OrderReviewID, false, false)){
        $OrderReviewInfo = $this->clsOrder->GetOrderReviewDetails($OrderReviewID);
        $ReviewUserInfo = $this->clsUser->GetUserDetails($OrderReviewInfo["account_id"]);
        
        $OrderInfo["review_remarks"] = $OrderReviewInfo["review_remarks"];
        $OrderInfo["review_account"] = $ReviewUserInfo["real_name"];
      }else{
        $OrderInfo["review_remarks"] = "--";
        $OrderInfo["review_account"] = "--";
      }
      
      //endregion 审批信息
    }
    
    return $OrderInfo;
  }
  
  /**
   * todo: 保存保险计划书
   *
   * @param $_order_sn
   * @param $_seller_captain_id
   * @param $_seller_member_id
   * @param $_customer_id
   * @param $_product_id
   * @param $_payment_years
   * @param $_guarantee_amount
   * @param $_year_premium_amount
   * @param $_seller_manager_remarks
   *
   * @param $_attachment
   *
   * @return array
   */
  public function PlanInfoSave($_order_sn, $_seller_captain_id, $_seller_member_id, $_customer_id, $_product_id, $_payment_years, $_guarantee_amount, $_year_premium_amount, $_seller_manager_remarks, $_attachment){
    //region 参数检查
    
    //region 检查团队长
    if(!IsNum($_seller_captain_id, false, false)){
      return ReturnError(L("_SELLER_CAPTAIN_ID_ERROR_"));
    }else{
      $SellerCaptainInfo = $this->clsUser->GetUserDetails($_seller_captain_id);
      
      if(!IsArray($SellerCaptainInfo)){
        return ReturnError(L("_SELLER_CAPTAIN_NOT_EXIST_"));
      }else{
        $UserGroupInfo = $this->clsUser->SellerCaptainUserGroup();
        
        if($SellerCaptainInfo["group_id"] != $UserGroupInfo["group_id"]){
          return ReturnError(L("_USER_GROUP_NOT_SELLER_CAPTAIN_"));
        }
      }
      
      $AddData["broker_company_id"] = $SellerCaptainInfo["parent_user_id"];
      $AddData["seller_captain_id"] = $SellerCaptainInfo["id"];
    }
    //endregion 检查团队长
    
    //region 检查客户经理
    if(!IsNum($_seller_member_id, false, false)){
      $AddData["seller_member_id"] = 0;
    }else{
      $SellerMemberInfo = $this->clsUser->GetUserDetails($_seller_member_id);
      
      if(!IsArray($SellerMemberInfo)){
        return ReturnError(L("_SELLER_CAPTAIN_NOT_EXIST_"));
      }else{
        $UserGroupInfo = $this->clsUser->SellereMemberUserGroup();
        
        if($SellerMemberInfo["group_id"] != $UserGroupInfo["group_id"]){
          return ReturnError(L("_USER_GROUP_NOT_SELLER_MEMBER_"));
        }
        
        if($SellerMemberInfo["parent_user_id"] != $SellerCaptainInfo["id"]){
          return ReturnError(L("_SELLER_MEMBER_NOT_SELLER_CAPTAIN_USER_"));
        }
      }
      
      $AddData["seller_member_id"] = $SellerMemberInfo["id"];
    }
    //endregion 检查客户经理
    
    //region 检查客户
    if(!IsNum($_customer_id, false, false)){
      return ReturnError(L("_CUSTOMER_ID_NULL_"));
    }else{
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($_customer_id);
      
      if(!IsArray($CustomerInfo)){
        return ReturnError(L("_CUSTOMER_NOT_EXIST_"));
      }
      
      $AddData["customer_id"] = $_customer_id;
    }
    //endregion 检查客户
    
    //region 检查产品
    if(!IsNum($_product_id, false, false)){
      return ReturnError(L("_PRODUCT_ID_NULL_"));
    }else{
      $ProductInfo = $this->clsProduct->GetProductDetails($_product_id);
      
      if(!IsArray($ProductInfo)){
        return ReturnError(L("_PRODUCT_NOT_EXIST_"));
      }
      
      $AddData["product_id"] = $_product_id;
    }
    //endregion 检查产品
  
    //region 检查缴费年限
    if(!IsN($_payment_years)){
      if(!IsNum($_payment_years, false, false)){
        return ReturnError(L("_ORDER_PAYMENT_YEARS_ERROR_"));
      }else{
        $AddData["payment_years"] = $_payment_years;
      }
    }
    //endregion 检查缴费年限
  
    //region 检查保障额度
    if(!IsN($_guarantee_amount)){
      if(!IsNum($_guarantee_amount, false, false)){
        return ReturnError(L("_ORDER_GUARANTEE_AMOUNT_ERROR_"));
      }else{
        $AddData["guarantee_amount"] = $_guarantee_amount;
      }
    }
    //endregion 检查保障额度
  
    //region 检查年缴保费
    if(!IsN($_year_premium_amount)){
      if(!IsNum($_year_premium_amount, false, false)){
        return ReturnError(L("_ORDER_YEAR_PREMIUM_AMOUNT_ERROR_"));
      }else{
        $AddData["year_premium_amount"] = $_year_premium_amount;
      }
    }
    //endregion 检查年缴保费
    
    //region 保存证件照图片
    if(IsArray($_attachment)){
      $FileInfo = $this->UploadAttachmentImage($_attachment);
      if($FileInfo["error"] != 0){
        return $FileInfo;
      }
      
      $AddData["attachment"] = $FileInfo["data"];
    }
    //endregion 保存证件照图片
    
    //endregion 参数检查
    
    $AddData["seller_manager_remarks"] = $_seller_manager_remarks;
    
    $OrderInfo = $this->clsOrder->GetOrderDetails($_order_sn);
    $nOrderStatus = $this->clsOrder->OrderStatusSignatoryStatus();
    $AddData["status"] = $nOrderStatus;
    $AddData["update_time"] = time();
    if(IsArray($OrderInfo)){
      if(
        $OrderInfo["status"] != $this->clsOrder->OrderStatusSignatoryStatus()
        && $OrderInfo["status"] != $this->clsOrder->OrderStatusSignatoryRejectStatus()
      ){
        return ReturnError("_ORDER_SIGNATORY_STATUS_CHNAGED_");
      }
      
      $sOrderSn = $OrderInfo["order_sn"];
      
      $AddData["id"] = $OrderInfo["id"];
      
      $SaveResult = $this->modOrder->save($AddData);
      
      if($SaveResult === false){
        return ReturnError(L("_SAVE_DATA_FAILURE_"));
      }
      
      $this->clsOrder->SetOrderDetailsCache($sOrderSn);
    }else{
      //region 添加基本数据
      $AddData["broker_company_remarks"] = "";
      
      $sOrderSn = $this->clsOrder->CreateOrderSn();
      $AddData["order_sn"] = $sOrderSn;
      $AddData["signed_time"] = time();
      $AddData["reservation_time"] = 0;
      $AddData["pay_time"] = 0;
      $AddData["visit_time"] = 0;
      
      $AddResult = $this->modOrder->add($AddData);
      
      if($AddResult === false){
        return ReturnError(L("_SAVE_DATA_FAILURE_"));
      }
      //endregion 添加基本数据
      
      //region 保存产品快照
      $BrokerCompanyInfo = $this->clsUser->GetUserDetails($ProductInfo["broker_company_id"]);
      $InsuranceCompanyInfo = $this->clsUser->GetUserDetails($ProductInfo["insurance_company_id"]);
      
      $ProductSnapshots["order_sn"] = $sOrderSn;
      $ProductSnapshots["product_id"] = $ProductInfo["id"];
      $ProductSnapshots["type_id"] = $ProductInfo["type_id"];
      $ProductSnapshots["type_name"] = $ProductInfo["type_name"];
      $ProductSnapshots["broker_company_user"] = $BrokerCompanyInfo["real_name"];
      $ProductSnapshots["insurance_company_user"] = $InsuranceCompanyInfo["real_name"];
      $ProductSnapshots["product_name"] = $ProductInfo["product_name"];
      $ProductSnapshots["payment_type"] = $ProductInfo["payment_type"];
      $ProductSnapshots["pay_type"] = $ProductInfo["pay_type"];
      $ProductSnapshots["min_age"] = $ProductInfo["min_age"];
      $ProductSnapshots["max_age"] = $ProductInfo["max_age"];
      $ProductSnapshots["validity_year"] = $ProductInfo["validity_year"];
      $ProductSnapshots["captain_first_rate"] = $ProductInfo["captain_first_rate"];
      $ProductSnapshots["captain_next_rate"] = $ProductInfo["captain_next_rate"];
      $ProductSnapshots["member_first_rate"] = $ProductInfo["member_first_rate"];
      $ProductSnapshots["member_next_rate"] = $ProductInfo["member_next_rate"];
      $ProductSnapshots["attachment_type"] = $ProductInfo["attachment_type"];
      $ProductSnapshots["attachment_id"] = $ProductInfo["attachment_id"];
      
      $modProductSnapshots = M("product_snapshots");
      $ProductSnapshotsResult = $modProductSnapshots->add($ProductSnapshots);
      
      if($ProductSnapshotsResult !== false){
        $SaveData["id"] = $AddResult;
        $SaveData["product_snapshots_id"] = $ProductSnapshotsResult;
        
        $SaveResult = $this->modOrder->save($SaveData);
      }
      //endregion 保存产品快照
    }
    
    $this->clsOrder->SetOrderDetailsCache($sOrderSn);
    
    $nUserID = $this->clsUser->ConstInsuranceCompanyUserID();
    $this->clsPolling->AddOrderStatusCount($nUserID, $nOrderStatus);
    
    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }
  //endregion 保险计划书

  //endregion 申请

  //region 审核
  /**
   * todo: 计划书列表
   *
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function AuditPlanList($_rownum=20, $_param=array()){
    $sField = "order_sn";
    $sWhere = "(status={$this->clsOrder->OrderStatusSignatoryStatus()} or status={$this->clsOrder->OrderStatusSignatoryApproveStatus()} or status={$this->clsOrder->OrderStatusSignatoryRejectStatus()})";
    $sOrder = "id desc";
    
    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "order_sn like '%{$_param["search_key"]}%'";
          $sWhere .= " or product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
          $sWhere .= " or customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
          $sWhere .= ")";
        }
      }else{
        switch($_param["search_case"]){
          case "order_sn":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and order_sn like '%{$_param["search_key"]}%'";
            }
            break;
          
          case "produc":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
            }
            break;
          
          case "customer":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
            }
            break;
        }
      }
    }
    
    $nTotal = $this->modOrder->where($sWhere)->count();
    
    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出
    
    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $OrderList = array();
    
    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);
      
      //region 产品快照信息
      $ProductInfo = $this->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
      $OrderInfo["product_name"] = $ProductInfo["product_name"];
      //endregion 产品快照信息
      
      //region 客户信息
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
      $OrderInfo["customer_name"] = $CustomerInfo["real_name"];
      //endregion 客户信息
      
      //region 预约状态
      $OrderStatusInfo = $this->clsOrder->OrderStatusGetOrderStatusConfig($OrderInfo["status"]);
      $OrderInfo["status_desc"] = $OrderStatusInfo["desc_style"];
      //endregion 预约状态
      
      array_push($OrderList, $OrderInfo);
    }
    
    return array("PageInfo"=>$PageShow, "DataList"=>$OrderList);
  }
  
  /**
   * todo: 计划书信息
   *
   * @param $_order_sn
   *
   * @return array
   *
   */
  public function AuditPlanInfo($_order_sn){
    $OrderInfo = array();
    
    if(!IsN($_order_sn)){
      $OrderInfo = $this->clsOrder->GetOrderDetails($_order_sn);
      
      $OrderInfo["signed_time"] = Time2FullDate($OrderInfo["signed_time"], "Y-m-d H:i");
      
      //region 预约状态
      $OrderStatusInfo = $this->clsOrder->OrderStatusGetOrderStatusConfig($OrderInfo["status"]);
      $OrderInfo["status_desc"] = $OrderStatusInfo["desc_style"];
      //endregion 预约状态
    }
    
    return $OrderInfo;
  }
  
  /**
   * todo: 待审核预约列表
   * @param       $_seller_captain_id
   * @param       $_seller_member_id
   * @param       $_order_status
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function OrderList($_seller_captain_id, $_seller_member_id, $_order_status, $_rownum=20, $_param=array()){
    $sField = "order_sn";
    $sWhere = "1=1";
    $sOrder = "id desc";

    switch($_order_status){
      case $this->clsOrder->OrderStatusSignatoryStatus():
        $sWhere .= " and (status=10 or status=12)";
        break;

      case $this->clsOrder->OrderStatusSubscribeStatus():
        $sWhere .= " and (status=20 or status=21 or status=22)";
        break;
    }

    if(IsNum($_seller_member_id, false, false)){
      $sWhere .= " and seller_member_id={$_seller_member_id}";
    }else{
      if(IsNum($_seller_captain_id, false, false)){
        $sWhere .= " and seller_captain_id={$_seller_captain_id}";
      }
    }

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "order_sn like '%{$_param["search_key"]}%'";
          $sWhere .= " or product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
          $sWhere .= " or customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
          $sWhere .= ")";
        }
      }else{
        switch($_param["search_case"]){
          case "order_sn":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and order_sn like '%{$_param["search_key"]}%'";
            }
            break;

          case "produc":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
            }
            break;

          case "customer":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
            }
            break;
        }
      }

      //销售经理
      if(IsNum($_param["search_seller_manager"], false, false)){
        $sWhere .= " and seller_manager_id = {$_param["search_seller_manager"]}";
      }

      //经纪公司
      if(IsNum($_param["search_broker_company"], false, false)){
        $sWhere .= " and broker_company_id = {$_param["search_broker_company"]}";
      }
    }

    $nTotal = $this->modOrder->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $OrderList = array();

    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);

      //region 产品信息
      $ProductInfo = $this->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_id"]);
      $OrderInfo["product_name"] = $ProductInfo["product_name"];
      //endregion 产品信息

      //region 客户信息
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
      $OrderInfo["customer_name"] = $CustomerInfo["real_name"];
      //endregion 客户信息

      if(IsNum($OrderInfo["seller_member_id"], false, false)){
        //region 客户经理信息
        $SellerMemberInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_member_id"]);
        $OrderInfo["seller_manager_name"] = $SellerMemberInfo["real_name"];
        //endregion 客户经理信息
      }else{
        //region 团队长信息
        $SellerCaptainInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_captain_id"]);
        $OrderInfo["seller_manager_name"] = $SellerCaptainInfo["real_name"];
        //endregion 团队长信息
      }

      //region 时间信息
      if(IsNum($OrderInfo["signed_time"], false, false)){
        $OrderInfo["signed_time"] = Time2FullDate($OrderInfo["signed_time"]);
      }

      if(IsNum($OrderInfo["reservation_time"], false, false)){
        $OrderInfo["reservation_time"] = Time2FullDate($OrderInfo["reservation_time"]);
      }else{
        $OrderInfo["reservation_time"] = "预约中";
      }

      if(IsNum($OrderInfo["pay_time"], false, false)){
        $OrderInfo["pay_time"] = Time2FullDate($OrderInfo["pay_time"]);
      }else{
        $OrderInfo["pay_time"] = "未消费";
      }

      if(IsNum($OrderInfo["visit_time"], false, false)){
        $OrderInfo["visit_time"] = Time2FullDate($OrderInfo["visit_time"]);
      }else{
        $OrderInfo["visit_time"] = "未回访";
      }
      //endregion 时间信息

      //region 预约状态
      $OrderStatusInfo = $this->clsOrder->OrderStatusGetOrderStatusConfig($OrderInfo["status"]);
      $OrderInfo["status_desc"] = $OrderStatusInfo["desc_style"];
      //endregion 预约状态

      array_push($OrderList, $OrderInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$OrderList);
  }
  
  /**
   * todo: 待审核预约列表
   * @param       $_seller_captain_id
   * @param       $_seller_member_id
   * @param       $_order_status
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function AuditSubscribeOrderList($_rownum=20, $_param=array()){
    $sField = "order_sn";
    $sWhere = "(status={$this->clsOrder->OrderStatusSubscribeStatus()} or status={$this->clsOrder->OrderStatusSubscribeApproveStatus()} or status={$this->clsOrder->OrderStatusSubscribeRejectStatus()})";
    $sWhere .= " and reservation_time>0";
    $sOrder = "id desc";
    
    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "order_sn like '%{$_param["search_key"]}%'";
          $sWhere .= " or product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
          $sWhere .= " or customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
          $sWhere .= ")";
        }
      }else{
        switch($_param["search_case"]){
          case "order_sn":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and order_sn like '%{$_param["search_key"]}%'";
            }
            break;
          
          case "produc":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
            }
            break;
          
          case "customer":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
            }
            break;
        }
      }
      
      //销售经理
      if(IsNum($_param["search_seller_manager"], false, false)){
        $sWhere .= " and seller_manager_id = {$_param["search_seller_manager"]}";
      }
      
      //经纪公司
      if(IsNum($_param["search_broker_company"], false, false)){
        $sWhere .= " and broker_company_id = {$_param["search_broker_company"]}";
      }
    }
    
    $nTotal = $this->modOrder->where($sWhere)->count();
    
    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出
    
    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $OrderList = array();
    
    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);
      
      array_push($OrderList, $OrderInfo);
    }
    
    return array("PageInfo"=>$PageShow, "DataList"=>$OrderList);
  }

  /**
   * todo: 审核待审核的预约
   *
   * @param $_user_id
   * @param $_order_sn
   * @param $_review_remarks
   * @param $_review_status
   * @param $_attachment
   *
   * @return array
   */
  public function AuditSignatoryOrderInfoSave($_user_id, $_order_sn, $_review_remarks, $_review_status, $_attachment){
    $AddData = array();

    //region 参数检查

    //region 检查账号ID
    if(!IsNum($_user_id, false, false)){
      return ReturnError(L("_ACCOUNT_ERROR_"));
    }else{
      $UserInfo = $this->clsUser->GetUserDetails($_user_id);

      if(!IsArray($UserInfo)){
        return ReturnError(L("_USER_NOT_EXIST_"));
      }

      $AddData["account_id"] = $UserInfo["id"];
    }
    //endregion 检查账号ID

    //region 检查账号类型
    $UserGroupInfo = $this->clsUser->SpecialUserGroup();
    if($UserInfo["group_id"] != $UserGroupInfo["group_id"]){
      return ReturnError(L("_ACCOUNT_NOT_PERMISSION_"));
    }else{
      $AddData["account_type"] = $this->clsUser->_UserType["special"];
    }
    //endregion 检查账号类型

    //region 检查预约编号
    if(IsN($_order_sn)){
      return ReturnError(L("_ORDER_SN_NULL_"));
    }else{
      $OrderInfo = $this->clsOrder->GetOrderDetails($_order_sn);

      if(!IsArray($OrderInfo)){
        return ReturnError(L("_ORDER_NOT_EXIST_"));
      }

      $AddData["order_sn"] = $_order_sn;
    }
    //endregion 检查预约编号

    //region 检查审批信息
    if(IsN($_review_remarks)){
      //return ReturnError(L("_ORDER_REVIEW_REMARKS_NULL_"));
    }else{
      $AddData["review_remarks"] = $_review_remarks;
    }
    //endregion 检查审批信息

    //region 检查审批状态
    if(IsN($_review_status)){
      return ReturnError(L("_ORDER_REVIEW_STATUS_ERROR_"));
    }else{
      switch($_review_status){
        case "approve":
          $nStatus = $this->clsOrder->OrderStatusSignatoryApproveStatus();
          break;

        case "reject":
          $nStatus = $this->clsOrder->OrderStatusSignatoryRejectStatus();
          break;

        default:
          return ReturnError(L("_ORDER_REVIEW_STATUS_ERROR_"));
          break;
      }
    }
    //endregion 检查审批状态

    //endregion 参数检查

    $AddData["status"] = $nStatus;
    $AddData["add_time"] = time();

    $AddResult = $this->modOrderReview->add($AddData);

    if($AddResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }

    $nReviewID = $AddResult;

    //region 更新订单审批状态
    $SaveData["id"] = $OrderInfo["id"];
    $SaveData["status"] = $nStatus;
    $SaveResult = $this->modOrder->save($SaveData);

    if($SaveResult === false){
      //region 删除审核数据
      $sWhere = "id={$nReviewID}";
      $this->modOrderReview->where($sWhere)->delete();
      //endregion 删除审核数据

      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    //endregion 更新订单审批状态

    //region 处理附件
    if(IsArray($_attachment)){
      if($_attachment["type"] == "image/jpeg" || $_attachment["type"] == "image/png"){
        $sFileType = "image";
      }else{
        $sFileType = $_attachment["type"];
      }

      switch($sFileType){
        case "image":
          $UploadResult = $this->UploadAttachmentImage($_attachment);
          $SaveAttachmentData["attachment_type"] = $this->clsProduct->_AttachmentType["image"];

          break;

        default:
          $UploadResult = $this->UploadAttachmentFile($_attachment);
          $SaveAttachmentData["attachment_type"] = $this->clsProduct->_AttachmentType["file"];
          break;
      }

      if($UploadResult["error"] != 0){
        return $UploadResult;
      }

      $SaveAttachmentData["id"] = $OrderInfo["id"];
      $SaveAttachmentData["attachment_id"] = $UploadResult["data"];

      $Result = $this->modOrder->save($SaveAttachmentData);
    }
    //endregion 处理附件

    $this->clsOrder->GetOrderReviewDetails($nReviewID);
    $this->clsOrder->SetOrderDetailsCache($OrderInfo["order_sn"]);
    $OrderInfo = $this->clsOrder->GetOrderDetails($OrderInfo["order_sn"]);

    if(IsNum($OrderInfo["seller_member_id"], false, false)){
      $nUserID = $OrderInfo["seller_member_id"];
    }else{
      if(IsNum($OrderInfo["seller_captain_id"], false, false)){
        $nUserID = $OrderInfo["seller_captain_id"];
      }
    }

    if(IsNum($nUserID, false, false)){
      $this->clsPolling->AddOrderStatusCount($nUserID, $OrderInfo["status"]);
      $this->clsPolling->DelOrderStatusCount($_user_id, $this->clsOrder->OrderStatusSignatoryStatus());
    }

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo: 审核预约预约
   * @param $_user_id
   * @param $_order_sn
   * @param $_review_remarks
   * @param $_review_status
   *
   * @return array
   */
  public function AuditSubscribeOrderInfoSave($_user_id, $_order_sn, $_review_remarks, $_review_status, $_attachment){
    $AddData = array();

    //region 参数检查

    //region 检查账号ID
    if(!IsNum($_user_id, false, false)){
      return ReturnError(L("_ACCOUNT_ERROR_"));
    }else{
      $UserInfo = $this->clsUser->GetUserDetails($_user_id);

      if(!IsArray($UserInfo)){
        return ReturnError(L("_USER_NOT_EXIST_"));
      }

      $AddData["account_id"] = $UserInfo["id"];
    }
    //endregion 检查账号ID

    //region 检查账号类型
    $UserGroupInfo = $this->clsUser->SpecialUserGroup();
    if($UserInfo["group_id"] != $UserGroupInfo["group_id"]){
      return ReturnError(L("_ACCOUNT_NOT_PERMISSION_"));
    }else{
      $AddData["account_type"] = $this->clsUser->_UserType["special"];
    }
    //endregion 检查账号类型

    //region 检查预约编号
    if(IsN($_order_sn)){
      return ReturnError(L("_ORDER_SN_NULL_"));
    }else{
      $OrderInfo = $this->clsOrder->GetOrderDetails($_order_sn);

      if(!IsArray($OrderInfo)){
        return ReturnError(L("_ORDER_NOT_EXIST_"));
      }

      $AddData["order_sn"] = $_order_sn;
    }
    //endregion 检查预约编号

    //region 检查审批信息
    if(IsN($_review_remarks)){
      //return ReturnError(L("_ORDER_REVIEW_REMARKS_NULL_"));
    }else{
      $AddData["review_remarks"] = $_review_remarks;
    }
    //endregion 检查审批信息

    //region 检查审批状态
    if(IsN($_review_status)){
      return ReturnError(L("_ORDER_REVIEW_STATUS_ERROR_"));
    }else{
      switch($_review_status){
        case "approve":
          $nStatus = $this->clsOrder->OrderStatusSubscribeApproveStatus();
          break;

        case "reject":
          $nStatus = $this->clsOrder->OrderStatusSubscribeRejectStatus();
          break;

        default:
          return ReturnError(L("_ORDER_REVIEW_STATUS_ERROR_"));
          break;
      }
    }
    //endregion 检查审批状态

    //endregion 参数检查

    $AddData["status"] = $nStatus;
    $AddData["add_time"] = time();

    $AddResult = $this->modOrderReview->add($AddData);

    if($AddResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }

    $nReviewID = $AddResult;

    //region 更新订单审批状态
    $SaveData["id"] = $OrderInfo["id"];
    $SaveData["status"] = $nStatus;
    $SaveResult = $this->modOrder->save($SaveData);

    if($SaveResult === false){
      //region 删除审核数据
      $sWhere = "id={$nReviewID}";
      $this->modOrderReview->where($sWhere)->delete();
      //endregion 删除审核数据

      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    //endregion 更新订单审批状态
  
    //region 处理附件
    if(IsArray($_attachment)){
      if($_attachment["type"] == "image/jpeg" || $_attachment["type"] == "image/png"){
        $sFileType = "image";
      }else{
        $sFileType = $_attachment["type"];
      }
    
      switch($sFileType){
        case "image":
          $UploadResult = $this->UploadAttachmentImage($_attachment);
          $SaveAttachmentData["attachment_type"] = $this->clsProduct->_AttachmentType["image"];
        
          break;
      
        default:
          $UploadResult = $this->UploadAttachmentFile($_attachment);
          $SaveAttachmentData["attachment_type"] = $this->clsProduct->_AttachmentType["file"];
          break;
      }
    
      if($UploadResult["error"] != 0){
        return $UploadResult;
      }
    
      $SaveAttachmentData["id"] = $OrderInfo["id"];
      $SaveAttachmentData["attachment_id"] = $UploadResult["data"];
    
      $Result = $this->modOrder->save($SaveAttachmentData);
    }
    //endregion 处理附件

    $this->clsOrder->GetOrderReviewDetails($nReviewID);
    $this->clsOrder->SetOrderDetailsCache($OrderInfo["order_sn"]);
    $NewOrderInfo = $this->clsOrder->GetOrderDetails($OrderInfo["order_sn"]);

    if(IsNum($NewOrderInfo["seller_member_id"], false, false)){
      $nUserID = $NewOrderInfo["seller_member_id"];
    }else{
      if(IsNum($NewOrderInfo["seller_captain_id"], false, false)){
        $nUserID = $NewOrderInfo["seller_captain_id"];
      }
    }

    if(IsNum($nUserID, false, false)){
      $this->clsPolling->AddOrderStatusCount($nUserID, $NewOrderInfo["status"]);
      $this->clsPolling->DelOrderStatusCount($_user_id, $OrderInfo["status"]);
    }

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }
  //endregion 审核

  //region 已完成预约
  /**
   * todo: 已完成预约列表
   * @param       $_seller_captain_id
   * @param       $_seller_member_id
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function CompletedOrderList($_seller_captain_id, $_seller_member_id, $_rownum=20, $_param=array()){

    $sField = "order_sn";
    $sWhere = "status={$this->clsOrder->OrderStatusPayStatus()}";
    $sOrder = "id desc";

    if(IsNum($_seller_member_id, false, false)){
      $sWhere .= " and seller_member_id={$_seller_member_id}";
    }else{
      if(IsNum($_seller_captain_id, false, false)){
        $sWhere .= " and seller_captain_id={$_seller_captain_id}";
      }
    }

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "order_sn like '%{$_param["search_key"]}%'";
          $sWhere .= " or product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
          $sWhere .= " or customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
          $sWhere .= ")";
        }
      }else{
        switch($_param["search_case"]){
          case "order_sn":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and order_sn like '%{$_param["search_key"]}%'";
            }
            break;

          case "produc":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
            }
            break;

          case "customer":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
            }
            break;
        }
      }
    }

    $nTotal = $this->modOrder->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $OrderList = array();

    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);

      //region 产品信息
      $ProductInfo = $this->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
      $OrderInfo["product_name"] = $ProductInfo["product_name"];
      //endregion 产品信息

      //region 客户信息
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
      $OrderInfo["customer_name"] = $CustomerInfo["real_name"];
      //endregion 客户信息

      if(IsNum($OrderInfo["seller_member_id"], false, false)){
        //region 客户经理信息
        $SellerMemberInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_member_id"]);
        $OrderInfo["seller_manager_name"] = $SellerMemberInfo["real_name"];
        //endregion 客户经理信息
      }else{
        //region 团队长信息
        $SellerCaptainInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_captain_id"]);
        $OrderInfo["seller_manager_name"] = $SellerCaptainInfo["real_name"];
        //endregion 团队长信息
      }

      //region 时间信息
      if(IsNum($OrderInfo["signed_time"], false, false)){
        $OrderInfo["signed_time"] = Time2FullDate($OrderInfo["signed_time"]);
      }

      if(IsNum($OrderInfo["reservation_time"], false, false)){
        $OrderInfo["reservation_time"] = Time2FullDate($OrderInfo["reservation_time"]);
      }else{
        $OrderInfo["reservation_time"] = "预约中";
      }

      if(IsNum($OrderInfo["pay_time"], false, false)){
        $OrderInfo["pay_time"] = Time2FullDate($OrderInfo["pay_time"]);
      }else{
        $OrderInfo["pay_time"] = "未消费";
      }

      if(IsNum($OrderInfo["visit_time"], false, false)){
        $OrderInfo["visit_time"] = Time2FullDate($OrderInfo["visit_time"]);
      }else{
        $OrderInfo["visit_time"] = "未回访";
      }
      //endregion 时间信息

      //region 预约状态
      $OrderStatusInfo = $this->clsOrder->OrderStatusGetOrderStatusConfig($OrderInfo["status"]);
      $OrderInfo["status_desc"] = $OrderStatusInfo["desc_style"];
      //endregion 预约状态

      array_push($OrderList, $OrderInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$OrderList);
  }
  //endregion 已完成预约

  //region 已交易预约
  /**
   * todo: 已交易预约列表
   * @param       $_seller_captain_id
   * @param       $_seller_member_id
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function BusinessOrderList($_seller_captain_id, $_seller_member_id, $_rownum=20, $_param=array()){

    $sField = "order_sn";
    $sWhere = "status={$this->clsOrder->OrderStatusPayStatus()}";
    $sOrder = "id desc";

    if(IsNum($_seller_member_id, false, false)){
      $sWhere .= " and seller_member_id={$_seller_member_id}";
    }else{
      if(IsNum($_seller_captain_id, false, false)){
        $sWhere .= " and seller_captain_id={$_seller_captain_id}";
      }
    }

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "order_sn like '%{$_param["search_key"]}%'";
          $sWhere .= " or product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
          $sWhere .= " or customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
          $sWhere .= ")";
        }
      }else{
        switch($_param["search_case"]){
          case "order_sn":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and order_sn like '%{$_param["search_key"]}%'";
            }
            break;

          case "produc":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and product_id in (select id from ". tn("product") ." where product_name like '%{$_param["search_key"]}%')";
            }
            break;

          case "customer":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and customer_id in (select id from ". tn("customer") ." where real_name like '%{$_param["search_key"]}%')";
            }
            break;
        }
      }
    }

    $nTotal = $this->modOrder->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $OrderSnList = $this->modOrder->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $OrderList = array();

    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $this->clsOrder->GetOrderDetails($val["order_sn"]);

      //region 产品信息
      $ProductInfo = $this->clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);
      $OrderInfo["product_name"] = $ProductInfo["product_name"];
      //endregion 产品信息

      //region 客户信息
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($OrderInfo["customer_id"]);
      $OrderInfo["customer_name"] = $CustomerInfo["real_name"];
      //endregion 客户信息

      if(IsNum($OrderInfo["seller_member_id"], false, false)){
        //region 客户经理信息
        $SellerMemberInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_member_id"]);
        $OrderInfo["seller_manager_name"] = $SellerMemberInfo["real_name"];
        //endregion 客户经理信息
      }else{
        //region 团队长信息
        $SellerCaptainInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_captain_id"]);
        $OrderInfo["seller_manager_name"] = $SellerCaptainInfo["real_name"];
        //endregion 团队长信息
      }

      //region 时间信息
      if(IsNum($OrderInfo["signed_time"], false, false)){
        $OrderInfo["signed_time"] = Time2FullDate($OrderInfo["signed_time"]);
      }

      if(IsNum($OrderInfo["reservation_time"], false, false)){
        $OrderInfo["reservation_time"] = Time2FullDate($OrderInfo["reservation_time"]);
      }else{
        $OrderInfo["reservation_time"] = "预约中";
      }

      if(IsNum($OrderInfo["pay_time"], false, false)){
        $OrderInfo["pay_time"] = Time2FullDate($OrderInfo["pay_time"]);
      }else{
        $OrderInfo["pay_time"] = "未消费";
      }

      if(IsNum($OrderInfo["visit_time"], false, false)){
        $OrderInfo["visit_time"] = Time2FullDate($OrderInfo["visit_time"]);
      }else{
        $OrderInfo["visit_time"] = "未回访";
      }
      //endregion 时间信息

      //region 预约状态
      $OrderStatusInfo = $this->clsOrder->OrderStatusGetOrderStatusConfig($OrderInfo["status"]);
      $OrderInfo["status_desc"] = $OrderStatusInfo["desc_style"];
      //endregion 预约状态

      array_push($OrderList, $OrderInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$OrderList);
  }
  //endregion 已交易预约


  //region 预约
  /**
   * todo: 删除预约
   *
   * @param $_id_str id字符串，逗号分割id
   *
   * @return array
   */
  public function OrderDelete($_order_sn_str){
    if(IsN($_order_sn_str)){
      return ReturnError(L("_ORDER_SN_NULL_"));
    }else{
      $SnList = explode(",", $_order_sn_str);

      foreach($SnList as $key=>$val){
        $OrderInfo = $this->clsOrder->GetOrderDetails($val);
        if(!IsArray($OrderInfo)){
          return ReturnError(L("_ORDER_NOT_EXIST_"));
        }
      }
    }

    $sWhere = "order_sn in ({$_order_sn_str})";
    $Result = $this->modOrder->where($sWhere)->delete();

    if($Result === false){
      return ReturnError(L("_DELETE_DATA_FAILURE_"));
    }

    foreach($SnList as $key=>$val){
      $this->clsOrder->SetOrderDetailsCache($val);
    }

    return ReturnCorrect(L("_DELETE_DATA_SUCCEED_"));
  }

  /**
   * todo: 刷新预约缓存
   */
  public function ResetOrderCache(){
    $sField = "id";
    $OrderIdList = $this->modOrder->field($sField)->select();

    foreach($OrderIdList as $key=>$val){
      $this->clsOrder->SetOrderDetailsCache($val["id"]);
    }

    return ReturnCorrect(L("_CACHE_REFRESH_SUCCEED_"));
  }
  //endregion 预约
}
