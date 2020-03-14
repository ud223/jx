<?php
return array(
    'URL_HTML_SUFFIX'=>'',
    'DEFAULT_MODULE' => 'Admin',    //默认模块
    'URL_MODEL' => '1', //URL模式：普通模式 0；PATHINFO模式 1；REWRITE模式 2；兼容模式 3
    
    //数据库配置 Begin
    'DB_TYPE'=> 'mysql',    // 数据库类型
    'DB_NAME'=>'bttencms',  // 数据库名称   

    //本地数据库
    'DB_HOST'=> 'localhost',    // 数据库服务器地址   
    'DB_USER'=>'root',  // 数据库用户名   
    'DB_PWD'=>'root',   // 数据库密码  

    
    'DB_PREFIX'=>  'btten_', // 数据库表前缀
    'DB_PORT'=>'3306',  // 数据库端口   
    'DB_CHARSET'=>'utf8',   //数据库编码 
    //数据库配置 End

    //比较符
    'COMPARE'=>array(
        'eq'=>'=',
        'neq'=>'!=',
        'gt'=>'>',
        'egt'=>'>=',
        'lt'=>'<',
        'elt'=>'<=',
    ),
    
    //验证码配置 Begin
    'VERIFY' => array(
        'useImgBg' => true, //是否使用背景图片 默认为false
        'fontSize' => 36,   //验证码字体大小（像素） 默认为25
        'useCurve'=>false,   //是否使用混淆曲线 默认为true
        'useNoise'=>true,   //是否添加杂点 默认为true
        'imageW'=>300,    //验证码宽度 设置为0为自动计算
        'imageH'=>0,    //验证码高度 设置为0为自动计算
        'length' => 4,  //验证码位数
        'fontttf' => '4.ttf', //指定验证码字体 默认为随机获取
        'seKey' => 'btten', //验证码的加密密钥
        //'useZh' => false,   //是否使用中文验证码
        //'codeSet' => '2345678abcdefghjkmnpqrstwxyz',
    ),
    //验证码配置 End
    
    //缓存配置
    'CACHE_CONFIG'=>array(
        'LEFT_MENU_DIR'=>'menu/leftmenu',   //左侧导航缓存目录
        'LEFT_APP_MENU_DIR'=>'menu/left_app_menu',   //左侧导航缓存目录
        'APP_MENU_DIR'=>'menu/appmenu',   //应用缓存目录
        'APP_MENU_DLL_DIR'=>'menu/appmenu_dll',   //应用缓存目录
        'REGION'=>'region/region'   //省市区缓存目录
    ),
);