<?php
//获取post参数值
function RP($_key){
  return trim(I("post.{$_key}"));
}

//获取get参数值
function RG($_key){
  return trim(I("get.{$_key}"));
}

//获取request参数值
function RR($_key, $_replace=""){
  if(IsN($_replace)){
    $Result = trim(I("request.{$_key}"));
  }else{
    $Result = trim(I("request.{$_key}"), $_replace);
  }

  return $Result;
}

/**
 * 检查变量是否为空
 * @param string $_str 字符串
 * @return bool true or false
 */
function IsN($_str){
  $sType = gettype($_str);

  if($sType == "string"){
    if($_str === "0"){
      return false;
    }
  }

  if($sType == "integer"){
    if($_str === 0){
      return false;
    }
  }

  return empty($_str);
}

/**
 * 检查是否是数字
 *
 * @param string $_str         字符串
 * @param bool   $_allow_zero
 * @param bool   $_allow_minus true:允许负数，false不允许负数
 *
 * @return array
 * @internal param bool $_allowzero true：允许0值，false：不允许0值
 */
function IsNum($_str, $_allow_zero=true, $_allow_minus=true){
  //检查0值和空值的情况
  if($_allow_zero){
    //允许0
    if(IsN($_str)){
      return false;
    }
  }else{
    //不允许0
    if(empty($_str)){
      return false;
    }
  }

  //检查是否数字
  if(!is_numeric($_str)){
    return false;
  }else{
    //判断是否允许负数
    if(!$_allow_minus){
      if($_str < 0){
        return false;
      }
    }

    return true;
  }
}

/**
 * todo:检查是否是合法的浮点数
 *
 * @param string $_str         字符串
 * @param int    $_digits      比较的小数点位数
 * @param bool   $_allowzero   true：允许0值，false：不允许0值
 * @param bool   $_allow_minus true:允许负数，false不允许负数
 *
 * @return bool
 */
function IsFloat($_str, $_digits=2, $_allow_zero=true, $_allow_minus=true){
  if(IsN($_str)){
    return false;
  }else{
    if(!IsNum($_str)){
      return false;
    }
  }

  $Result = bccomp((string)$_str, "0", $_digits);

  if($_allow_zero){
    if($_allow_minus){
      return true;
    }else{
      if($Result < 0){
        return false;
      }else{
        return true;
      }
    }
  }else{
    if($Result <= 0){
      return false;
    }else{
      return true;
    }
  }
}

/**
 * todo: 检查是否是数组
 *
 * @param      $_arr
 * @param bool $_allow_null
 *
 * @return bool
 */
function IsArray($_arr, $_allow_null=false){
  if($_allow_null === false){
    if(!is_array($_arr) || empty($_arr)){
      return false;
    }
  }else{
    if(!is_array($_arr)){
      return false;
    }
  }

  return true;
}

/**
 * 中断程序并打印SQL
 * @param Model $_model 对象
 * @return mixed
 */
function showsql($_model){
  exit($_model->getLastSql());
}

/**
 * 返回正确提示信息
 * @param string $_str 返回状态提示内容
 * @return array error：0正确，info：提示信息
 */
function ReturnCorrect($_str='', $_data=array(), $_code=0){
  return array("error"=>0, "info"=>$_str, "data"=>$_data, "code"=>$_code);
}

/**
 * 返回错误提示信息
 * @param string $_str 返回状态提示内容
 * @param array $_data 返回数据内容
 * @return array error：0正确，info：提示信息
 */
function ReturnError($_str='', $_data=array(), $_code=0){
  return array("error"=>1, "info"=>$_str, "data"=>$_data, "code"=>$_code);
}

/**
 * ajax返回数据
 * @param array $_data 返回数据内容
 */
function AjaxReturn($_data=array()){
  exit(json_encode($_data));
}

/**
 * 返回错误提示信息
 * @param string $_str 返回状态提示内容
 * @return array error：1错误，info：提示信息
 */
function AjaxReturnError($_str='', $_data=array()){
  $arrResult = array("error"=>1, "info"=>$_str, "data"=>$_data);
  exit(json_encode($arrResult));
}

/**
 * 返回正确提示信息
 * @param string $_str 返回状态提示内容
 * @param array $_data 返回数据内容
 * @return array error：0正确，info：提示信息
 */
