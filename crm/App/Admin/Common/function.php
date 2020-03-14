<?php
//返回完成json
function SuccessReturn($info){
    $result = array();
    $result['status'] = true;
    $result['msg'] = $info;

    exit(json_encode($result));
}

//返回失败json
function ErrorReturn($info){
    $result = array();
    $result['status'] = false;
    $result['msg'] = $info;

    exit(json_encode($result));
}

//获取登录的管理员ID
function get_s_id(){
    return session('AdmUserID');
}

//获取登录的管理员账号
function get_s_name(){
    return session('AdmName');
}

//获取登录的管理员组ID
function get_s_gid(){
    return session('AdmGroupID');
}

//设置登录的管理员头像
function get_s_headimg(){
    return session('HeadImage');
}

//获取上次登录的ip
function get_s_ip(){
    return session('AdmIP');
}

//获取上次登录的时间
function get_s_date(){
    return session('AdmDate');
}

//获取登录的管理员组组名称
function get_s_gname(){
    return session('AdmGroupName');
}

//获取权限标识
function get_s_auth(){
    return session(C('AUTH_CONFIG.USER_AUTH_KEY'));
}

//获取登录的管理员ID
function set_s_id($str){
    session('AdmUserID',$str);
}

//设置登录的管理员账号
function set_s_name($str){
    session('AdmName',$str);
}

//设置登录的管理员组ID
function set_s_gid($str){
    session('AdmGroupID',$str);
}

//设置登录的管理员组名称
function set_s_gname($str){
    session('AdmGroupName',$str);
}

//设置登录的管理员头像
function set_s_headimg($str){
    session('HeadImage',$str);
}

//设置上次登录的ip
function set_s_ip($str){
    session('AdmIP',$str);
}

//设置上次登录的时间
function set_s_date($str){
    session('AdmDate',$str);
}

//设置权限标识
function set_s_auth($str){
    session(C('AUTH_CONFIG.USER_AUTH_KEY'),$str);
}

//获取图片上传配置信息
function GetImageConfig(){
    return C('UPLOAD_IMAGE');
}

//获取文件上传配置信息
function GetFileConfig(){
    return C('UPLOAD_FIEL');
}

/*
 * 从数据库读取菜单信息，并设置缓存。缓存的是数组
*/
function SyncSystemMenu(){
    $model = M("system_category");
    $MenuList = $model->where("`left_val` > 1 and `dept` > 0")->order("`left_val`, `order`")->select();

    $nCount = sizeof($MenuList);
    $arrMainMenu = array();

    for($i=0; $i<$nCount; $i++){
        if((int)$MenuList[$i]["parentid"] === 1){
            array_push($arrMainMenu, $MenuList[$i]);
        }else{
            continue;
        }
    }

    $nMainCount = sizeof($arrMainMenu);

    for($i=0; $i<$nMainCount; $i++){
        $arrSubMenu = array();

        for($k=0; $k<$nCount; $k++){
            if((int)$MenuList[$k]["parentid"] === 1){
                continue;
            }else{
                if($MenuList[$k]["parentid"] == $arrMainMenu[$i]["menuid"]){
                    array_push($arrSubMenu, $MenuList[$k]);
                }else{
                    continue;
                }
            }
        }

        $arrMainMenu[$i]["sub"] = $arrSubMenu;
    }

    SetSystemMenuCache($arrMainMenu);

    return $arrMainMenu;
}

/**
* 获取左侧导航栏目缓存
* @return 栏目数据数组
*/
function GetSystemMenuCache(){
    $arrMenu = F(GetSystemMenuCacheDir());

    if(empty($arrMenu)){
        $arrMenu = SyncSystemMenu();
    }

    return $arrMenu;
}

/*
 * 设置左侧导航栏目缓存
 * @param $_CacheContent 缓存的内容
*/
function SetSystemMenuCache($_CacheContent){
    F(GetSystemMenuCacheDir(),$_CacheContent);
}

