<?php
namespace CustomerRreservationSystem\Controller;
use Think\Controller;

class RegionController extends Controller {
  public function GetSubRegionOption(){
    $nParentID = RR("parent_id");
    $sSubType = RR("sub_type");
    
    $clsRegion = new \Org\ZhiHui\Region();
    
    $Reuslt = array();
    
    switch($sSubType){
      case "city":
        if(!IsNum($nParentID, false, false)){
          AjaxReturnCorrect(L("_REGION_PROVINCE_ID_ERROR_"), "");
        }
        
        $RegionList = $clsRegion->GetCityList($nParentID);

        if(!IsArray($RegionList)){
          AjaxReturnCorrect(L("_REGION_CITY_LIST_NOT_GET_"), "");
        }

        foreach($RegionList as $key=>$val){
          $Info["key"] = $val["city_name"];
          $Info["val"] = $val["city_id"];

          array_push($Reuslt, $Info);
        }
        break;
      
      case "district":
        if(!IsNum($nParentID, false, false)){
          AjaxReturnCorrect(L("_REGION_CITY_ID_ERROR_"), "");
        }
        
        $RegionList = $clsRegion->GetDistrictList($nParentID);

        if(!IsArray($RegionList)){
          AjaxReturnCorrect(L("_REGION_DISTRICT_LIST_NOT_GET_"), "");
        }
        
        foreach($RegionList as $key=>$val){
          $Info["key"] = $val["district_name"];
          $Info["val"] = $val["district_id"];
          
          array_push($Reuslt, $Info);
        }
        break;
      
      default:
        AjaxReturnCorrect(L("_REGION_TYPE_ERROR_"), "");
        break;
    }
    
    AjaxReturnCorrect("", $Reuslt);
  }
}