using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Security.Principal;
using System.Web;

namespace Flowpie.Models
{
    public class MyPrincipal : System.Security.Principal.IPrincipal
    {
        private System.Security.Principal.IIdentity identity;
        private ArrayList roleList;

        public MyPrincipal(string userID, string password)
        {
            // 
            // TODO: 在此处添加构造函数逻辑 
            // 
            identity = new MyIdentity(userID, password);
            if (identity.IsAuthenticated)
            {
                //如果通过验证则获取该用户的Role，这里可以修改为从数据库中 
                //读取指定用户的Role并将其添加到Role中，本例中直接为用户添加一个Admin角色 
                roleList = new ArrayList();
                roleList.Add(identity.AuthenticationType);
            }
            else
            {
                // do nothing 
            }
        }

        public ArrayList RoleList
        {
            get
            {
                return roleList;
            }
        }
        #region IPrincipal 成员 

        public System.Security.Principal.IIdentity Identity
        {
            get
            {
                // TODO:   添加 MyPrincipal.Identity getter 实现 
                return identity;
            }
            set
            {
                identity = value;
            }
        }

        public bool IsInRole(string role)
        {
            // TODO:   添加 MyPrincipal.IsInRole 实现 
            return roleList.Contains(role); ;
        }

        #endregion
    }
}