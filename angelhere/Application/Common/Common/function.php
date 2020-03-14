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
function is_login(){
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

function WriteLog($_str, $_type="DEBUG"){
    Think\Log::write($_str, $_type);
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author
 */
function is_administrator($uid = null){
    $uid = is_null($uid) ? is_login() : $uid;
    return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author
 */
function str2arr($str, $glue = ','){
    return explode($glue, $str);
}


/**
 * 加密
 * @param intger $type 类型
 * @param bool $all 是否返回全部类型
 * @author
 */
function fit_md5($string, $ext = 'wq3mdju&#*'){
    return md5(md5($string).$ext);
}


/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

function fit_api($result,$code,$msg,$data){
    $json = array(
        'result'=>$result,
        'code'=>$code,
        'msg'=>$msg,
        'data'=>$data
    );

    $str = json_encode($json);

    return $str;
}

