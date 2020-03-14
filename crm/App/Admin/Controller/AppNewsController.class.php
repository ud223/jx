<?php
namespace Admin\Controller;
use Think\Controller;

class AppNewsController extends CommonController {
    //页面模型
    private $PageModel = "AppNews";
    
    public function index(){
        //获取查询表单提交的参数，并格式化成Sql查询条件 Begin
        $sAct = param("act");
        $sWhere = "1=1";
        $arrSearchData = array();
        
        if(!empty($sAct)){
            if($sAct == "search"){
                $arrSearchResult = GetSearchSql($_POST, $this->PageModel);
                
                $arrWhere = $arrSearchResult["where"];
                $arrSearchData = $arrSearchResult["searchvalue"];
                
                if(!empty($arrWhere)){
                    $sWhere = join(" and ",$arrWhere);
                } 
            }
        }
        //获取查询表单提交的参数，并格式化成Sql查询条件 End
        
        $MenuMainID = param("navappmid");
        $MenuSubID = param("navappsid");

        $model = M("app_menu");
        if(!empty($MenuSubID)){
            $sTitleWhere = "`menuid`={$MenuSubID}";
            $sListWhere = "btten_app_news.`menuid`={$MenuSubID}";
        }else{
            $sTitleWhere = "`menuid`={$MenuMainID}";
            $sListWhere = "btten_app_news.`menuid`={$MenuMainID}";
        }
        
        $MenuInfo = $model->field("menu_name")->where($sTitleWhere)->find();
        
        $this->_PageTitle = "{$MenuInfo["menu_name"]}内容列表";
        
        if(!empty($MenuSubID)){
            $FixedParam = "navappmid/{$MenuMainID}/navappsid/{$MenuSubID}";
        }else{
            $FixedParam = "navappmid/{$MenuMainID}";
        }
        
        //添加列表数据的url
        $this->_PageListAddUrl = __MODULE__ . "/AppNews/add/{$FixedParam}";
        
        //修改列表数据的url
        $this->_PageListEditUrl = __MODULE__ . "/AppNews/edit/{$FixedParam}";

        //删除列表数据的url
        $this->_PageListDeleteUrl = __MODULE__ . "/AppNews/del/{$FixedParam}";
        
        //查询列表数据的url
        $this->_PageSearchUrl = __ACTION__ . "/{$FixedParam}";
        
        //自定义按钮
        $this->_PageCustomBtn = array(
            array("title"=>"搜索", "event"=>"$('#search_fir').modal('show');", "href"=>"javascript:void(0);", "icon"=>"icon-search", "css"=>"btn-warning"),
            array("title"=>"添加内容", "event"=>"", "href"=>$this->_PageListAddUrl, "icon"=>"icon-plus", "css"=>"btn-info"),
            array("title"=>"删除所选内容", "event"=>"btten.DeleteSelectData('{$this->_PageListDeleteUrl}');", "href"=>"javascript:void(0);", "icon"=>"icon-trash", "css"=>"btn-danger"),
        );
            
        //搜索字段配置，搜索配置获取后，可根据需要进行参数微调
        $arrSearchConfig = GetListPageSearchFieldConfig($this->PageModel, $arrSearchData);

        //列表页字段配置，配置获取后，可根据需要进行参数微调
        $listData["field"] = GetListPageFieldConfig($this->PageModel);
        
        //获取查询语句中的表名、join语句、查询字段
        $arrSqlInfo = GetListPageData($this->PageModel, $listData["field"]);
        
        $sWhere .= " and {$sListWhere}";
        
        $sOrder = "btten_app_news.`newsid` desc";
        
        //分页读取数据库数据 Begin
        $PagingCfg = array(
            'pagemodel' => $this->PageModel,  //表名
            'tablename' => $arrSqlInfo["maintable"],  //表名
            'field' => $arrSqlInfo["field"],
            'jointable' => $arrSqlInfo["join"],
            'where' =>$sWhere,
            'order' => $sOrder,  //排序字段
            'pageindex' => param('pageNum'),
            'recordnum' => param('numPerPage'),
            'url'=>__ACTION__ . "/" . $FixedParam,
            'param'=>$arrSearchData,
        );
        
        $Paging = new \Btten\Paging($PagingCfg);
        $NewsList = $Paging->GetPagingData();
        //分页读取数据库数据 End
        
        //格式化显示数据 Begin
        $nCount = sizeof($NewsList["data"]);

        for($i=0; $i<$nCount; $i++){
            $NewsInfo = $NewsList["data"][$i];

            if($NewsInfo["status"] == 0){
                $NewsInfo["status"] = AddLableToStringSuccess('<i class="icon-ok"></i>');
            }else{
                $NewsInfo["status"] = AddLableToStringError('<i class="icon-ban-circle"></i> ');
            }

            $NewsInfo["addtime"] = date('Y-m-d', $NewsInfo["addtime"]);
            $NewsInfo["modifytime"] = date('Y-m-d', $NewsInfo["modifytime"]);
            
            $NewsList["data"][$i] = $NewsInfo;
        }
        //格式化显示数据 End

        $listData["data"] = $NewsList;
        $listData["search"] = $arrSearchConfig;
        
        $this->displayNewsList($listData);
    }
    