function AjaxReturnCorrect($_str='', $_data=array()){
  $arrResult = array("error"=>0, "info"=>$_str, "data"=>$_data);
  exit(json_encode($arrResult));
}

//封装打印函数
function p($data, $die = true) {
  echo '<pre>';
  print_r($data);
  echo '</pre>';
  $die && die();
}

/**
 * 返回完整的表名
 * @param string $_name 数据库表名，不带前缀
 * @return string 完整的表名
 */
function tn($_name){
  return C('DB_PREFIX') . $_name;
}

/**
 * 给密码加密
 * @param string $_password 明文密码
 * @return array salt:安全码， pwd：加密后的密码
 */
function PasswordMd5($_password, $_salt=''){
  if(empty($_salt)){
    $nRand = rand(1, 9990);
  }else{
    $nRand = $_salt;
  }

  $sPassword = md5(md5($_password) . $nRand);

  return array('salt'=>$nRand, 'pwd'=>$sPassword);
}

/**
 * 返回完整的图片路径，含域名
 * @param string $_url 不完整的图片url
 * @return string
 */
function FullImageUrl($_url, $path=''){
  if(empty($_url)){
    return '';
  }

  if(substr($_url, 0, 7) == "http://" || substr($_url, 0, 8) == "https://"){
    return $_url;
  }

  if(empty($path)){
    if(strstr($_url,"images/") === false){
      $_url = "images/{$_url}";
    }
  }

  $sImageUrl = IMAGE_DOMIAN . $path . $_url;

  return $sImageUrl;
}

function GetCacheOnOff(){
  return C('CacheOnOff');
}

/**
 * 获取缩略图文件名
 * @param string $_imgfilename 原图文件名
 * @param array $_arrwh 宽高数组
 * @return bool true or false
 */
function GetThumbImageNameIndex($_imgfilename, $_arrwh){
  $sThumbName = str_replace(".jpg", ".jpg!{$_arrwh[0]}_{$_arrwh[1]}.jpg", $_imgfilename);

  return $sThumbName;
}

/**
 * 获取缩略图的文件后缀名
 *
 * @param array $_arrwh 宽高数组
 *
 * @return bool true or false
 * @internal param string $_imgfilename 原图文件名
 */
function GetThumbImageSizeSuffix($_arrwh){
  $SizeSuffix = array();

  foreach($_arrwh as $key=>$val){
    $Info = "!{$val[0]}_{$val[1]}.jpg";
    array_push($SizeSuffix, $Info);
  }

  return $SizeSuffix;
}

/**
 * 将时间戳格式化成全日期字符
 *
 * @param int    $_time 时间戳
 * @param string $_style
 *
 * @return string 日期字符串
 * @internal param string $_typestr 时间格式化的样式
 */
function Time2FullDate($_time, $_style="Y-m-d H:i:s"){
  if(empty($_time)){
    return '';
  }

  return date($_style, $_time);
}

/**
 * 返回管理后台跳转完整路径
 * @param string $ModAct 模型/控制器
 * @return array error：1错误，info：提示信息
 */
function GotoUrl($ModAct){
  return __APP__ . '/' . MODULE_NAME . '/' . $ModAct;
}

/**
 * 设置cookie前缀
 */
function GetCookiePrefix(){
  return "ZHIHUI_CRS_";
}

/**
 * 写入log文件
 *
 * @param string $_content 内容
 * @param string $_type
 *
 * @internal param int $id 商品ID
 */
function WriteLog($_content, $_type='DEBUG'){
  Think\Log::write($_content, $_type);
}

/**
 * 验证手机号是否正确
 * @param string $mobile 手机号
 * @return bool true or false
 */
function isMobile($mobile) {
  if (!is_numeric($mobile)) {
    return false;
  }

  return preg_match('#^13[\d]{9}$|^14[\d]{9}$|^15[\d]{9}$|^17[\d]{9}$|^18[\d]{9}$#', $mobile) ? true : false;
}

/**
 * todo:检查邮件地址
 *
 * @param $_email
 *
 * @return bool
 */
function isEMail($_email){
  if(IsN($_email)) {
    return false;
  }

  return preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $_email) ? true : false;
}

/**
 * 验证邮编是否正确
 * @param int $str 邮编
 * @return bool true or false
 */
function isZipcode($str){
  return (preg_match('#^[0-9]\d{5}$#',$str))?true:false;
}

/**
 * todo: 判断字符串是否为日期格式
 * @param $_date_str
 *
 * @return int
 */
