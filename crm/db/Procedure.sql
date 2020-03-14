-- ----------------------------
-- Procedure structure for `exec_system_category_add` 添加系统栏目
-- ----------------------------
DROP PROCEDURE IF EXISTS `exec_system_category_add`;
DELIMITER ;;
CREATE PROCEDURE `exec_system_category_add`(
	in p_id int, 
	in p_type int,
	in p_name varchar(50),
	in p_menurul varchar(256),	
	in p_intro varchar(500),	
	in p_content text,
	in p_image varchar(100),	
	in p_order int,
	in p_status int,	
	in p_admid int,	
	in p_addtime int 
)
BEGIN
	DECLARE pr INT;	/*右值*/
	DECLARE lvv INT;/*层级*/
	DECLARE aff INT;/*计数器*/
	DECLARE af INT DEFAULT 0;
	DECLARE deforder INT DEFAULT 0;
	
	SET @result = null;
	
	SELECT `right_val`,`dept` INTO pr,lvv FROM `btten_system_category` WHERE `menuid` = p_id;
	
	IF pr THEN
		START TRANSACTION;
		UPDATE `btten_system_category` SET `left_val`=`left_val` + 2 WHERE `left_val`> pr;
		
		SELECT ROW_COUNT() INTO aff;
		
		SET af = aff+af;
		UPDATE `btten_system_category` SET `right_val` = `right_val` + 2 WHERE `right_val`>= pr;
		
		SELECT ROW_COUNT() INTO aff;
		SET af = aff+af;

		IF p_id >= 1 THEN
			IF p_order <= 0 THEN
				SELECT MAX(`order`) INTO p_order FROM `btten_system_category` WHERE `parentid` = p_id;

				IF p_order IS NULL THEN
					SET p_order = 1;
				ELSE
					SET p_order = p_order + 1;
				END IF;
			END IF;
		ELSE
			IF p_order <= 0 THEN
				SELECT MAX(`order`) + 1 INTO p_order FROM `btten_system_category` WHERE `menuid` = p_id;
			END IF;
		END IF;

		INSERT INTO `btten_system_category` ( `type`, `menu_name`, `url`, `intro`, `content`, `image`, `parentid`, `left_val`, `right_val`, `order`, `menu_path`, `dept`, `child`, `status`, `addtime`, `modifytime`, `admid` ) 
		VALUES (p_type, p_name, p_menurul, p_intro, p_content, p_image, p_id, pr, pr+1, p_order, '', lvv+1, 0, p_status, p_addtime, p_addtime, p_admid );	
		
		update `btten_system_category` set `child` = `child` + 1 where `menuid` = p_id;

		SELECT ROW_COUNT() INTO aff;
		SET af = aff+af;
		
		IF af >= 2 THEN
			COMMIT;
			SET @result = 1000;
			SELECT 1000 AS result;
		ELSE
			ROLLBACK;
			SET @result = 1002;
			SELECT 1002 AS result;
		END IF;
	ELSE
		SET @result = 1001;
		SELECT 1001 AS result;
	END IF;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for `exec_system_category_modify` 修改系统栏目
