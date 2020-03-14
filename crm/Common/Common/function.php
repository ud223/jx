<?php
function IsN($_obj){
    $sType = gettype($_obj);

    if($sType == "string"){
        if($_obj === "0"){
            return false;
        }
    }
    
    if($sType == "integer"){
        if($_obj === 0){
            return false;
        }
    } 
    
    return empty($_obj);
}

function IsNum($_str, $_allowzero=true){
    if($_allowzero){
        if(IsN($_str)){
            return false;
        }
    }else{
        if(empty($_str)){
            return false;
        }
    }
    
    if(!is_numeric($nUid)){
        return false;
    }else{
        return true;
    }
}

/**
 * 打印数组
 * @param array $arr 要打印的数组。
 * @return string
 */
function exitp($arr){
    exit(print_r($arr));
}

/**
 * 打印Thinkphp生成的Sql语句
 * @param Model $model Thinkphp Model对象。
 * @return string
 */
function showsql($model){
    return $model->getLastSql();
}

/**
 * 获取参数
 * @param string $key 参数名。
 * @return string
 */
function param($key){
    if(!IsN($_POST[$key])){
//        $sResult = I("post.{$key}");
        $sResult = trim($_POST[$key]);
    }else{
//        $sResult = I("get.{$key}");
        $sResult = trim($_GET[$key]);
    }
    
    return $sResult;
}

/**
 * 计算字符串长度，中文计算2，英文1
 * @param string $errid 错误码。
 * @return string
 */
function StrCount($str){
    $nLen = (strlen($str) + mb_strlen($str,'UTF8')) / 2;

   return $nLen;
}

//执行存储过程
function ExecProc($proc){	
    /* Connect to a MySQL server */ 
    $link = mysqli_connect( 
    C('DB_HOST'), /* The host to connect to */ 
    C('DB_USER'), /* The user to connect as */ 
    C('DB_PWD'), /* The password to use */ 
    C('DB_NAME')
    ); /* The default database to query */ 

    if (!$link) { 
        printf("Can't connect to MySQL Server. Errorcode: %s\n", mysqli_connect_error()); 
        exit(); 
    } 
    $sResult = 0;
    /* Send a query to the server */ 
    mysqli_query($link,"set names utf8");
    if ($result = mysqli_query($link, $proc)) { 
            /* Fetch the results of the query */ 
        while( $row = mysqli_fetch_array($result) ){ 
                $sResult = $row[0];
        } 
            /* Destroy the result set and free the memory used for it */ 
        mysqli_free_result($result); 
    } 

    /* Close the connection */ 
    mysqli_close($link);
    return $sResult;
}

//加密
function jiami($str){
    $key = C("JIAMI_KEY");
    $key=md5($key);
    $k=md5(rand(0,100));//相当于动态密钥
    $k=substr($k,0,3);
    $tmp="";
    
    for($i=0;$i<strlen($str);$i++){
        $tmp.=substr($str,$i,1) ^ substr($key,$i,1);
    }
    
    return base64_encode($k.$tmp);
}  

//解密
function jiemi($str){
    $key = C("JIAMI_KEY");
    $len=strlen($str);
    $key=md5($key);
    $str=base64_decode($str);
    $str=substr($str,3,$len-3);
    $tmp="";
    
    for($i=0;$i<strlen($str);$i++){
        $tmp.=substr($str,$i,1) ^ substr($key,$i,1);
    }
    
    return $tmp;
}  

//创建随机数数组
function CreatRandArray($_min, $_max, $_count) {  
    $numbers = range ($_min, $_max);  
    shuffle($numbers);  //随机打乱数组  
    $result = array_slice($numbers, 1, $_count);  
    return $result;  
} 

//获取2个数组才差集
function fun_array_diff($array_1, $array_2) {
    //$array_2 = array_flip($array_2);
    foreach ($array_1 as $key => $item) {
        if (isset($array_2[$key])) {
            unset($array_1[$key]);
        }
     }

    return $array_1;
}

