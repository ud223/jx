<?php
namespace Admin\Controller;
use Think\Controller;

class SystemController extends CommonController {
    public function update_cache(){
        $sAction = param("act");
        $sFunName = param("fun");
        
        if($sAction == "update"){
            if($sFunName == 'SyncSystemMenu'){
                $BttenCache = new \Btten\BttenCache();
                
                $BttenCache->SyncSystenNav();
                $SystenMenu = SyncSystemMenu();
                SuccessInfo("系统栏目缓存更新成功......");
            }
            
            if($sFunName == 'SyncAppMenu'){
                $BttenCache = new \Btten\BttenCache();
                
                $Menu = $BttenCache->SyncAppMenu();
                $AppMenu = SyncAppMenu();
                SuccessInfo("应用栏目缓存更新成功......");
            }
            
            if($sFunName == 'SyncAppMenu_DLL'){
                $AppMenuDll = SyncAppMenu_DLL();
                SuccessInfo("应用栏目(下拉框)缓存更新成功......");
            }
        }
        
        $this->display();
    }
    
    public function config(){
        //保存提交的数据 Begin
        if($_POST){
            $nAddTime = time();
            
            $AddData = array(
                "id"=>  param("id"),
                "site_name"=> param("site_name"),
                "phone"=> param("phone"),
                "ad"=> param("ad"),
                "copyright"=> param("copyright"),
                "admid"=>  get_s_id(),
                "modifytime"=> $nAddTime,
            );
            
            if(empty($AddData["id"])){
                $addReturn = $this->SetData("system_config", $AddData);
            }else{
                $addReturn = $this->SetData("system_config", $AddData, "id");
            }
            
            if($addReturn >= 0){
                SuccessInfo("保存成功");
            }else{
                ErrorInfo("保存失败");
            }
        }
        //保存提交的数据 End
        
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");

        if(!empty($MenuSubID)){
            $FixedParam = "navmid/{$MenuMainID}/navsid/{$MenuSubID}";
            $nMenuID = $MenuSubID;
        }else{
            $FixedParam = "navmid/{$MenuMainID}";
            $nMenuID = $MenuMainID;
        }
        
        $model = M("system_category");
        $MenuInfo = $model->where("`menuid`={$nMenuID}")->find();
        
        $this->_PageTitle = "配置{$MenuInfo["menu_name"]}";
        
        $this->_PostUrl = __MODULE__ . "/System/config/{$FixedParam}";
        $this->_PostBackUrl = __MODULE__ . "/System/config/{$FixedParam}";

        //自定义按钮
        $this->_PageCustomBtn = array(
            array("id"=>"btnSave", "title"=>"保存", "event"=>"checkdata();", "icon"=>"icon-ok", "css"=>"btn-info"),
            array("title"=>"取消", "event"=>"history.go(-1);", "href"=>"javascript:void(0);", "icon"=>"", "css"=>"btn-default"),
        );
        
        //页面数据配置 Begin
        $arrPageConfig = GetSetPageFieldConfig("AppConfig", false);
        
        $nCount = sizeof($arrPageConfig);
        
        $model = M("system_config");
        $arrConfig = $model->order("id")->find();
        
        if(!empty($arrConfig)){
            for($i=0; $i<$nCount; $i++){
                $sKey = $arrPageConfig[$i]["id"];
                $arrPageConfig[$i]["value"] = $arrConfig[$sKey];
            }
        }

        $this->displayNewsSet($arrPageConfig);
    }
}

