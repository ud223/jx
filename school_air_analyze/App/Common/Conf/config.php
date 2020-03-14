<?php
/**
 *
 * 日    期：2015-09-15
 * 版    本：1.0.0
 * 功能说明：配置文件。
 *
 **/
return array(
    //网站配置信息
    'URL' => 'http://'.$_SERVER['HTTP_HOST'], //网站根URL
    'COOKIE_SALT' => 'sucaihuo', //设置cookie加密密钥
    //备份配置
    'DB_PATH_NAME' => 'db',   //备份目录名称,主要是为了创建备份目录
    'DB_PATH' => './db/',     //数据库备份路径必须以 / 结尾；
    'DB_PART' => '20971520',  //该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M
    'DB_COMPRESS' => '1',         //压缩备份文件需要PHP环境支持gzopen,gzwrite函数        0:不压缩 1:启用压缩
    'DB_LEVEL' => '9',         //压缩级别   1:普通   4:一般   9:最高
    //扩展配置文件
    'LOAD_EXT_CONFIG' => 'db',
    
    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 2, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符
    'COMMON_TOKEN'         => 'token_zhxh13',//api访问通用token

    /* SESSION全局配置 */
    'SESSION_OPTIONS' => array(
        'path' => RUNTIME_PATH . 'Temp/',
        'use_cookies' => 1,         //是否在客户端用 cookie 来存放会话 ID，1是开启
        'use_trans_sid' => true,    //跨页传递
        'expire' => 43200,
    ),
);