-- ----------------------------
DROP PROCEDURE IF EXISTS `exec_system_category_modify`;
DELIMITER ;;
CREATE PROCEDURE `exec_system_category_modify`(
	in p_mid int, 
	in p_tomid int,
	in p_name varchar(50), 
	in p_menurul varchar(100), 
	in p_order int,
	in p_type int,
	in p_intro varchar(500),	
	in p_content text,
	in p_image varchar(100),	
	in p_menustatus int, 
	in p_opid int, 
	in p_opdate int
)
BEGIN
	DECLARE pr INT;	/*右值*/
	DECLARE lvv INT;/*层级*/
	DECLARE aff INT;/*计数器*/
	DECLARE af INT DEFAULT 0;
	declare LeftID int;/*左值*/
	declare MenuNum int;/*记录数*/		  		
	declare nParentID int;
	declare DeptNum int;/*层级*/
	declare RightID int;/*右值*/
	declare mName varchar(50);
	declare mURL varchar(100);			
	declare nDept int;/*深度*/
	declare parid int;/*父栏目ID*/

	SET @result = null;
	SELECT `parentid` INTO parid FROM `btten_system_category` WHERE `menuid` = p_mid;
		
	IF parid = p_tomid THEN	/*如果没有修改栏目所属的父级栏目*/
		/*更新基本信息*/
		update `btten_system_category` set `menu_name` = p_name, `url` = p_menurul, `order` = p_order, `type` = p_type, `intro` = p_intro, `content` = p_content, `image` = p_image, `admid` = p_opid, `modifytime` = p_opdate where `menuid` = p_mid;

		/*返回影响的行数*/
		SELECT ROW_COUNT() INTO aff;
		set af = aff + af + 1;

		/*更新该栏目及其子栏目的状态*/
		select `left_val`, `right_val` into LeftID, RightID FROM `btten_system_category` where `menuid` = p_mid;
		update `btten_system_category` set `status` = p_menustatus where `left_val` >= LeftID and `right_val` <= RightID;
	ELSE
		SELECT `left_val`,`parentid` INTO LeftID,nParentID FROM `btten_system_category` WHERE `menuid` = p_mid;
		SELECT COUNT(*) INTO MenuNum FROM `btten_system_category` WHERE `menuid` = p_mid;

	 /*SELECT LeftID;*/
   
		IF LeftID > 1 AND MenuNum > 0 THEN
			IF LeftID != 1 THEN
						/*更新左值*/
						UPDATE btten_system_category SET `left_val` = `left_val` - 2 WHERE `left_val` > LeftID;
						SELECT ROW_COUNT() INTO aff;
						set af = aff + af;
						
						/*更新右值*/
						UPDATE btten_system_category SET `right_val` = `right_val` - 2 WHERE `right_val` > LeftID;
						SELECT ROW_COUNT() INTO aff;
						set af = aff + af;
						
						UPDATE btten_system_category SET `child` = `child` - 1 WHERE `menuid` = nParentID;
			END IF;
			/*更新其它栏目的左右值 End*/
		END IF;

		/*更新移动栏目的左右值 Begin*/
		SELECT `dept`,`right_val` INTO DeptNum,RightID FROM `btten_system_category` WHERE `menuid` = p_tomid;

		IF RightID > 1 THEN
			UPDATE btten_system_category SET `left_val` = `left_val` + 2 WHERE `left_val` > RightID;
			SELECT ROW_COUNT() INTO aff;
			set af = aff + af;

			UPDATE btten_system_category SET `right_val` = `right_val` + 2 WHERE `right_val` >= RightID;
			SELECT ROW_COUNT() INTO aff;
			set af = aff + af;


			IF p_name = "" THEN
					SELECT `menu_name` INTO mName FROM `btten_system_category` WHERE `menuid` = p_mid;
			ELSE
					set mName = p_name;
			END IF;

			IF p_menurul = "" THEN
					SELECT `url` INTO mURL FROM `btten_system_category` WHERE `menuid` = p_mid;
			ELSE
					set mURL = p_menurul;
			END IF;

			SELECT `dept` INTO nDept FROM `btten_system_category` WHERE `menuid` = p_tomid;

			update `btten_system_category` set `menu_name` = mName, `url` = mURL, `left_val` = RightID, `right_val` = RightID+1, `parentid` = p_tomid, `order` =p_order, `status` = p_menustatus, `dept` = nDept + 1, `admid` = p_opid, `modifytime` = p_opdate, `type` = p_type, `intro` = p_intro, `content` = p_content, `image` = p_image where `menuid` = p_mid;

			update `btten_system_category` set `child` = `child` + 1 where `menuid` = p_tomid;
		END IF;
	END IF;

	IF af >= 2 THEN
		SET @result = 1000;
		SELECT 1000 AS result;
	ELSE
		SET @result = 1002;
		SELECT 1002 AS result;
	END IF;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for `exec_system_category_del`	 删除系统栏目
