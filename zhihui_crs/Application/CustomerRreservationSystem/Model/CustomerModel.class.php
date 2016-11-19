<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

class CustomerModel extends Model {
  Protected $autoCheckFields = false;

  private $_CustomerRealNameLength = 10;

  private $modCustomer = null;
  
  private $clsCustomer = null;
  private $clsBrokerCompany = null;
  private $clsRegion = null;
  
  function __construct() {
    $this->modCustomer = M("customer");
    
    $this->clsCustomer = new \Org\ZhiHui\Customer();
    $this->clsBrokerCompany = new \Org\ZhiHui\BrokerCompany();
    $this->clsRegion = new \Org\ZhiHui\Region();
  }
  
  //region 客户
  /**
   * todo:客户列表
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function CustomerList($_rownum=20, $_param=array()){
    $sField = "id";
    $sWhere = "1=1";
    $sOrder = "add_time desc";

    if(!empty($_param)){
      if(IsN($_param["search_case"])){
        if(!IsN($_param["search_key"])){
          $sWhere .= " and (";
          $sWhere .= "real_name like '%{$_param["search_key"]}%'";
          $sWhere .= " or phone like '%{$_param["search_key"]}%'";
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

          case "real_name":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and real_name like '%{$_param["search_key"]}%'";
            }
            break;

          case "phone":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and phone like '%{$_param["search_key"]}%'";
            }
            break;

          case "wechat":
            if(!IsN($_param["search_key"])){
              $sWhere .= " and wechat like '%{$_param["search_key"]}%'";
            }
            break;
        }
      }

      if(IsNum($_param["search_broker_company"], false, false)){
        $sWhere .= " and broker_company_id = {$_param["search_broker_company"]}";
      }
    }

    $nTotal = $this->modCustomer->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $CustomerIdList = $this->modCustomer->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $CustomerList = array();

    foreach($CustomerIdList as $key=>$val){
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($val["id"]);

      $CustomerInfo["broker_company_name"] = "--";
      if(IsNum($CustomerInfo["broker_company_id"], false, false)){
        $BrokerCompanyInfo = $this->clsBrokerCompany->GetBrokerCompanyDetails($CustomerInfo["broker_company_id"]);
        $CustomerInfo["broker_company_name"] = $BrokerCompanyInfo["broker_company_name"];
      }

      array_push($CustomerList, $CustomerInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$CustomerList);
  }

  /**
   * todo: 客户信息
   * @param $_seller_manager_id 客户ID
   *
   * @return array
   */
  public function CustomerInfo($_customer_id){
    $CustomerInfo = array();

    if(IsNum($_customer_id, false, false)){
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($_customer_id);
      
      $CustomerInfo["birthday"] = Time2FullDate($CustomerInfo["birthday"], "Y-m-d");
    }

    return $CustomerInfo;
  }