/*
 * 获取栏目缓存目录
 * @param $_CacheContent 缓存的内容
*/
function GetSystemMenuCacheDir(){
    return C('CACHE_CONFIG.LEFT_MENU_DIR');
}

/*
 * 从数据库读取应用栏目信息，并设置缓存。缓存的是数组
*/
function SyncAppMenu(){
    $model = M("app_menu");
    $MenuList = $model->where("`left_val` > 1 and `dept` > 0")->order("`left_val`, `order`")->select();

    SetAppMenuCache($MenuList);

    return $MenuList;
}

/*
 * 获取应用栏目缓存
*/
function GetAppMenuCache(){
    $arrMenu = F(GetAppMenuCacheDir());

    if(empty($arrMenu)){
        $arrMenu = SyncAppMenu();
    }
    
    return $arrMenu;
}

/*
 * 设置应用栏目缓存
 * @param $_CacheContent 缓存的内容
*/
function SetAppMenuCache($_CacheContent){
    F(GetAppMenuCacheDir(),$_CacheContent);
}

/*
 * 获取应用栏目缓存目录
 * @param $_CacheContent 缓存的内容
*/
function GetAppMenuCacheDir(){
    return C('CACHE_CONFIG.APP_MENU_DIR');
}

/*
 * 获取应用栏目名缩进标记
 * @param $_currarr 当前栏目数组
 * @param $_backarr 前一个栏目数组
 * @param $_nextarr 后一个栏目数组
*/
function GetAppMenuSign($_currarr = array(), $_backarr = array(), $_nextarr = array()){
    $sResult = "";
    
    if($_currarr["dept"] > 1){
        $sResult = "";

        //如何当前栏目的parentid == 上一个栏目的menuid
        //表示上一个栏目是父栏目，当前栏目是其子栏目
        if($_currarr["parentid"] == $_backarr["menuid"]){
            //判断下一个栏目是不是与当前栏目同属于一个父栏目
            if($_currarr["parentid"] != $_nextarr["parentid"]){
                $sResult .= "└&nbsp;&nbsp;";
            }else{
                $sResult .= "├&nbsp;&nbsp;";
            }
        }else{
            //如果上一个栏目不是父栏目
            //判断下一个栏目是不是与当前栏目同属于一个父栏目
            if($_currarr["parentid"] != $_nextarr["parentid"]){
                $sResult .= "└&nbsp;&nbsp;";
            }else{
                $sResult .= "├&nbsp;&nbsp;";
            }
        }
    }
    
    return $sResult;
}

/*
 * 给字符加上默认的背景
 * @param $_str 字符串
*/
function AddLableToStringDefault($_str){
    return '<span class="label label-default">'. $_str .'</span>';
}

/*
 * 给字符加上成功的背景
 * @param $_str 字符串
*/
function AddLableToStringSuccess($_str){
    return '<span class="label label-success">'. $_str .'</span>';
}

/*
 * 给字符加上成功的背景
 * @param $_str 字符串
*/
function AddLableToStringInfo($_str){
    return '<span class="label label-info">'. $_str .'</span>';
}

/*
 * 给字符加上错误的背景
 * @param $_str 字符串
*/
function AddLableToStringError($_str){
    return '<span class="label label-danger">'. $_str .'</span>';
}

/*
 * 给字符加上警告的背景
 * @param $_str 字符串
*/
function AddLableToStringWarning($_str){
    return '<span class="label label-warning">'. $_str .'</span>';
}

/*
 * 给字符加上主要的背景
 * @param $_str 字符串
*/
function AddLableToStringPrimary($_str){
    return '<span class="label label-primary">'. $_str .'</span>';
}

//格式化从数据库中读取的图片配置json字符串
function FmtDbImgJson($_jsonStr){
    $arrImage = json_decode($_jsonStr, true);
    $arrReturn = array();
    
    $nCount = sizeof($arrImage);

    for($i=0; $i<$nCount; $i++){
        array_push($arrReturn, '<img src="' . __ROOT__ . '/Public' . $arrImage[$i]["thumb_url"] .'" width="'. $arrImage[$i]["thumb_width"] .'" height="'. $arrImage[$i]["thumb_height"] .'" />');
    }

    return join('', $arrReturn);
}
    