-- ----------------------------
DROP PROCEDURE IF EXISTS `exec_system_category_del`;
DELIMITER ;;
CREATE PROCEDURE `exec_system_category_del`( in p_mid int )
BEGIN
	DECLARE pl INT;
	DECLARE pn INT;
	DECLARE aff INT;
	DECLARE af INT DEFAULT 0;
	
	SET @result = null;
	SET @parentid = null;
	SET @name = null;
	
	SELECT a.`left_val`,IFNULL(COUNT(b.`menuid`),0),a.`parentid`,a.`menu_name` INTO pl,pn,@parentid,@name FROM `btten_system_category` AS a LEFT JOIN `btten_system_category` AS b ON a.`menuid`=b.`parentid` WHERE a.`menuid`= p_mid GROUP BY b.`parentid`;
	
	IF pl&&!pn THEN
		IF pl!=1 THEN
			START TRANSACTION;
			UPDATE `btten_system_category` SET `left_val`=`left_val` - 2 WHERE `left_val`>pl;

			SELECT ROW_COUNT() INTO aff;
			SET af = aff+af;
			UPDATE `btten_system_category` SET `right_val`=`right_val` - 2 WHERE `right_val`>pl;

			SELECT ROW_COUNT() INTO aff;
			SET af = aff+af;

			DELETE FROM `btten_system_category` WHERE `menuid` = p_mid;
			UPDATE `btten_system_category` SET `child`=`child` - 1 WHERE `menuid`=@parentid;
			SELECT ROW_COUNT() INTO aff;
			SET af = aff+af;

			IF af >= 2 THEN
				COMMIT;
				SET @result = 1000;
				SELECT 1000 AS result;
			ELSE
				ROLLBACK;
				SET @result = 1002;
				SELECT 1002 AS result;
			END IF;
		ELSE
			SET @result = 1004;
			SELECT 1004 AS result;
		END IF;	
	ELSEIF pn&&pl THEN
		SET @result = 1003;
		SELECT 1003 AS result;
ELSE
	SET @result = 1001;
	SELECT 1001 AS result;
END IF;		

END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for `exec_app_menu_add` 添加系统栏目
-- ----------------------------
DROP PROCEDURE IF EXISTS `exec_app_menu_add`;
DELIMITER ;;
CREATE PROCEDURE `exec_app_menu_add`(
	in p_id int, 
	in p_type int,
	in p_name varchar(50),
	in p_menurul varchar(256),	
	in p_intro varchar(500),	
	in p_content text,
	in p_image text,	
	in p_order int,
	in p_status int,	
	in p_admid int,	
	in p_addtime int 
)
BEGIN
	DECLARE pr INT;	/*右值*/
	DECLARE lvv INT;/*层级*/
	DECLARE aff INT;/*计数器*/
	DECLARE af INT DEFAULT 0;
	DECLARE deforder INT DEFAULT 0;
	
	SET @result = null;
	
	SELECT `right_val`,`dept` INTO pr,lvv FROM `btten_app_menu` WHERE `menuid` = p_id;
	
	IF pr THEN
		START TRANSACTION;
		UPDATE `btten_app_menu` SET `left_val`=`left_val` + 2 WHERE `left_val`> pr;
		
		SELECT ROW_COUNT() INTO aff;
		
		SET af = aff+af;
		UPDATE `btten_app_menu` SET `right_val` = `right_val` + 2 WHERE `right_val`>= pr;
		
		SELECT ROW_COUNT() INTO aff;
		SET af = aff+af;

		IF p_id >= 1 THEN
			IF p_order <= 0 THEN
				SELECT MAX(`order`) INTO p_order FROM `btten_app_menu` WHERE `parentid` = p_id;

				IF p_order IS NULL THEN
					SET p_order = 1;
				ELSE
					SET p_order = p_order + 1;
				END IF;
			END IF;
		ELSE
			IF p_order <= 0 THEN
				SELECT MAX(`order`) + 1 INTO p_order FROM `btten_app_menu` WHERE `menuid` = p_id;
			END IF;
		END IF;

		INSERT INTO `btten_app_menu` ( `type`, `menu_name`, `url`, `intro`, `content`, `image`, `parentid`, `left_val`, `right_val`, `order`, `menu_path`, `dept`, `child`, `status`, `addtime`, `modifytime`, `admid` ) 
		VALUES (p_type, p_name, p_menurul, p_intro, p_content, p_image, p_id, pr, pr+1, p_order, '', lvv+1, 0, p_status, p_addtime, p_addtime, p_admid );	
		
		update `btten_app_menu` set `child` = `child` + 1 where `menuid` = p_id;

		SELECT ROW_COUNT() INTO aff;
		SET af = aff+af;
		
		IF af >= 2 THEN
			COMMIT;
			SET @result = 1000;
			SELECT 1000 AS result;
		ELSE
			ROLLBACK;
			SET @result = 1002;
			SELECT 1002 AS result;
		END IF;
	ELSE
		SET @result = 1001;
		SELECT 1001 AS result;
	END IF;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for `exec_app_menu_modify` 修改系统栏目
