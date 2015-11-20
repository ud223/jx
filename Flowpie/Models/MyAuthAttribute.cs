using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using System.Data;

namespace Flowpie.Models
{
    public class MyAuthAttribute : AuthorizeAttribute
    {
        private string _sMessage = "操作成功!";
        private int _code = 0;

        /// <summary>
        /// 所有类的内部消息属性
        /// </summary>
        public string Message
        { get { return this._sMessage; } set { this._sMessage = value; } }

        private bool _bResult = true;

        /// <summary>
        /// 所有类的内部操作结果属性
        /// </summary>
        public bool Result
        { get { return this._bResult; } set { this._bResult = value; } }

        // 只需重载此方法，模拟自定义的角色授权机制  
        protected override bool AuthorizeCore(HttpContextBase httpContext)
        {
            CacheLib.Cache cache = new CacheLib.Cache();
            AccountLib.UserHandle userHandle = new AccountLib.UserHandle();

            string key = userHandle.getTicket();

            if (key == null)
            {
                this._code = 1;

                return false;
            }

            MyPrincipal user = cache.Get<MyPrincipal>(key);

            if (user == null)
            {
                this._code = 1;

                return false;
            }

            if (!user.Identity.IsAuthenticated)//判断用户是否通过验证
            {
                this._code = 1;
                return false;
            }

            string[] StrRoles = Roles.Split(',');//通过逗号来分割允许进入的用户角色

            if (string.IsNullOrWhiteSpace(Roles))//如果只要求用户登录，即可访问的话
            {
                this._code = 0;

                return true;
            }

            bool isAccess = JudgeAuthorize(user.Identity.Name, StrRoles);

            if (StrRoles.Length < 1 || !isAccess) //先判断是否有设用户权限，如果没有不允许访问
            {
                this._code = 2;

                return false;
            }

            return true;
        }
        /// <summary>
        /// 根据用户名判断用户是否有对应的权限
        /// </summary>
        /// <param name="UserName"></param>
        /// <param name="StrRoles"></param>
        /// <returns></returns>
        private bool JudgeAuthorize(string UserName, string[] StrRoles)
        {
            string UserAuth = GetRole(UserName);  //从数据库中读取用户的权限
            return StrRoles.Contains(UserAuth,    //将用户的权限跟权限列表中做比较
                             StringComparer.OrdinalIgnoreCase);  //忽略大小写
        }

        // 返回用户对应的角色， 在实际中， 可以从SQL数据库中读取用户的角色信息  
        private string GetRole(string name)
        {
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            //获取数据访问操作端
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            string strSql = "SELECT UserTypeText FROM sy_users LEFT JOIN sy_usertype ON sy_users.UserTypeID = sy_usertype.UserTypeID WHERE Name = '@Name@';";

            strSql = strSql.Replace("@Name@", name);

            dataClient.SqlText = strSql;

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;

            if (!DatabaseLib.Tools.tableIsNull(ds))
            {
                return "none";
            }
            else
            {
                return ds.Tables[0].Rows[0]["UserTypeText"].ToString();
            }
        }

        protected override void HandleUnauthorizedRequest(AuthorizationContext filterContext)
        {
            if (this._code == 2)
            {
                HttpContext.Current.Response.Redirect("/account/login");
            }
            else
            {
                base.HandleUnauthorizedRequest(filterContext);
            } 
        }
    }
}