//检查文件夹并创建
function Directory($dir){
    return is_dir($dir) or (Directory(dirname($dir)) and mkdir($dir, 0777));
}

//返回错误json字符串
function ErrorInfo($_msg){
    exit('{"info":"'. $_msg .'","status":0,"url":""}');
}

//返回错误json字符串
function SuccessInfo($_msg){
    exit('{"info":"'. $_msg .'","status":1,"url":""}');
}

/*
 * 根据配置，创建html控件
*/
function CreateHtmlWidget($_cfg=array()){
    if(empty($_cfg)){
        return;
    }

    $nCfgCount = sizeof($_cfg);

    for($i=0; $i<$nCfgCount; $i++){
        $config = $_cfg[$i];
        
        R('Widget/Control/'.$config["control"], array($config));  
    }
}

//获取地区数据
function GetRegion(){
    $arrRegion = F(GetRegionCacheDir());

    if(empty($arrRegion)){
        $arrRegion = SyncRegion();
    }

    return $arrRegion;
}

/*
 * 获取省市区缓存目录
 * @param $_CacheContent 缓存的内容
*/
function GetRegionCacheDir(){
    return C('CACHE_CONFIG.REGION');
}

/*
 * 从数据库读取省市区信息，并设置缓存。缓存的是数组
*/
function SyncRegion(){
    $model = M("region_province");
    $ProvinceList = $model->field("`provinceid` as `val`, `province` as `key`")->select();

    $model = M("region_city");
    $CityList = $model->field("`cityid` as `val`, `city` as `key`, `provinceid` as `parentid`")->select();

    $model = M("region_area");
    $AreaList = $model->field("`areaid` as `val`, `area` as `key`, `cityid` as `parentid`")->select();

    $arrCache = array(
        'province'=>$ProvinceList,
        'city'=>$CityList,
        'area'=>$AreaList,
    );
    
    SetRegionCache($arrCache);

    return $arrCache;
}

/*
 * 设置省市区缓存
 * @param $_CacheContent 缓存的内容
*/
function SetRegionCache($_CacheContent){
    F(GetRegionCacheDir(),$_CacheContent);
}

//格式化从数据库中读取的图片配置json字符串
function GetImgToJson($_jsonStr, $_width=0, $_height=0, $_single=true){
    $arrImage = json_decode($_jsonStr, true);
    $arrReturn = array();
    
    $nCount = sizeof($arrImage);

    for($i=0; $i<$nCount; $i++){
        array_push($arrReturn, array("imageid"=>$arrImage[$i]["imageid"], "image"=>$arrImage[$i]["image"], "path"=>$arrImage[$i]["path"], "width"=>$arrImage[$i]["width"], "height"=>$arrImage[$i]["height"]));
    }

    if($_single){
        return $arrReturn[0];
    }else{
        return $arrReturn;
    }
}

//格式化从数据库中读取的上传文件配置json字符串
function GetFilesToJson($_jsonStr, $_single=true){
    $arrFiles = json_decode($_jsonStr, true);
    $arrReturn = array();
    
    $nCount = sizeof($arrFiles);
    
    for($i=0; $i<$nCount; $i++){
        array_push($arrReturn, array("name"=>$arrFiles[$i]["name"], "size"=>$arrFiles[$i]["size"], "ext"=>$arrFiles[$i]["ext"], "savename"=>$arrFiles[$i]["savename"], "savepath"=>$arrFiles[$i]["savepath"]));
    }

    if($_single){
        return $arrReturn[0];
    }else{
        return $arrReturn;
    }
}

/*
 * 请求错误时返回的josn数据
 * */
function ApiErrorRetrun($info="", $status=0){
    $return = array();
    $result["status"] = $status;
    $result["info"] = $info;

    exit(json_encode($result));
}

/*
 * 请求结果正确时返回的josn数据
 * */
function ApiSuccessReturn($data=array(), $info="", $other=array(), $status=1){
    $result["status"] = $status;
    $result["info"] = $info;
    $result["data"] = $data;

    if(!empty($other)){
        $result = array_merge($other, $result);
    }

    exit(json_encode($result));
}