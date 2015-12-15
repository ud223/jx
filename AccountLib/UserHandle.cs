using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data;
using System.Web;
using System.Web.Security;

namespace AccountLib
{
    public class UserHandle : BaseLib.BaseClass
    {
        public DataSet Login(string name, string password)
        {
            password = CommonLib.Common.Function.toMD5String(password);

            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            //获取数据访问操作端
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            string strSql = "SELECT sy_users.*, UserTypeText, SchoolText FROM sy_users LEFT JOIN sy_usertype ON sy_users.UserTypeID = sy_usertype.UserTypeID left join app_schools on sy_users.SchoolID = app_schools.SchoolID WHERE Name = '@Name@';";

            strSql = strSql.Replace("@Name@", name);

            dataClient.SqlText = strSql;

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;

            if (!DatabaseLib.Tools.tableIsNull(ds))
            {
                return null;
            }
            else
            {
                if (ds.Tables[0].Rows[0]["Password"].ToString() != password)
                {
                    return null;
                }

                return ds;
            }
        }

        /// <summary>
        /// 保存登陆用户票据
        /// </summary>
        /// <param name="key"></param>
        public void saveTicket(string key)
        {
            CacheLib.Cookie cookie = new CacheLib.Cookie();
            FormsAuthenticationTicket ticket = new FormsAuthenticationTicket(1, "auth", DateTime.Now, DateTime.Now.AddMinutes(60), false, key, FormsAuthentication.FormsCookiePath);

            string encTicket = FormsAuthentication.Encrypt(ticket);
            //清除老的票据
            cookie.Delete("auth");

            cookie.AddCookie("auth", encTicket);
        }

        /// <summary>
        /// 获取票据
        /// </summary>
        /// <returns></returns>
        public string getTicket()
        {
            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string encTicket = cookie.GetCookie("auth");

            if (CommonLib.Common.Validate.IsNullString(encTicket) == "")
                return null;

            FormsAuthenticationTicket ticket = FormsAuthentication.Decrypt(encTicket);

            return ticket.UserData;
        }
    }
}
