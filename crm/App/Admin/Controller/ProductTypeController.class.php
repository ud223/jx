<?php
namespace Admin\Controller;
use Think\Controller;

class ProductTypeController extends CommonController {
    //页面模型
    private $PageModel = "ProductType";
    
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
        
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        
        $model = M("system_category");
        if(!empty($MenuSubID)){
            $sTitleWhere = "`menuid`={$MenuSubID}";
            $FixedParam = "navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        }else{
            $sTitleWhere = "`menuid`={$MenuMainID}";
            $FixedParam = "navmid/{$MenuMainID}";
        }
        
        $MenuInfo = $model->field("menu_name")->where($sTitleWhere)->find();
        
        $this->_PageTitle = "{$MenuInfo["menu_name"]}列表";
        
        //添加列表数据的url
        $this->_PageListAddUrl = __MODULE__ . "/ProductType/add/{$FixedParam}";
        
        //修改列表数据的url
        $this->_PageListEditUrl = __MODULE__ . "/ProductType/edit/{$FixedParam}";

        //删除列表数据的url
        $this->_PageListDeleteUrl = __MODULE__ . "/ProductType/del/{$FixedParam}";
        
        //查询列表数据的url
        $this->_PageSearchUrl = __ACTION__ . "/{$FixedParam}";
        
        //自定义按钮
        $this->_PageCustomBtn = array(
            array("title"=>"搜索", "event"=>"$('#search_fir').modal('show');", "href"=>"javascript:void(0);", "icon"=>"icon-search", "css"=>"btn-warning"),
            array("title"=>"添加字典数据", "event"=>"", "href"=>$this->_PageListAddUrl, "icon"=>"icon-plus", "css"=>"btn-info"),
            array("title"=>"删除所选内容", "event"=>"btten.DeleteSelectData('{$this->_PageListDeleteUrl}');", "href"=>"javascript:void(0);", "icon"=>"icon-trash", "css"=>"btn-danger"),
        );
        
        //搜索字段配置，搜索配置获取后，可根据需要进行参数微调
        $arrSearchConfig = GetListPageSearchFieldConfig($this->PageModel, $arrSearchData);

        //列表页字段配置，配置获取后，可根据需要进行参数微调
        $listData["field"] = GetListPageFieldConfig($this->PageModel);
        
        //获取查询语句中的表名、join语句、查询字段
        $arrSqlInfo = GetListPageData($this->PageModel, $listData["field"]);
        
        if(!empty($sListWhere)){
            $sWhere .= " and {$sListWhere}";
        }
        
        $sOrder = C("DB_PREFIX") . "product_type.`id` desc";
        
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
        $ProductTypeList = $Paging->GetPagingData();
        //分页读取数据库数据 End


        $listData["data"] = $ProductTypeList;
        $listData["search"] = $arrSearchConfig;
        
        $this->displayNewsList($listData);
    }
    
    public function add(){
        //保存提交的数据 Begin
        if($_POST){
            $nAddTime = time();
            
            $AddData = array(
                "typename"=> param("typename"),
                "addtime"=> $nAddTime,
                "modifytime"=> $nAddTime,
                "admid"=>  get_s_id(),
            );
            
            $addReturn = $this->SetData("product_type", $AddData);
            
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
        }else{
            $FixedParam = "navmid/{$MenuMainID}";
        }
        
        $model = M("system_category");
        if(!empty($MenuSubID)){
            $sTitleWhere = "`menuid`={$MenuSubID}";
            $DataMenuID = $MenuSubID;
        }else{
            $sTitleWhere = "`menuid`={$MenuMainID}";
            $DataMenuID = $MenuMainID;
        }
        
        $MenuInfo = $model->field("menu_name")->where($sTitleWhere)->find();
        
        $this->_PageTitle = "添加{$MenuInfo["menu_name"]}数据";
        
        $this->_PostUrl = __MODULE__ . "/ProductType/add/{$FixedParam}";
        $this->_PostBackUrl = __MODULE__ . "/ProductType/index/{$FixedParam}";

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
                "id"=> param("id"),
                "typename"=> param("typename"),
                "modifytime"=> $nAddTime,
                "admid"=>  get_s_id(),
            );
            
            $addReturn = $this->SetData("product_type", $AddData, "id");
            
            if($addReturn >= 0){
                SuccessInfo("保存成功");
            }else{
                ErrorInfo("保存失败");
            }
        }
        //保存提交的数据 End
        
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        $nTypeID = param("selectid");

        if(!empty($MenuSubID)){
            $FixedParam = "navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        }else{
            $FixedParam = "navmid/{$MenuMainID}";
        }
        
        $model = M("system_category");
        if(!empty($MenuSubID)){
            $sTitleWhere = "`menuid`={$MenuSubID}";
            $DataMenuID = $MenuSubID;
        }else{
            $sTitleWhere = "`menuid`={$MenuMainID}";
            $DataMenuID = $MenuMainID;
        }
        
        $MenuInfo = $model->field("menu_name")->where($sTitleWhere)->find();
        
        $this->_PageTitle = "添加{$MenuInfo["menu_name"]}数据";
        
        $this->_PostUrl = __MODULE__ . "/ProductType/edit/{$FixedParam}";
        $this->_PostBackUrl = __MODULE__ . "/ProductType/index/{$FixedParam}";


        //自定义按钮
        $this->_PageCustomBtn = array(
            array("id"=>"btnSave", "title"=>"保存", "event"=>"checkdata();", "icon"=>"icon-ok", "css"=>"btn-info"),
            array("title"=>"取消", "event"=>"history.go(-1);", "href"=>"javascript:void(0);", "icon"=>"", "css"=>"btn-default"),
        );
        
        $model = M("product_type");
        $NewsInfo = $model->where("id={$nTypeID}")->find();
        
        //页面数据配置 Begin
        $arrPageConfig = GetSetPageFieldConfig($this->PageModel, false);
        
        $nCount = sizeof($arrPageConfig);
        
        for($i=0; $i<$nCount; $i++){
            $sKey = $arrPageConfig[$i]["id"];
            $arrPageConfig[$i]["value"] = $NewsInfo[$sKey];
        }
        
        $this->displayNewsSet($arrPageConfig);
    }
    
    public function del(){
        $sIds = param("delid");
        $this->DelFromID($sIds, "product_type", "id");
    }
}