    //添加栏目
    public function add(){
        //保存提交的数据 Begin
        if($_POST){
            $nAddTime = time();
            
            $AddData = array(
                "menuid"=>  param("menuid"),
                "title"=> param("title"),
                "intro"=> param("intro"),
                "content"=> param("content"),
                "status"=> param("status"),
                "admid"=>  get_s_id(),
                "addtime"=> $nAddTime,
                "modifytime"=> $nAddTime,
            );
            
            $addReturn = $this->SetData("app_news", $AddData);
            
            if($addReturn >= 0){
                SuccessInfo("保存成功");
            }else{
                ErrorInfo("保存失败");
            }
        }
        //保存提交的数据 End
        
        $MenuMainID = param("navappmid");
        $MenuSubID = param("navappsid");
        
        if(!empty($MenuSubID)){
            $FixedParam = "navappmid/{$MenuMainID}/navappsid/{$MenuSubID}";
        }else{
            $FixedParam = "navappmid/{$MenuMainID}";
        }
        
        $model = M("app_menu");
        if(!empty($MenuSubID)){
            $sTitleWhere = "`menuid`={$MenuSubID}";
            $DataMenuID = $MenuSubID;
        }else{
            $sTitleWhere = "`menuid`={$MenuMainID}";
            $DataMenuID = $MenuMainID;
        }
        
        $MenuInfo = $model->field("menu_name")->where($sTitleWhere)->find();
        
        $this->_PageTitle = "添加{$MenuInfo["menu_name"]}内容";
        
        $this->_PostUrl = __MODULE__ . "/AppNews/add/{$FixedParam}";
        $this->_PostBackUrl = __MODULE__ . "/AppNews/index/{$FixedParam}";

        //自定义按钮
        $this->_PageCustomBtn = array(
            array("id"=>"btnSave", "title"=>"保存", "event"=>"checkdata();", "icon"=>"icon-ok", "css"=>"btn-info"),
            array("title"=>"取消", "event"=>"history.go(-1);", "href"=>"javascript:void(0);", "icon"=>"", "css"=>"btn-default"),
        );
        
        //页面数据配置 Begin
        $arrPageConfig = GetSetPageFieldConfig($this->PageModel);
        
        $nCount = sizeof($arrPageConfig);
        
        for($i=0; $i<$nCount; $i++){
            if($arrPageConfig[$i]["id"] == "menuid"){
                $arrPageConfig[$i]["value"] = $DataMenuID;
                break;
            }
        }
        
        $this->displayNewsSet($arrPageConfig);
    }
    
    public function edit(){
        //保存提交的数据 Begin
        if($_POST){
            $nAddTime = time();

            $AddData = array(
                "newsid"=> param("newsid"),
                "menuid"=>  param("menuid"),
                "title"=> param("title"),
                "intro"=> param("intro"),
                "content"=> param("content"),
                "status"=> param("status"),
                "admid"=>  get_s_id(),
                "modifytime"=> $nAddTime,
            );
            
            $addReturn = $this->SetData("app_news", $AddData, "newsid");
            
            if($addReturn >= 0){
                SuccessInfo("保存成功");
            }else{
                ErrorInfo("保存失败");
            }
        }
        //保存提交的数据 End
        
        $MenuMainID = param("navappmid");
        $MenuSubID = param("navappsid");
        $nNewsID = param("selectid");

        if(!empty($MenuSubID)){
            $FixedParam = "navappmid/{$MenuMainID}/navappsid/{$MenuSubID}";
        }else{
            $FixedParam = "navappmid/{$MenuMainID}";
        }
        
        $model = M("app_menu");
        if(!empty($MenuSubID)){
            $sTitleWhere = "`menuid`={$MenuSubID}";
            $DataMenuID = $MenuSubID;
        }else{
            $sTitleWhere = "`menuid`={$MenuMainID}";
            $DataMenuID = $MenuMainID;
        }
        
        $MenuInfo = $model->field("menu_name")->where($sTitleWhere)->find();
        
        $this->_PageTitle = "添加{$MenuInfo["menu_name"]}内容";
        
        $this->_PostUrl = __MODULE__ . "/AppNews/edit/{$FixedParam}";
        $this->_PostBackUrl = __MODULE__ . "/AppNews/index/{$FixedParam}";

        //自定义按钮
        $this->_PageCustomBtn = array(
            array("id"=>"btnSave", "title"=>"保存", "event"=>"checkdata();", "icon"=>"icon-ok", "css"=>"btn-info"),
            array("title"=>"取消", "event"=>"history.go(-1);", "href"=>"javascript:void(0);", "icon"=>"", "css"=>"btn-default"),
        );
        
        $model = M("app_news");
        $NewsInfo = $model->where("newsid={$nNewsID}")->find();
        
        //页面数据配置 Begin
        $arrPageConfig = GetSetPageFieldConfig($this->PageModel, false);
        
        $nCount = sizeof($arrPageConfig);
        
        for($i=0; $i<$nCount; $i++){
            $sKey = $arrPageConfig[$i]["id"];
            $arrPageConfig[$i]["value"] = $NewsInfo[$sKey];
        }
        
        $this->displayNewsSet($arrPageConfig);
    }
    
    //删除栏目
    public function del(){
        $sIds = param("delid");
        $this->DelFromID($sIds, "app_news", "newsid");
    }
}