function isDateTime($_date_str){
  $Result = strtotime($_date_str);
  return $Result;
}

function CheckAccountPassword($_str){
  $nStrLen = strlen($_str);

  if($nStrLen < 6 || $nStrLen > 20){
    return ReturnError(L("_USER_PASSWORD_FORMAT_ERROR_"), array(), 1010);
  }else{
    $FmtResult = (preg_match('/^[a-z\d]{6,20}$/i',$_str)) ? true : false;

    if(!$FmtResult){
      return ReturnError(L("_USER_PASSWORD_FORMAT_ERROR_"), array(), 1010);
    }else{
      return ReturnCorrect();
    }
  }
}

/**
 * todo: 删除文件
 *
 * @param string $_file_path 要删除的文件路径
 *
 * @return bool
 */
function DeleteFile($_file_path){
  if(IsN($_file_path)){
    return false;
  }

  $DelResult = @unlink($_file_path);

  if ($DelResult !== false) {
    return true;
  } else {
    return false;
  }
}

/**
 * 将xml转为array
 *
 * @param string $xml
 *
 * @return array
 * @throws WxPayException
 */
function FromXml($xml){
  if(!$xml){
    return ReturnError("xml数据异常！");
  }

  libxml_disable_entity_loader(true);

  $XmlValue = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

  return ReturnCorrect("", $XmlValue);
}

/**
 * todo:Thinkphp字符串转译的问题
 * @param $_str 字符串
 *
 * @return string
 */
function StrStripslashes($_str){
  return stripslashes(htmlspecialchars_decode($_str));
}

/**
 * todo:获取字符串中英文混合长度
 * @param $str string 字符串
 * @param $$charset string 编码
 * @return 返回字符串长度，1中文=1位，2英文=1位
 */
function strLength($str, $charset='UTF8'){
  return (strlen($str) + mb_strlen($str, $charset)) / 2;
}

/**
 * todo 对多维数组进行排序
 *
 * @param     $arrays 需要处理的数组
 * @param     $sort_key 针对排序的字段
 * @param int $sort_order 排序方式
 * @param int $sort_type 排序字段
 *
 * @return array|bool
 */
function my_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){
  if(is_array($arrays)){
    foreach ($arrays as $array){
      if(is_array($array)){
        $key_arrays[] = $array[$sort_key];
      }else{
        return false;
      }
    }
  }else{
    return false;
  }

  array_multisort($key_arrays,$sort_order,$sort_type,$arrays);

  return $arrays;
}

/**
 * 判断是不是手机访问网站
 * @return bool true是手机访问，false不是手机访问
 */
function is_mobile_request(){
  $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
  $mobile_browser = '0';
  
  if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT']))){
    $mobile_browser++;
  }
  
  if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false)){
    $mobile_browser++;
  }
  
  if (isset($_SERVER['HTTP_X_WAP_PROFILE'])){
    $mobile_browser++;
  }
  
  if (isset($_SERVER['HTTP_PROFILE'])){
    $mobile_browser++;
  }
  
  $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
  $mobile_agents = array(
    'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
    'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
    'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
    'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
    'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
    'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
    'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
    'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
    'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
  );
  
  if (in_array($mobile_ua, $mobile_agents)){
    $mobile_browser++;
  }
  
  if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false){
    $mobile_browser++;
  }
  
  // Pre-final check to reset everything if the user is on Windows  
  if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false){
    $mobile_browser = 0;
  }
  
  // But WP7 is also Windows, with a slightly different characteristic  
  if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false){
    $mobile_browser++;
  }
  
  //>0表示是手机访问，转向wap版本
  if($mobile_browser > 0){
    return true;
  }else{
    return false;
  }
}

/**
 * todo:格式化分页信息数据
 *
 * @param int $_total 总数
 * @param int $_page  第几页数据
 * @param int $_rows  页记录数
 *
 * @return \分页数据数组
 *
 */
function FmtPagin($_total, $_page, $_rows){
  $nMore = 0; //是否还有下一页数据， 0没有，1有
  
  $nCurrRows = ($_page-1) * $_rows;
  if($_total > $nCurrRows){
    $nMore = 1;
  }
  
  $Reslut = array(
    "total"=>(int)$_total,
    "count"=>(int)$_rows,
    "more"=>(int)$nMore
  );
  
  return $Reslut;
}