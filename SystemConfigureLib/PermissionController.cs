using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data;
using System.Collections;

namespace SystemConfigureLib
{
    public class PermissionController : iController
    {
        public List<Hashtable> getPermissionByMenuID(string menuid)
        {
            this.SqlText = "select sy_permission.*, sy_usertype.UserTypeText from sy_permission left join sy_usertype on sy_permission.UserTypeID = sy_usertype.UserTypeID where MenuID = " + menuid;

            return base.Query(this.SqlText);
        }

        /// <summary>
        /// 获取改菜单没有没有被配置的用户角色
        /// </summary>
        /// <param name="menuid"></param>
        /// <returns></returns>
        public List<Hashtable> getUserTypeNotInConfig(string menuid)
        {
            this.SqlText = "select sy_usertype.* from sy_usertype where UserTypeID not in (select usertypeid from sy_permission where menuid = "+ menuid + ")";

            return base.Query(this.SqlText);
        }

        public Hashtable getPermissionByMenuIDAndUserTypeID(string menuid, string userTypeId)
        {
            this.SqlText = "select * from sy_permission left join sy_menus on sy_permission.MenuID = sy_menus.MenuID where sy_permission.MenuID =" + menuid + " and UserTypeID = "+ userTypeId;

            return this.load("");
        }

        public List<Hashtable> getPermissionByUserID(string userTypeId)
        {
            this.SqlText = "select sy_permission.*, AccessFile from sy_permission left join sy_menus on sy_permission.MenuID = sy_menus.MenuID where UserTypeID = " + userTypeId;

            return base.Query(this.SqlText);
        }

        public override string add(Hashtable data)
        {
            this.SqlText = "insert into sy_permission(MenuID, UserTypeID, IsAdd, IsModify, IsDelete, IsView, SelectType, CreateAt, ModifyAt) values(@MenuID@, @UserTypeID@, @IsAdd@, @IsModify@, @IsDelete@, @IsView@, @SelectType@, '@CreateAt@', '@ModifyAt@'); select permissionid from sy_permission order by permissionid desc limit 1";

            return base.add(data);
        }

        public override void save(Hashtable data)
        {
            this.SqlText = "update sy_permission set IsAdd=@IsAdd@, IsModify=@IsModify@, IsDelete=@IsDelete@, IsView=@IsView@, SelectType=@SelectType@ where permissionid = @permissionid@";

            base.save(data);
        }

        public override void delete(string id)
        {
            this.SqlText = "delete from sy_permission where permissionid=";

            base.delete(id);
        }

        public void deleteByMenuID(string menuid)
        {
            this.SqlText = "delete from sy_permission where MenuID=";

            base.delete(menuid);
        }
    }
}
