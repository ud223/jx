<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

//分页配置 Begin
define('PAGE_LIST_ROWS', 10);
define('PAGE_NUM_SHOWN', 10);
//分页配置 End

define('SITE_URL', 'http://test.360guanggu.com/btcms');

define('SITE_PATH', dirname(__FILE__));

define('DATA_ROOT_PATH', SITE_PATH . '/Public');

//上传文件目录 Begin
define('UPLOAD_PATH', '/upload');

//图片上传文件目录 Begin
define('UPLOAD_IMAGES_PATH', UPLOAD_PATH . '/images');

//默认图片文件目录 Begin
define('DEFAULT_IMAGES_PATH', UPLOAD_IMAGES_PATH . '/default');

//文件上传文件目录 Begin
define('UPLOAD_FILES_PATH', UPLOAD_PATH . '/files');

//默认文件文件目录 Begin
define('DEFAULT_FILES_PATH', UPLOAD_FILES_PATH . '/default');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

// 定义应用目录
define('APP_PATH','./App/');

// 缓存目录设置
define ( 'RUNTIME_PATH', './Runtime/' );

//定义公共模块的目录，放到应用目录外
define('COMMON_PATH','./Common/');

//定义支付宝异步通知页面路径
define('ALIPAY_NOTIFY', SITE_URL . '/index.php/Payment/Alipay/alipay_notify');

//定义支付宝异步通知页面路径 : SITE_URL . '/index.php/Payment/Alipay/alipay_return'
define('ALIPAY_RETURN', '');

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单