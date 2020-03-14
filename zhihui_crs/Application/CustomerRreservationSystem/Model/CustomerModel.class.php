<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

class CustomerModel extends Model {
  Protected $autoCheckFields = false;

  private $_CustomerRealNameLength = 10;

  private $modCustomer = null;

  public $clsCustomer = null;
  public $clsUser = null;
  public $clsRegion = null;
  
  function __construct() {
    $this->modCustomer = M("customer");
    
    $this->clsCustomer = new \Org\ZhiHui\Customer();
    $this->clsUser = new \Org\ZhiHui\User();
    $this->clsRegion = new \Org\ZhiHui\Region();
  }
  
  //region 客户
  /**
   * todo:客户列表
   *
   * @param int   $_seller_captain_id
   * @param int   $_seller_member_id
   * @param int   $_rownum
   * @param array $_param
   *
   * @return array
   */
  public function CustomerList($_seller_captain_id, $_seller_member_id=0, $_rownum=20, $_param=array()){
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

      if(IsNum($_seller_captain_id, false, false)){
        $sWhere .= " and seller_captain_id={$_seller_captain_id}";
      }

      if(IsNum($_seller_member_id, false, false)){
        $sWhere .= " and seller_member_id={$_seller_member_id}";
      }
    }

    $nTotal = $this->modCustomer->where($sWhere)->count();

    $Page = new \Org\ZhiHui\ZhiHuiPage($nTotal, $_rownum);
    $PageShow = $Page->show();// 分页显示输出

    $CustomerIdList = $this->modCustomer->field($sField)->where($sWhere)->order($sOrder)->limit($Page->firstRow.','.$Page->listRows)->select();
    $CustomerList = array();

    foreach($CustomerIdList as $key=>$val){
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($val["id"]);

      $CustomerInfo["seller_captain_name"] = "--";
      if(IsNum($CustomerInfo["seller_captain_id"], false, false)){
        $UserInfo = $this->clsUser->GetUserDetails($CustomerInfo["seller_captain_id"]);
        $CustomerInfo["seller_captain_name"] = $UserInfo["real_name"];
      }

      $CustomerInfo["seller_member_name"] = "--";
      if(IsNum($CustomerInfo["seller_member_id"], false, false)){
        $UserInfo = $this->clsUser->GetUserDetails($CustomerInfo["seller_member_id"]);
        $CustomerInfo["seller_member_name"] = $UserInfo["real_name"];
      }

      array_push($CustomerList, $CustomerInfo);
    }

