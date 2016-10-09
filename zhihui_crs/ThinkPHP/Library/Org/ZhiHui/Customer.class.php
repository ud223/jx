<?php
/**
 * Created by PhpStorm.
 * User: 淼
 * Date: 2015/4/29
 * Time: 11:41
 */

namespace Org\ZhiHui;

class Customer {
  private $modCustomer = null;
  
  public $_IdCardType = array(
    array(
      "key"=>"身份证",
      "val"=>"1",
    ),
    array(
      "key"=>"护照",
      "val"=>"2",
    ),
  );

  public $_Gender = array(
    array(
      "key"=>"男",
      "val"=>"1",
    ),
    array(
      "key"=>"女",
      "val"=>"2",
    ),
  );
  
  function __construct() {
    $this->modCustomer = M("customer");
  }
  
  //region 缓存
  //region 客户信息缓存
  /**
   * 获取客户信息
   * @param $_customer_id 客户ID
   * @return mixed
   */
  public function GetCustomerDetails($_customer_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetCustomerDetailsCache($_customer_id);

      if(empty($Info)){
        $Info = $this->GetCustomerDetailsDb($_customer_id);
      }
    }else{
      $Info = $this->GetCustomerDetailsDb($_customer_id);
    }
    
    if(IsArray($Info)){
      $clsPicture = new \Org\ZhiHui\Picture();
      $clsRegion = new \Org\ZhiHui\Region();

      $Info["province_name"] = "";
      if(IsNum($Info["province_id"], false, false)){
        $ProvinceInfo = $clsRegion->GetProvinceDetails($Info["province_id"]);
        $Info["province_name"] = $ProvinceInfo["province_name"];
      }

      $Info["city_name"] = "";
      if(IsNum($Info["city_id"], false, false)){
        $CityInfo = $clsRegion->GetCityDetails($Info["city_id"]);
        $Info["city_name"] = $CityInfo["city_name"];
      }

      $Info["district_name"] = "";
      if(IsNum($Info["district_id"], false, false)){
        $DistrictInfo = $clsRegion->GetDistrictDetails($Info["district_id"]);
        $Info["district_name"] = $DistrictInfo["district_name"];
      }
      
      if(IsNum($Info["idcard_img_a"], false, false)){
        $PictureInfo = $clsPicture->GetPictureDetails($Info["idcard_img_a"]);
        $PicSizeKB = $PictureInfo["pic_size"]/1024;
        $PicSizeMB = $PicSizeKB/1024;
        
        $PictureInfo["full_file"] = FullImageUrl($PictureInfo["file"]);
        $PictureInfo["pic_size_kb"] = round($PicSizeKB, 2);
        $PictureInfo["pic_size_mb"] = round($PicSizeMB, 2);

        $PictureInfo["add_date"] = Time2FullDate($PictureInfo["add_time"], "Y-m-d");
        
        $Info["idcard_img_a"] = $PictureInfo;
      }
    }

    return $Info;
  }

  /**
   * 设置客户信息数据缓存
   * @param $_customer_id 客户ID
   * @param array $_data 客户信息数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetCustomerDetailsCache($_customer_id, $_data=NULL){
    F($this->GetCustomerDetailsCacheKey($_customer_id), $_data);
  }

  /**
   * 获取客户信息数据缓存
   * @param $_customer_id 客户ID
   * @return mixed
   */
  private function GetCustomerDetailsCache($_customer_id){
    return F($this->GetCustomerDetailsCacheKey($_customer_id));
  }

  /**
   * 获取客户信息缓存Key
   * @param $_customer_id 客户ID
   * @return mixed
   */
  private function GetCustomerDetailsCacheKey($_customer_id){
    return "Customer/Details/{$_customer_id}";
  }

  /**
   * todo:从数据库中获取客户信息数据
   * @param $_customer_id 客户ID
   *
   * @return mixed
   */
  private function GetCustomerDetailsDb($_customer_id){
    $CustomerDetails = array();

    if(!IsNum($_customer_id, false, false)){
      return $CustomerDetails;
    }

    $sWhere = "id={$_customer_id}";
    $CustomerDetails = $this->modCustomer->where($sWhere)->find();

    if(!empty($CustomerDetails)){
      $this->SetCustomerDetailsCache($_customer_id, $CustomerDetails);
    }

    return $CustomerDetails;
  }
  //endregion 客户信息缓存
  //endregion 缓存

  /**
   * todo: 客户Option选项列表
   * @return array
   */
  public function CustomerOption($_broker_company_id=0){
    $sField = "id";
    $sWhere = "1=1";
    $sOrder = "id desc";

    if(IsNum($_broker_company_id, false, false)){
      $sWhere = "broker_company_id={$_broker_company_id}";
    }

    $CustomerIdList = $this->modCustomer->field($sField)->where($sWhere)->order($sOrder)->select();
    $CustomerOptionList = array();

    foreach($CustomerIdList as $key=>$val){
      $CustomerInfo = $this->GetCustomerDetails($val["id"]);

      $OptionInfo["key"] = $CustomerInfo["real_name"];
      
      if(!IsN($CustomerInfo["phone"])){
        $OptionInfo["key"] .= "（{$CustomerInfo["phone"]}）";
      }
      
      $OptionInfo["val"] = $CustomerInfo["id"];

      array_push($CustomerOptionList, $OptionInfo);
    }

    return $CustomerOptionList;
  }
}