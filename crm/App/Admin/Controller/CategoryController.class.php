<?php
namespace Admin\Controller;
use Think\Controller;

class CategoryController extends CommonController {
    public function index(){
        $this->_PageTitle = "系统栏目列表";
        
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        
        //添加列表数据的url
        $this->_PageListAddUrl = __MODULE__ . "/Category/add/navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        
        //修改列表数据的url
        $this->_PageListEditUrl = __MODULE__ . "/Category/edit/navmid/{$MenuMainID}/navsid/{$MenuSubID}";

        //删除列表数据的url
        $this->_PageListDeleteUrl = __MODULE__ . "/Category/del";
        
        //自定义按钮
        $this->_PageCustomBtn = array(
            array("title"=>"添加栏目", "event"=>"", "href"=>$this->_PageListAddUrl, "icon"=>"icon-plus", "css"=>"btn-info"),
            //array("title"=>"删除所选栏目", "event"=>"", "href"=>"javascript:void(0);", "icon"=>"icon-trash", "css"=>"btn-danger"),
        );
        
        $arrMenuList = GetSystemMenuCache();
        
        $nCount = sizeof($arrMenuList);
        $listData = array();
        
        for($i=0; $i<$nCount; $i++){
            array_push($listData, $this->SetSystemMenuArrayItem($arrMenuList[$i]));
            
            if(!empty($arrMenuList[$i]["sub"])){
                $nSubCount = sizeof($arrMenuList[$i]["sub"]);
                
                for($k=0; $k<$nSubCount; $k++){
                    $sSing = "&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;&nbsp;";
                    
                    if(($k+1) >= $nSubCount){
                        $sSing = "&nbsp;&nbsp;&nbsp;&nbsp;└&nbsp;&nbsp;";
                    }
                    
                    $arrMenuList[$i]["sub"][$k]["menu_name"] = "{$sSing}{$arrMenuList[$i]["sub"][$k]["menu_name"]}";
                    
                    array_push($listData, $this->SetSystemMenuArrayItem($arrMenuList[$i]["sub"][$k]));
                }
            }
        }
        
        $this->displayCategoryList($listData);
    }

    //添加栏目
    public function add(){
        if($_POST){
            //data中数组元素的顺序，需要与存储过程中的参数顺序一致。
            $AddData = array(
                "parentid"=> param("parentid"),
                "type"=> 0,
                "menu_name"=> "'". param("menu_name") ."'",
                "url"=> "'". param("url") ."'",
                "intro"=> "''",
                "content"=> "''",
                "image"=> "''",
                "order"=> 0,
                "status"=>  param("status"),
                "admid"=>  get_s_id(),
                "addtime"=> time(),
            );

            
            $return = $this->SetMenuData("exec_system_category_add",$AddData);
            
            if($return){
                SuccessInfo("栏目保存成功");
            }else{
                ErrorInfo("栏目保存失败");
            }
        }
    
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        $ParentID = param("parentid");
        
        $this->_PageTitle = "添加系统栏目";
        $this->_PostUrl = __MODULE__ . "/Category/add/navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        $this->_PostBackUrl = __MODULE__ . "/Category/index/navmid/{$MenuMainID}/navsid/{$MenuSubID}";

        
        //自定义按钮
        $this->_PageCustomBtn = array(
            array("id"=>"btnSave", "title"=>"保存", "event"=>"checkdata();", "icon"=>"icon-ok", "css"=>"btn-info"),
            array("title"=>"取消", "event"=>"history.go(-1);", "href"=>"javascript:void(0);", "icon"=>"", "css"=>"btn-default"),
        );
        
        //页面数据配置 Begin
        //注意第一条中包含数组括号 [ ，最后一条包含数组括号 ]
        $arrPage = array();
        array_push($arrPage, '[{"control":"system_category", "name":"parentid", "id":"parentid", "title":"所属栏目", "tips":"", "value":"'. $ParentID .'", "icon":false, "inside_text":"", "required":true, "rule":"", "default":"", "error":"格式错误", "nulltip":"选择所属栏目", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"textbox", "name":"menu_name", "id":"menu_name", "title":"栏目名称", "tips":"", "value":"", "icon":false, "inside_text":"", "required":true, "rule":"", "default":"", "error":"格式错误", "nulltip":"请输入栏目名称", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"textbox", "name":"url", "id":"url", "title":"栏目链接", "tips":"控制器名/操作名", "value":"", "icon":false, "inside_text":"", "css":"", "required":true, "rule":"", "default":"", "error":"格式错误", "nulltip":"请输入栏目链接", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"radio", "name":"status", "id":"status", "title":"栏目状态", "tips":"", "value":"0", "icon":false, "inside_text":"", "css":"last", "required":true, "rule":"", "default":"", "error":"格式错误", "nulltip":"请输入栏目链接", "minlen":0, "maxlen":0, "option":[{"key":"正常", "val":"0"},{"key":"禁用", "val":"1"}]}]');
        $sJsonConfig = join(",", $arrPage);

        $arrPageConfig = json_decode($sJsonConfig, true);
        //页面数据配置 End

        $this->displayCategoryAdd($arrPageConfig);
    }
    