//生成列表页字段配置
function GetListPageFieldConfig($_modelname){
    $model = M("model_field");
    $FieldList = $model->where("mdlid = (select mdlid from btten_model where mdlname = '{$_modelname}') and islist = 'true' and status = 1")->order("`order`, fieldid")->select();

    $arrReturn = array();
    $nCount = sizeof($FieldList);

    for($i=0; $i<$nCount; $i++){
        $FieldInfo = $FieldList[$i];
        $arrConfig = json_decode($FieldList[$i]["listconfig"], true);

        $arrReturn[$i] = array(
            "key"=>$FieldInfo["fieldname"], 
            "table"=>$FieldInfo["tablename"], 
            "val"=>$FieldInfo["fieldtitle"], 
            "style"=>$arrConfig["style"], 
            "css"=>$arrConfig["css"],
            "ishide"=>$arrConfig["ishide"],
        );
    }

    return $arrReturn;
}

//获取列表页字段数据
function GetListPageData($_modelname, $_field=array()){
    //获取页面模型中的配置
    $model = M("model");
    $PageModel = $model->where("mdlname = '{$_modelname}'")->find();

    $arrJoinCase = array();
    $arrField = array();

    //主表名称
    $sTableName = $PageModel["main_table"];

    //如果连接了其它的表
    if(!IsN($PageModel["join_table"])){
        //拆分连接的表，以及join条件
        $JoinTable = explode(",", $PageModel["join_table"]);
        $JoinCase = explode(",", $PageModel["join_table_case"]);

        $nCount = sizeof($JoinTable);

        //循环连接的表
        for($i=0; $i<$nCount; $i++){
            $JoinTable[$i] = C("DB_PREFIX") . $JoinTable[$i];   //增加表前缀
            //
            //设置join条件
            $sJoinCase = $PageModel["join_type"] . " " . $JoinTable[$i] . " on " . $JoinCase[$i];
            array_push($arrJoinCase, $sJoinCase);
        }
    }

    $sJoin = join(" ", $arrJoinCase);

    //组合查询的字段
    if(!IsN($_field)){
        $nCount = sizeof($_field);

        for($i=0; $i<$nCount; $i++){
            $sFieldName = C("DB_PREFIX") . $_field[$i]["table"] . ".`" . $_field[$i]["key"] . "`";
            array_push($arrField, $sFieldName);
        }
    }

    $sField = join("," , $arrField);

    return array("maintable"=>$PageModel["main_table"], "join"=>$sJoin, "field"=>$sField);
}
    
//生成列表页搜索指端配置
function GetListPageSearchFieldConfig($_modelname, $_param=array()){
    //获取字段配置数据
    $model = M("model_field");
    $FieldList = $model->where("mdlid = (select mdlid from btten_model where mdlname = '{$_modelname}') and `issearch` = 'true' and status = 1")->order("`order`, fieldid")->select();

    $arrReturn = array();
    $nCount = sizeof($FieldList);

    for($i=0; $i<$nCount; $i++){
        $FieldInfo = $FieldList[$i];
        $arrConfig = json_decode($FieldInfo["searchconfig"], true);
        $arrReturn[$i]["prefix"] = 'search_' . $FieldInfo["tablename"] . '_';
        $arrReturn[$i]["name"] = $FieldInfo["fieldname"];
        $arrReturn[$i]["id"] = $arrReturn[$i]["prefix"] . $FieldInfo["fieldname"];
        $arrReturn[$i]["title"] = $FieldInfo["fieldtitle"];
        

        foreach($arrConfig as $key=>$val) {
            $arrReturn[$i][$key] = $val;
        }
        
        if(!empty($_param)){
            $arrReturn[$i]["value"] = $_param[$FieldInfo["fieldname"]];
        }

        if(($i+1) >= $nCount){
            $arrReturn[$i]["css"] = $FieldInfo["css"] . ' last';
        }else{
            $arrReturn[$i]["css"] = $FieldInfo["css"];
        }
    }

    return $arrReturn;
}