  /**
   * todo: 保存客户
   * 
   * @param $_customer_id 客户ID
   * @param $_broker_company_id 经纪公司ID
   * @param $_real_name 真实姓名
   * @param $_gender 性别
   * @param $_nationality 国籍
   * @param $_idcard_type 证件类型
   * @param $_idcard_no 证件号
   * @param $_file_idcard_img_a 上传的证件照文件
   * @param $_idcard_img_a_id 证件照文件ID
   * @param $_birthday 出生日期
   * @param $_province_id 省份ID
   * @param $_city_id 城市ID
   * @param $_district_id 区县ID
   * @param $_address 详细地址
   * @param $_phone 手机号
   * @param $_wechat 微信号
   *
   * @return array
   */
  public function CustomerSave($_customer_id, $_broker_company_id, $_real_name, $_gender, $_nationality, $_idcard_type, $_idcard_no, $_file_idcard_img_a, $_idcard_img_a_id, $_birthday, $_province_id, $_city_id, $_district_id, $_address, $_phone, $_wechat){
    $CustomerInfo = array();

    //region 数据检查,并赋值保存的数据
    //检查客户信息
    if(IsNum($_customer_id, false, false)){
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($_customer_id);

      if(!IsArray($CustomerInfo)){
        return ReturnError(L("_CUSTOMER_NOT_EXIST_"));
      }

      $SaveBaseData["id"] = $_customer_id;
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
    
    //检查姓名
    if(IsN($_real_name)){
      return ReturnError(L("_CUSTOMER_REAL_NAME_NULL_"));
    }else{
      $SaveBaseData["real_name"] = $_real_name;
    }
    
    //region 检查性别
    if(IsN($_gender)){
      return ReturnError(L("_CUSTOMER_GENDER_NULL_"));
    }else{
      $FoundError = true;
      
      foreach($this->clsCustomer->_Gender as $key=>$val){
        if($_gender === $val["key"]){
          $FoundError = false;
          break;
        }
      }
      
      if($FoundError){
        return ReturnError(L("_CUSTOMER_GENDER_ERROR_"));
      }

      $SaveBaseData["gender"] = $_gender;
    }
    //endregion 检查性别
    
    //region 检查国籍
    if(IsN($_nationality)){
      return ReturnError(L("_CUSTOMER_NATIONALITY_NULL_"));
    }else{
      $SaveBaseData["nationality"] = $_nationality;
    }
    //endregion 检查国籍

    //region 检查证件类型
    if(IsN($_idcard_type)){
      return ReturnError(L("_CUSTOMER_IDCARD_TYPE_NULL_"));
    }else{
      $FoundError = true;

      foreach($this->clsCustomer->_IdCardType as $key=>$val){
        if($_idcard_type === $val["key"]){
          $FoundError = false;
          break;
        }
      }

      if($FoundError){
        return ReturnError(L("_CUSTOMER_IDCARD_TYPE_ERROR_"));
      }

      $SaveBaseData["idcard_type"] = $_idcard_type;
    }
    //endregion 检查证件类型
    
    //region 检查证件号
    if(IsN($_idcard_no)){
      return ReturnError(L("_CUSTOMER_IDCARD_NO_NULL_"));
    }else{
      $SaveBaseData["idcard_no"] = $_idcard_no;
    }
    //endregion 检查证件号

    //region 保存证件照图片
    if(IsArray($_file_idcard_img_a)){
      $nImageId = $this->UploadCustomerIdCardImage($_file_idcard_img_a);

      if($nImageId["error"] != 0){
        return $nImageId;
      }

      $SaveBaseData["idcard_img_a"] = $nImageId["data"];
    }
    //endregion 保存证件照图片

    //检查出生日期
    if(!IsN($_birthday)){
      $isDate=strtotime($_birthday)?strtotime($_birthday):false;
      
      if($isDate === false){
        return ReturnError(L("_CUSTOMER_BIRTHDAY_ERROR_"));
      }

      $SaveBaseData["birthday"] = $isDate;
    }
    
    //region 检查地址
    if(!IsNum($_province_id, false, false)){
      return ReturnError(L("_REGION_PROVINCE_ID_NULL_"));
    }else{
      $SaveBaseData["province_id"] = $_province_id;
    }

    if(!IsNum($_city_id, false, false)){
      return ReturnError(L("_REGION_CITY_ID_NULL_"));
    }else{
      $SaveBaseData["city_id"] = $_city_id;
    }

    if(!IsNum($_district_id, false, false)){
      $_district_id = 0;
    }

    if(IsN($_address, false, false)){
      return ReturnError(L("_CUSTOMER_ADDRESS_NULL_"));
    }else{
      $SaveBaseData["address"] = $_address;
    }

    $SaveBaseData["province_id"] = $_province_id;
    $SaveBaseData["city_id"] = $_city_id;
    $SaveBaseData["district_id"] = $_district_id;
    $SaveBaseData["address"] = $_address;
    //endregion 检查地址

    //检查手机号
    if(!IsN($_phone)){
      if(!isMobile($_phone)){
        return ReturnError(L("_MOBILE_PHONE_ERROR_"));
      }

      $SaveBaseData["phone"] = $_phone;
    }

    if(!IsN($_wechat)){
      $SaveBaseData["wechat"] = $_wechat;
    }
    //endregion 数据检查,并赋值保存的数据

    //region 添加基本数据
    $nTime = time();

    if(IsArray($CustomerInfo)){
      $SaveBaseData["update_time"] = $nTime;
      
      $SaveBaseResult = $this->modCustomer->save($SaveBaseData);

      $nCustomerID = $CustomerInfo["id"];
    }else{
      $SaveBaseData["add_time"] = $nTime;
      $SaveBaseData["update_time"] = $nTime;

      $SaveBaseResult = $this->modCustomer->add($SaveBaseData);

      $nCustomerID = $SaveBaseResult;
    }

    if($SaveBaseResult === false){
      return ReturnError(L("_SAVE_DATA_FAILURE_"));
    }
    //endregion 添加基本数据

    $this->clsCustomer->SetCustomerDetailsCache($nCustomerID);

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"));
  }

  /**
   * todo: 删除客户
   *
   * @param $_id_str id字符串，逗号分割id
   *
   * @return array
   */
  public function CustomerDelete($_id_str){
    if(IsN($_id_str)){
      return ReturnError(L("_CUSTOMER_ID_NULL_"));
    }else{
      $IdList = explode(",", $_id_str);

      foreach($IdList as $key=>$val){
        if(!IsNum($val, false, false)){
          return ReturnError(L("_CUSTOMER_ID_ERROR_"));
        }
      }
    }

    $sWhere = "id in ({$_id_str})";
    $Result = $this->modCustomer->where($sWhere)->delete();

    if($Result === false){
      return ReturnError(L("_DELETE_DATA_FAILURE_"));
    }

    foreach($IdList as $key=>$val){
      $this->clsCustomer->SetCustomerDetailsCache($val);
    }

    return ReturnCorrect(L("_DELETE_DATA_SUCCEED_"));
  }

  /**
   * todo: 刷新客户缓存
   */
  public function ResetCustomerCache(){
    $sField = "id";
    $CustomerIdList = $this->modCustomer->field($sField)->select();

    foreach($CustomerIdList as $key=>$val){
      $this->clsCustomer->SetCustomerDetailsCache($val["id"]);
    }

    return ReturnCorrect(L("_CACHE_REFRESH_SUCCEED_"));
  }

  /**
   * todo: 上传图片
   */
  private function UploadCustomerIdCardImage($_file){
    if(!IsArray($_file)){
      return ReturnError(L("_UPLOAD_FILE_NOT_EXIST_"));
    }

    $clsUploadFile = new \Org\ZhiHui\UploadFile();
    $clsUploadFile->FilePathType = "customer_idcard";
    $clsUploadFile->FileNameType = "time";

    $UploadResult = $clsUploadFile->UploadImage($_file);

    if($UploadResult["error"] !=0){
      return $UploadResult;
    }

    $UploadImageInfo = $UploadResult["data"]["idcard_img"];
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
  //endregion 客户
}
