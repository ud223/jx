<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * ThinkPHP 简体中文语言包
 */
return array(
  //region 核心语言变量
  '_MODULE_NOT_EXIST_'     => '无法加载模块',
  '_CONTROLLER_NOT_EXIST_' =>	'无法加载控制器',
  '_ERROR_ACTION_'         => '非法操作',
  '_LANGUAGE_NOT_LOAD_'    => '无法加载语言包',
  '_TEMPLATE_NOT_EXIST_'   => '模板不存在',
  '_MODULE_'               => '模块',
  '_ACTION_'               => '操作',
  '_MODEL_NOT_EXIST_'      => '模型不存在或者没有定义',
  '_VALID_ACCESS_'         => '没有权限',
  '_XML_TAG_ERROR_'        => 'XML标签语法错误',
  '_DATA_TYPE_INVALID_'    => '非法数据对象！',
  '_OPERATION_WRONG_'      => '操作出现错误',
  '_NOT_LOAD_DB_'          => '无法加载数据库',
  '_NO_DB_DRIVER_'         => '无法加载数据库驱动',
  '_NOT_SUPPORT_DB_'       => '系统暂时不支持数据库',
  '_NO_DB_CONFIG_'         => '没有定义数据库配置',
  '_NOT_SUPPORT_'          => '系统不支持',
  '_CACHE_TYPE_INVALID_'   => '无法加载缓存类型',
  '_FILE_NOT_WRITABLE_'   => '目录（文件）不可写',
  '_METHOD_NOT_EXIST_'     => '方法不存在！',
  '_CLASS_NOT_EXIST_'      => '实例化一个不存在的类！',
  '_CLASS_CONFLICT_'       => '类名冲突',
  '_TEMPLATE_ERROR_'       => '模板引擎错误',
  '_CACHE_WRITE_ERROR_'    => '缓存文件写入失败！',
  '_TAGLIB_NOT_EXIST_'     => '标签库未定义',
  '_OPERATION_FAIL_'       => '操作失败！',
  '_OPERATION_SUCCESS_'    => '操作成功！',
  '_SELECT_NOT_EXIST_'     => '记录不存在！',
  '_EXPRESS_ERROR_'        => '表达式错误',
  '_TOKEN_ERROR_'          => '表单令牌错误',
  '_RECORD_HAS_UPDATE_'    => '记录已经更新',
  '_NOT_ALLOW_PHP_'        => '模板禁用PHP代码',
  '_PARAM_ERROR_'          => '参数错误或者未定义',
  '_ERROR_QUERY_EXPRESS_'  => '错误的查询条件',
  //endregion 核心语言变量
  
  //region 自定义语言变量
  
  //region 通用
  '_VERIFY_CODE_ERROR_'  => '验证码错误',
  '_VERIFY_CODE_NULL_'  => '请输入验证码',

  '_SAVE_DATA_FAILURE_' => '保存失败',
  '_SAVE_DATA_SUCCEED_' => '保存成功',

  '_DELETE_DATA_FAILURE_' => '删除失败',
  '_DELETE_DATA_SUCCEED_' => '删除成功',

  '_MOBILE_PHONE_ERROR_'=>'手机号格式错误',
  '_QQ_ERROR_'=>'QQ号错误',
  '_EMIAL_ERROR_'=>'电子邮件地址错误',

  '_LOGIN_SUCCEED_'  => '登陆成功',
  '_LOGIN_FAILURE_'  => '登陆失败',
  '_LOGIN_AGAIN_'  => '请重新登陆',
  '_LOGIN_NAME_NULL_'  => '请输入登录帐号',
  '_LOGIN_PASSWORD_NULL_'  => '请输入登录密码',
  '_LOGIN_TYPE_ERROR_'  => '帐号类型错误',
  '_LOGIN_PASSWORD_ERROR_'  => '账号或密码错误',

  '_LOGOUT_SUCCEED_'  => '注销成功',
  '_LOGOUT_FAILURE_'  => '注销失败',

  '_ACCOUNT_ERROR_'=>'帐号错误',
  '_ACCOUNT_NOT_PERMISSION_'=>'帐号没有访问权限',
  
  //region 文件上传
  '_UPLOAD_FAILURE_' => '上传失败',
  '_UPLOAD_SUCCEED_' => '上传成功',
  
  '_UPLOAD_FILE_NOT_EXIST_'=>'上传的文件不存在',

  //endregion 文件上传
  
  //endregion 通用
  
  //region 管理员
  '_ADMIN_USER_ACT_ADD_' => '添加管理员',
  '_ADMIN_USER_ADD_SUCCEED_' => '管理员添加成功',
  '_ADMIN_USER_ADD_FAILURE_' => '管理员添加失败',

  '_ADMIN_USER_ACT_EDIT_' => '修改管理员',
  '_ADMIN_USER_EDIT_SUCCEED_' => '管理员修改成功',
  '_ADMIN_USER_EDIT_FAILURE_' => '管理员修改失败',

  '_ADMIN_USER_ACT_DEL_' => '删除管理员',
  '_ADMIN_USER_DEL_SUCCEED_' => '管理员删除成功',
  '_ADMIN_USER_DEL_FAILURE_' => '管理员删除失败',

  '_ADMIN_ID_ERROR_'  => '管理员编号错误',
  '_ADMIN_ID_NULL_'  => '管理员编号不能为空',
  '_ADMIN_ACCOUNT_NULL_'  => '账号不能为空',
  '_ADMIN_ACCOUNT_EXIST_'  => '账号名称已被使用',
  '_ADMIN_ACCOUNT_MAX_LENGTH_ERROR_'  => '登陆账号长度不能超过%d个字符',
  '_ADMIN_REAL_NAME_MAX_LENGTH_ERROR_'  => '管理员真实姓名长度不能超过%d个字符',
  '_ADMIN_PASSWORD_NULL_'  => '密码不能为空',
  '_ADMIN_PASSWORD_LENGTH_NULL_'  => '密码长度不能小于6位',
  '_ADMIN_NOT_EXIST_'  => '账号不存在',
  '_ADMIN_ACCOUNT_DISABLE_'  => '该帐号暂时不可用',
  '_ADMIN_CURR_PASSWORD_NULL_'  => '请输入当前登陆密码',
  '_ADMIN_CURR_PASSWORD_ERROR_'  => '当前登陆密码错误',
  '_ADMIN_NEW_PASSWORD_NULL_'  => '请输入新的登陆密码',
  '_ADMIN_OLD_NEW_PASSWORD_NOT_EQUAL_'  => '两次输入的密码不一致',
  '_ADMIN_PASSWORD_CHANGE_FAILURE_'  => '管理员密码修改失败',
  '_ADMIN_PASSWORD_CHANGE_SUCCESS_'  => '管理员密码修改成功',
  //endregion 管理员

  //region 管理员组
  '_ADMIN_GROUP_ACT_ADD_' => '添加管理员组',
  '_ADMIN_GROUP_ADD_SUCCEED_' => '管理员组添加成功',
  '_ADMIN_GROUP_ADD_FAILURE_' => '管理员组添加失败',

  '_ADMIN_GROUP_ACT_EDIT_' => '修改管理员组',
  '_ADMIN_GROUP_EDIT_SUCCEED_' => '管理员组修改成功',
  '_ADMIN_GROUP_EDIT_FAILURE_' => '管理员组修改失败',

  '_ADMIN_GROUP_ACT_DEL_' => '删除管理员组',
  '_ADMIN_GROUP_DEL_SUCCEED_' => '管理员组删除成功',
  '_ADMIN_GROUP_DEL_FAILURE_' => '管理员组删除失败',

  '_ADMIN_GROUP_ID_NULL_'  => '管理员组编号不能为空',
  '_ADMIN_GROUP_ID_ERROR_'  => '管理员组编号错误',
  '_ADMIN_GROUP_DATA_NOT_EXIST_'  => '管理员组不存在',
  '_ADMIN_GROUP_NAME_NULL_'  => '管理员组名称不能为空',
  '_ADMIN_GROUP_NAME_MAX_LENGTH_ERROR_'  => '管理员组名称长度不能超过%d个字符',
  '_ADMIN_GROUP_NAME_EXIST_'  => '管理员组名称已经存在',
  '_ADMIN_GROUP_NAME_EQ_OLD_NAME_'  => '管理员组名称与原名称相同',
  //endregion 管理员组
  
  //region 用户账号
  '_USER_ID_NULL_' => '用户账号ID不能为空',
  '_USER_ID_ERROR_' => '用户账号ID错误',
  '_USER_PARENT_ID_NULL_' => '父级用户账号ID不能为空',
  '_USER_PARENT_ID_ERROR_' => '父级用户账号ID错误',
  '_USER_PARENT_NOT_EXIST_' => '父级用户账号不存在',
  '_USER_NOT_EXIST_' => '用户账号不存在',
  '_USER_ACCOUNT_NAME_NULL_' => '必须输入用户账号名',
  '_USER_ACCOUNT_NAME_EXIST_' => '用户账号名已存在',
  '_USER_ACCOUNT_MAX_LENGTH_ERROR_'  => '用户账号长度不能超过%d个字符',
  '_USER_REAL_NAME_NULL_'  => '必须输入用户的真实姓名',
  '_USER_REAL_NAME_MAX_LENGTH_ERROR_'  => '真实姓名长度不能超过%d个字符',
  '_USER_PASSWORD_NULL_'  => '密码不能为空',
  '_USER_PASSWORD_LENGTH_NULL_'  => '密码长度不能小于6位',
  '_USER_BASE_SALARY_ERROR_'  => '基本薪资错误',

  '_USER_GROUP_NOT_SELLER_CAPTAIN_'  => '用户不属于团队长组',
  '_USER_GROUP_NOT_SELLER_MEMBER_'  => '用户不属于客户经理组',

  '_SELLER_CAPTAIN_ID_NULL_' => '团队长ID不能为空',
  '_SELLER_CAPTAIN_ID_ERROR_' => '团队长ID错误',
  '_SELLER_CAPTAIN_NOT_EXIST_' => '团队长不存在',

  '_SELLER_MEMBER_ID_NULL_' => '客户经理ID不能为空',
  '_SELLER_MEMBER_ID_ERROR_' => '客户经理ID错误',
  '_SELLER_MEMBER_NOT_EXIST_' => '客户经理不存在',
  '_SELLER_MEMBER_NOT_SELLER_CAPTAIN_USER_' => '客户经理不属于此团队长',

  //endregion 用户账号
  
  //region 经纪公司
  '_BROKER_COMPANY_ID_NULL_' => '经纪公司ID不能为空',
  '_BROKER_COMPANY_ID_ERROR_' => '经纪公司ID错误',
  '_BROKER_COMPANY_NEED_SELECT_' => '请选择经纪公司',
  '_BROKER_COMPANY_NOT_EXIST_' => '经纪公司不存在',
  '_BROKER_COMPANY_NAME_NULL_' => '必须输入经纪公司名称',
  '_BROKER_COMPANY_NAME_EXIST_' => '经纪公司名称已存在',
  //endregion 经纪公司

  //region 经纪公司帐号
  '_BROKER_COMPANY_ACCOUNT_ID_NULL_' => '经纪公司帐号ID不能为空',
  '_BROKER_COMPANY_ACCOUNT_ID_ERROR_' => '经纪公司帐号ID错误',
  '_BROKER_COMPANY_ACCOUNT_NEED_SELECT_' => '请选择经纪公司帐号',
  '_BROKER_COMPANY_ACCOUNT_NOT_EXIST_' => '经纪公司帐号不存在',
  '_BROKER_COMPANY_ACCOUNT_NAME_NULL_' => '必须输入经纪公司登录帐号名',
  '_BROKER_COMPANY_ACCOUNT_NAME_EXIST_' => '经纪公司登录帐号名已存在',
  '_BROKER_COMPANY_ACCOUNT_MAX_LENGTH_ERROR_'  => '登陆账号长度不能超过%d个字符',
  '_BROKER_COMPANY_REAL_NAME_NULL_' => '必须输入经纪公司帐号的真实姓名',
  '_BROKER_COMPANY_REAL_NAME_MAX_LENGTH_ERROR_'  => '真实姓名长度不能超过%d个字符',
  '_BROKER_COMPANY_PASSWORD_NULL_'  => '密码不能为空',
  '_BROKER_COMPANY_PASSWORD_LENGTH_NULL_'  => '密码长度不能小于6位',
  //endregion 经纪公司帐号

  //region 经纪公司门店
  '_BROKER_COMPANY_STORE_ID_NULL_' => '经纪公司门店ID不能为空',
  '_BROKER_COMPANY_STORE_ID_ERROR_' => '经纪公司门店ID错误',
  '_BROKER_COMPANY_STORE_NEED_SELECT_' => '请选择经纪公司门店',
  '_BROKER_COMPANY_STORE_NOT_EXIST_' => '经纪公司门店不存在',
  '_BROKER_COMPANY_STORE_NAME_NULL_' => '必须输入经纪公司门店名称',
  '_BROKER_COMPANY_STORE_NAME_EXIST_' => '经纪公司门店名称已存在',
  //endregion 经纪公司门店

  //region 销售经理
  '_SELLER_MANAGER_ID_NULL_' => '销售经理ID不能为空',
  '_SELLER_MANAGER_ID_ERROR_' => '销售经理ID错误',
  '_SELLER_MANAGER_NOT_EXIST_' => '销售经理不存在',
  '_SELLER_MANAGER_ACCOUNT_NAME_NULL_' => '必须输入销售经理帐号名',
  '_SELLER_MANAGER_ACCOUNT_NAME_EXIST_' => '销售经理登录帐号名已存在',
  '_SELLER_MANAGER_ACCOUNT_MAX_LENGTH_ERROR_'  => '登陆账号长度不能超过%d个字符',
  '_SELLER_MANAGER_REAL_NAME_NULL_'  => '必须输入销售经理的真实姓名',
  '_SELLER_MANAGER_REAL_NAME_MAX_LENGTH_ERROR_'  => '真实姓名长度不能超过%d个字符',
  '_SELLER_MANAGER_PASSWORD_NULL_'  => '密码不能为空',
  '_SELLER_MANAGER_PASSWORD_LENGTH_NULL_'  => '密码长度不能小于6位',
  '_SELLER_MANAGER_BASE_SALARY_ERROR_'  => '基本薪资错误',
  //endregion 销售经理

  //region 客户
  '_CUSTOMER_ID_NULL_' => '客户ID不能为空',
  '_CUSTOMER_ID_ERROR_' => '客户ID错误',
  '_CUSTOMER_NOT_EXIST_' => '客户不存在',
  '_CUSTOMER_REAL_NAME_NULL_' => '必须输入客户姓名',
  '_CUSTOMER_GENDER_NULL_' => '必须选择客户性别',
  '_CUSTOMER_GENDER_ERROR_' => '客户性别错误',
  '_CUSTOMER_NATIONALITY_NULL_' => '必须输入客户国籍',
  '_CUSTOMER_IDCARD_TYPE_NULL_' => '必须选择证件类型',
  '_CUSTOMER_IDCARD_TYPE_ERROR_' => '证件类型错误',
  '_CUSTOMER_IDCARD_NO_NULL_' => '必须输入证件号',
  '_CUSTOMER_IDCARD_FILE_NULL_' => '必须上传证件照文件',
  '_CUSTOMER_BIRTHDAY_ERROR_' => '出生日期格式错误',
  '_CUSTOMER_ADDRESS_NULL_' => '必须输入客户详细地址',
  //endregion 客户

  //region 区域/省市区
  '_REGION_TYPE_ERROR_' => '省市区类型错误',
  
  //region 省份
  '_REGION_PROVINCE_ID_NULL_' => '必须选择省份',
  '_REGION_PROVINCE_ID_ERROR_' => '省份编号错误',
  '_REGION_PROVINCE_NAME_ERROR_' => '省份名称不能为空',
  //endregion 省份

  //region 城市
  '_REGION_CITY_ID_NULL_' => '必须选择城市',
  '_REGION_CITY_ID_ERROR_' => '城市编号错误',
  '_REGION_CITY_NAME_ERROR_' => '城市名称不能为空',
  '_REGION_CITY_LIST_NOT_GET_' => '未获取到城市列表',
  //endregion 城市

  //region 区/县
  '_REGION_DISTRICT_ID_NULL_' => '必须选择区/县',
  '_REGION_DISTRICT_ID_ERROR_' => '区/县编号错误',
  '_REGION_DISTRICT_NAME_ERROR_' => '区/县名称不能为空',
  '_REGION_DISTRICT_LIST_NOT_GET_' => '未获取到区/县列表',
  //endregion 区/县
  //endregion 区域/省市区

  //region 服务商
  '_SERVICE_PROVIDERS_ID_NULL_'  => '服务商编号不能为空',
  '_SERVICE_PROVIDERS_ID_ERROR_'  => '服务商编号错误',
  '_SERVICE_PROVIDERS_DATA_NOT_EXIST_'  => '服务商不存在',
  '_SERVICE_PROVIDERS_NAME_NULL_'  => '服务商名称不能为空',
  '_SERVICE_PROVIDERS_NAME_EXIST_'  => '服务商名称已经存在',
  '_SERVICE_PROVIDERS_NAME_EQ_OLD_NAME_'  => '服务商名称与原名称相同',
  //endregion 服务商

  //region 产品类型
  '_PRODUCT_TYPE_ID_NULL_'  => '产品类型编号不能为空',
  '_PRODUCT_TYPE_ID_ERROR_'  => '产品类型编号错误',
  '_PRODUCT_TYPE_NOT_EXIST_'  => '产品类型不存在',
  '_PRODUCT_TYPE_NAME_NULL_'  => '产品类型名称不能为空',
  '_PRODUCT_TYPE_NAME_EXIST_'  => '产品类型名称已经存在',
  //endregion 产品类型

  //region 产品
  '_PRODUCT_ID_NULL_'  => '产品编号不能为空',
  '_PRODUCT_ID_ERROR_'  => '产品编号错误',
  '_PRODUCT_NOT_EXIST_'  => '产品不存在',
  '_PRODUCT_NAME_NULL_'  => '产品名称不能为空',
  '_PRODUCT_NAME_EXIST_'  => '产品名称已经存在',
  '_PRODUCT_PRICE_ERROR_'  => '产品价格错误',
  '_PRODUCT_PAYMENT_TYPE_ERROR_'  => '产品支付方式错误',
  '_PRODUCT_PAY_TYPE_ERROR_'  => '产品缴费方式错误',
  '_PRODUCT_MIN_AGE_NULL_'  => '投保年龄不能为空',
  '_PRODUCT_MIN_AGE_ERROR_'  => '投保最小年龄必须是大于0的数字',
  '_PRODUCT_MAX_AGE_ERROR_'  => '投保最大年龄必须是大于0的数字',
  '_PRODUCT_MAX_MIN_ERROR_'  => '投保最大年龄必须大于最小年龄',
  '_PRODUCT_CAPTAIN_FIRST_RATE_ERROR_'  => '团队长首年提成比例错误',
  '_PRODUCT_CAPTAIN_NEXT_RATE_ERROR_'  => '团队长次年提成比例错误',
  '_PRODUCT_MEMBER_FIRST_RATE_ERROR_'  => '客户经理首年提成比例错误',
  '_PRODUCT_MEMBER_NEXT_RATE_ERROR_'  => '客户经理次年提成比例错误',
  '_PRODUCT_VALIDITY_YEAR_ERROR_'  => '产品保险期间错误',
  //endregion 产品
  
  //region 预约
  '_ORDER_SN_NULL_'  => '预约编号不能为空',
  '_ORDER_NOT_EXIST_'  => '预约不存在',
  '_ORDER_STATUS_ERROR_'  => '预约状态错误',
  '_ORDER_SIGNATORY_STATUS_CHNAGED_'  => '预约签约状态已改变',
  '_ORDER_ATTACHMENT_NULL_'  => '必须上传预约的附件',
  '_ORDER_RESERVATION_TIME_ERROR_'  => '预约时间错误',
  '_ORDER_RESERVATION_TIME_MIN_ERROR_'  => '预约时间必须大于当前时间',
  '_ORDER_PAY_TIME_ERROR_'  => '消费时间错误',
  '_ORDER_PAY_AMOUNT_ERROR_'  => '消费金额错误',
  '_ORDER_VISIT_TIME_ERROR_'  => '访问时间错误',
  
  '_ORDER_PRODUCT_SNAPSHOTS_SAVE_FAILURE_'  => '产品快照保存失败',
  '_ORDER_PRODUCT_SNAPSHOTS_SAVE_SUCCEED_'  => '产品快照保存成功',
  
  '_ORDER_PAYMENT_YEARS_ERROR_'  => '缴费年限错误',
  '_ORDER_GUARANTEE_AMOUNT_ERROR_'  => '保障额度错误',
  '_ORDER_YEAR_PREMIUM_AMOUNT_ERROR_'  => '年缴保费错误',
  '_ORDER_PLAN_ATTACHMENT_NULL_'  => '必须上传计划书附件',
  
  '_ORDER_REVIEW_NOT_EDIT_'  => '预约被驳回，无法进行编辑修改！',

  '_ORDER_REVIEW_REMARKS_NULL_'  => '预约审批意见不能为空',
  '_ORDER_REVIEW_STATUS_ERROR_'  => '预约审批状态错误',
  //endregion 预约
  
  //region 佣金
  '_COMMISSION_ID_ERROR_'  => '佣金编号错误',
  '_COMMISSION_DATE_ERROR_'  => '佣金日期错误',
  '_COMMISSION_NOT_EXIST_'  => '佣金数据不存在',
  '_COMMISSION_RANK_ERROR_'  => '本月排名错误',
  '_COMMISSION_ACHIEVEMENTS_ERROR_'  => '本月业绩错误',
  '_COMMISSION_RAKE_ERROR_'  => '本月提成错误',
  '_COMMISSION_BONUS_ERROR_'  => '本月奖金错误',
  '_COMMISSION_SUBSIDY_ERROR_'  => '其它补贴错误',
  '_COMMISSION_TAX_ERROR_'  => '扣税错误',
  '_COMMISSION_OTHER_MINUS_ERROR_'  => '其他扣除错误',
  '_COMMISSION_SHOULD_PAY_ERROR_'  => '应发工资错误',
  '_COMMISSION_ACTUAL_DELIVERY_ERROR_'  => '应发工资错误',
  '_COMMISSION_STATUS_ERROR_'  => '实发工资错误',
  '_COMMISSION_REMARKS_ERROR_'  => '备注',

  '_COMMISSION_STANDARD_NOT_EXIST_'  => '佣金提成标准数据不存在',
  '_COMMISSION_STANDARD_ID_NULL_'  => '佣金提成标准编号不能为空',
  '_COMMISSION_STANDARD_ID_ERROR_'  => '佣金提成标准编号错误',
  '_COMMISSION_STANDARD_NAME_NULL_'  => '佣金提成标准编名称不能为空',
  '_COMMISSION_STANDARD_NAME_EXIST_'  => '佣金提成标准已经存在',
  //endregion 佣金
  
  //endregion 自定义语言变量
);
