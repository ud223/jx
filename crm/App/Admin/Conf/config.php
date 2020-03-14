<?php
return array(
    'TMPL_ACTION_ERROR' => 'Public:error',
    'TMPL_ACTION_SUCCESS' => 'Public:success',

    //图片文件上传配置 Begin
    'UPLOAD_IMAGE' => array(
        'size'=>5242880,   //单位：字节, 共5M
        'type'=>'jpg|jpeg|png|gif', //允许上传的类型
        'desc'=>'image/png, image/gif, image/jpg, image/jpeg',  //上传类型说明
    ),
    //图片文件上传配置 Begin
    
    //文件上传配置 Begin
    'UPLOAD_FIEL' => array(
        'size'=>5242880,   //单位：字节, 共5M
        'type'=>'rar|zip|pdf',  //允许上传的类型
        'desc'=>'rar|zip|pdf',  //上传类型说明
    ),
    //文件上传配置 Begin
    
    'AUTH_CONFIG' => array(  
        'AUTH_ON' => true, //认证开关  
        'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证。  
        'AUTH_GROUP' => 'btten_auth_group', //用户组数据表名  
        'AUTH_GROUP_ACCESS' => 'btten_auth_group_access', //用户组明细表  
        'AUTH_RULE' => 'btten_auth_rule', //权限规则表  
        'AUTH_USER' => 'btten_auth_admin',//管理员信息表
        'USER_AUTH_KEY' =>  'authId',	// 用户认证SESSION标记
    ),
    
    //系统版权配置
    'support'=>'2014 &copy; test',
);