-- ----------------------------
DROP PROCEDURE IF EXISTS `exec_app_menu_modify`;
DELIMITER ;;
CREATE PROCEDURE `exec_app_menu_modify`(
	in p_mid int, 
	in p_tomid int,
	in p_name varchar(50), 
	in p_menurul varchar(100), 
	in p_order int,
	in p_type int,
	in p_intro varchar(500),	
	in p_content text,
	in p_image text,	
	in p_menustatus int, 
	in p_opid int, 
	in p_opdate int
)
BEGIN
	DECLARE pr INT;	/*右值*/
	DECLARE lvv INT;/*层级*/
	DECLARE aff INT;/*计数器*/
	DECLARE af INT DEFAULT 0;
	declare LeftID int;/*左值*/
	declare MenuNum int;/*记录数*/		  		
	declare nParentID int;
	declare DeptNum int;/*层级*/
	declare RightID int;/*右值*/
	declare mName varchar(50);
	declare mURL varchar(100);			
	declare nDept int;/*深度*/
	declare parid int;/*父栏目ID*/

	SET @result = null;
	SELECT `parentid` INTO parid FROM `btten_app_menu` WHERE `menuid` = p_mid;
		
	IF parid = p_tomid THEN	/*如果没有修改栏目所属的父级栏目*/
		/*更新基本信息*/
		update `btten_app_menu` set `menu_name` = p_name, `url` = p_menurul, `order` = p_order, `type` = p_type, `intro` = p_intro, `content` = p_content, `image` = p_image, `admid` = p_opid, `modifytime` = p_opdate where `menuid` = p_mid;

		/*返回影响的行数*/
		SELECT ROW_COUNT() INTO aff;
		set af = aff + af + 1;

		/*更新该栏目及其子栏目的状态*/
		select `left_val`, `right_val` into LeftID, RightID FROM `btten_app_menu` where `menuid` = p_mid;
		update `btten_app_menu` set `status` = p_menustatus where `left_val` >= LeftID and `right_val` <= RightID;
	ELSE
		SELECT `left_val`,`parentid` INTO LeftID,nParentID FROM `btten_app_menu` WHERE `menuid` = p_mid;
		SELECT COUNT(*) INTO MenuNum FROM `btten_app_menu` WHERE `menuid` = p_mid;

	 /*SELECT LeftID;*/
   
		IF LeftID > 1 AND MenuNum > 0 THEN
			IF LeftID != 1 THEN
						/*更新左值*/
						UPDATE btten_app_menu SET `left_val` = `left_val` - 2 WHERE `left_val` > LeftID;
						SELECT ROW_COUNT() INTO aff;
						set af = aff + af;
						
						/*更新右值*/
						UPDATE btten_app_menu SET `right_val` = `right_val` - 2 WHERE `right_val` > LeftID;
						SELECT ROW_COUNT() INTO aff;
						set af = aff + af;
						
						UPDATE btten_app_menu SET `child` = `child` - 1 WHERE `menuid` = nParentID;
			END IF;
			/*更新其它栏目的左右值 End*/
		END IF;

		/*更新移动栏目的左右值 Begin*/
		SELECT `dept`,`right_val` INTO DeptNum,RightID FROM `btten_app_menu` WHERE `menuid` = p_tomid;

		IF RightID > 1 THEN
			UPDATE btten_app_menu SET `left_val` = `left_val` + 2 WHERE `left_val` > RightID;
			SELECT ROW_COUNT() INTO aff;
			set af = aff + af;

			UPDATE btten_app_menu SET `right_val` = `right_val` + 2 WHERE `right_val` >= RightID;
			SELECT ROW_COUNT() INTO aff;
			set af = aff + af;


			IF p_name = "" THEN
					SELECT `menu_name` INTO mName FROM `btten_app_menu` WHERE `menuid` = p_mid;
			ELSE
					set mName = p_name;
			END IF;

			IF p_menurul = "" THEN
					SELECT `url` INTO mURL FROM `btten_app_menu` WHERE `menuid` = p_mid;
			ELSE
					set mURL = p_menurul;
			END IF;

			SELECT `dept` INTO nDept FROM `btten_app_menu` WHERE `menuid` = p_tomid;

			update `btten_app_menu` set `menu_name` = mName, `url` = mURL, `left_val` = RightID, `right_val` = RightID+1, `parentid` = p_tomid, `order` =p_order, `status` = p_menustatus, `dept` = nDept + 1, `admid` = p_opid, `modifytime` = p_opdate, `type` = p_type, `intro` = p_intro, `content` = p_content, `image` = p_image where `menuid` = p_mid;

			update `btten_app_menu` set `child` = `child` + 1 where `menuid` = p_tomid;
		END IF;
	END IF;

	IF af >= 2 THEN
		SET @result = 1000;
		SELECT 1000 AS result;
	ELSE
		SET @result = 1002;
		SELECT 1002 AS result;
	END IF;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for `exec_app_menu_del`	 删除系统栏目
