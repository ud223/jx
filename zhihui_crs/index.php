<?php
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

// 定义应用目录
define('APP_PATH','./Application/');

// 缓存目录设置
define('RUNTIME_PATH', './Runtime/' );

//定义公共模块的目录，放到应用目录外
define('COMMON_PATH','./Common/');

//region 自定义全局
define('SITE_PATH', dirname(__FILE__));
define('IMAGE_DOMIAN', "http://localhost/zhihui/");
//endregion 自定义全局

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';