<?php
return array(
  'DEFAULT_MODULE' => 'CustomerRreservationSystem',    //默认模块
  'URL_CASE_INSENSITIVE' => false,
  'URL_MODEL' => '1', //URL模式：普通模式 0；PATHINFO模式 1；REWRITE模式 2；兼容模式 3
  'APP_DEBUG' => true,
  'DEFAULT_CHARSET' => 'utf-8',

  //region 数据库配置
  'DB_TYPE'=> 'mysql',    // 数据库类型

  //本地数据库
  'DB_HOST'=> 'rdsa2y1yp95n3965rug4o.mysql.rds.aliyuncs.com',    // 数据库服务器地址
  'DB_NAME'=>'zhihui_crs',  // 数据库名称
  'DB_USER'=>'my',  // 数据库用户名
  'DB_PWD'=>'123EWQasd',   // 数据库密码
  'DB_PREFIX'=>  'zhihui_', // 数据库表前缀
  'DB_PORT'=>'3306',  // 数据库端口
  'DB_CHARSET'=>'utf8',   //数据库编码
  //endregion 数据库配置
);