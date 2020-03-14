<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

class OrderModel extends Model {
  Protected $autoCheckFields = false;

  private $modOrder = null;
  private $modOrderReview = null;
  
  private $clsOrder = null;
  private $clsBrokerCompany = null;
  private $clsServiceProviders = null;
  private $clsSellerManager = null;
  private $clsCustomer = null;
  private $clsProduct = null;
  private $clsLogin = null;
  
  function __construct() {
    $this->modOrder = M("order");
    $this->modOrderReview = M("order_review");
    
    $this->clsOrder = new \Org\ZhiHui\Order();
    $this->clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    $this->clsServiceProviders = new \Org\ZhiHui\ServiceProviders();
    $this->clsSellerManager = new \Org\ZhiHui\SellerManager();
    $this->clsCustomer = new \Org\ZhiHui\Customer();
    $this->clsProduct = new \Org\ZhiHui\Product();
    $this->clsLogin = new \Org\ZhiHui\Login();
  }
  
  //region 预约
  /**
   * todo:预约列表
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function OrderList($_rownum=20, $_param=array()){
    $sField = "order_sn";
    $sWhere = "1=1";
    $sOrder = "review asc, id desc";

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

      //服务商
      if(IsNum($_param["search_service_providers"], false, false)){
        $sWhere .= " and service_providers_id = {$_param["search_service_providers"]}";
      }

      //销售经理
      if(IsNum($_param["search_seller_manager"], false, false)){
        $sWhere .= " and seller_manager_id = {$_param["search_seller_manager"]}";
      }

      //经纪公司
      if(IsNum($_param["search_broker_company"], false, false)){
        $sWhere .= " and broker_company_id = {$_param["search_broker_company"]}";
      }

      //门店
      if(IsNum($_param["search_store"], false, false)){
        $sWhere .= " and store_id = {$_param["search_store"]}";
      }

      //门店
      if(IsNum($_param["search_store"], false, false)){
        $sWhere .= " and store_id = {$_param["search_store"]}";
      }

      //审批状态
      if(IsNum($_param["search_review"], true, false)){
        $sWhere .= " and review = {$_param["search_review"]}";
      }

      //预约状态状态
      if(IsNum($_param["search_status"], true, false)){
        $sWhere .= " and status = {$_param["search_status"]}";
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

      //region 销售经理信息
      $SellerManagerInfo = $this->clsSellerManager->GetSellerManagerDetails($OrderInfo["seller_manager_id"]);
      $OrderInfo["seller_manager_name"] = $SellerManagerInfo["real_name"];
      //endregion 销售经理信息

      //region 服务商信息
      $ServiceProvidersInfo = $this->clsServiceProviders->GetServiceProvidersDetails($OrderInfo["service_providers_id"]);
      $OrderInfo["service_providers_name"] = $ServiceProvidersInfo["service_providers_name"];
      //endregion 服务商信息

      //region 门店信息
      $StoreInfo = $this->clsBrokerCompany->GetBrokerCompanyStoreDetails($OrderInfo["store_id"]);
      $OrderInfo["store_name"] = $StoreInfo["store_name"];
      //endregion 门店信息

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
      
      //region 审批状态
      $OrderInfo["review_desc"] = $this->FmtStatus("review", $OrderInfo["review"]);
      $OrderInfo["review_account"] = "--";
      $OrderInfo["review_remarks"] = "";
      
      if(IsNum($OrderInfo["review"], false, false)){
        $OrderReviewInfo = $this->clsOrder->OrderSnGetLastReviewInfo($OrderInfo["order_sn"], $OrderInfo["status"]);
        
        if(IsArray($OrderReviewInfo)){
          $OrderInfo["review_account"] = $OrderReviewInfo["account_name"];
          $OrderInfo["review_remarks"] = $OrderReviewInfo["review_remarks"];
        }
      }
      //endregion 审批状态
      
      //region 预约状态
      $OrderInfo["status_desc"] = $this->FmtStatus('status', $OrderInfo["status"]);
      //endregion 预约状态
      
      array_push($OrderList, $OrderInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$OrderList);
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

      //region 审批状态
      $OrderInfo["review_desc"] = $this->FmtStatus("review", $OrderInfo["review"]);
      //endregion 审批状态

      //region 预约状态
      $OrderInfo["status_desc"] = $this->FmtStatus('status', $OrderInfo["status"]);
      //endregion 预约状态
    }

    return $OrderInfo;
  }

  /**
   * todo: 添加预约
   *
   * @param $_order_sn 预约编号
   * @param $_account_type 账号类型
   * @param $_broker_company_id 经纪公司ID
   * @param $_seller_manager_id 销售经理ID
   * @param $_store_id 门店ID
   * @param $_service_providers_id 服务商ID
   * @param $_customer_id 客户ID
   * @param $_product_id 产品ID
   * @param $_attachment 上传的附件文件
   * @param $_attachment_id 附件文件ID
   * @param $_seller_manager_remarks 销售经理备注
   *
   * @return array
   */
  public function OrderAdd($_order_sn, $_account_type, $_broker_company_id, $_seller_manager_id, $_store_id, $_service_providers_id, $_customer_id, $_product_id, $_attachment, $_attachment_id, $_seller_manager_remarks){
    //region 数据检查,并赋值保存的数据
    $clsLog = new \Org\ZhiHui\Login();
    
    if($_account_type !== $clsLog->GetLoginTypeAdmin() && $_account_type !== $clsLog->GetLoginTypeBrokerCompany() && $_account_type !== $clsLog->GetLoginTypeSellerManager()){
      return ReturnError(L("_LOGIN_TYPE_ERROR_"));
    }

    $OrderInfo = array();
    if(!IsN($_order_sn)){
      $OrderInfo = $this->clsOrder->GetOrderDetails($_order_sn);
    }

    //region 检查经纪公司
    if(!IsNum($_broker_company_id, false, false)){
      return ReturnError(L("_BROKER_COMPANY_NEED_SELECT_"));
    }else{
      $BrokerCompanyInfo = $this->clsBrokerCompany->GetBrokerCompanyDetails($_broker_company_id);

      if(!IsArray($BrokerCompanyInfo)){
        return ReturnError(L("_BROKER_COMPANY_NOT_EXIST_"));
      }

      $AddData["broker_company_id"] = $_broker_company_id;
    }
    //endregion 检查经纪公司
    
    //region 检查销售经理
    if(!IsNum($_seller_manager_id, false, false)){
      return ReturnError(L("_SELLER_MANAGER_ID_NULL_"));
    }else{
      $SellerManagerInfo = $this->clsSellerManager->GetSellerManagerDetails($_seller_manager_id);
      
      if(!IsArray($SellerManagerInfo)){
        return ReturnError(L("_SELLER_MANAGER_NOT_EXIST_"));
      }

      $AddData["seller_manager_id"] = $_seller_manager_id;
    }
    //endregion 检查销售经理

    //region 检查门店
    if(!IsNum($_store_id, false, false)){
      return ReturnError(L("_BROKER_COMPANY_STORE_ID_NULL_"));
    }else{
      $StoreInfo = $this->clsBrokerCompany->GetBrokerCompanyStoreDetails($_store_id);

      if(!IsArray($StoreInfo)){
        return ReturnError(L("_BROKER_COMPANY_STORE_NOT_EXIST_"));
      }

      $AddData["store_id"] = $_store_id;
    }
    //endregion 检查门店

    //region 检查服务商
    if(!IsNum($_service_providers_id, false, false)){
      return ReturnError(L("_SERVICE_PROVIDERS_ID_NULL_"));
    }else{
      $ServiceProvidersInfo = $this->clsServiceProviders->GetServiceProvidersDetails($_service_providers_id);

      if(!IsArray($ServiceProvidersInfo)){
        return ReturnError(L("_SERVICE_PROVIDERS_DATA_NOT_EXIST_"));
      }

      $AddData["service_providers_id"] = $_service_providers_id;
    }
    //endregion 检查服务商

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

    //region 保存证件照图片
    if(IsArray($_attachment)){
      //$FileInfo = $this->UploadAttachment($_attachment);
      $FileInfo = $this->UploadAttachmentImage($_attachment);
      if($FileInfo["error"] != 0){
        return $FileInfo;
      }

      $AddData["attachment"] = $FileInfo["data"];
    }
    //endregion 保存证件照图片
    
    //endregion 数据检查,并赋值保存的数据

    //region 添加基本数据
    $AddData["seller_manager_remarks"] = $_seller_manager_remarks;
    $AddData["broker_company_remarks"] = "";

    if(IsArray($OrderInfo)){
      $AddData["id"] = $OrderInfo["id"];
      $AddData["review"] = 0;
      $AddResult = $this->modOrder->save($AddData);
    }else{
      $sOrderSn = $this->clsOrder->CreateOrderSn();
      $AddData["order_sn"] = $sOrderSn;
      $AddData["signed_time"] = time();
      $AddData["reservation_time"] = 0;
      $AddData["pay_time"] = 0;
      $AddData["visit_time"] = 0;
      $AddData["review"] = 0;
      $AddData["status"] = 0;

      $AddResult = $this->modOrder->add($AddData);
    }

    if($AddResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    //endregion 添加基本数据

    $this->clsOrder->SetOrderDetailsCache($sOrderSn);

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo: 获取预约最后一条审批信息
   * @param $_order_sn 订单编号
   *
   * @return array|mixed
   */
  public function OrderLastReviewInfo($_order_sn, $_status){
    $ReviewInfo = $this->clsOrder->OrderSnGetLastReviewInfo($_order_sn, $_status);
    
    return $ReviewInfo;
  }

  /**
   * todo: 获取预约最后一条审批信息
   * @param $_order_sn 订单编号
   *
   * @return array|mixed
   */
  public function OrderReviewList($_order_sn){
    $ReviewList = $this->clsOrder->OrderSnGetReviewList($_order_sn);

    return $ReviewList;
  }

  /**
   * todo: 审核预约
   *
   * @param $_account_id     账号ID
   * @param $_account_type   账号类型
   * @param $_order_sn       预约编号
   * @param $_review_remarks 审批意见
   * @param $_review_status 审批状态
   *
   * @return array
   */
  public function OrderInfoReviewSave($_account_id, $_account_type, $_order_sn, $_review_remarks, $_review_status){
    $AddData = array();
    
    //region 参数检查
    
    //region 检查账号ID
    if(!IsNum($_account_id, false, false)){
      return ReturnError(L("_ACCOUNT_ERROR_"));
    }else{
      $AddData["account_id"] = $_account_id;
    }
    //endregion 检查账号ID
    
    //region 检查账号类型
    if($_account_type === $this->clsLogin->GetLoginTypeSellerManager()){
      return ReturnError(L("_ACCOUNT_NOT_PERMISSION_"));
    }else{
      if($_account_type !== $this->clsLogin->GetLoginTypeAdmin() && $_account_type !== $this->clsLogin->GetLoginTypeBrokerCompany()){
        return ReturnError(L("_LOGIN_TYPE_ERROR_"));
      }
      
      $AddData["account_type"] = $_account_type;
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
      return ReturnError(L("_ORDER_REVIEW_REMARKS_NULL_"));
    }else{
      $AddData["review_remarks"] = $_review_remarks;
    }
    //endregion 检查审批信息

    //region 检查审批信息
    if(IsN($_review_remarks)){
      return ReturnError(L("_ORDER_REVIEW_REMARKS_NULL_"));
    }else{
      $AddData["review_remarks"] = $_review_remarks;
    }
    //endregion 检查审批信息
    
    //region 检查审批状态
    if(!IsNum($_review_status, false, false)){
      return ReturnError(L("_ORDER_REVIEW_STATUS_ERROR_"));
    }else{
      if($_review_status < 1 || $_review_status > 3){
        return ReturnError(L("_ORDER_REVIEW_STATUS_ERROR_"));
      }

      $AddData["review"] = $_review_status;
    }
    //endregion 检查审批状态
    
    //endregion 参数检查

    $AddData["status"] = $OrderInfo["status"];
    $AddData["add_time"] = time();
    
    $AddResult = $this->modOrderReview->add($AddData);
    
    if($AddResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    
    $nReviewID = $AddResult;
    
    //region 更新订单审批状态
    $SaveData["id"] = $OrderInfo["id"];
    $SaveData["review"] = $_review_status;
    $SaveResult = $this->modOrder->save($SaveData);
    
    if($SaveResult === false){
      //region 删除审核数据
      $sWhere = "id={$nReviewID}";
      $this->modOrderReview->where($sWhere)->delete();
      //endregion 删除审核数据

      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    //endregion 更新订单审批状态

    $this->clsOrder->GetOrderReviewDetails($nReviewID);
    $this->clsOrder->SetOrderDetailsCache($OrderInfo["order_sn"]);
    $this->clsOrder->GetOrderDetails($OrderInfo["order_sn"]);
    
    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo: 销售经理保存修改的预约信息
   * @param $_order_sn 预约编号
   * @param $_submit_action 提交动作
   * @param $_store_id 门店ID
   * @param $_reservation_time 预约时间
   * @param $_pay_time 消费时间
   * @param $_pay_amount 消费金额
   * @param $_visit_time 回访时间
   * @param $_seller_manager_remarks 销售经理备注
   *
   * @return array
   */
  public function OrderEditSave($_order_sn, $_submit_action, $_store_id, $_reservation_time, $_pay_time, $_pay_amount, $_visit_time, $_seller_manager_remarks){
    //region 检查参数
    //region 检查预约编号
    if(IsN($_order_sn)){
      return ReturnError(L("_ORDER_SN_NULL_"));
    }else{
      $OrderInfo = $this->clsOrder->GetOrderDetails($_order_sn);

      if(!IsArray($OrderInfo)){
        return ReturnError(L("_ORDER_NOT_EXIST_"));
      }
      
      if($OrderInfo["review"] == 3){
        return ReturnError(L("_ORDER_REVIEW_NOT_EDIT_"));
      }

      $SaveData["id"] = $OrderInfo["id"];
    }
    //endregion 检查预约编号

    //region 检查门店
    if(IsNum($_store_id, false, false)){
      $StoreInfo = $this->clsBrokerCompany->GetBrokerCompanyStoreDetails($_store_id);

      if(!IsArray($StoreInfo)){
        return ReturnError(L("_BROKER_COMPANY_STORE_ID_ERROR_"));
      }

      $SaveData["store_id"] = $_store_id;
    }
    //endregion 检查门店
    
    if(!IsN($_seller_manager_remarks)){
      $SaveData["seller_manager_remarks"] = $_seller_manager_remarks;
    }
    
    $nCurrTime = time();
    
    switch($_submit_action){
      case "again":
        break;
      
      case "save":
        
        break;

      case "reservation":
        $nReservationTime = isDateTime($_reservation_time);
        
        if($nReservationTime === false){
          return ReturnError(L("_ORDER_RESERVATION_TIME_ERROR_"));
        }else{
          if($nReservationTime <= $nCurrTime){
            return ReturnError(L("_ORDER_RESERVATION_TIME_MIN_ERROR_"));
          }
        }

        $SaveData["reservation_time"] = $nReservationTime;
        $SaveData["status"] = 1;
        $SaveData["review"] = 0;
        
        break;

      case "pay":
        $nPayTime = isDateTime($_pay_time);

        if($nPayTime === false){
          return ReturnError(L("_ORDER_PAY_TIME_ERROR_"));
        }
        
        if(!IsFloat($_pay_amount, 2, false, false)){
          return ReturnError(L("_ORDER_PAY_AMOUNT_ERROR_"));
        }

        $SaveData["pay_time"] = $nPayTime;
        $SaveData["pay_amount"] = $_pay_amount;
        $SaveData["status"] = 2;
        break;

      case "visit":
        $nVisitTime = isDateTime($_visit_time);

        if($nVisitTime === false){
          return ReturnError(L("_ORDER_VISIT_TIME_ERROR_"));
        }

        $SaveData["visit_time"] = $nVisitTime;
        $SaveData["status"] = 3;
        break;
    }
    //endregion 检查参数
    
    $SaveResult = $this->modOrder->save($SaveData);
    
    if($SaveResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    
    $this->clsOrder->SetOrderDetailsCache($OrderInfo["order_sn"]);
    
    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"), $SaveResult);
  }
  
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

  /**
   * todo: 上传图片
   */
  private function UploadAttachment($_file){
    if(!IsArray($_file)){
      return ReturnError(L("_UPLOAD_FILE_NOT_EXIST_"));
    }

    $clsUploadFile = new \Org\ZhiHui\UploadFile();
    $clsUploadFile->FilePathType = "order_attachment";
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

  /**
   * todo: 上传图片
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
   * todo: 格式化状态
   * @param $_field 字段名
   * @param $_val 值
   */
  private function FmtStatus($_field, $_val){
    if($_field == 'review'){
      switch($_val){
        case "0" :
          return FmtValueStyle(4, "等待审核");
          break;

        case "1" :
          return FmtValueStyle(1, "审核中");
          break;

        case "2" :
          return FmtValueStyle(3, "已通过");
          break;

        case "3" :
          return FmtValueStyle(5, "拒绝");
          break;
      }
    }

    if($_field == 'status'){
      switch($_val){
        case "0" :
          return FmtValueStyle(1, "已签约");
          break;

        case "1" :
          return FmtValueStyle(2, "已预约");
          break;

        case "2" :
          return FmtValueStyle(3, "已消费");
          break;

        case "3" :
          return FmtValueStyle(3, "已回访");
          break;
      }
    }
  }
  //endregion 预约
}