-- ----------------------------
DROP PROCEDURE IF EXISTS `exec_app_menu_del`;
DELIMITER ;;
CREATE PROCEDURE `exec_app_menu_del`( in p_mid int )
BEGIN
	DECLARE pl INT;
	DECLARE pn INT;
	DECLARE aff INT;
	DECLARE af INT DEFAULT 0;
	
	SET @result = null;
	SET @parentid = null;
	SET @name = null;
	
	SELECT a.`left_val`,IFNULL(COUNT(b.`menuid`),0),a.`parentid`,a.`menu_name` INTO pl,pn,@parentid,@name FROM `btten_app_menu` AS a LEFT JOIN `btten_app_menu` AS b ON a.`menuid`=b.`parentid` WHERE a.`menuid`= p_mid GROUP BY b.`parentid`;
	
	IF pl&&!pn THEN
		IF pl!=1 THEN
			START TRANSACTION;
			UPDATE `btten_app_menu` SET `left_val`=`left_val` - 2 WHERE `left_val`>pl;

			SELECT ROW_COUNT() INTO aff;
			SET af = aff+af;
			UPDATE `btten_app_menu` SET `right_val`=`right_val` - 2 WHERE `right_val`>pl;

			SELECT ROW_COUNT() INTO aff;
			SET af = aff+af;

			DELETE FROM `btten_app_menu` WHERE `menuid` = p_mid;
			UPDATE `btten_app_menu` SET `child`=`child` - 1 WHERE `menuid`=@parentid;
			SELECT ROW_COUNT() INTO aff;
			SET af = aff+af;

			IF af >= 2 THEN
				COMMIT;
				SET @result = 1000;
				SELECT 1000 AS result;
			ELSE
				ROLLBACK;
				SET @result = 1002;
				SELECT 1002 AS result;
			END IF;
		ELSE
			SET @result = 1004;
			SELECT 1004 AS result;
		END IF;	
	ELSEIF pn&&pl THEN
		SET @result = 1003;
		SELECT 1003 AS result;
ELSE
	SET @result = 1001;
	SELECT 1001 AS result;
END IF;		

END
;;
DELIMITER ;
