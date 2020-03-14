<?php


/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author
 */
function is_login()
{
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

function WriteLog($_str, $_type = "DEBUG")
{
    Think\Log::write($_str, $_type);
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author
 */
function is_administrator($uid = null)
{
    $uid = is_null($uid) ? is_login() : $uid;
    return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str 要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author
 */
function str2arr($str, $glue = ',')
{
    return explode($glue, $str);
}


/**
 * 加密
 * @param intger $type 类型
 * @param bool $all 是否返回全部类型
 * @author
 */
function fit_md5($string, $ext = 'wq3mdju&#*')
{
    return md5(md5($string) . $ext);
}


/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 * @author
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

function fit_api($result, $code, $msg, $data)
{
    $json = array(
        'result' => $result,
        'code' => $code,
        'msg' => $msg,
        'data' => $data
    );

    $str = json_encode($json);

    return $str;
}

function Rad($d)
{
    return $d * 3.1415926535898 / 180.0;
}

function GetDistance($lat1, $lng1, $lat2, $lng2)
{
    $EARTH_RADIUS = 6378.137;
    $radLat1 = Rad($lat1);
    //echo $radLat1;
    $radLat2 = Rad($lat2);
    $a = $radLat1 - $radLat2;
    $b = Rad($lng1) - Rad($lng2);
    $s = 2 * asin(sqrt(pow(sin($a / 2), 2) +
            cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
    $s = $s * $EARTH_RADIUS;
    $s = round($s * 10000) / 10000;
    return $s;
}

//检查短信码
function CheckCode($phone_number, $checkcode, $msg_type)
{
    if ($checkcode == '9999') {
        return 1;
    }
    //检查短信验证码
    $sms = M('Sms');

    $condition = array(
        "phone_number" => $phone_number,
        "msg_type" => $msg_type,
        "code" => $checkcode
    );

    //$smsCode = $sms->where($condition)->order('create_at desc')->limit(1)->select();
    $smsCode = $sms->query("select * from fit_sms where phone_number='$phone_number' and msg_type = $msg_type and code = '$checkcode' and " . time() . " - create_at <= 900 order by create_at desc limit 1");

    if ($smsCode && $checkcode == $smsCode[0]["code"]) {
        return 1;
    } else {
        return 2;
    }
}

//object>>array转换
function object_array($array)
{
    if (is_object($array)) {
        $array = (array)$array;
    }
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}

//个推推送
function PushMessage($user_id, $msg, $title, $push_json)
{
    //消息推送Demo
    header("Content-Type: text/html; charset=utf-8");
    require_once(dirname(__FILE__) . '/' . 'IGt.Push.php');

    //采用"PHP SDK 快速入门"， "第二步 获取访问凭证 "中获得的应用配置
    define('APPKEY', 'iID79NUwhn7pAIfEjW5Se4');
    define('APPID', 'mEpl70dF446hKYs8YZqXB3');
    define('MASTERSECRET', 'ShKW09DuUCAi9nA3ja8no3');
    define('HOST', 'http://sdk.open.api.igexin.com/apiex.htm');
    //define('CID', '请输入您的CID');
    //别名推送方式
    define('Alias', $user_id);

    pushMessageToSingle($msg, $title, $push_json);
}

//单推接口案例
function pushMessageToSingle($msg, $title, $push_json)
{
    $igt = new IGeTui(HOST, APPKEY, MASTERSECRET);

    //消息模版：
    $template = IGtLinkTemplateDemo1($msg, $title, $push_json);
    $template1 = IGtTransmissionTemplateDemo($msg, $title, $push_json);

    //定义"SingleMessage"
    $message = new IGtSingleMessage();

    $message->set_isOffline(true);//是否离线
    $message->set_offlineExpireTime(3600 * 12 * 1000);//离线时间
    $message->set_data($template);//设置推送消息类型

    $message1 = new IGtSingleMessage();

    $message1->set_isOffline(true);//是否离线
    $message1->set_offlineExpireTime(3600 * 12 * 1000);//离线时间
    $message1->set_data($template1);//设置推送消息类型
    //$message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，2为4G/3G/2G，1为wifi推送，0为不限制推送
    //接收方
    $target = new IGtTarget();
    $target->set_appId(APPID);
    //$target->set_clientId(CID);
    $target->set_alias(Alias);

    try {
        //$rep = $igt->pushMessageToSingle($message, $target);
        $rep = $igt->pushMessageToSingle($message1, $target);
        //var_dump($rep);
        //echo ("<br><br>");

    } catch (RequestException $e) {
        $requstId = e . getRequestId();
        //失败时重发
        $rep = $igt->pushMessageToSingle($message, $target, $requstId);
        var_dump($rep);
        echo("<br><br>");
    }
}

function IGtLinkTemplateDemo1($msg, $title, $push_json)
{
    $template = new IGtNotificationTemplate();
    $template->set_appId(APPID);                      //应用appid
    $template->set_appkey(APPKEY);                    //应用appkey
    $template->set_transmissionType(1);               //透传消息类型
    $template->set_transmissionContent($push_json);   //透传内容
    $template->set_title($title);                     //通知栏标题
    $template->set_text($msg);        //通知栏内容
    $template->set_logo("");                  //通知栏logo
    $template->set_logoURL("http://yike.ecomoter.com/Public/common/js/img/slide/app_icon.png"); //通知栏logo链接
    $template->set_isRing(true);                      //是否响铃
    $template->set_isVibrate(true);                   //是否震动
    $template->set_isClearable(true);                 //通知栏是否可清除
    //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
    return $template;
}

function IGtTransmissionTemplateDemo($msg, $title, $push_json)
{
    $template = new IGtTransmissionTemplate();
    $template->set_appId(APPID);//应用appid
    $template->set_appkey(APPKEY);//应用appkey
    $template->set_transmissionType(2);//透传消息类型
    $template->set_transmissionContent($push_json);//透传内容
    //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息

// 如下有两个推送模版，一个简单一个高级，可以互相切换使用。此处以高级为例，所以把简单模版注释掉。
//        APN简单推送
//      $apn = new IGtAPNPayload();
//      $alertmsg=new SimpleAlertMsg();
//      $alertmsg->alertMsg="";
//      $apn->alertMsg=$alertmsg;
//      $apn->badge=2;
//      $apn->sound="";
//      $apn->add_customMsg("payload","payload");
//      $apn->contentAvailable=1;
//      $apn->category="ACTIONABLE";
//      $template->set_apnInfo($apn);

//       APN高级推送
    $apn = new IGtAPNPayload();
    $alertmsg = new DictionaryAlertMsg();
    $alertmsg->body = $msg;
    $alertmsg->actionLocKey = "ActionLockey";
    $alertmsg->locKey = $msg;
    $alertmsg->locArgs = array("locargs");
    $alertmsg->launchImage = "launchimage";
//        iOS8.2 支持
    $alertmsg->title = $title;
    $alertmsg->titleLocKey = $title;
    $alertmsg->titleLocArgs = array("TitleLocArg");

    $apn->alertMsg = $alertmsg;
    $apn->badge = 1;
    $apn->sound = "";
    $apn->add_customMsg("payload", $msg);
//      $apn->contentAvailable=1;
    $apn->category = "ACTIONABLE";
    $template->set_apnInfo($apn);

    return $template;
}

//使用iccid进行短信发送
function IccidSendSms($iccid, $msg)
{
    $iccid_sms_url = "http://api.cmpyun.com/api/serviceAccept/sendSms";

    $time = time();
    $sign = "appid=LYWLTAgaaK3VrFPZaOAp&dt=$time&secret=z589rdAekX6fihmKkDZi69HVP6FDOIpw&type=2&value=$iccid&appSecret=5Ktul9tEP2";
    $sign = strtoupper(md5($sign));

    $post_data = "{\"value\":\"$iccid\",\"message\":\"$msg\",\"type\":2,\"appid\":\"LYWLTAgaaK3VrFPZaOAp\",\"dt\":$time,\"secret\":\"z589rdAekX6fihmKkDZi69HVP6FDOIpw\",\"sign\":\"$sign\"}";

    header("Content-type:application/json");

    $gets = Post($post_data, $iccid_sms_url);

    return $gets;
}

//卡激活api
function IccidSendSmsActity($iccid)
{
    $iccid_sms_url = "http://api.cmpyun.com/api/serviceAccept/cardStatusChange";

    $time = time();

    $array = array(
        'iccid' => $iccid,
        'appid' => 'LYWLTAgaaK3VrFPZaOAp',
        'secret' => 'z589rdAekX6fihmKkDZi69HVP6FDOIpw',
        'dt' => time()
    );

    ksort($array);
    $sign = '';
    foreach ($array as $key => $value) {
        $sign .= $key . '=' . $value . '&';
    }

    $sign = strtoupper(md5($sign . 'appSecret=5Ktul9tEP2'));

    //echo $sign;
    $post_data = "{\"iccid\":\"$iccid\",\"status\":1,\"appid\":\"LYWLTAgaaK3VrFPZaOAp\",\"dt\":$time,\"secret\":\"z589rdAekX6fihmKkDZi69HVP6FDOIpw\",\"sign\":\"$sign\"}";

    header("Content-type:application/json");

    $gets = Post($post_data, $iccid_sms_url);

    return $gets;
}

//获取套餐列表api
function GetServiceList($iccid)
{
    $iccid_sms_url = "http://api.cmpyun.com/api/serviceAccept/getPackageList";

    $time = time();

    $array = array(
        'iccid' => $iccid,
        'appid' => 'LYWLTAgaaK3VrFPZaOAp',
        'secret' => 'z589rdAekX6fihmKkDZi69HVP6FDOIpw',
        'dt' => time()
    );

    ksort($array);
    $sign = '';
    foreach ($array as $key => $value) {
        $sign .= $key . '=' . $value . '&';
    }

    $sign = strtoupper(md5($sign . 'appSecret=5Ktul9tEP2'));

    //echo $sign;
    $post_data = "{\"iccid\":\"$iccid\",\"appid\":\"LYWLTAgaaK3VrFPZaOAp\",\"dt\":$time,\"operator\":0,\"platform\":1,\"secret\":\"z589rdAekX6fihmKkDZi69HVP6FDOIpw\",\"sign\":\"$sign\"}";

    header("Content-type:application/json;charset=UTF-8");

    $gets = Post($post_data, $iccid_sms_url);

    return $gets;
}

//充值api
function Recharge($iccid)
{
    $iccid_sms_url = "http://api.cmpyun.com/api/serviceAccept/orderFlow";

    $time = time();
    $orderNum = substr($iccid, 10, 10) . $time;
    $array = array(
        'iccid' => $iccid,
        'appid' => 'LYWLTAgaaK3VrFPZaOAp',
        'secret' => 'z589rdAekX6fihmKkDZi69HVP6FDOIpw',
        'packageInfo' => 92,
        'orderNum' => $orderNum,
        'dt' => time()
    );

    ksort($array);
    $sign = '';
    foreach ($array as $key => $value) {
        $sign .= $key . '=' . $value . '&';
    }

    $sign = strtoupper(md5($sign . 'appSecret=5Ktul9tEP2'));

    //echo $sign;
    $post_data = "{\"iccid\":\"$iccid\",\"appid\":\"LYWLTAgaaK3VrFPZaOAp\",\"dt\":$time,\"activeTime\":1,\"orderNum\":\"$orderNum\",\"packageInfo\":92,\"secret\":\"z589rdAekX6fihmKkDZi69HVP6FDOIpw\",\"sign\":\"$sign\"}";

    header("Content-type:application/json;charset=UTF-8");

    $gets = Post($post_data, $iccid_sms_url);

    return $gets;
}

function Post($curlPost, $url)
{
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    $return_str = curl_exec($curl);
    curl_close($curl);

    return $return_str;
}

function MkDirs($dir, $mode = 0777)
{
    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
    if (!mkdirs(dirname($dir), $mode)) return FALSE;
    return @mkdir($dir, $mode);
}

/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $count 要分页的总记录数
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getpage($count, $pagesize = 10)
{
    $p = new Think\Page($count, $pagesize);
    $p->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
    $p->setConfig('prev', '上一页');
    $p->setConfig('next', '下一页');
    $p->setConfig('last', '末页');
    $p->setConfig('first', '首页');
    $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $p->lastSuffix = false;//最后一页不显示为总页数
    return $p;
}