<?php
namespace Admin\Controller;
use Think\Controller;

class AppMenuController extends CommonController {
    //页面模型
    private $PageModel = "AppCategory";
    
    public function index(){
        $this->_PageTitle = "应用栏目列表";
        
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        
        //添加列表数据的url
        $this->_PageListAddUrl = __MODULE__ . "/AppMenu/add/navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        
        //修改列表数据的url
        $this->_PageListEditUrl = __MODULE__ . "/AppMenu/edit/navmid/{$MenuMainID}/navsid/{$MenuSubID}";

        //删除列表数据的url
        $this->_PageListDeleteUrl = __MODULE__ . "/AppMenu/del";
        
        //可以添加任意层级的菜单
        $this->_MenuAddChild = false;
        
        //自定义按钮
        $this->_PageCustomBtn = array(
            array("title"=>"添加栏目", "event"=>"", "href"=>__MODULE__ . "/AppMenu/add/navmid/{$MenuMainID}/navsid/{$MenuSubID}", "icon"=>"icon-plus", "css"=>"btn-info"),
            //array("title"=>"删除所选栏目", "event"=>"", "href"=>"javascript:void(0);", "icon"=>"icon-trash", "css"=>"btn-danger"),
        );
        
        $listData = GetAppMenuCache();
        
        $nCount = sizeof($listData);
        
        for($i=0; $i<$nCount; $i++){
            $sSing = GetAppMenuSign($listData[$i], $listData[$i-1], $listData[$i+1]);
            $listData[$i]["menu_name"] = $sSing . $listData[$i]["menu_name"];
            $listData[$i]["statusstr"] = $this->_Status[$listData[$i]["status"]];
        }

        $this->displayCategoryList($listData);
    }
    
    public function add(){
        //保存提交的数据 Begin
        if($_POST){                        
            //data中数组元素的顺序，需要与存储过程中的参数顺序一致。
            $AddData = array(
                "parentid"=> param("parentid"),
                "type"=> 0,
                "menu_name"=> "'". param("menu_name") ."'",
                "en_menu_name"=> "'". param("en_menu_name") ."'",
                "bgcolor"=> "'". param("bgcolor") ."'",
                "url"=> "'". param("url") ."'",
                "navurl"=> "'". param("navurl") ."'",
                "intro"=> "''",
                "content"=> "''",
                "image"=> "'". param("image") ."'",
                "order"=> 0,
                "status"=>  param("status"),
                "admid"=>  get_s_id(),
                "addtime"=> time(),
            );
            
            $addReturn = $this->SetMenuData("exec_app_menu_add",$AddData);

            if($addReturn === true){
                SuccessInfo("数据保存成功");
            }else{
                ErrorInfo("数据保存失败");
            }
        }
        //保存提交的数据 End
        
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        $ParentID = param("parentid");
        
        $this->_PageTitle = "添加栏目";
        $this->_PostUrl = __MODULE__ . "/AppMenu/add/navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        $this->_PostBackUrl = __MODULE__ . "/AppMenu/index/navmid/{$MenuMainID}/navsid/{$MenuSubID}";

        
        //自定义按钮
        $this->_PageCustomBtn = array(
            array("id"=>"btnSave", "title"=>"保存", "event"=>"checkdata();", "icon"=>"icon-ok", "css"=>"btn-info"),
            array("title"=>"取消", "event"=>"history.go(-1);", "href"=>"javascript:void(0);", "icon"=>"", "css"=>"btn-default"),
        );
        
        //页面数据配置 Begin
        $arrPageConfig = GetSetPageFieldConfig($this->PageModel);
        $nCount = sizeof($arrPageConfig);
        
        for($i=0; $i<$nCount; $i++){
            if($arrPageConfig[$i]["id"] == "parentid"){
                $arrPageConfig[$i]["value"] = $ParentID;
            }
        }
        
        $this->displayNewsSet($arrPageConfig);
    }
    
    public function edit(){
        //保存提交的数据 Begin
        if($_POST){
            //data中数组元素的顺序，需要与存储过程中的参数顺序一致。
            $EditData = array(
                "menuid"=> param("menuid"),
                "parentid"=> param("parentid"),
                "menu_name"=> "'". param("menu_name") ."'",
                "en_menu_name"=> "'". param("en_menu_name") ."'",
                "bgcolor"=> "'". param("bgcolor") ."'",
                "url"=> "'". param("url") ."'",
                "navurl"=> "'". param("navurl") ."'",
                "order"=> 0,
                "type"=> 0,
                "intro"=> "''",
                "content"=> "''",
                "image"=> "'". param("image") ."'",
                "status"=>  param("status"),
                "admid"=>  get_s_id(),
                "modifytime"=> time(),
            );
//            exitp($EditData);
            $addReturn = $this->SetMenuData("exec_app_menu_modify",$EditData);

            if($addReturn === true){
                SuccessInfo("数据保存成功");
            }else{
                ErrorInfo("数据保存失败");
            }
        }
        //保存提交的数据 End
        
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        $nAppMenuID = param("menuid");
        
        $this->_PageTitle = "添加栏目";
        $this->_PostUrl = __MODULE__ . "/AppMenu/edit/navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        $this->_PostBackUrl = __MODULE__ . "/AppMenu/index/navmid/{$MenuMainID}/navsid/{$MenuSubID}";

        
        //自定义按钮
        $this->_PageCustomBtn = array(
            array("id"=>"btnSave", "title"=>"保存", "event"=>"checkdata();", "icon"=>"icon-ok", "css"=>"btn-info"),
            array("title"=>"取消", "event"=>"history.go(-1);", "href"=>"javascript:void(0);", "icon"=>"", "css"=>"btn-default"),
        );
        
        //页面数据配置 Begin
        $arrPageConfig = GetSetPageFieldConfig($this->PageModel, false);
        $nCount = sizeof($arrPageConfig);
        
        $model = M("app_menu");
        $AppMenuInfo = $model->where("`menuid`={$nAppMenuID}")->find();
        
        for($i=0; $i<$nCount; $i++){
            $sKey = $arrPageConfig[$i]["id"];
            
            $arrPageConfig[$i]["value"] = $AppMenuInfo[$sKey];
        }
        
        $this->displayNewsSet($arrPageConfig);
    }
    
    //删除栏目
    public function del(){
        $sIds = param("delid");
        $DelData = explode(',', $sIds);

        $return = $this->SetMenuData("exec_app_menu_del",$DelData);
            
        if($return){
            SuccessInfo("栏目删除成功");
        }else{
            ErrorInfo("栏目删除失败");
        }
    }
    
    //格式化缓存的系统栏目
    private function SetAppMenuArrayItem($_arr){        
        $arrItem = array(
            "menuid"=>$_arr["menuid"],
            "type"=>$_arr["type"],
            "menu_name"=>$_arr["menu_name"],
            "url"=>$_arr["url"],
            "intro"=>$_arr["intro"],
            "content"=>$_arr["content"],
            "image"=>$_arr["image"],
            "parentid"=>$_arr["parentid"],
            "left_val"=>$_arr["left_val"],
            "right_val"=>$_arr["right_val"],
            "order"=>$_arr["order"],
            "menu_path"=>$_arr["menu_path"],
            "dept"=>$_arr["dept"],
            "child"=>$_arr["child"],
            "status"=>$_arr["status"],
            "statusstr"=>$this->_Status[$_arr["status"]],
            "addtime"=>$_arr["addtime"],
            "modifytime"=>$_arr["modifytime"],
            "admid"=>$_arr["admid"],
        );
        
        return $arrItem;
    }
}

