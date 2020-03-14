<?php
namespace Admin\Controller;
use Think\Controller;

class AdminController extends CommonController {
    //页面模型
    private $PageModel = "AdminUser";

    public function user(){
        //获取查询表单提交的参数，并格式化成Sql查询条件 Begin
        $sAct = param("act");
        $sWhere = "";
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
        
        $this->_PageTitle = "管理员列表";
        
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        
        $FixedParam = "navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        
        //添加列表数据的url
        $this->_PageListAddUrl = __MODULE__ . "/Admin/user_add/{$FixedParam}";
        
        //修改列表数据的url
        $this->_PageListEditUrl = __MODULE__ . "/Admin/user_edit/{$FixedParam}";

        //删除列表数据的url
        $this->_PageListDeleteUrl = __MODULE__ . "/Admin/user_del/{$FixedParam}";
        
        //查询列表数据的url
        $this->_PageSearchUrl = __ACTION__ . "/{$FixedParam}";
        
        //自定义按钮
        $this->_PageCustomBtn = array(
            array("title"=>"搜索", "event"=>"$('#search_fir').modal('show');", "href"=>"javascript:void(0);", "icon"=>"icon-search", "css"=>"btn-warning"),
            array("title"=>"添加管理员", "event"=>"", "href"=>$this->_PageListAddUrl, "icon"=>"icon-plus", "css"=>"btn-info"),
            array("title"=>"删除所选管理员", "event"=>"btten.DeleteSelectData('{$this->_PageListDeleteUrl}');", "href"=>"javascript:void(0);", "icon"=>"icon-trash", "css"=>"btn-danger"),
        );
            
        //搜索字段配置，搜索配置获取后，可根据需要进行参数微调
        $arrSearchConfig = GetListPageSearchFieldConfig($this->PageModel, $arrSearchData);

        //列表页字段配置，配置获取后，可根据需要进行参数微调
        $listData["field"] = GetListPageFieldConfig($this->PageModel);

        //获取查询语句中的表名、join语句、查询字段
        $arrSqlInfo = GetListPageData($this->PageModel, $listData["field"]);
        
        $sOrder = "btten_auth_admin.`id` desc";
        
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
            
            if($NewsInfo["status"] == 1){
                $NewsInfo["status"] = AddLableToStringSuccess('<i class="icon-ok"></i>');
            }else{
                $NewsInfo["status"] = AddLableToStringError('<i class="icon-ban-circle"></i> ');
            }
            
            $NewsInfo["headimg"] = FmtDbImgJson($NewsInfo["headimg"]);

            if($NewsInfo["logintime"] == 0){
                $NewsInfo["logintime"] = '';
            }else{
                $NewsInfo["logintime"] = date('Y-m-d', $NewsInfo["logintime"]);
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
    
    //修改管理员账号信息
    public function user_edit(){        
        //保存提交的数据 Begin
        if($_POST){
            $sPwd = param("pwd");
            
            $AddAdmData = array(
                "id"=>  param("id"),
                "username"=>  param("username"),
                "headimg"=> param("headimg"),
                "truename"=> param("truename"),
                "province"=> param("province"),
                "city"=> param("city"),
                "area"=> param("area"),
                "phone"=> param("phone"),
                "email"=> param("email"),
                "qq"=> param("qq"),
                "status"=> param("status"),
                "admid"=>  get_s_id(),
                "modifytime"=> time(),
            );
            
            $model = M("auth_admin");
            $ExistAdm = $model->where("id!={$AddAdmData["id"]} and username = '{$AddAdmData["username"]}'")->select();
            
            if(!empty($ExistAdm)){
                ErrorInfo("修改的管理员账号已经存在，请重新设定账号名称！");
            }

            if(!IsN($sPwd)){
                $sPwd = md5($sPwd);
                $AddAdmData["pwd"] = $sPwd;
            }

            $AdmReturn = $this->SetData("auth_admin", $AddAdmData, "id");
            
            if(!$AdmReturn){
                ErrorInfo("管理员信息保存失败");
            }
            
            $AddGroupData = array(
                "uid"=>$AddAdmData["id"],
                "group_id"=>param("group_id")
            );
            
            $GroupReturn = $this->SetData("auth_group_access", $AddGroupData, "uid", "uid={$AddGroupData["uid"]}");
            
            if ($GroupReturn !== false) {
                SuccessInfo("保存成功");
            }else{
                ErrorInfo("管理员所属组保存失败");
            }
        }
        //保存提交的数据 End
        
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        $nAdmID = param("selectid");
        
        $this->_PageTitle = "修改管理员信息";
        
        $FixedParam = "navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        $this->_PostUrl = __MODULE__ . "/Admin/user_edit/{$FixedParam}";
        $this->_PostBackUrl = __MODULE__ . "/Admin/user/{$FixedParam}";

        //自定义按钮
        $this->_PageCustomBtn = array(
            array("id"=>"btnSave", "title"=>"保存", "event"=>"checkdata();", "icon"=>"icon-ok", "css"=>"btn-info"),
            array("title"=>"取消", "event"=>"history.go(-1);", "href"=>"javascript:void(0);", "icon"=>"", "css"=>"btn-default"),
        );
        
        //页面字段配置 Begin
        $arrPageConfig = GetSetPageFieldConfig($this->PageModel, false);
    
        $nCount = sizeof($arrPageConfig);
        
        //微调配置，修改时，密码为非必填项
        for($i=0; $i<$nCount; $i++){
            if($arrPageConfig[$i]["name"] == 'pwd'){
                $arrPageConfig[$i]["required"] = 'false';
            }
        }
        //页面字段配置 End

        //数据赋值 Begin
        $model = M("auth_admin as a");
        $NewsInfo = $model->field("a.*, g.id as `group_id`")->join("left join btten_auth_group_access as ga on a.id = ga.uid left join btten_auth_group as g on g.id = ga.group_id")->where("a.id={$nAdmID}")->find();

        $nCount = sizeof($arrPageConfig);
        
        for($i=0; $i<$nCount; $i++){
            $sKey = $arrPageConfig[$i]["id"];
            
            //不对密码进行赋值
            if($sKey != "pwd"){
                $arrPageConfig[$i]["value"] = $NewsInfo[$sKey];
            }
        }
        //数据赋值 End
        
        $this->displayNewsSet($arrPageConfig);
    }

    public function user_add(){
        //保存提交的数据 Begin
        if($_POST){
            $sPwd = param("pwd");
            
            $AddAdmData = array(
                "username"=>  param("username"),
                "headimg"=> param("headimg"),
                "truename"=> param("truename"),
                "phone"=> param("phone"),
                "email"=> param("email"),
                "qq"=> param("qq"),
                "status"=> param("status"),
                "admid"=>  get_s_id(),
                "modifytime"=> time(),
            );
            
            $model = M("auth_admin");
            $ExistAdm = $model->where("id!={$AddAdmData["id"]} and username = '{$AddAdmData["username"]}'")->select();
            
            if(!empty($ExistAdm)){
                ErrorInfo("修改的管理员账号已经存在，请重新设定账号名称！");
            }

            if(IsN($sPwd)){
                ErrorInfo("请输入登陆密码");
            }else{
                $sPwd = md5($sPwd);
                $AddAdmData["pwd"] = $sPwd;
            }

            $AdmReturn = $this->SetData("auth_admin", $AddAdmData);
            
            if(!$AdmReturn){
                ErrorInfo("管理员信息保存失败");
            }
            
            $AddGroupData = array(
                "uid"=>$AdmReturn,
                "group_id"=>param("group_id")
            );
            
            $GroupReturn = $this->SetData("auth_group_access", $AddGroupData);

            if($GroupReturn){
                SuccessInfo("保存成功");
            }else{
                ErrorInfo("管理员所属组保存失败");
            }
        }
        //保存提交的数据 End
        
        $MenuMainID = param("navmid");
        $MenuSubID = param("navsid");
        
        $FixedParam = "navmid/{$MenuMainID}/navsid/{$MenuSubID}";
        
        $this->_PageTitle = "添加管理员";
        $this->_PostUrl = __MODULE__ . "/Admin/user_add/{$FixedParam}";
        $this->_PostBackUrl = __MODULE__ . "/Admin/user/{$FixedParam}";

        
        //自定义按钮
        $this->_PageCustomBtn = array(
            array("id"=>"btnSave", "title"=>"保存", "event"=>"checkdata();", "icon"=>"icon-ok", "css"=>"btn-info"),
            array("title"=>"取消", "event"=>"history.go(-1);", "href"=>"javascript:void(0);", "icon"=>"", "css"=>"btn-default"),
        );
        
        //页面数据配置 Begin
        $arrPageConfig = GetSetPageFieldConfig($this->PageModel);
        
        $this->displayNewsSet($arrPageConfig);
    }
    
    //删除管理员
    public function user_del(){
        $sIds = param("delid");
        $this->DelFromIDReturn($sIds, "auth_admin", "id");
        $this->DelFromID($sIds, "auth_group_access", "uid");
    }
    
    public function test(){
        
        $model = M("auth_admin");
        $AdmList = $model->field("`id`,`username`")->select();
//        exitp($AdmList);
        $this->assign("list",$AdmList);
        $this->assign("cntid",  param("cntid"));
        $this->display();
    }
}

