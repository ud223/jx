<?php
namespace Admin\Controller;
use Think\Controller;

class ModelController extends CommonController {
    //列表类模型数据列表
    public function list_model(){
        $this->_PageTitle = "模型列表";
        
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        
        $FixedParam = "navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        
        //添加列表数据的url
        $this->_PageListAddUrl = __MODULE__ . "/Model/add_list/{$FixedParam}";
        
        //修改列表数据的url
        $this->_PageListEditUrl = __MODULE__ . "/Model/edit_list/{$FixedParam}";

        //删除列表数据的url
        $this->_PageListDeleteUrl = __MODULE__ . "/Model/del_list/{$FixedParam}";

        
        //自定义按钮
        $this->_PageCustomBtn = array(
            array("title"=>"添加模型", "event"=>"", "href"=>$this->_PageListAddUrl, "icon"=>"icon-plus", "css"=>"btn-info"),
            array("title"=>"删除所选模型", "event"=>"btten.DeleteSelectData('{$this->_PageListDeleteUrl}');", "href"=>"javascript:void(0);", "icon"=>"icon-trash", "css"=>"btn-danger"),
        );
            
        $listData["field"] = array();
        
        //页面列表字段配置 Begin
        //列表字段标题，必须包含主表的数据ID，用作配置多选框值，删除修改的参数
        array_push($listData["field"], array("key"=>"mdlid","val"=>"编号", css=>'style="width:60px; text-align:center;"'));
        array_push($listData["field"], array("key"=>"mdlname","val"=>"模型名称"));
        array_push($listData["field"], array("key"=>"intro","val"=>"模型说明"));
        array_push($listData["field"], array("key"=>"main_table","val"=>"主表名称"));
        array_push($listData["field"], array("key"=>"join_table","val"=>"连接表名称", css=>'style="width:100px; text-align:center;"'));
        array_push($listData["field"], array("key"=>"join_type","val"=>"连接方式"));
        array_push($listData["field"], array("key"=>"addtime","val"=>"添加时间", "type"=>"date"));
        array_push($listData["field"], array("key"=>"modifytime","val"=>"修改时间", "type"=>"date"));
        //页面列表字段配置 End
        
        $sWhere = "";
        $sOrder = "`mdlid` desc";
        
        //分页读取数据库数据 Begin
        $PagingCfg = array(
            'tablename' => "model",  //表名
            'field' => "`mdlid`, `mdlname`, `intro`, `main_table`, `join_table`, `join_type`, `addtime`, `modifytime`",
            'order' => $sOrder,  //排序字段
            'pageindex' => param('pageNum'),
            'recordnum' => param('numPerPage'),
            'url'=>__ACTION__ . "/" . $FixedParam,
            'param'=>$arrSearchData,
        );
        
        $Paging = new \Btten\Paging($PagingCfg);
        $NewsList = $Paging->GetPagingData();
        //分页读取数据库数据 End

        $this->assign("FixedParam", $FixedParam);
        
        //页面标题
        $this->assign("pagetitle", $this->_PageTitle);
        
        //页面按钮
        $this->assign("BtnCustom", $this->_PageCustomBtn);
        
        //列表页数据修改Url
        $this->assign("PageListAddUrl", $this->_PageListAddUrl);
        
        //列表页数据修改Url
        $this->assign("PageListEditUrl", $this->_PageListEditUrl);
        
        //列表页数据删除Url
        $this->assign("PageListDeleteUrl", $this->_PageListDeleteUrl);
        
        //列表页数据删除Url
        $this->assign("PageSearchUrl", $this->_PageSearchUrl);
        
        $listData["data"] = $NewsList;
        
        //列表数据
        $this->assign("listdata", $listData);

        $this->display("list");
    }
    
