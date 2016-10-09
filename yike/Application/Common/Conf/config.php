<?php
return array(
	//'配置项'=>'配置值'
    'project_name'=>'易客电动车',
    'MODULE_ALLOW_LIST' => array('Home', 'Coach',  'Index', 'Admin', 'Manage', 'User', 'Public'),
    'URL_MODEL'=>2,

    'APP_SUB_DOMAIN_DEPLOY'   =>    1,  // 开启子域名配置
    'APP_SUB_DOMAIN_RULES'    =>    array(
        'admin.webetter100.com'    => 'Admin',       // admin.yike.com域名指向后台管理模块 webetter100
        'mall.webetter100.com'     => 'Mall',        // mall.yike.com域名指向商城模块 webetter100
        'bbs.webetter100.com'      => 'Bbs',         // bbs.yike.com域名指向论坛模块
        'test.webetter100.com'     => 'Home',        // test.yike.com域名指向主模块
    ),

    /* 数据库配置 */
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => 'rdsa2y1yp95n3965rug4o.mysql.rds.aliyuncs.com', // 服务器地址 rdsa2y1yp95n3965rug4o.mysql.rds.aliyuncs.com  192.168.0.148
    'DB_NAME'   => 'yike', // 数据库名
    'DB_USER'   => 'my', // 用户名 my root
    'DB_PWD'    => '123EWQasd',  // 密码  123EWQasd  1111
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'fit_', // 数据库表前缀
);