    //修改栏目
    public function edit(){
        if($_POST){
            //data中数组元素的顺序，需要与存储过程中的参数顺序一致。
            $EditData = array(
                "menuid"=> param("menuid"),
                "parentid"=> param("parentid"),
                "menu_name"=> "'". param("menu_name") ."'",
                "url"=> "'". param("url") ."'",
                "order"=> 0,
                "type"=> 0,
                "intro"=> "''",
                "content"=> "''",
                "image"=> "''",
                "status"=>  param("status"),
                "admid"=>  get_s_id(),
                "modifytime"=> time(),
            );

            $return = $this->SetMenuData("exec_system_category_modify",$EditData);
            
            if($return){
                SuccessInfo("栏目保存成功");
            }else{
                ErrorInfo("栏目保存失败");
            }
        }
    
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        $SelectMenuID = param("menuid");
        
        $this->_PageTitle = "添加系统栏目";
        $this->_PostUrl = __MODULE__ . "/Category/edit/navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        $this->_PostBackUrl = __MODULE__ . "/Category/index/navmid/{$MenuMainID}/navsid/{$MenuSubID}";

        //自定义按钮
        $this->_PageCustomBtn = array(
            array("id"=>"btnSave", "title"=>"保存", "event"=>"checkdata();", "icon"=>"icon-ok", "css"=>"btn-info"),
            array("title"=>"取消", "event"=>"history.go(-1);", "href"=>"javascript:void(0);", "icon"=>"", "css"=>"btn-default"),
        );
            
        $model = M("system_category");
        $MenuInfo = $model->where("menuid={$SelectMenuID}")->find();
        
        //页面数据配置 Begin
        //注意第一条中包含数组括号 [ ，最后一条包含数组括号 ]
        $arrPage = array();
        array_push($arrPage, '[{"control":"hidden", "name":"menuid", "id":"menuid", "value":"'. $SelectMenuID .'", "required":true, "rule":"", "default":"", "error":"格式错误", "nulltip":"数据非法，没有指定的栏目ID。", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"system_category", "name":"parentid", "id":"parentid", "title":"所属栏目", "tips":"", "value":"", "icon":false, "inside_text":"", "required":true, "rule":"", "error":"格式错误", "nulltip":"选择所属栏目", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"textbox", "name":"menu_name", "id":"menu_name", "title":"栏目名称", "tips":"", "value":"", "icon":false, "inside_text":"", "required":true, "rule":"", "error":"格式错误", "nulltip":"请输入栏目名称", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"textbox", "name":"url", "id":"url", "title":"栏目链接", "tips":"控制器名/操作名", "value":"", "icon":false, "inside_text":"", "css":"", "required":true, "rule":"", "error":"格式错误", "nulltip":"请输入栏目链接", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"radio", "name":"status", "id":"status", "title":"栏目状态", "tips":"", "value":"0", "icon":false, "inside_text":"", "css":"last", "required":true, "rule":"", "error":"格式错误", "nulltip":"请输入栏目链接", "minlen":0, "maxlen":0, "option":[{"key":"正常", "val":"0"},{"key":"禁用", "val":"1"}]}]');
        $sJsonConfig = join(",", $arrPage);

        $arrPageConfig = json_decode($sJsonConfig, true);
        //页面数据配置 End
        
        $nCount = sizeof($arrPageConfig);
        
        for($i=0; $i<$nCount; $i++){
            $sKey = $arrPageConfig[$i]["id"];
            $arrPageConfig[$i]["value"] = $MenuInfo[$sKey];
        }

        $this->displayCategoryAdd($arrPageConfig);
    }
    
    //删除栏目
    public function del(){
        $sIds = param("delid");
        $DelData = explode(',', $sIds);

        $return = $this->SetMenuData("exec_system_category_del",$DelData);
            
        if($return){
            SuccessInfo("栏目删除成功");
        }else{
            ErrorInfo("栏目删除失败");
        }
    }
    
    //格式化缓存的系统栏目
    private function SetSystemMenuArrayItem($_arr){        
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