/*
 *  根据Post参数，生成Sql语句的where条件
 * $_param Post参数
 * $_pagemodel 页面模型名称
 * 返回值：array
 */
function GetSearchSql($_param=array(), $_pagemodel=""){
    //获取字段配置
    $model = M("model_field");
    $sFields = "`tablename`, `fieldname`, `searchconfig`";
    $FieldList = $model->field($sFields)->where("`mdlid` = (select `mdlid` from btten_model where `mdlname` = '{$_pagemodel}') and `issearch` = 'true' and `status` = 1")->order("`order`, `fieldid`")->select();
    $nCount = sizeof($FieldList);

    $arrWhere = array();
    
    //根据参数来循环创建条件
    foreach($_param as $key=>$val){
        //值不为空时
        if(!IsN($val)){
            for($i=0; $i<$nCount; $i++){
                //如果字段名 =  参数名
                if($FieldList[$i]["fieldname"] == $key){
                    //获取搜索项配置
                    $arrConfig = json_decode($FieldList[$i]["searchconfig"], true);
                    
                    //拼接查询字段：表名.字段名
                    $sFieldName = C("DB_PREFIX") . "{$FieldList[$i]["tablename"]}.`{$FieldList[$i]["fieldname"]}`";
                    
                    //输出比较运算符
                    if($arrConfig["compare"] == 'like'){
                        //模糊查询
                        array_push($arrWhere, "{$sFieldName} like '%{$val}%'");
                    }else{
                        //比较查询
                        $sSign = C("COMPARE.{$arrConfig["compare"]}");
                        array_push($arrWhere, "{$sFieldName}{$sSign}{$val}");
                    }
                    
                    //查询的参数值，用于分页链接和查询表单默认值
                    $arrSearchValue[$key] = $val;

                    break;
                }
            }
        }
    }
    
    $arrReturn = array("where"=>$arrWhere, "searchvalue"=>$arrSearchValue);

    return $arrReturn;
}

/**
* 获取添加/修改的页面字段配置
* @param string $_modelname 页面模型名称
* @param bool $_type 是否使用添加页配置
* @return Array
*/
function GetSetPageFieldConfig($_modelname, $_type=true){
    //获取字段配置数据
    $model = M("model_field");
    $sFields = "`fieldid`, `mdlid`, `tablename`, `fieldname`, `order`, `fieldtitle`";
    
    if($_type){
            $sTypeWhere = "`isadd` = 'true'";
            $sFields .= ", `addconfig` as `inputconfig`";
        }else{
            $sTypeWhere = "`isedit` = 'true'";
            $sFields .= ", `editconfig` as `inputconfig`";
        }
    
    $FieldList = $model->field($sFields)->where("`mdlid` = (select `mdlid` from btten_model where `mdlname` = '{$_modelname}') and {$sTypeWhere} and `status` = 1")->order("`order`, `fieldid`")->select();
    
    $arrReturn = array();
    $nCount = sizeof($FieldList);

    for($i=0; $i<$nCount; $i++){
        $FieldInfo = $FieldList[$i];
        
        //格式化配置
        $arrConfig = json_decode($FieldInfo["inputconfig"], true);

        $arrReturn[$i]["name"] = $FieldInfo["fieldname"];
        $arrReturn[$i]["id"] = $FieldInfo["fieldname"];
        $arrReturn[$i]["title"] = $FieldInfo["fieldtitle"];
        
        //赋值输入项配置
        foreach($arrConfig as $key=>$val) {
            $arrReturn[$i][$key] = $val;
        }
        
        //如果是最后一行，加上样式 last
        if(($i+1) >= $nCount){
            $arrReturn[$i]["css"] = $arrConfig["css"] . ' last';
        }else{
            $arrReturn[$i]["css"] = $arrConfig["css"];
        }
    }

    return $arrReturn;
}
    
