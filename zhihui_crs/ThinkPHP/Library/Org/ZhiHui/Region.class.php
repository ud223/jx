<?php
/**
 * 地区缓存类
 * User: 淼
 * Date: 2015/4/29
 * Time: 11:41
 */

namespace Org\ZhiHui;


class Region {
  private $modProvince = null;
  private $modCity = null;
  private $modDistrict = null;

  function __construct() {
    $this->modProvince = M("area_province");
    $this->modCity = M("area_city");
    $this->modDistrict = M("area_district");

    $this->_DefaultAuthor = $this->clsSiteConfig->_SiteFullName;
  }
  
  //region 省份缓存
  /**
   * 获取省份详情
   * @param int $_province_id 省份id
   * @return mixed
   */
  public function GetProvinceDetails($_province_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetProvinceDetailsCache($_province_id);

      if(empty($Info)){
        $Info = $this->GetProvinceDetailsDb($_province_id);
      }
    }else{
      $Info = $this->GetProvinceDetailsDb($_province_id);
    }

    return $Info;
  }

  /**
   * 设置省份详情数据缓存
   * @param int $_province_id 省份id
   * @param array $_data 商品省份详情数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetProvinceDetailsCache($_province_id, $_data=NULL){
    F($this->GetProvinceDetailsCacheKey($_province_id), $_data);
  }

  /**
   * todo:更新省份详情缓存
   */
  public function UpdateProvinceFieldCache($_id, $_key, $_val){
    $ProvinceDetails = $this->GetProvinceDetails($_id);
    $ProvinceDetails[$_key] = $_val;

    $this->SetProvinceDetailsCache($_id, $ProvinceDetails);
  }

  /**
   * 获取省份详情数据缓存
   * @param int $_province_id 省份id
   * @return mixed
   */
  private function GetProvinceDetailsCache($_province_id){
    return F($this->GetProvinceDetailsCacheKey($_province_id));
  }

  /**
   * 获取省份详情缓存Key
   * @param int $_province_id 省份id
   * @return mixed
   */
  private function GetProvinceDetailsCacheKey($_province_id){
    return "Region/Province/Details/{$_province_id}";
  }

  /**
   * 从数据库中获取省份详情数据
   * @param int $_province_id 省份id
   * @return mixed
   */
  private function GetProvinceDetailsDb($_province_id){
    $sWhere = "province_id={$_province_id}";
    $ProvinceDetails = $this->modProvince->where($sWhere)->find();

    if(!empty($ProvinceDetails)){
      $this->SetProvinceDetailsCache($_province_id, $ProvinceDetails);
    }

    return $ProvinceDetails;
  }
  //endregion 省份缓存

  //region 城市缓存
  /**
   * 获取城市详情
   * @param int $_city_id 城市id
   * @return mixed
   */
  public function GetCityDetails($_city_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetCityDetailsCache($_city_id);

      if(empty($Info)){
        $Info = $this->GetCityDetailsDb($_city_id);
      }
    }else{
      $Info = $this->GetCityDetailsDb($_city_id);
    }

    return $Info;
  }

  /**
   * 设置城市详情数据缓存
   * @param int $_city_id 城市id
   * @param array $_data 商品城市详情数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetCityDetailsCache($_city_id, $_data=NULL){
    F($this->GetCityDetailsCacheKey($_city_id), $_data);
  }

  /**
   * todo:更新城市详情缓存
   */
  public function UpdateCityFieldCache($_id, $_key, $_val){
    $CityDetails = $this->GetCityDetails($_id);
    $CityDetails[$_key] = $_val;

    $this->SetCityDetailsCache($_id, $CityDetails);
  }

  /**
   * 获取城市详情数据缓存
   * @param int $_city_id 城市id
   * @return mixed
   */
  private function GetCityDetailsCache($_city_id){
    return F($this->GetCityDetailsCacheKey($_city_id));
  }

  /**
   * 获取城市详情缓存Key
   * @param int $_city_id 城市id
   * @return mixed
   */
  private function GetCityDetailsCacheKey($_city_id){
    return "Region/City/Details/{$_city_id}";
  }

  /**
   * 从数据库中获取城市详情数据
   * @param int $_city_id 城市id
   * @return mixed
   */
  private function GetCityDetailsDb($_city_id){
    $sWhere = "city_id={$_city_id}";
    $CityDetails = $this->modCity->where($sWhere)->find();

    if(!empty($CityDetails)){
      $this->SetCityDetailsCache($_city_id, $CityDetails);
    }

    return $CityDetails;
  }
  //endregion 城市缓存

  //region 区县缓存
  /**
   * 获取区县详情
   * @param int $_district_id 区县id
   * @return mixed
   */
  public function GetDistrictDetails($_district_id){
    if(GetCacheOnOff()){
      //使用缓存
      $Info = $this->GetDistrictDetailsCache($_district_id);

      if(empty($Info)){
        $Info = $this->GetDistrictDetailsDb($_district_id);
      }
    }else{
      $Info = $this->GetDistrictDetailsDb($_district_id);
    }

    return $Info;
  }

  /**
   * 设置区县详情数据缓存
   * @param int $_district_id 区县id
   * @param array $_data 商品区县详情数据，默认值为NULL，传空参数时，表示删除缓存
   * @return mixed
   */
  public function SetDistrictDetailsCache($_district_id, $_data=NULL){
    F($this->GetDistrictDetailsCacheKey($_district_id), $_data);
  }

  /**
   * todo:更新区县详情缓存
   */
  public function UpdateDistrictFieldCache($_id, $_key, $_val){
    $DistrictDetails = $this->GetDistrictDetails($_id);
    $DistrictDetails[$_key] = $_val;

    $this->SetDistrictDetailsCache($_id, $DistrictDetails);
  }

  /**
   * 获取区县详情数据缓存
   * @param int $_district_id 区县id
   * @return mixed
   */
  private function GetDistrictDetailsCache($_district_id){
    return F($this->GetDistrictDetailsCacheKey($_district_id));
  }

  /**
   * 获取区县详情缓存Key
   * @param int $_district_id 区县id
   * @return mixed
   */
  private function GetDistrictDetailsCacheKey($_district_id){
    return "Region/District/Details/{$_district_id}";
  }

  /**
   * 从数据库中获取区县详情数据
   * @param int $_district_id 区县id
   * @return mixed
   */
  private function GetDistrictDetailsDb($_district_id){
    $sWhere = "district_id={$_district_id}";
    $DistrictDetails = $this->modDistrict->where($sWhere)->find();

    if(!empty($DistrictDetails)){
      $this->SetDistrictDetailsCache($_district_id, $DistrictDetails);
    }

    return $DistrictDetails;
  }
  //endregion 区县缓存

  /**
   * todo: 获取省份列表
   * @return array
   */
  public function GetProvinceList($_is_all=true){
    $sField = "province_id";
    
    if(!$_is_all){
      $sWhere = "`status` = 1";
    }
    
    $sOrder = "`status` desc, province_id";
    
    $ProvinceIdList = $this->modProvince->field($sField)->where($sWhere)->order($sOrder)->select();
    
    $ProvinceList = array();
    
    foreach($ProvinceIdList as $key=>$val){
      $ProvinceInfo = $this->GetProvinceDetails($val["province_id"]);
      
      if(!empty($ProvinceInfo)){
        array_push($ProvinceList, $ProvinceInfo);
      }
    }
    
    return $ProvinceList;
  }
  
  /**
   * todo: 获取城市列表
   * @param $_province_id 省份ID
   *
   * @return array
   */
  public function GetCityList($_province_id, $_is_all=true){
    $CityList = array();
    
    if(!IsNum($_province_id, false, false)){
      return $CityList;
    }
    
    $sField = "city_id";
    $sWhere = "province_id={$_province_id}";

    if(!$_is_all){
      $sWhere .= " and `status`= 1";
    }

    $sOrder = "`status` desc, city_id";
    
    $CityIdList = $this->modCity->field($sField)->where($sWhere)->order($sOrder)->select();

    foreach($CityIdList as $key=>$val){
      $CityInfo = $this->GetCityDetails($val["city_id"]);

      if(!empty($CityInfo)){
        array_push($CityList, $CityInfo);
      }
    }

    return $CityList;
  }

  /**
   * todo: 获取区县列表
   * @param $_city_id 城市ID
   *
   * @return array
   */
  public function GetDistrictList($_city_id, $_is_all=true){
    $DistrictList = array();

    if(!IsNum($_city_id, false, false)){
      return $DistrictList;
    }

    $sField = "district_id";
    $sWhere = "city_id={$_city_id}";
    
    if(!$_is_all){
      $sWhere .= " and `status` = 1";
    }

    $sOrder = "`status` desc, city_id";
    
    $DistrictIdList = $this->modDistrict->field($sField)->where($sWhere)->order($sOrder)->select();

    foreach($DistrictIdList as $key=>$val){
      $DistrictInfo = $this->GetDistrictDetails($val["district_id"]);

      if(!empty($DistrictInfo)){
        array_push($DistrictList, $DistrictInfo);
      }
    }

    return $DistrictList;
  }
}