    //添加列表类模型
    public function add_list(){
        if($_POST){
            $AddTime = time();
            $AddData = array(
                "mdlname"=>  param("mdlname"),
                "intro"=> param("intro"),
                "join_type"=> param("join_type"),
                "main_table"=> param("main_table"),
                "join_table"=> param("join_table"),
                "join_table_case"=> param("join_table_case"),
                //"center_table"=> param("center_table"),
                "addtime"=> $AddTime,
                "modifytime"=> $AddTime,
                "admid"=>  get_s_id(),
            );

            //替换掉数据库表前缀，确保表名的正确性
            $AddData["join_table_case"] = str_replace(C("DB_PREFIX"),  "", $AddData["join_table_case"]);
            
            //将换行符替换成 ， 号分隔符
            $AddData["join_table"] = preg_replace("/[\r\n]+/", ',', $AddData["join_table"]);
            $AddData["join_table_case"] = preg_replace("/[\r\n]+/", ',', $AddData["join_table_case"]);
            
            //替换掉数据库表前缀，确保表名的正确性
            $AddData["main_table"] = str_replace(C("DB_PREFIX"),"", $AddData["main_table"]);
            $AddData["join_table"] = str_replace(C("DB_PREFIX"),"", $AddData["join_table"]);
            
            $arrJoinTable = explode(",", $AddData["join_table"]);
            $arrJoinTableCase = explode(",", $AddData["join_table_case"]);
            $nCount = sizeof($arrJoinTable);
            
            //给join条件的表名增加前缀
            for($i=0; $i<$nCount; $i++){
                $arrJoinTableCase[$i] = str_replace($arrJoinTable[$i], C("DB_PREFIX") . $arrJoinTable[$i], $arrJoinTableCase[$i]);
            }
            
            $AddData["join_table_case"] = join(",", $arrJoinTableCase);
            
            //增加join条件中，主表的前缀
            $AddData["join_table_case"] = str_replace($AddData["main_table"], C("DB_PREFIX") . $AddData["main_table"], $AddData["join_table_case"]);

            $return = $this->SetData("model", $AddData);

            if($return){
                SuccessInfo("保存成功");
            }else{
                ErrorInfo("保存失败");
            }
        }
        
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        
        $this->_PageTitle = "修改列表类模型";
        $this->_PostUrl = __MODULE__ . "/Model/add_list/navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        $this->_PostBackUrl = __MODULE__ . "/Model/list_model/navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        
        //自定义按钮
        $this->_PageCustomBtn = array(
            array("id"=>"btnSave", "title"=>"保存", "event"=>"checkdata();", "icon"=>"icon-ok", "css"=>"btn-info"),
            array("title"=>"取消", "event"=>"history.go(-1);", "href"=>"javascript:void(0);", "icon"=>"", "css"=>"btn-default"),
        );
            
        //页面数据配置 Begin
        //注意第一条中包含数组括号 [ ，最后一条包含数组括号 ]
        $arrPage = array();
        
        array_push($arrPage, '[{"control":"textbox", "name":"mdlname", "id":"mdlname", "title":"模型名称", "tips":"程序中通过模型名称来读取字段", "value":"", "icon":false, "inside_text":"", "required":true, "rule":"", "error":"格式错误", "nulltip":"", "minlen":1, "maxlen":50}');
        array_push($arrPage, '{"control":"textbox", "name":"intro", "id":"intro", "title":"模型说明", "tips":"描述此模型大致的用途", "value":"", "icon":false, "inside_text":"", "required":true, "rule":"", "error":"格式错误", "nulltip":"", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"textbox", "name":"main_table", "id":"main_table", "title":"主表名称", "tips":"Sql语句中查询的主表", "value":"", "icon":false, "inside_text":"", "required":true, "rule":"", "error":"格式错误", "nulltip":"", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"radio", "name":"join_type", "id":"join_type", "title":"连接类型", "tips":"", "value":"left join", "icon":false, "inside_text":"", "option":[{"key":"left join","val":"left join"},{"key":"right join","val":"right join"},{"key":"inner join","val":"inner join"}], "required":false, "rule":"", "error":"", "nulltip":"", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"textarea", "name":"join_table", "id":"join_table", "title":"连接表名称", "tips":"多个表用回车隔开", "value":"", "icon":false, "inside_text":"", "required":false, "rule":"", "error":"", "nulltip":"", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"textarea", "name":"join_table_case", "id":"join_table_case", "title":"join的on条件", "tips":"例如： table1.id=table2.id。多个on条件用回车隔开。条件的先后顺序，必须与连接表的顺序一致。", "value":"", "icon":false, "inside_text":"", "css":"last", "required":false, "rule":"", "error":"格式错误", "nulltip":"", "minlen":0, "maxlen":0}]');
        
        $sJsonConfig = join(",", $arrPage);
        
        $arrPageConfig = json_decode($sJsonConfig, true);
        //页面数据配置 End
        
        $this->displayNewsSet($arrPageConfig);
    }
    
