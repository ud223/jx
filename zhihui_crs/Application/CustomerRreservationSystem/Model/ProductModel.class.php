<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

class ProductModel extends Model {
  Protected $autoCheckFields = false;

  private $modProduct = null;
  private $modProductType = null;
  
  private $clsProduct = null;
  private $clsBrokerCompany = null;
  private $clsServiceProviders = null;
  
  function __construct() {
    $this->modProduct = M("product");
    $this->modProductType = M("product_type");
    
    $this->clsProduct = new \Org\ZhiHui\Product();
    $this->clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
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
      $Info["service_providers_name"] = "";
      $Info["store"] = $this->FmtListStore($ProductInfo["store_id"]);
      $Info["intro"] = $ProductInfo["intro"];
      $Info["remarks"] = $ProductInfo["remarks"];
      $Info["sell_number"] = $ProductInfo["sell_number"];
      $Info["status"] = $ProductInfo["status"];
      $Info["status_style"] = $this->FmtStatusStyle("status", $ProductInfo["status"]);
      
      //region 服务商名称
      $BrokerCompany = $this->clsBrokerCompany->GetBrokerCompanyDetails($ProductInfo["broker_company_id"]);
      
      if(IsArray($BrokerCompany)){
        $Info["broker_company_name"] = $BrokerCompany["broker_company_name"];
      }
      //endregion 服务商名称
      
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
    }

    return $ProductInfo;
  }

  /**
   * todo: 保存产品
   * @param $_product_id 产品ID
   * @param $_broker_company_id 经纪公司ID
   * @param $_product_type 产品类型
   * @param $_product_name 产品名称
   * @param $_product_price 产品价格
   * @param $_store 门店id字符串，逗号分割
   * @param $_product_intro 产品简介
   * @param $_product_content 产品详细内容
   * @param $_product_remarks 产品描述
   * @param $_product_status 产品状态
   *
   * @return array
   */
  public function ProductSave($_product_id, $_broker_company_id, $_product_type, $_service_providers_id, $_product_name, $_product_price, $_store_str, $_product_intro, $_product_content, $_product_remarks, $_product_status){
    $ProductInfo = array();
    
    //region 参数检查并赋值
    if(IsNum($_product_id, false, false)){
      $ProductInfo = $this->clsProduct->GetProductDetails($_product_id);
    }

    //region 检查经纪公司
    if(!IsNum($_broker_company_id, false, false)){
      return ReturnError(L("_BROKER_COMPANY_NEED_SELECT_"));
    }else{
      $BrokerCompanyInfo = $this->clsBrokerCompany->GetBrokerCompanyDetails($_broker_company_id);

      if(!IsArray($BrokerCompanyInfo)){
        return ReturnError(L("_BROKER_COMPANY_NOT_EXIST_"));
      }

      $SaveBaseData["broker_company_id"] = $_broker_company_id;
    }
    //endregion 检查经纪公司

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
    
    //region 检查服务商
    if(!IsNum($_service_providers_id, false, false)){
      return ReturnError(L("_SERVICE_PROVIDERS_ID_NULL_"));
    }else{
      $ServiceProvidersInfo = $this->clsServiceProviders->GetServiceProvidersDetails($_service_providers_id);
      
      if(!IsArray($ServiceProvidersInfo)){
        return ReturnError(L("_SERVICE_PROVIDERS_DATA_NOT_EXIST_"));
      }

      $SaveBaseData["service_providers_id"] = $_service_providers_id;
    }
    //endregion 检查服务商

    //region 检查产品名称
    if(IsN($_product_name)){
      return ReturnError(L("_PRODUCT_NAME_NULL_"));
    }else{
      $sWhere = "product_name='{$_product_name}'";
      $sWhere .= " and type_id='{$_product_type}'";
      $sWhere .= " and broker_company_id='{$_broker_company_id}'";
      
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
    
    //region 检查产品价格
    if(!IsFloat($_product_price, 2, false, false)){
      return ReturnError(L("_PRODUCT_PRICE_ERROR_"));
    }else{
      $SaveBaseData["product_price"] = $_product_price;
    }
    //endregion 检查产品价格
    
    //region 检查产品门店
    if(IsN($_store_str)){
      return ReturnError(L("_BROKER_COMPANY_STORE_ID_NULL_"));
    }else{
      $StoreIdList = explode(",", $_store_str);
      
      foreach($StoreIdList as $key=>$val){
        if(!IsNum($val, false, false)){
          if(IsN($val)){
            $val = "空值";
          }
          
          return ReturnError(L("_BROKER_COMPANY_STORE_ID_ERROR_")."! 错误的门店ID:{$val}");
        }else{
          $StoreInfo = $this->clsBrokerCompany->GetBrokerCompanyStoreDetails($val);
          
          if(!IsArray($StoreInfo)){
            return ReturnError(L("_BROKER_COMPANY_STORE_NOT_EXIST_")."! 错误的门店ID:{$val}");
          }
        }
      }
    }

    $SaveBaseData["store_id"] = ",{$_store_str},";
    //endregion 检查产品门店
    
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
   * todo: 格式化商品列表显示的门店样式
   * @param $_store_id_str
   */
  public function FmtListStore($_store_id_str){
    if(IsN($_store_id_str)){
      return "";
    }
    
    $StoreIdList = explode(",", $_store_id_str);
    $StoreNumber = sizeof($StoreIdList);
    
    if($StoreNumber == 1){
      $StoreInfo = $this->clsBrokerCompany->GetBrokerCompanyStoreDetails($StoreIdList[0]);
      
      return $StoreInfo["store_name"];
    }else{
      $StoreName = "";
      
      foreach($StoreIdList as $key=>$val){
        $StoreInfo = $this->clsBrokerCompany->GetBrokerCompanyStoreDetails($val);
        
        $StoreName .= "{$StoreInfo["store_name"]},";
      }
      
      return trim($StoreName, ",");
    }
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