    return array("PageInfo"=>$PageShow, "DataList"=>$CustomerList);
  }

  /**
   * todo: 获取客户经理的客户列表
   * @param $_user_id
   *
   * @return array
   */
  public function SellerMemberCustomerList($_user_id){
    if(!IsNum($_user_id, false, false)){
      return array();
    }

    $sField = "id";
    $sWhere = "seller_member_id={$_user_id}";
    $CustomerIdList = $this->modCustomer->field($sField)->where($sWhere)->select();
    $CustomerList = array();

    foreach($CustomerIdList as $key=>$val){
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($val["id"]);

      array_push($CustomerList, $CustomerInfo);
    }

    return $CustomerList;
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
   * @param $_customer_id       客户ID
   * @param $_seller_captain_id 团队长ID
   * @param $_seller_member_id  客户经理ID
   * @param $_real_name         真实姓名
   * @param $_gender            性别
   * @param $_nationality       国籍
   * @param $_idcard_type       证件类型
   * @param $_idcard_no         证件号
   * @param $_file_idcard_img_a 上传的证件照文件
   * @param $_idcard_img_a_id   证件照文件ID
   * @param $_birthday          出生日期
   * @param $_province_id       省份ID
   * @param $_city_id           城市ID
   * @param $_district_id       区县ID
   * @param $_address           详细地址
   * @param $_phone             手机号
   * @param $_wechat            微信号
   *
   * @return array
   */
  public function CustomerSave($_customer_id, $_seller_captain_id, $_seller_member_id, $_real_name, $_gender, $_nationality, $_idcard_type, $_idcard_no, $_file_idcard_img_a, $_idcard_img_a_id, $_birthday, $_province_id, $_city_id, $_district_id, $_address, $_phone, $_wechat){
    $CustomerInfo = array();

    //region 数据检查,并赋值保存的数据
    //region 检查客户信息
    if(IsNum($_customer_id, false, false)){
      $CustomerInfo = $this->clsCustomer->GetCustomerDetails($_customer_id);

      if(!IsArray($CustomerInfo)){
        return ReturnError(L("_CUSTOMER_NOT_EXIST_"));
      }

      $SaveBaseData["id"] = $_customer_id;
    }
    //endregion 检查客户信息

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

      $SaveBaseData["broker_company_id"] = $SellerCaptainInfo["parent_user_id"];
      $SaveBaseData["seller_captain_id"] = $SellerCaptainInfo["id"];
    }
    //endregion 检查团队长

    //region 检查客户经理
    if(!IsNum($_seller_member_id, false, false)){
      return ReturnError(L("_SELLER_MEMBER_ID_ERROR_"));
    }else{
      $SellerMemberInfo = $this->clsUser->GetUserDetails($_seller_member_id);

      if(!IsArray($SellerMemberInfo)){
        return ReturnError(L("_SELLER_MEMBER_NOT_EXIST_"));
      }else{
        $UserGroupInfo = $this->clsUser->SellereMemberUserGroup();

        if($SellerMemberInfo["group_id"] != $UserGroupInfo["group_id"]){
          return ReturnError(L("_USER_GROUP_NOT_SELLER_MEMBER_"));
        }

        if($SellerMemberInfo["parent_user_id"] != $SellerCaptainInfo["id"]){
          return ReturnError(L("_SELLER_MEMBER_NOT_SELLER_CAPTAIN_USER_"));
        }
      }

      $SaveBaseData["seller_member_id"] = $SellerMemberInfo["id"];
    }
    //endregion 检查客户经理
    
    //region 检查姓名
    if(IsN($_real_name)){
      return ReturnError(L("_CUSTOMER_REAL_NAME_NULL_"));
    }else{
      $SaveBaseData["real_name"] = $_real_name;
    }
    //endregion 检查姓名
    
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
    /*
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
    */
    //endregion 检查证件类型
    
    //region 检查证件号
    /*
    if(IsN($_idcard_no)){
      return ReturnError(L("_CUSTOMER_IDCARD_NO_NULL_"));
    }else{
      $SaveBaseData["idcard_no"] = $_idcard_no;
    }
    */
    //endregion 检查证件号

    //region 保存证件照图片
    /*
    if(IsArray($_file_idcard_img_a)){
      $nImageId = $this->UploadCustomerIdCardImage($_file_idcard_img_a);

      if($nImageId["error"] != 0){
        return $nImageId;
      }

      $SaveBaseData["idcard_img_a"] = $nImageId["data"];
    }
    */
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
    /*
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
    */
    //endregion 检查地址

    //检查手机号
    /*
    if(!IsN($_phone)){
      if(!isMobile($_phone)){
        return ReturnError(L("_MOBILE_PHONE_ERROR_"));
      }

      $SaveBaseData["phone"] = $_phone;
    }

    if(!IsN($_wechat)){
      $SaveBaseData["wechat"] = $_wechat;
    }
    */
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

    return ReturnCorrect(L("_SAVE_DATA_SUCCEED_"), $nCustomerID);
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

  public function CustomerPayList($_customer_id){
    $CustomerPayList = array();

    if(!IsNum($_customer_id, false, false)){
      return $CustomerPayList;
    }

    $clsProduct = new \Org\ZhiHui\Product();
    $clsOrder = new \Org\ZhiHui\Order();
    
    $sField = "order_sn";
    $sWhere = "customer_id={$_customer_id}";
    $sWhere .= " and status>={$clsOrder->OrderStatusPayStatus()}";
    $sOrder = "pay_time desc";

    $modOrder = M("order");

    $OrderSnList = $modOrder->field($sField)->where($sWhere)->order($sOrder)->select();

    foreach($OrderSnList as $key=>$val){
      $OrderInfo = $clsOrder->GetOrderDetails($val["order_sn"]);

      if(IsNum($OrderInfo["seller_member_id"], false, false)){
        $UserInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_member_id"]);
      }else{
        if(IsNum($OrderInfo["seller_captain_id"], false, false)){
          $UserInfo = $this->clsUser->GetUserDetails($OrderInfo["seller_captain_id"]);
        }
      }

      $ProductInfo = $clsProduct->GetProductSnapshotsDetails($OrderInfo["product_snapshots_id"]);

      $Info["order_sn"] = $OrderInfo["order_sn"];
      $Info["pay_amount"] = $OrderInfo["year_premium_amount"];
      $Info["pay_time"] = Time2FullDate($OrderInfo["pay_time"]);
      $Info["seller_name"] = $UserInfo["real_name"];
      $Info["product_type_name"] = $ProductInfo["type_name"];
      $Info["product_name"] = $ProductInfo["product_name"];

      array_push($CustomerPayList, $Info);
    }

    return $CustomerPayList;
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