    //修改列表类模型
    public function edit_list(){
        if($_POST){
            $AddData = array(
                "mdlid"=>  param("mdlid"),
                "mdlname"=>  param("mdlname"),
                "intro"=> param("intro"),
                "join_type"=> param("join_type"),
                "main_table"=> param("main_table"),
                "join_table"=> param("join_table"),
                "join_table_case"=> param("join_table_case"),
                "modifytime"=> time(),
                "admid"=>  get_s_id(),
            );
            
            //替换掉数据库表前缀，确保表名的正确性
            $AddData["join_table_case"] = str_replace(C("DB_PREFIX"),  "", $AddData["join_table_case"]);
            
            //将换行符替换成 ， 号分隔符
            $AddData["join_table"] = preg_replace("/[\r\n]+/", ',', $AddData["join_table"]);
            $AddData["join_table_case"] = preg_replace("/[\r\n]+/", ',', $AddData["join_table_case"]);
            
            //替换掉数据库表前缀，确保表名的正确性
            $AddData["main_table"] = str_replace(C("DB_PREFIX"),"", $AddData["main_table"]);
            $AddData["join_table"] = str_replace(C("DB_PREFIX"),"", $AddData["join_table"]);
            
            $arrJoinTable = explode(",", $AddData["join_table"]);
            $arrJoinTableCase = explode(",", $AddData["join_table_case"]);
            $nCount = sizeof($arrJoinTable);
            
            //给join条件的表名增加前缀
            for($i=0; $i<$nCount; $i++){
                $arrJoinTableCase[$i] = str_replace($arrJoinTable[$i], C("DB_PREFIX") . $arrJoinTable[$i], $arrJoinTableCase[$i]);
            }
            
            $AddData["join_table_case"] = join(",", $arrJoinTableCase);
            
            //增加join条件中，主表的前缀
            $AddData["join_table_case"] = str_replace($AddData["main_table"], C("DB_PREFIX") . $AddData["main_table"], $AddData["join_table_case"]);
//            exitp($AddData);
            $return = $this->SetData("model", $AddData, "mdlid");

            if($return){
                SuccessInfo("保存成功");
            }else{
                ErrorInfo("保存失败");
            }
        }
        
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        $mdlid = param("mdlid");
        
        $this->_PageTitle = "修改列表类模型";
        $this->_PostUrl = __MODULE__ . "/Model/edit_list/navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        $this->_PostBackUrl = __MODULE__ . "/Model/list_model/navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        
        //自定义按钮
        $this->_PageCustomBtn = array(
            array("id"=>"btnSave", "title"=>"保存", "event"=>"checkdata();", "icon"=>"icon-ok", "css"=>"btn-info"),
            array("title"=>"取消", "event"=>"history.go(-1);", "href"=>"javascript:void(0);", "icon"=>"", "css"=>"btn-default"),
        );
            
        //页面数据配置 Begin
        //注意第一条中包含数组括号 [ ，最后一条包含数组括号 ]
        $arrPage = array();
        
        array_push($arrPage, '[{"control":"hidden", "name":"mdlid", "id":"mdlid", "value":"", "required":true, "rule":"", "error":"格式错误", "nulltip":"数据非法，没有指定的栏目ID。", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"textbox", "name":"mdlname", "id":"mdlname", "title":"模型名称", "tips":"程序中通过模型名称来读取字段", "value":"", "icon":false, "inside_text":"", "required":true, "rule":"", "error":"格式错误", "nulltip":"", "minlen":1, "maxlen":50}');
        array_push($arrPage, '{"control":"textbox", "name":"intro", "id":"intro", "title":"模型说明", "tips":"描述此模型大致的用途", "value":"", "icon":false, "inside_text":"", "required":true, "rule":"", "error":"格式错误", "nulltip":"", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"textbox", "name":"main_table", "id":"main_table", "title":"主表名称", "tips":"Sql语句中查询的主表", "value":"", "icon":false, "inside_text":"", "required":true, "rule":"", "error":"格式错误", "nulltip":"", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"radio", "name":"join_type", "id":"join_type", "title":"连接类型", "tips":"", "value":"", "icon":false, "inside_text":"", "option":[{"key":"left join","val":"left join"},{"key":"right join","val":"right join"},{"key":"inner join","val":"inner join"}], "required":false, "rule":"", "error":"", "nulltip":"", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"textarea", "name":"join_table", "id":"join_table", "title":"连接表名称", "tips":"多个表用回车隔开", "value":"", "icon":false, "inside_text":"", "required":false, "rule":"", "error":"", "nulltip":"", "minlen":0, "maxlen":0}');
        array_push($arrPage, '{"control":"textarea", "name":"join_table_case", "id":"join_table_case", "title":"join的on条件", "tips":"例如： table1.id=table2.id。多个on条件用回车隔开。条件的先后顺序，必须与连接表的顺序一致。", "value":"", "icon":false, "inside_text":"", "css":"last", "required":false, "rule":"", "error":"格式错误", "nulltip":"", "minlen":0, "maxlen":0}]');
        
        $sJsonConfig = join(",", $arrPage);
        
        $arrPageConfig = json_decode($sJsonConfig, true);
        //页面数据配置 End
        
        $nCount = sizeof($arrPageConfig);
        
        $model = M("model");
        $ModelInfo = $model->where("mdlid={$mdlid}")->find();
        
        for($i=0; $i<$nCount; $i++){
            $sKey = $arrPageConfig[$i]["id"];
            
            if($sKey == "join_table" || $sKey == "join_table_case"){
                $arrPageConfig[$i]["value"] = str_replace(",", "\r\n", $ModelInfo[$sKey]);
            }else{
                $arrPageConfig[$i]["value"] = $ModelInfo[$sKey];
            }
        }
        
        $this->displayNewsSet($arrPageConfig);
    }
    
