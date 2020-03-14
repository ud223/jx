<?php
return array(
	//'配置项'=>'配置值'
    'project_name'=>'E客智慧电动车',
    'MODULE_ALLOW_LIST' => array('Home', 'Coach',  'Index', 'Admin', 'Manage', 'User', 'Public'),
    'URL_MODEL'=>3,

    'APP_SUB_DOMAIN_DEPLOY'   =>    1,  // 开启子域名配置
    'APP_SUB_DOMAIN_RULES'    =>    array(
        'admin.ecomoter.com'    => 'Admin',       // admin.ecomoter.com域名指向后台管理模块 webetter100  ecomoter.com
        'mall.ecomoter.com'     => 'Mall',        // mall.ecomoter.com域名指向商城模块 test.yike.com webetter100
        'bbs.ecomoter.com'      => 'Bbs',         // bbs.yike.com域名指向论坛模块
        'yike.ecomoter.com'     => 'Home',        // yike.ecomoter.com域名指向主模块  yike.webetter100.com
        'www.ecomoter.com' => 'Mall'
    ),

    /* 数据库配置 */
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => 'rm-wz905k146lk63hxdfo.mysql.rds.aliyuncs.com', // 服务器地址 rm-wz905k146lk63hxdfo.mysql.rds.aliyuncs.com  192.168.0.118
    'DB_USER'   => 'yike', // 用户名 yike root
    'DB_PWD'    => 'Yike223adsn',  // 密码  123EWQasd  1111
    'DB_PORT'   => '3306', // 端口
    'DB_NAME'   => 'yike', // 数据库名
    'DB_PREFIX' => 'fit_', // 数据库表前缀
);