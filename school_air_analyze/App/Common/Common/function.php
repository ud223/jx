<?php

/**
 *
 * 版权所有：素材火<qwadmin.sucaihuo.com>
 * 作    者：素材水<hanchuan@sucaihuo.com>
 * 日    期：2015-09-17
 * 版    本：1.0.0
 * 功能说明：模块公共文件。
 *
 * */
function UpImage($callBack = "image", $width = 100, $height = 100, $image = "") {

    echo '<iframe scrolling="no" frameborder="0" border="0" onload="this.height=this.contentWindow.document.body.scrollHeight;this.width=this.contentWindow.document.body.scrollWidth;" width=' . $width . ' height="' . $height . '"  src="' . U('Upload/uploadpic', array('Width' => $width, 'Height' => $height, 'BackCall' => $callBack)) . '"></iframe>
         <input type="hidden" ' . 'value = "' . $image . '"' . 'name="' . $callBack . '" id="' . $callBack . '">';
}

function BatchImage($callBack = "image", $width = 100, $height = 100, $image = "") {

    echo '<iframe scrolling="no" frameborder="0" border="0" width=100% onload="this.height=this.contentWindow.document.body.scrollHeight;" src="' . U('Upload/batchpic', array('Width' => $width, 'Height' => $height, 'BackCall' => $callBack)) . '"></iframe>
		<input type="hidden" ' . 'value = "' . $image . '"' . 'name="' . $callBack . '" id="' . $callBack . '">';
}

/*
 * 函数：网站配置获取函数
 * @param  string $k      可选，配置名称
 * @return array          用户数据
 */

function setting($k = 'all') {
    $cache = S($k);
    //如果缓存不为空直接返回
    if (null != $cache) {
        return $cache;
    }
    $data = '';
    $setting = M('setting');
    //判断是否查询全部设置项
    if ($k == 'all') {
        $setting = $setting->field('k,v')->select();
        foreach ($setting as $v) {
            $config[$v['k']] = $v['v'];
        }
        $data = $config;
    } else {
        $result = $setting->where("k='{$k}'")->find();
        $data = $result['v'];
    }
    //建立缓存
    if ($data) {
        S($k, $data);
    }
    return $data;
}

/**
 * 函数：格式化字节大小
 * @param  number $size 字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) {
        $size /= 1024;
    }
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 函数：加密
 * @param string            密码
 * @return string           加密后的密码
 */
function password($password) {
    /*
     * 后续整强有力的加密函数
     */
    return md5('Q' . $password . 'W');
}

/**
 * 随机字符
 * @param number $length 长度
 * @param string $type 类型
 * @param number $convert 转换大小写
 * @return string
 */
function random($length = 6, $type = 'string', $convert = 0) {
    $config = array(
        'number' => '1234567890',
        'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
        'all' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
    );

    if (!isset($config[$type])) {
        $type = 'string';
    }
    $string = $config[$type];

    $code = '';
    $strlen = strlen($string) - 1;
    for ($i = 0; $i < $length; $i++) {
        $code .= $string{mt_rand(0, $strlen)};
    }
    if (!empty($convert)) {
        $code = ($convert > 0) ? strtoupper($code) : strtolower($code);
    }
    return $code;
}

//获取所有的子级id
function category_get_sons($sid, &$array = array()) {
    //获取当前sid下的所有子栏目的id
    $categorys = M("category")->where("pid = {$sid}")->select();

    $array = array_merge($array, array($sid));
    foreach ($categorys as $category) {
        category_get_sons($category['id'], $array);
    }
    $data = $array;
    unset($array);
    return $data;
}

/**
 * 获取文章url地址
 * url结构：ttp://wwww.qwadmin.com/分类/子分类/子分类/id.html
 * 使用方法：模板中{:articleUrl(array('aid'=>$val['aid']))}
 *
 *
 * @param $data
 * @return $string
 */
function articleUrl($data) {
    //如果数组为空直接返回空字符
    if (!$data) {
        return '';
    }
    //如果参数错误直接返回空字符
    if (!isset($data['aid'])) {
        return '';
    }

    $aid = (int) $data['aid'];

    //获取文章信息
    $article = M('article')->where(array('aid' => $aid))->find();
    //获取当前内容所在分类
    $category = M('category')->where(array('id' => $article['sid']))->find();
    //获取当前分类
    $categoryUrl = $category['dir'];
    //遍历获取当前文章所在分类的有上级分类并且组合url
    while ($category['pid'] <> 0) {
        $category = M('category')->where(array('id' => $category['pid']))->find();
        $categoryUrl = $category['dir'] . "/" . $categoryUrl;
        //如果上级分类已经无上级分类则退出
    }

    $categoryUrl = __ROOT__ . "/" . $categoryUrl;
    //组合文章url
    $articleUrl = $categoryUrl . '/' . $aid . ".html";
    return $articleUrl;
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 */
function get_nav_url($url) {
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;
        default:
            $url = U($url);
            break;
    }
    return $url;
}

function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
}
function base64ToImage($image_code) {
    $photo = array();

//    $s = base64_decode(str_replace('data:image/jpeg;base64,', '', $image_code));

    $base64_string= explode(',', $image_code); //截取data:image/png;base64, 这个逗号后的字符
    $data= base64_decode($base64_string[1]); //对截取后的字符使用base64_decode进行解码

    $pic_name = build_order_no() . '.jpg';

    $path = '/Public/uploads/' . $pic_name;

    $full_pic_name = '/var/www/myatlist'. $path;

    $file_count = file_put_contents($full_pic_name, $data);
//    echo ajax_api(true, 0,'上传图片失败1!', $file_count); exit;
    if (!$file_count) {
        return false;
    }

    $photo['photo_name'] = $pic_name;
    $photo['path'] = $path;
    $photo['full_path'] = $full_pic_name;
    $photo['file_name'] = $pic_name;

    return $photo;
}