    //删除列表模型
    public function del_list(){
        $sIds = param("delid");
        $this->DelFromID($sIds, "model", "mdlid");
    }
    
    //列表类模型字段列表
    public function field_list(){
        if(!empty($_POST)){
            $sAct = param("act");
            
            switch ($sAct){
                //修改数据状态
                case "status" :
                    $SaveData = array(
                        "fieldid" => param("dataid"),
                        "status" => param("status"),
                    );
                    
                    if(!is_numeric($SaveData["fieldid"])){
                        ErrorInfo("数据编号错误");
                    }
                    
                    if((int)$SaveData["status"] != 0 && (int)$SaveData["status"] != 1){
                        ErrorInfo("状态错误");
                    }
                    
                    //通过绝对值，设置状态的变更
                    $SaveData["status"] = abs(((int)$SaveData["status"] - 1));
                    
                    $sReturn = $this->SetData("model_field", $SaveData, "fieldid");
                    
                    if($sReturn > 0){
                        SuccessInfo("状态修改成功");
                    }else{
                        ErrorInfo("状态修改失败");
                    }
                    
                    break;
                    
                //数据排序
                case "order" :
                    $sData = param("data");
                    $arrData = json_decode($sData, true);
                    //$model->addAll($arrData);
                    
                    $nCount = sizeof($arrData);
                    
                    for($i=0; $i<$nCount; $i++){
                        $this->SetData("model_field", $arrData[$i], "fieldid");
                    }
                    
                    SuccessInfo("状态修改成功");
                    break;
                
                //字段同步
                case "sync" :
                    //模型id
                    $nModelID = param("modelid");
                    
                    if(!is_numeric($nModelID) || ($nModelID <= 0)){
                        ErrorInfo("模型编号错误");
                    }
                    
                    //获取数据库表字段名
                    $arrDbField = $this->GetFieldListData($nModelID);
                    
                    //获取model_field表中存储的字段
                    $model = M("model_field");
                    $FieldList = $model->field("mdlid,tablename,fieldname,ispk")->where("mdlid={$nModelID}")->select();
                    
                    
                    //查找model_field表中缺少的字段 Begin
                    $nCount = sizeof($arrDbField);
                    $nModelCount = sizeof($FieldList);
                    
                    $AddData = array();
                    
                    //循环数据库表字段
                    for($i=0; $i<$nCount; $i++){
                        $DbFieldInfo = $arrDbField[$i];
                        $blHave = false;
                        
                        //循环model_field表中的字段
                        for($k=0; $k<$nModelCount; $k++){
                            $ModelFieldInfo = $FieldList[$k];
                            
                            //使用表名+字段名，来确保字段唯一
                            $sDbFieldName = "{$DbFieldInfo["tablename"]}.{$DbFieldInfo["fieldname"]}";
                            
                            //使用表名+字段名，来确保字段唯一
                            $sModelFieldName = "{$ModelFieldInfo["tablename"]}.{$ModelFieldInfo["fieldname"]}";

                            if($sDbFieldName == $sModelFieldName){
                                $blHave = true;
                                break;
                            }else{
                                $blHave = false;
                            }
                        }
                        
                        //不存在时，添加到准备写入数据库的数组中
                        if(!$blHave){
                            array_push($AddData, array("mdlid"=>$nModelID, "tablename"=>$DbFieldInfo["tablename"], "fieldname"=>$DbFieldInfo["fieldname"], "ispk"=>$DbFieldInfo["ispk"]));
                        }
                    }
                    //查找model_field表中缺少的字段 End
                    
                    if(!empty($AddData)){
                        $model = M("model_field");
                        $model->addAll($AddData);
                    }
                    
                    SuccessInfo("同步成功");

                    break;
            }
        }
        
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        $mdlid = param("mdlid");
        
        if(!is_numeric($mdlid) || ($mdlid <= 0)){
            $this->error("模型指定错误");
            exit("");
        }
        
        $FixedParam = "navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        
        $model = M("model");
        $this->_PageTitle = $model->getField("intro");
        
        //添加列表数据的url
        $this->_PageListAddUrl = __MODULE__ . "/Model/field_add/{$FixedParam}";
        
        //修改列表数据的url
        $this->_PageListEditUrl = __MODULE__ . "/Model/field_edit/{$FixedParam}";

        //删除列表数据的url
        $this->_PageListDeleteUrl = __MODULE__ . "/Model/field_del/{$FixedParam}";
        
        //自定义按钮
        $this->_PageCustomBtn = array(
            array("title"=>"排序", "event"=>"SetOrder();", "href"=>"javascript:void(0);", "icon"=>"icon-ok", "css"=>"btn-info"),
            array("title"=>"同步字段", "event"=>"SyncField({$mdlid});", "href"=>"javascript:void(0);", "icon"=>"icon-refresh", "css"=>"btn-info"),
        );
        
        $listData["field"] = array();
        
        //页面列表字段配置 Begin
        //列表字段标题，必须包含主表的数据ID，用作配置多选框值，删除修改的参数
        //array_push($listData["field"], array("key"=>"fieldid","val"=>"编号", css=>'style="width:60px; text-align:center;"'));
        array_push($listData["field"], array("key"=>"fieldname","val"=>"字段名", css=>'style="width:160px;"'));
        array_push($listData["field"], array("key"=>"tablename","val"=>"所属表", css=>'style="width:160px;"'));
        array_push($listData["field"], array("key"=>"fieldtitle","val"=>"显示标题", css=>'style="width:160px;"'));
        array_push($listData["field"], array("key"=>"mdlname","val"=>"所属模型", css=>'style="width:160px; text-align:center;"'));
        array_push($listData["field"], array("key"=>"addconfig","val"=>"使用控件", css=>'style="width:160px;"'));
        array_push($listData["field"], array("key"=>"islist_sign","val"=>"列表页字段", css=>'style="width:80px; text-align:center;"'));
        array_push($listData["field"], array("key"=>"isadd_sign","val"=>"添加页字段", css=>'style="width:80px; text-align:center;"'));
        array_push($listData["field"], array("key"=>"isedit_sign","val"=>"修改页字段", css=>'style="width:80px; text-align:center;"'));
        array_push($listData["field"], array("key"=>"issearch_sign","val"=>"搜索页字段", css=>'style="width:80px; text-align:center;"'));
        array_push($listData["field"], array("key"=>"status_sign","val"=>"状态", css=>'style="width:60px; text-align:center;"'));
        //页面列表字段配置 End
        
        $model = M("model_field as f");
        $sField = "f.*, m.mdlname";
        $sJoin = "btten_model as m on f.mdlid = m.mdlid";
        $sWhere = "f.mdlid={$mdlid}";
        $sOrderBy = "f.`order`, f.`tablename`, f.`fieldid`";
        
        //获取字段列表
        $FieldList = $model->field($sField)->join($sJoin)->where($sWhere)->order($sOrderBy)->select();
        
        //如果字段列表为空，则从数据库中重新读取表字段
        if(empty($FieldList)){
            $this->AddFieldToDb($mdlid);
            $FieldList = $model->field($sField)->join($sJoin)->where($sWhere)->order($sOrderBy)->select();
        }
        
        $nCount = sizeof($FieldList);
        
        //格式化列表数据
        for($i=0; $i<$nCount; $i++){
            $arrAddConfig = json_decode($FieldList[$i]["addconfig"], true);
            $FieldList[$i]["addconfig"] = $arrAddConfig["control"];
            
            if($FieldList[$i]["islist"] === "true"){
                $FieldList[$i]["islist_sign"] = AddLableToStringSuccess('<i class="icon-ok"></i>');
            }else{
                $FieldList[$i]["islist_sign"] = AddLableToStringError('<i class="icon-ban-circle"></i> ');
            }
            
            if($FieldList[$i]["isadd"] === "true"){
                $FieldList[$i]["isadd_sign"] = AddLableToStringSuccess('<i class="icon-ok"></i>');
            }else{
                $FieldList[$i]["isadd_sign"] = AddLableToStringError('<i class="icon-ban-circle"></i> ');
            }
            
            if($FieldList[$i]["isedit"] === "true"){
                $FieldList[$i]["isedit_sign"] = AddLableToStringSuccess('<i class="icon-ok"></i>');
            }else{
                $FieldList[$i]["isedit_sign"] = AddLableToStringError('<i class="icon-ban-circle"></i> ');
            }
            
            if($FieldList[$i]["issearch"] === "true"){
                $FieldList[$i]["issearch_sign"] = AddLableToStringSuccess('<i class="icon-ok"></i>');
            }else{
                $FieldList[$i]["issearch_sign"] = AddLableToStringError('<i class="icon-ban-circle"></i>');
            }
            
            if((int)$FieldList[$i]["status"] === 1){
                $FieldList[$i]["status_sign"] = AddLableToStringSuccess('<i class="icon-ok"></i>');
            }else{
                $FieldList[$i]["status_sign"] = AddLableToStringError('<i class="icon-ban-circle"></i>');
            }
        }
        
        //赋值页面变量 Begin
        $listData["data"] = $FieldList;
        
        //页面标题
        $this->assign("pagetitle", $this->_PageTitle);
        
        //页面按钮
        $this->assign("BtnCustom", $this->_PageCustomBtn);
        
        //列表页数据修改Url
        $this->assign("PageListAddUrl", $this->_PageListAddUrl);
        
        //列表页数据修改Url
        $this->assign("PageListEditUrl", $this->_PageListEditUrl);
        
        //列表页数据删除Url
        $this->assign("PageListDeleteUrl", $this->_PageListDeleteUrl);
        
        //列表页数据删除Url
        $this->assign("PageSearchUrl", $this->_PageSearchUrl);
        
        //列表数据
        $this->assign("listdata", $listData);
        //赋值页面变量 End
        
        $this->display();
    }
    