//格式化配置的Option，包括组合输入的Option格式数据已经函数生成的格式数据
function FmtOption($_strjson="", $_funname=""){
    if(empty($_strjson) && empty($_funname)){
        return "";
    }
    
    $ArrOption  = array();

    //判断人工输入的Option格式选项
    if(!empty($_strjson)){
        $ArrOption = explode(';', $_strjson);

        $nOptionCount = sizeof($ArrOption);

        for($i=0; $i<$nOptionCount; $i++){
            $arrItem = explode('|', $ArrOption[$i]);

            $ArrOption[$i] = array("key"=>$arrItem[0], "val"=>$arrItem[1]);
        }
    }

    //判断有没有配置函数
    if(!empty($_funname)){
        //执行函数
        eval('$arrFunOption = ' . $_funname . ';');
        
        //合并数组
        $ArrOption = array_merge($ArrOption,$arrFunOption);
    }

    return $ArrOption;
}

//格式化配置的缩略图生成的尺寸
function FmtThumbSize($_sizestr){
    if(empty($_sizestr)){
        return "";
    }
    
    $arrThumb = explode(';', $_sizestr);
    $nCount = sizeof($arrThumb);
    $arrThumbSize = array();
    
    for($i=0; $i<$nCount; $i++){
        $arrTemp = explode('*', $arrThumb[$i]);
        array_push($arrThumbSize, array("width"=>$arrTemp[0], "height"=>$arrTemp[1]));
    }
    
    return $arrThumbSize;
}

//获取管理员组的Option数据
function GetAdminGroupOption(){
    $model = M("auth_group");
    $GroupList = $model->field("`id` as `val`, `title` as `key`")->select();

    return $GroupList;
}

//获取账号状态
function GetAccountStatus(){
    $arrReturn = array();
    
    array_push($arrReturn, array("key"=>"正常", "val"=>1));
    array_push($arrReturn, array("key"=>"禁用", "val"=>0));

    return $arrReturn;
}

function FmtJsEvent($_event, $_fun){
    if(empty($_event)){
        return "";
    }
    
    $arrEvent = explode('|', $_event);
    $arrFun = explode('|', $_fun);
    
    $nCount = sizeof($arrEvent);
    $arrReturn = array();
    
    for($i=0; $i<$nCount; $i++){
        if(!empty($arrFun[$i])){
            array_push($arrReturn, "{$arrEvent[$i]}=\"{$arrFun[$i]}\"");
        }
    }
    
    return join(" ", $arrReturn);
}

function GetAppMenu_DLL(){
    $arrMenu = F(GetAppMenuDllCacheDir());

    if(empty($arrMenu)){
        $arrMenu = SyncAppMenu_DLL();
    }
    
    return $arrMenu;
}

function SyncAppMenu_DLL(){
    F(GetAppMenuDllCacheDir(), NULL);
    
    $arrReturn = array();
    
    $model = M("app_menu");
    $ParentMenuList = $model->field("`menuid` as `val`, `menu_name` as `key`")->where('`parentid` = 1 and `status` = 0')->select();
    $nParentCount = sizeof($ParentMenuList);
    
    for($i=0; $i<$nParentCount; $i++){
        array_push($arrReturn, $ParentMenuList[$i]);
        
        $SubMenuList = $model->field("`menuid` as `val`, `menu_name` as `key`")->where("`parentid` = {$ParentMenuList[$i]["val"]} and `status` = 0")->select();
        $nSubCount = sizeof($SubMenuList);
        
        for($k=0; $k<$nSubCount; $k++){
            if(($k+1) >= $nSubCount){
                $SubMenuList[$k]["key"] = "└  " . $SubMenuList[$k]["key"];
            }else{
                $SubMenuList[$k]["key"] = "├  " . $SubMenuList[$k]["key"];
            }
            
            array_push($arrReturn, $SubMenuList[$k]);
        }
    }
    F(GetAppMenuDllCacheDir(),$arrReturn);
    return $arrReturn;
}

function GetAppMenuDllCacheDir(){
    return C('CACHE_CONFIG.APP_MENU_DLL_DIR');
}