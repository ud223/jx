<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

class ProductModel extends Model {
  Protected $autoCheckFields = false;

  private $modProduct = null;
  private $modProductType = null;
  
  public $clsProduct = null;
  public $clsUser = null;
  public $clsServiceProviders = null;
  
  function __construct() {
    $this->modProduct = M("product");
    $this->modProductType = M("product_type");
    
    $this->clsProduct = new \Org\ZhiHui\Product();
    $this->clsUser = new \Org\ZhiHui\User();
    $this->clsServiceProviders = new \Org\ZhiHui\ServiceProviders();
  }
  
  //region 产品类别
  /**
   * todo:产品类别列表
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function ProductTypeList($_rownum=20, $_param=array()){
    $sField = "id";
    $sWhere = "1=1";
    $sOrder = "id desc";

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "type_name like '%{$_param["search_key"]}%'";
          $sWhere .= ")";
        }
      }
    }

    $nTotal = $this->modProductType->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $ProductTypeIdList = $this->modProductType->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $ProductTypeList = array();

    foreach($ProductTypeIdList as $key=>$val){
      $ProductTypeInfo = $this->clsProduct->GetProductTypeDetails($val["id"]);

      array_push($ProductTypeList, $ProductTypeInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$ProductTypeList);
  }

  /**
   * todo: 保存产品类别
   * 
   * @param $_product_type_name 产品类别名称
   * @param $_broker_company_id 经纪公司ID
   *
   * @return array
   */
  public function ProductTypeSave($_product_type_name, $_broker_company_id){
    //region 数据检查,并赋值保存的数据
    if(IsN($_product_type_name)){
      return ReturnError(L("_PRODUCT_TYPE_NAME_NULL_"));
    }else{
      $sWhere = "type_name='{$_product_type_name}'";
      $ProductNumber = $this->modProductType->where($sWhere)->count();
      
      if(IsNum($ProductNumber, false, false)){
        return ReturnError(L("_PRODUCT_TYPE_NAME_EXIST_"));
      }

      $SaveBaseData["type_name"] = $_product_type_name;
    }

    //region 检查经纪公司
    if(!IsNum($_broker_company_id, false, false)){
      $_broker_company_id = 0;
      //return ReturnError(L("_BROKER_COMPANY_NEED_SELECT_"));
    }else{
      $BrokerCompanyInfo = $this->clsBrokerCompany->GetBrokerCompanyDetails($_broker_company_id);

      if(!IsArray($BrokerCompanyInfo)){
        return ReturnError(L("_BROKER_COMPANY_NOT_EXIST_"));
      }
    }
    $SaveBaseData["broker_company_id"] = $_broker_company_id;
    //endregion 检查经纪公司
    
    //endregion 数据检查,并赋值保存的数据

    //region 添加基本数据
    $SaveBaseResult = $this->modProductType->add($SaveBaseData);

    $nProductID = $SaveBaseResult;

    if($SaveBaseResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    //endregion 添加基本数据

    $this->clsProduct->SetProductTypeDetailsCache($nProductID);

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo:快速保存产品类别信息
   */
  public function QuickModifyProductTypeField($_id, $_key, $_val){
    if(!IsNum($_id, false) || IsN($_key) || IsN($_val)){
      return ReturnError(L('_PARAM_ERROR_'));
    }

    $map["id"] = $_id;
    $map[$_key] = $_val;

    $Result = $this->modProductType->save($map);

    if($Result !== false){
      $this->clsProduct->SetProductTypeDetailsCache($_id);
      return ReturnCorrect(L('_SAVE_DATA_SUCCEED_'));
    }else{
      return ReturnError(L('_SAVE_DATA_FAILURE_'));
    }
  }
  
  /**
   * todo: 删除产品类别
   *
   * @param $_id_str id字符串，逗号分割id
   *
   * @return array
   */
  public function ProductTypeDelete($_id_str){
    if(IsN($_id_str)){
      return ReturnError(L("_PRODUCT_TYPE_ID_NULL_"));
    }else{
      $IdList = explode(",", $_id_str);

      foreach($IdList as $key=>$val){
        if(!IsNum($val, false, false)){
          return ReturnError(L("_PRODUCT_TYPE_ID_ERROR_"));
        }
      }
    }

    $sWhere = "id in ({$_id_str})";
    $Result = $this->modProductType->where($sWhere)->delete();

    if($Result === false){
      return ReturnError(L("_DELETE_DATA_FAILURE_"));
    }

    foreach($IdList as $key=>$val){
      $this->clsProduct->SetProductTypeDetailsCache($val);
    }

    return ReturnCorrect(L("_DELETE_DATA_SUCCEED_"));
  }

  /**
   * todo: 刷新产品类别缓存
   */
  public function ResetProductTypeCache(){
    $sField = "id";
    $ProductIdList = $this->modProductType->field($sField)->select();

    foreach($ProductIdList as $key=>$val){
      $this->clsProduct->SetProductTypeDetailsCache($val["id"]);
    }

    return ReturnCorrect(L("_CACHE_REFRESH_SUCCEED_"));
  }
  //endregion 产品类别

  //region 产品
  /**
   * todo:产品列表
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function ProductList($_rownum=20, $_param=array()){
    $sField = "id";
    $sWhere = "1=1";
    $sOrder = "id desc";

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "product_name like '%{$_param["search_key"]}%'";
          
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

          case "product_name":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and product_name like '%{$_param["search_key"]}%'";
            }

            break;
        }
      }

      //产品类型
      if(IsNum($_param["search_product_type"], false, false)){
        $sWhere .= " and type_id = {$_param["search_product_type"]}";
      }

      //服务商
      if(IsNum($_param["search_service"], false, false)){
        $sWhere .= " and service_providers_id = {$_param["search_service"]}";
      }

      //门店
      if(IsNum($_param["store_id"], false, false)){
        $sWhere .= " and store_id like '%,{$_param["store_id"]}%,'";
      }

      //发布状态
      if(IsNum($_param["search_status"], true, false)){
        if($_param["search_status"] == 0 || $_param["search_status"] == 1){
          $sWhere .= " and status = {$_param["search_status"]}";
        }
      }
      
    }

    $nTotal = $this->modProduct->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $ProductIdList = $this->modProduct->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $ProductList = array();

    foreach($ProductIdList as $key=>$val){
      $ProductInfo = $this->clsProduct->GetProductDetails($val["id"]);

      $Info["id"] = $ProductInfo["id"];
      $Info["broker_company_name"] = "";
      $Info["type_name"] = "";
      $Info["product_name"] = $ProductInfo["product_name"];
      $Info["product_price"] = $ProductInfo["product_price"];
      $Info["pay_type"] = $ProductInfo["pay_type"];
      $Info["payment_type"] = $ProductInfo["payment_type"];
      $Info["service_providers_name"] = "";
      $Info["min_age"] = $ProductInfo["min_age"];
      $Info["max_age"] = $ProductInfo["max_age"];
      $Info["validity_year"] = $ProductInfo["validity_year"];
      $Info["captain_first_rate"] = ($ProductInfo["captain_first_rate"]*100);
      $Info["captain_next_rate"] = ($ProductInfo["captain_next_rate"]*100);
      $Info["member_first_rate"] = ($ProductInfo["member_first_rate"]*100);
      $Info["member_next_rate"] = ($ProductInfo["member_next_rate"]*100);
      $Info["intro"] = $ProductInfo["intro"];
      $Info["remarks"] = $ProductInfo["remarks"];
      $Info["sell_number"] = $ProductInfo["sell_number"];
      $Info["status"] = $ProductInfo["status"];
      $Info["status_style"] = $this->FmtStatusStyle("status", $ProductInfo["status"]);
      
      //region 产品类别名称
      $ProductTypeInfo = $this->clsProduct->GetProductTypeDetails($ProductInfo["type_id"]);
      
      if(IsArray($ProductTypeInfo)){
        $Info["type_name"] = $ProductTypeInfo["type_name"];
      }
      //endregion 产品类别名称

      //region 产品类别名称
      $ServiceProvidersInfo = $this->clsServiceProviders->GetServiceProvidersDetails($ProductInfo["service_providers_id"]);
      
      if(IsArray($ServiceProvidersInfo)){
        $Info["service_providers_name"] = $ServiceProvidersInfo["service_providers_name"];
      }
      //endregion 产品类别名称
      
      array_push($ProductList, $Info);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$ProductList);
  }
  
  public function AjaxProductList($_page=1, $_rownum=20){
    $sField = "id";
    $sWhere = "status=1";
    $sOrder = "id desc";
    
    if(!IsNum($_page, false, false)){
      $_page = 1;
    }
    
    $nStartRows = ($_page-1)*$_rownum;
    $nTotal = $this->modProduct->where($sWhere)->count();
    $ProductIdList = $this->modProduct->field($sField)->where($sWhere)->order($sOrder)->limit($nStartRows.','.$_rownum)->select();
    $ProductList = array();
  
    foreach($ProductIdList as $key=>$val){
      $ProductInfo = $this->clsProduct->GetProductDetails($val["id"]);
    
      $Info["id"] = $ProductInfo["id"];
      $Info["broker_company_name"] = "";
      $Info["type_name"] = "";
      $Info["product_name"] = $ProductInfo["product_name"];
      $Info["product_price"] = $ProductInfo["product_price"];
      $Info["pay_type"] = $ProductInfo["pay_type"];
      $Info["payment_type"] = $ProductInfo["payment_type"];
      $Info["service_providers_name"] = "";
      $Info["min_age"] = $ProductInfo["min_age"];
      $Info["max_age"] = $ProductInfo["max_age"];
      $Info["validity_year"] = $ProductInfo["validity_year"];
      $Info["captain_first_rate"] = ($ProductInfo["captain_first_rate"]*100);
      $Info["captain_next_rate"] = ($ProductInfo["captain_next_rate"]*100);
      $Info["member_first_rate"] = ($ProductInfo["member_first_rate"]*100);
      $Info["member_next_rate"] = ($ProductInfo["member_next_rate"]*100);
      $Info["intro"] = $ProductInfo["intro"];
      $Info["remarks"] = $ProductInfo["remarks"];
      $Info["sell_number"] = $ProductInfo["sell_number"];
      $Info["status"] = $ProductInfo["status"];
      $Info["status_style"] = $this->FmtStatusStyle("status", $ProductInfo["status"]);
    
      //region 产品类别名称
      $ProductTypeInfo = $this->clsProduct->GetProductTypeDetails($ProductInfo["type_id"]);
    
      if(IsArray($ProductTypeInfo)){
        $Info["type_name"] = $ProductTypeInfo["type_name"];
      }
      //endregion 产品类别名称
    
      //region 产品类别名称
      $ServiceProvidersInfo = $this->clsServiceProviders->GetServiceProvidersDetails($ProductInfo["service_providers_id"]);
    
      if(IsArray($ServiceProvidersInfo)){
        $Info["service_providers_name"] = $ServiceProvidersInfo["service_providers_name"];
      }
      //endregion 产品类别名称
    
      array_push($ProductList, $Info);
    }
  
    return array("PageInfo"=>FmtPagin($nTotal, $_page, $_rownum), "DataList"=>$ProductList);
  }

  /**
   * todo: 产品信息
   * @param $_product_id 产品ID
   *
   * @return array
   */
  public function ProductInfo($_product_id){
    $ProductInfo = array();

    if(IsNum($_product_id, false, false)){
      $ProductInfo = $this->clsProduct->GetProductDetails($_product_id);

      $ProductInfo["captain_first_rate"] *= 100; 
      $ProductInfo["captain_next_rate"] *= 100; 
      $ProductInfo["member_first_rate"] *= 100; 
      $ProductInfo["member_next_rate"] *= 100;
    }

    return $ProductInfo;
  }

  /**
   * todo: 保存产品
   *
   * @param $_product_id      产品ID
   * @param $_product_type    产品类型
   * @param $_product_name    产品名称
   * @param $_pay_type        缴费类型
   * @param $_payment_type
   * @param $_product_price   产品价格
   * @param $_min_age         最小投保年龄
   * @param $_max_age         最大投保年龄
   * @param $_captain_first_rate  团队长首年提成比例
   * @param $_captain_next_rate   团队长次年提成比例
   * @param $_member_first_rate   客户经理首年提成比例
   * @param $_member_next_rate    客户经理次年提成比例
   * @param $_validity_year   投保期间
   * @param $_product_intro   产品简介
   * @param $_product_content 产品详细内容
   * @param $_product_remarks 产品描述
   * @param $_product_status  产品状态
   * @param $_attachment 附件
   *
   * @return array
   */
  public function ProductSave($_product_id, $_product_type, $_product_name, $_pay_type, $_payment_type, $_product_price, $_min_age, $_max_age, $_captain_first_rate, $_captain_next_rate, $_member_first_rate, $_member_next_rate, $_validity_year, $_product_intro, $_product_content, $_product_remarks, $_product_status, $_attachment){
    $ProductInfo = array();
    $nBrokerCompanyUserID = $this->clsUser->ConstBrokerCompanyUserID();
    $nInsuranceCompanyUserID = $this->clsUser->ConstInsuranceCompanyUserID();

    //region 参数检查并赋值
    if(IsNum($_product_id, false, false)){
      $ProductInfo = $this->clsProduct->GetProductDetails($_product_id);
    }

    //region 检查产品类型
    if(!IsNum($_product_type, false, false)){
      return ReturnError(L("_PRODUCT_TYPE_ID_NULL_"));
    }else{
      $ProductTypeInfo = $this->clsProduct->GetProductTypeDetails($_product_type);

      if(!IsArray($ProductTypeInfo)){
        return ReturnError(L("_PRODUCT_TYPE_NOT_EXIST_"));
      }

      $SaveBaseData["type_id"] = $_product_type;
    }
    //endregion 检查产品类型

    //region 检查产品名称
    if(IsN($_product_name)){
      return ReturnError(L("_PRODUCT_NAME_NULL_"));
    }else{
      $sWhere = "product_name='{$_product_name}'";
      $sWhere .= " and type_id='{$_product_type}'";
      $sWhere .= " and broker_company_id='{$nBrokerCompanyUserID}'";
      
      if(IsArray($ProductInfo)){
        $sWhere .= " and id!={$_product_id}";
      }

      $ProductNumber = $this->modProduct->where($sWhere)->count();

      if(IsNum($ProductNumber, false, false)){
        return ReturnError(L("_PRODUCT_NAME_EXIST_"));
      }

      $SaveBaseData["product_name"] = $_product_name;
    }
    //endregion 检查产品名称

    //region 检查缴费方式
    if(IsN($_pay_type)){
      return ReturnError(L("_PRODUCT_PAY_TYPE_ERROR_"));
    }else{
      if(!in_array($_pay_type, $this->clsProduct->_PayType)){
        return ReturnError(L("_PRODUCT_PAY_TYPE_ERROR_"));
      }

      $SaveBaseData["pay_type"] = $_pay_type;
    }
    //endregion 检查支付方式

    //region 检查支付方式
    if(IsN($_payment_type)){
      return ReturnError(L("_PRODUCT_PAYMENT_TYPE_ERROR_"));
    }else{
//      if(!in_array($_payment_type, $this->clsProduct->_PaymentType)){
//        return ReturnError(L("_PRODUCT_PAYMENT_TYPE_ERROR_"));
//      }

      $SaveBaseData["payment_type"] = $_payment_type;
    }
    //endregion 检查支付方式

    //region 检查产品价格
    $SaveBaseData["product_price"] = 0;
//    if(!IsFloat($_product_price, 2, false, false)){
//      return ReturnError(L("_PRODUCT_PRICE_ERROR_"));
//    }else{
//      $SaveBaseData["product_price"] = $_product_price;
//    }
    //endregion 检查产品价格

    //region 检查投保年龄
    if(IsN($_min_age)){
      return ReturnError(L("_PRODUCT_MIN_AGE_NULL_"));
    }else{
      $SaveBaseData["min_age"] = $_min_age;
//      $SaveBaseData["max_age"] = $_min_age;
    }

//    if(!IsNum($_max_age, false, false)){
//      return ReturnError(L("_PRODUCT_MAX_AGE_ERROR_"));
//    }else{
//      $SaveBaseData["max_age"] = $_max_age;
//    }
//
//    if($_max_age <= $_min_age){
//      return ReturnError(L("_PRODUCT_MAX_MIN_ERROR_"));
//    }
    //endregion 检查投保年龄

    //region 检查团队长首年提成比例
    if(!IsNum($_captain_first_rate, false, false)){
      return ReturnError(L("_PRODUCT_CAPTAIN_FIRST_RATE_ERROR_"));
    }else{
      $SaveBaseData["captain_first_rate"] = round(($_captain_first_rate/100), 2);
    }
    //endregion 检查团队长首年提成比例

    //region 检查团队长次年提成比例
    if(!IsNum($_captain_next_rate, false, false)){
      return ReturnError(L("_PRODUCT_CAPTAIN_NEXT_RATE_ERROR_"));
    }else{
      $SaveBaseData["captain_next_rate"] = round(($_captain_next_rate/100), 2);
    }
    //endregion 检查团队长次年提成比例

    //region 检查客户经理首年提成比例
    if(!IsNum($_member_first_rate, false, false)){
      return ReturnError(L("_PRODUCT_MEMBER_FIRST_RATE_ERROR_"));
    }else{
      $SaveBaseData["member_first_rate"] = round(($_member_first_rate/100), 2);
    }
    //endregion 检查客户经理首年提成比例

    //region 检查客户经理次年提成比例
    if(!IsNum($_member_next_rate, false, false)){
      return ReturnError(L("_PRODUCT_MEMBER_NEXT_RATE_ERROR_"));
    }else{
      $SaveBaseData["member_next_rate"] = round(($_member_next_rate/100), 2);
    }
    //endregion 检查客户经理次年提成比例

    //region 检查保险期间
    if(IsN($_validity_year)){
      return ReturnError(L("_PRODUCT_VALIDITY_YEAR_ERROR_"));
    }else{
      $SaveBaseData["validity_year"] = $_validity_year;
    }
    //endregion 检查保险期间

    //region 检查产品状态
    if($_product_status != 0 && $_product_status != 1){
      $SaveBaseData["status"] = 0;
    }else{
      $SaveBaseData["status"] = $_product_status;
    }
    //endregion 检查产品状态
    
    //endregion 参数检查并赋值
    
    //region 准备写入数据
    $nTime = time();

    $SaveBaseData["insurance_company_id"] = $nInsuranceCompanyUserID;
    $SaveBaseData["broker_company_id"] = $nBrokerCompanyUserID;
    $SaveBaseData["intro"] = $_product_intro;
    $SaveBaseData["content"] = $_product_content;
    $SaveBaseData["remarks"] = $_product_remarks;
    
    if(IsArray($ProductInfo)){
      $SaveBaseData["id"] = $_product_id;
      $SaveBaseData["update_time"] = $nTime;
      
      $Result = $this->modProduct->save($SaveBaseData);
    }else{
      $SaveBaseData["add_time"] = $nTime;
      $SaveBaseData["update_time"] = $nTime;

      $Result = $this->modProduct->add($SaveBaseData);
      $_product_id = $Result;
    }
    //endregion 准备写入数据
    
    if($Result === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }

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

      $SaveAttachmentData["id"] = $_product_id;
      $SaveAttachmentData["attachment_id"] = $UploadResult["data"];

      $Result = $this->modProduct->save($SaveAttachmentData);
    }
    //endregion 处理附件

    //region 处理缓存
    $this->clsProduct->SetProductDetailsCache($_product_id);
    $this->clsProduct->GetProductDetails($_product_id);
    //endregion 处理缓存
    
    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo: 删除产品
   *
   * @param $_id_str id字符串，逗号分割id
   *
   * @return array
   */
  public function ProductDelete($_id_str){
    if(IsN($_id_str)){
      return ReturnError(L("_PRODUCT_ID_ERROR_"));
    }else{
      $IdList = explode(",", $_id_str);

      foreach($IdList as $key=>$val){
        if(!IsNum($val, false, false)){
          return ReturnError(L("_PRODUCT_ID_ERROR_"));
        }
      }
    }

    $sWhere = "id in ({$_id_str})";
    $Result = $this->modProduct->where($sWhere)->delete();

    if($Result === false){
      return ReturnError(L("_DELETE_DATA_FAILURE_"));
    }

    foreach($IdList as $key=>$val){
      $this->clsProduct->SetProductDetailsCache($val);
    }

    return ReturnCorrect(L("_DELETE_DATA_SUCCEED_"));
  }

  /**
   * todo: 刷新产品缓存
   */
  public function ResetProductCache(){
    $sField = "id";
    $ProductIdList = $this->modProduct->field($sField)->select();

    foreach($ProductIdList as $key=>$val){
      $this->clsProduct->SetProductDetailsCache($val["id"]);
    }

    return ReturnCorrect(L("_CACHE_REFRESH_SUCCEED_"));
  }

  /**
   * todo: 上传图片
   */
  public function UploadProductContentImage($_file){
    if(!IsArray($_file)){
      return ReturnError(L("_UPLOAD_FILE_NOT_EXIST_"));
    }

    $clsUploadFile = new \Org\ZhiHui\UploadFile();
    $clsUploadFile->FilePathType = "product_content";
    $clsUploadFile->FileNameType = "time";

    $UploadResult = $clsUploadFile->UploadImage($_file);
    
    if($UploadResult["error"] !=0){
      return $UploadResult;
    }

    $UploadImageInfo = $UploadResult["data"]["ProductContentImage"];
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
    
    $Result["url"] = FullImageUrl($UploadImageInfo["file"]);
    $Result["original_name"] = $UploadImageInfo["name"];
    
    return ReturnCorrect(L("_UPLOAD_SUCCEED_"), $Result);
  }

  /**
   * todo: 上传图片
   */
  private function UploadAttachmentImage($_file){
    if(!IsArray($_file)){
      return ReturnError(L("_UPLOAD_FILE_NOT_EXIST_"));
    }

    $clsUploadFile = new \Org\ZhiHui\UploadFile();
    $clsUploadFile->FilePathType = "product_attachment_image";
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

  /**
   * 格式化状态样式
   * @return array
   */
  private function FmtStatusStyle($_field, $_val){
    if($_field == 'status'){
      switch($_val){
        case "0" :
          return FmtValueStyle(5, "未发布");
          break;

        case "1" :
          return FmtValueStyle(1, "已发布");
          break;
      }
    }
  }
  //endregion 产品
}