    public function field_del(){
        $sIds = param("delid");
        $this->DelFromID($sIds, "model_field", "fieldid");
    }
    
    public function field_edit(){
        if(!empty($_POST)){
            $sFieldPublic = param("public");    //公共字段配置
            $sFieldList = param("list");    //列表页字段配置
            $sFieldAdd = param("add");  //编辑页字段配置
            $sFieldEdit = param("edit");  //编辑页字段配置
            $sFieldSearch = param("search");    //查询页字段配置
            
            $arrData = json_decode($sFieldPublic, true);
            $arrData["listconfig"] = $sFieldList;
                    
            if(!IsN($sFieldAdd)){
                $sFieldAdd = preg_replace("/[\r\n]+/", ';', $sFieldAdd);    //替换回车换行符
                $arrData["addconfig"] = $sFieldAdd;
            }
            
            if(!IsN($sFieldEdit)){
                $sFieldEdit = preg_replace("/[\r\n]+/", ';', $sFieldEdit);    //替换回车换行符
                $arrData["editconfig"] = $sFieldEdit;
            }
            
            if(!IsN($sFieldSearch)){
                $sFieldSearch = preg_replace("/[\r\n]+/", ';', $sFieldSearch);    //替换回车换行符
                $arrData["searchconfig"] = $sFieldSearch;
            }
            
            $return = $this->SetData("model_field", $arrData, "fieldid");
            
            if($return){
                SuccessInfo("保存成功");
            }else{
                ErrorInfo("保存失败");
            }
        }
        
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        $nFieldID = param("selectid");
        
        $this->_PageTitle = "配置字段属性";
        $this->_PostUrl = __MODULE__ . "/Model/field_edit/navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        $this->_PostBackUrl = __MODULE__ . "/Model/field_list/navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        
        //自定义按钮
        $this->_PageCustomBtn = array(
            array("id"=>"btnSave", "title"=>"保存", "event"=>"checkdata();", "icon"=>"icon-ok", "css"=>"btn-info"),
            array("title"=>"取消", "event"=>"history.go(-1);", "href"=>"javascript:void(0);", "icon"=>"", "css"=>"btn-default"),
        );
        
        $model = M("model_field");
        $arrFieldInfo = $model->where("fieldid={$nFieldID}")->find();

        //列表页配置 
        $arrFieldInfo["listconfig"] = json_decode($arrFieldInfo["listconfig"], true);
        
        foreach ($arrFieldInfo["listconfig"]["extend"] as $key => $value) {
            $arrFieldInfo["listconfig"]["extend"][$key] = str_replace(";", "\\r\\n", $value);
        }
        
        //添加页配置
        $arrFieldInfo["addconfig"] = json_decode($arrFieldInfo["addconfig"], true);
        $arrFieldInfo["addconfig"]["event"] = str_replace('"', '\\"', $arrFieldInfo["addconfig"]["event"]);
        
        foreach ($arrFieldInfo["addconfig"]["extend"] as $key => $value) {
            $arrFieldInfo["addconfig"]["extend"][$key] = str_replace(";", "\\r\\n", $value);
        }
        
        //修改页配置
        $arrFieldInfo["editconfig"] = json_decode($arrFieldInfo["editconfig"], true);
        $arrFieldInfo["editconfig"]["event"] = str_replace('"', '\\"', $arrFieldInfo["editconfig"]["event"]);
        
        foreach ($arrFieldInfo["editconfig"]["extend"] as $key => $value) {
            $arrFieldInfo["editconfig"]["extend"][$key] = str_replace(";", "\\r\\n", $value);
        }

        //查询页配置
        $arrFieldInfo["searchconfig"] = json_decode($arrFieldInfo["searchconfig"], true);
            
        foreach ($arrFieldInfo["searchconfig"]["extend"] as $key => $value) {
            $arrFieldInfo["searchconfig"]["extend"][$key] = str_replace(";", "\\r\\n", $value);
        }
        
        //页面标题
        $this->assign("pagetitle", $this->_PageTitle);
        
        //页面按钮
        $this->assign("BtnCustom", $this->_PageCustomBtn);
        
        //表单提交按钮
        $this->assign("PostUrl", $this->_PostUrl);
        
        //表单提交成功后的返回地址
        $this->assign("PostBackUrl", $this->_PostBackUrl);
//        exitp($arrFieldInfo);
        $this->assign("datainfo", $arrFieldInfo);
        $this->display();
    }

