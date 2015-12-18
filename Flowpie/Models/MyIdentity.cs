using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Data;

namespace Flowpie.Models
{
    public class MyIdentity : System.Security.Principal.IIdentity
    {
        private string userID;
        private string password;

        public Models.User user;

        public MyIdentity(string currentUserID, string currentPassword)
        {
            userID = currentUserID;
            password = currentPassword;
        }

        private bool CanPass()
        {
            ////这里朋友们可以根据自己的需要改为从数据库中验证用户名和密码， 
            ////这里为了方便我直接指定的字符串 
            //if (userID == "yan0lovesha" && password == "iloveshasha")
            //{
            //    return true;
            //}
            //else
            //{
            //    return false;
            //}
            AccountLib.UserHandle userHandle = new AccountLib.UserHandle();

            System.Data.DataSet ds = userHandle.Login(userID, password);

            if (ds == null)
            {
                return false;
            }
            else
            {
                #region 独立加入的用户信息及权限信息
                System.Collections.Hashtable ht = new System.Collections.Hashtable();

                foreach (DataColumn dc in ds.Tables[0].Columns)
                {
                    string key = dc.ColumnName;
                    string value = CommonLib.Common.Validate.IsNullString(ds.Tables[0].Rows[0][dc.ColumnName]);

                    ht.Add(key, value);
                }

                user = new User();

                user.UserID = ht["UserID"].ToString();
                user.Name = ht["Name"].ToString();
                user.HeadPic = ht["HeadPic"].ToString();
                user.Email = ht["Email"].ToString();
                user.Phone = ht["Phone"].ToString();
                user.UserTypeID = ht["UserTypeID"].ToString();
                user.UserTypeText = ht["UserTypeText"].ToString();
                user.SchoolID = ht["SchoolID"].ToString();
                user.SchoolText = ht["SchoolText"].ToString();

                this.userID = user.Name;

                SystemConfigureLib.PermissionController permissionController = new SystemConfigureLib.PermissionController();

                List<System.Collections.Hashtable> list =  permissionController.getPermissionByUserID(user.UserTypeID);

                user.Permissions = new List<Permission>();

                foreach (System.Collections.Hashtable item in list)
                {
                    Permission permission = new Permission();

                    permission.PermissionID = item["PermissionID"].ToString();
                    permission.MenuID = item["MenuID"].ToString();
                    permission.UserTypeID = item["UserTypeID"].ToString();
                    permission.IsAdd = item["IsAdd"].ToString();
                    permission.IsModify = item["IsModify"].ToString();
                    permission.IsDelete = item["IsDelete"].ToString();
                    permission.SelectType = item["SelectType"].ToString();
                    permission.AccessFile = item["AccessFile"].ToString();

                    user.Permissions.Add(permission);
                }

                //缓存用户数据
                this.storeUserData();

                #endregion;

                this._sAuthenticationType = ds.Tables[0].Rows[0]["UserTypeText"].ToString();

                return true;
            }
        }

        public string Password
        {
            get
            {
                return password;
            }
            set
            {
                password = value;
            }
        }

        #region IIdentity 成员 

        public bool IsAuthenticated
        {
            get
            {
                // TODO:   添加 MyIdentity.IsAuthenticated getter 实现 
                return CanPass();
            }
        }

        public string Name
        {
            get
            {
                // TODO:   添加 MyIdentity.Name getter 实现 
                return userID;
            }
        }

        private string _sAuthenticationType;

        //这个属性我们可以根据自己的需要来灵活使用,在本例中没有用到它 
        public string AuthenticationType
        {
            get
            {
                // TODO:   添加 MyIdentity.AuthenticationType getter 实现 
                return this._sAuthenticationType;
            }
        }

        private void storeUserData()
        {
            CacheLib.Cache cache = new CacheLib.Cache();
            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string key = "usr_";

            key = cache.Add(key, this.user);

            cookie.AddCookie("user_key", key);
        }

        #endregion
    }
}