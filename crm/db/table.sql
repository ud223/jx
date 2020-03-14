-- ----------------------------
-- btten_auth_rule，规则表，
-- id:主键，name：规则唯一标识, title：规则中文名称 status 状态：为1正常，为0禁用，condition：规则表达式，为空表示存在就验证，不为空表示按照条件验证
-- ----------------------------
DROP TABLE IF EXISTS `btten_auth_rule`;
CREATE TABLE `btten_auth_rule` (  
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,  
	`name` VARCHAR(80) NOT NULL DEFAULT '' COMMENT '规则唯一标识',  
	`title` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '规则中文名称',  
	`type` 	text 	COMMENT '分类，模块名',    
	`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',  
	`condition` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证',  # 规则附件条件,满足附加条件的规则,才认为是有效的规则
	PRIMARY KEY (`id`),  
	UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- btten_auth_group 用户组表， 
-- id：主键， title:用户组中文名称， rules：用户组拥有的规则id， 多个规则","隔开，status 状态：为1正常，为0禁用
-- ----------------------------
DROP TABLE IF EXISTS `btten_auth_group`;
CREATE TABLE `btten_auth_group` ( 
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT, 
	`title` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '用户组中文名称', 
	`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用', 
	`rules` VARCHAR(80) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id， 多个规则","隔开', 
	`menuids`	VARCHAR(100) NOT NULL DEFAULT '' COMMENT '用户组拥有的菜单id， 多个菜单","隔开', 
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- btten_auth_group_access 用户组明细表
-- uid:用户id，group_id：用户组id
-- ----------------------------
DROP TABLE IF EXISTS `btten_auth_group_access`;
CREATE TABLE `btten_auth_group_access` (  
	`uid` int(11) unsigned NOT NULL COMMENT '用户id',  
	`group_id` int(11) unsigned NOT NULL COMMENT '用户组id', 
	UNIQUE KEY `uid_group_id` (`uid`,`group_id`),  
	KEY `uid` (`uid`), 
	KEY `group_id` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- btten_auth_admin 管理员表
-- uid:用户id，group_id：用户组id
-- ----------------------------
DROP TABLE IF EXISTS `btten_auth_admin`;
CREATE TABLE `btten_auth_admin` (
	`id` 					int(11) 			NOT NULL AUTO_INCREMENT,
	`username` 		VARCHAR(32) 	NOT NULL DEFAULT '' 	COMMENT '用户名',  
	`pwd` 				VARCHAR(32) 	NOT NULL DEFAULT '' 	COMMENT '密码',
	`truename` 		VARCHAR(50) 	NOT NULL DEFAULT '' 	COMMENT '真实姓名',
	`phone` 			VARCHAR(50) 	NOT NULL DEFAULT '' 	COMMENT '联系电话',
	`email` 			VARCHAR(100) 	NOT NULL DEFAULT '' 	COMMENT '邮箱',
	`login_ip` 		VARCHAR(100) 	NOT NULL DEFAULT '' 	COMMENT '上次登录ip',
	`login_date` 	int(11) 			NOT NULL DEFAULT '0' 	COMMENT '上次登录日期',
	`addtime` 		int(11) 			NOT NULL DEFAULT '0' 	COMMENT '添加日期',
	`modifytime` 	int(11) 			NOT NULL DEFAULT '0' 	COMMENT '最后修改日期',
	`admid` 			int(11) 			NOT NULL DEFAULT '0' 	COMMENT '修改人ID',
	`status` 			tinyint(1) 		NOT NULL DEFAULT '1' 	COMMENT '状态。1正常，2锁定',
	`hide` 				tinyint(1) 		NOT NULL DEFAULT '1' 	COMMENT '是否隐藏。1正常，2隐藏',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- btten_system_config 系统配置
-- ----------------------------
DROP TABLE IF EXISTS `btten_system_config`;
CREATE TABLE `btten_system_config` (
	`id` 						int(11) 			NOT NULL AUTO_INCREMENT,
	`site_name`			VARCHAR(50)		NOT NULL DEFAULT '' 	COMMENT '站点名称',
	`domain_name`		VARCHAR(100)	NOT NULL DEFAULT '' 	COMMENT '站点域名',
	`logo`					VARCHAR(100)	NOT NULL DEFAULT '' 	COMMENT 'logo图片',
	`image_size`		int(11)				NOT NULL DEFAULT '0' 	COMMENT '允许上传的图片大小，单位KB',
	`image_type`		int(11)				NOT NULL DEFAULT '0' 	COMMENT '允许上传图片的文件类型',
	`image_desc`		int(11)				NOT NULL DEFAULT '0' 	COMMENT '允许上传图片的文件类型说明',
	`file_size`			int(11)				NOT NULL DEFAULT '0' 	COMMENT '允许上传的文件大小，单位KB',
	`file_type`			int(11)				NOT NULL DEFAULT '0' 	COMMENT '允许上传的文件类型',
	`file_desc`			int(11)				NOT NULL DEFAULT '0' 	COMMENT '允许上传的文件类型说明',
	`service_mail`	VARCHAR(100)	NOT NULL DEFAULT '' 	COMMENT '客服邮箱',
	`page_title`		VARCHAR(50)		NOT NULL DEFAULT '' 	COMMENT '首页页面标题',
	`page_key`			VARCHAR(500)	NOT NULL DEFAULT '' 	COMMENT '首页页面关键字',
	`page_desc`			VARCHAR(500)	NOT NULL DEFAULT '' 	COMMENT '首页页面描述',
	`copyright`			VARCHAR(500)	NOT NULL DEFAULT '' 	COMMENT '版权信息',
	`js_plug`				VARCHAR(500)	NOT NULL DEFAULT '' 	COMMENT 'js插件链接',
	`status` 				tinyint(1) 		NOT NULL DEFAULT '1' 	COMMENT '状态。1正常，2关闭',
	`close_cause` 	VARCHAR(100) 	NOT NULL DEFAULT '' 	COMMENT '关闭原因',
	`modifytime` 		int(11) 			NOT NULL DEFAULT '0' 	COMMENT '最后修改日期',
	`admid` 				int(11) 			NOT NULL DEFAULT '0' 	COMMENT '修改人ID',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- btten_systen_category 站点栏目分类表
-- ----------------------------
DROP TABLE IF EXISTS `btten_systen_category`;
CREATE TABLE `btten_systen_category` (
  `menuid` 			int(4) 				NOT NULL AUTO_INCREMENT,
  `type` 				int(4)				NOT NULL DEFAULT '0'	COMMENT '类型',	
  `menu_name` 	varchar(50) 	NOT NULL DEFAULT ''		COMMENT '栏目名',
  `url` 				varchar(100)	NOT NULL DEFAULT ''		COMMENT '栏目链接',
  `intro` 			varchar(500)	NOT NULL DEFAULT ''		COMMENT '栏目简介',
  `content` 		text																COMMENT '栏目详细内容',
  `image` 			varchar(100)	NOT NULL DEFAULT ''		COMMENT '图片',
  `parentid` 		int(11) 			NOT NULL DEFAULT '0'	COMMENT '父栏目ID',
  `left_val` 		int(11) 			NOT NULL DEFAULT '0'	COMMENT '左值',
  `right_val` 	int(11) 			NOT NULL DEFAULT '0'	COMMENT '右值',
	`order` 			int(11) 			NOT NULL DEFAULT '0'	COMMENT '排序',
  `menu_path` 	varchar(100) 	NOT NULL DEFAULT ''		COMMENT '父栏目ID字符串 , 号分割',
  `dept` 				int(11) 			NOT NULL DEFAULT '0'	COMMENT '深度/层级',
  `child` 			int(11) 			NOT NULL DEFAULT '0'	COMMENT '子栏目数量',
  `status` 			tinyint(1) 		NOT NULL DEFAULT '0' 	COMMENT '0为正常，1为隐藏。',
  `addtime` 		int(11) 			NOT NULL DEFAULT '0' 	COMMENT '添加时间',
	`modifytime` 	int(11) 			NOT NULL DEFAULT '0' 	COMMENT '最后修改时间',
  `admid` 			int(11) 			NOT NULL DEFAULT '0' 	COMMENT '最后修改的管理员ID',
  PRIMARY KEY (`menuid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- btten_app_menu 站点栏目分类表
-- ----------------------------
DROP TABLE IF EXISTS `btten_app_menu`;
CREATE TABLE `btten_app_menu` (
  `menuid` 			int(4) 				NOT NULL AUTO_INCREMENT,
  `type` 				int(4)				NOT NULL DEFAULT '0'	COMMENT '类型',	
  `menu_name` 	varchar(50) 	NOT NULL DEFAULT ''		COMMENT '栏目名',
  `url` 				varchar(100)	NOT NULL DEFAULT ''		COMMENT '栏目链接',
  `intro` 			varchar(500)	NOT NULL DEFAULT ''		COMMENT '栏目简介',
  `content` 		text																COMMENT '栏目详细内容',
  `image` 			varchar(100)	NOT NULL DEFAULT ''		COMMENT '图片',
  `parentid` 		int(11) 			NOT NULL DEFAULT '0'	COMMENT '父栏目ID',
  `left_val` 		int(11) 			NOT NULL DEFAULT '0'	COMMENT '左值',
  `right_val` 	int(11) 			NOT NULL DEFAULT '0'	COMMENT '右值',
	`order` 			int(11) 			NOT NULL DEFAULT '0'	COMMENT '排序',
  `menu_path` 	varchar(100) 	NOT NULL DEFAULT ''		COMMENT '父栏目ID字符串 , 号分割',
  `dept` 				int(11) 			NOT NULL DEFAULT '0'	COMMENT '深度/层级',
  `child` 			int(11) 			NOT NULL DEFAULT '0'	COMMENT '子栏目数量',
  `status` 			tinyint(1) 		NOT NULL DEFAULT '0' 	COMMENT '0为正常，1为隐藏。',
  `addtime` 		int(11) 			NOT NULL DEFAULT '0' 	COMMENT '添加时间',
	`modifytime` 	int(11) 			NOT NULL DEFAULT '0' 	COMMENT '最后修改时间',
  `admid` 			int(11) 			NOT NULL DEFAULT '0' 	COMMENT '最后修改的管理员ID',
  PRIMARY KEY (`menuid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- btten_app_news 新闻表
-- ----------------------------
DROP TABLE IF EXISTS `btten_app_news`;
CREATE TABLE `btten_app_news` (
  `newsid` 			int(11) 			NOT NULL AUTO_INCREMENT,
  `menuid` 			int(11) 			NOT NULL DEFAULT '0' 	COMMENT '分类id',
  `title` 			varchar(100) 	NOT NULL DEFAULT '' 	COMMENT '标题',
  `sub_title` 	varchar(50) 	NOT NULL DEFAULT '' 	COMMENT '子标题',
  `head_title` 	varchar(50) 	NOT NULL DEFAULT '' 	COMMENT '头条标题',
  `author` 			varchar(50) 	NOT NULL DEFAULT '' 	COMMENT '作者',
  `source` 			varchar(100) 	NOT NULL DEFAULT '' 	COMMENT '来源',
  `lat` 				varchar(100) 	NOT NULL DEFAULT '' 	COMMENT '纬度',
  `lng` 				varchar(100) 	NOT NULL DEFAULT '' 	COMMENT '经度',
  `lbs_address` varchar(100) 	NOT NULL DEFAULT '' 	COMMENT '坐标地址',
  `tag` 				varchar(100) 	NOT NULL DEFAULT '' 	COMMENT '标签',
  `intro` 			varchar(500) 	NOT NULL DEFAULT '' 	COMMENT '简介',
  `content` 		text 					 											COMMENT '内容',
	`thumb` 			text 					 											COMMENT '缩略图',
  `image` 			text 					 											COMMENT '图片',
	`praise_num` 	int(11) 			NOT NULL DEFAULT '0'	COMMENT '顶/赞数',
  `hits_num` 		int(11) 			NOT NULL DEFAULT '0' 	COMMENT '点击数',
  `share_num` 	int(11) 			NOT NULL DEFAULT '0' 	COMMENT '分享数',
  `comment_num` int(11) 			NOT NULL DEFAULT '0' 	COMMENT '评论数',
  `order` 			int(11) 			NOT NULL DEFAULT '0' 	COMMENT '排序',
  `is_top` 			tinyint(1) 		NOT NULL DEFAULT '0' 	COMMENT '是否置顶：0普通，1置顶',
  `is_head` 		tinyint(1) 		NOT NULL DEFAULT '0' 	COMMENT '是否头条：0普通，1头条',
  `is_pic` 			tinyint(1) 		NOT NULL DEFAULT '0' 	COMMENT '是否图片新闻：0普通，1图片新闻',
  `is_comment` 	tinyint(1) 		NOT NULL DEFAULT '0' 	COMMENT '允许评论：0允许，1不允许',
  `is_goto` 		tinyint(1) 		NOT NULL DEFAULT '0' 	COMMENT '外链新闻：0普通，1外链新闻',
  `goto_rul` 		varchar(200) 	NOT NULL DEFAULT '' 	COMMENT '外链地址',
  `status` 			tinyint(1) 		NOT NULL DEFAULT '0' 	COMMENT '状态：0正常，1未审核，2隐藏',
  `addtime` 		int(11) 			NOT NULL DEFAULT '0' 	COMMENT '添加时间',
	`modifytime` 	int(11) 			NOT NULL DEFAULT '0' 	COMMENT '最后修改时间',
  `admid` 			int(11) 			NOT NULL DEFAULT '0' 	COMMENT '最后修改的管理员ID',
  PRIMARY KEY (`newsid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- ----------------------------
-- btten_upload_images 上传文件信息表
-- ----------------------------
DROP TABLE IF EXISTS `btten_upload_images`;
CREATE TABLE `btten_upload_images` (
  `imgid` 			int(11) 			NOT NULL AUTO_INCREMENT,
	`filename`	VARCHAR(500)	NOT NULL DEFAULT '' 	COMMENT '文件原始文件名',
	`type`	VARCHAR(100)	NOT NULL DEFAULT '' 	COMMENT '文件上传类型',
	`width`	int(11) 			NOT NULL DEFAULT '0' 	COMMENT '图片文件宽',
	`height`	int(11) 			NOT NULL DEFAULT '0' 	COMMENT '图片文件高',
	`size`	 		int(11) 			NOT NULL DEFAULT '0' 	COMMENT '文件原始大小,单位:字节',
	`key`	VARCHAR(100)	NOT NULL DEFAULT '' 	COMMENT '文件上传参数名',
	`mime`	VARCHAR(10)	NOT NULL DEFAULT '' 	COMMENT '文件mime类型',
	`ext`	VARCHAR(10)	NOT NULL DEFAULT '' 	COMMENT '文件后缀',
	`md5`	VARCHAR(32)	NOT NULL DEFAULT '' 	COMMENT '上传文件的md5哈希验证字符串 仅当hash设置开启后有效',
	`sha1`	VARCHAR(100)	NOT NULL DEFAULT '' 	COMMENT '上传文件的sha1哈希验证字符串 仅当hash设置开启后有效',
	`savename`	VARCHAR(100)	NOT NULL DEFAULT '' 	COMMENT '文件保存名',
	`savepath`	VARCHAR(100)	NOT NULL DEFAULT '' 	COMMENT '文件保存路径',
  `addtime` 		int(11) 			NOT NULL DEFAULT '0' 	COMMENT '添加时间',
  `admid` 			int(11) 			NOT NULL DEFAULT '0' 	COMMENT '最后修改的管理员ID',
  PRIMARY KEY (`imgid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- btten_upload_files 上传文件信息表
-- ----------------------------
DROP TABLE IF EXISTS `btten_upload_files`;
CREATE TABLE `btten_upload_files` (
  `fileid` 			int(11) 			NOT NULL AUTO_INCREMENT,
	`filename`	VARCHAR(500)	NOT NULL DEFAULT '' 	COMMENT '文件原始文件名',
	`type`	VARCHAR(100)	NOT NULL DEFAULT '' 	COMMENT '文件上传类型',
	`size`	 		int(11) 			NOT NULL DEFAULT '0' 	COMMENT '文件原始大小,单位:字节',
	`key`	VARCHAR(100)	NOT NULL DEFAULT '' 	COMMENT '文件上传参数名',
	`ext`	VARCHAR(10)	NOT NULL DEFAULT '' 	COMMENT '文件后缀',
	`md5`	VARCHAR(32)	NOT NULL DEFAULT '' 	COMMENT '上传文件的md5哈希验证字符串 仅当hash设置开启后有效',
	`sha1`	VARCHAR(100)	NOT NULL DEFAULT '' 	COMMENT '上传文件的sha1哈希验证字符串 仅当hash设置开启后有效',
	`savename`	VARCHAR(100)	NOT NULL DEFAULT '' 	COMMENT '文件保存名',
	`savepath`	VARCHAR(100)	NOT NULL DEFAULT '' 	COMMENT '文件保存路径',
  `addtime` 		int(11) 			NOT NULL DEFAULT '0' 	COMMENT '添加时间',
  `admid` 			int(11) 			NOT NULL DEFAULT '0' 	COMMENT '最后修改的管理员ID',
  PRIMARY KEY (`fileid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