    //获取表字段
    private function GetFieldListData($_mdlid){
        $model = M("model");
        $ModelInfo = $model->where("mdlid={$_mdlid}")->find();

        $arrField = array();
        
        $arrField = $this->AddFieldToArray($arrField, $ModelInfo["main_table"], "main");
        
        if(!empty($ModelInfo["center_table"])){
            $arrField = $this->AddFieldToArray($arrField, $ModelInfo["center_table"], "center");
        }
        
        if(!empty($ModelInfo["join_table"])){
            $arrField = $this->AddFieldToArray($arrField, $ModelInfo["join_table"], "join");
        }
        
        $nCount = sizeof($arrField);
        
        $arrReturn = array();
        
        for($i=0; $i<$nCount; $i++){
            array_push($arrReturn, array("mdlid"=>$_mdlid, "tablename"=>$arrField[$i]["table"], "fieldname"=>$arrField[$i]["filed"], "ispk"=>$arrField[$i]["pk"]));
        }
        
        return $arrReturn;
    }

    //将字段信息添加到model_field表中
    private function AddFieldToDb($_mdlid){
        $addData = $this->GetFieldListData($_mdlid);
        
        $model = M("model_field");
        $model->addAll($addData);
    }
    
    //添加字段信息到字段数组中
    private function AddFieldToArray($_arrMain, $_table, $_type){
        if(empty($_arrMain)){
            $_arrMain = array();
        }
        
        $model = M($_table);
        $arrJoin = $model->getDbFields();
        $sPk = $model->getPk();
        
        $nCount = sizeof($arrJoin);
            
        for($i=0; $i<$nCount; $i++){
            if($sPk == $arrJoin[$i]){
                $isPK = true;
            }else{
                $isPK = false;
            }
            
            array_push($_arrMain, array("table"=>$_table, "filed"=>$arrJoin[$i], "pk"=>$isPK, "tabletype"=>$_type));
        }
        
        return $_arrMain;
    }
}

