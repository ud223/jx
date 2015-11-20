using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data;


namespace SystemConfigureLib
{
    public class UserController : iController
    {
        /// <summary>
        /// 获取所有用户, 分页暂时没做
        /// </summary>
        /// <returns></returns>
        public override List<System.Collections.Hashtable> getAll()
        {
            //DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            ////获取数据访问操作端
            //DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            //dataClient.SqlText = "SELECT * FROM sy_users";

            //DataSet ds = dataClient.Query();

            //this.Message = dataClient.Message;
            //this.Result = dataClient.Result;

            //List<System.Collections.Hashtable> list = null;

            //if (this.Result)
            //{
            //    list = new List<System.Collections.Hashtable>();

            //    foreach (DataRow row in ds.Tables[0].Rows)
            //    {
            //        System.Collections.Hashtable ht = new System.Collections.Hashtable();

            //        foreach (DataColumn dc in ds.Tables[0].Columns)
            //        {
            //            string key = dc.ColumnName;
            //            string value = row[dc.ColumnName].ToString();

            //            ht.Add(key, value);
            //        }

            //        list.Add(ht);

            //    }

            //    return list;
            //}
            //else
            //    return null;

            this.SqlText = "SELECT * FROM sy_users";

            return base.getAll();
        }


        public override System.Collections.Hashtable load(string id)
        {
            //DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            //DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            //string strSql = "SELECT * FROM sy_users WHERE UserID = " + id;

            //dataClient.SqlText = strSql;

            //DataSet ds = dataClient.Query();

            //this.Message = dataClient.Message;
            //this.Result = dataClient.Result;

            //if (this.Result)
            //{
            //    if (!DatabaseLib.Tools.tableIsNull(ds))
            //    {
            //        return null;
            //    }

            //    System.Collections.Hashtable item = new System.Collections.Hashtable();

            //    foreach (DataColumn dc in ds.Tables[0].Columns)
            //    {
            //        string key = dc.ColumnName;
            //        string value = CommonLib.Common.Validate.IsNullString(ds.Tables[0].Rows[0][dc.ColumnName]);

            //        item.Add(key, value);
            //    }

            //    return item;

            //}
            //else
            //{
            //    return null;
            //}

            this.SqlText = "SELECT * FROM sy_users WHERE UserID = ";

            return base.load(id);
        }

        /// <summary>
        /// 新增用户
        /// </summary>
        /// <param name="data"></param>
        /// <returns>返回新增用户ID</returns>
        public override string add(System.Collections.Hashtable data)
        {
            //DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            //DatabaseLib.Tools tools = new DatabaseLib.Tools();

            ////获取数据访问操作端
            //DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            //string strSql = "INSERT INTO sy_users(Name, Password, PassStr, HeadPic, Email, Phone, DeptID, UserTypeID, CreateAt, ModifyAt) VALUES('@Name@', '@Password@', '@PassStr@', '@HeadPic@', '@Email@', '@Phone@', @DeptID@, @UserTypeID@, '@CreateAt@', '@ModifyAt@'); SELECT UserID FROM sy_users ORDER BY UserID DESC LIMIT 0, 1";

            //foreach (System.Collections.DictionaryEntry item in data)
            //{
            //    string strKey = "@" + item.Key + "@";
            //    string strValue = item.Value.ToString();

            //    //用户密码md5 加密
            //    if (item.Key.ToString() == "Password")
            //    {
            //        strSql = strSql.Replace("@PassStr@", strValue);

            //        strValue = CommonLib.Common.Function.toMD5String(strValue);
            //    }

            //    strSql = strSql.Replace(strKey, strValue);
            //}

            //strSql = tools.fixSqlText(strSql);

            //dataClient.SqlText = strSql;

            //DataSet ds = dataClient.Query();

            //this.Message = dataClient.Message;
            //this.Result = dataClient.Result;

            //if (this.Result)
            //{
            //    return ds.Tables[0].Rows[0][0].ToString();
            //}
            //else
            //{
            //    return null;
            //}

            //foreach (System.Collections.DictionaryEntry item in data)
            //{
            //    //用户密码md5 加密
            //    if (item.Key.ToString() == "Password")
            //    {
            //        strSql = strSql.Replace("@PassStr@", strValue);

            //        data[] = CommonLib.Common.Function.toMD5String(strValue);
            //    }
            //}

            string strValue = data["Password"].ToString();

            data.Add("PassStr", strValue);

            data["Password"] = CommonLib.Common.Function.toMD5String(strValue);

            this.SqlText = "INSERT INTO sy_users(Name, Password, PassStr, HeadPic, Email, Phone, DeptID, UserTypeID, CreateAt, ModifyAt) VALUES('@Name@', '@Password@', '@PassStr@', '@HeadPic@', '@Email@', '@Phone@', @DeptID@, @UserTypeID@, '@CreateAt@', '@ModifyAt@'); SELECT UserID FROM sy_users ORDER BY UserID DESC LIMIT 0, 1";

            return base.add(data);
        }

        /// <summary>
        /// 保存用户信息
        /// </summary>
        /// <param name="id"></param>
        /// <param name="data"></param>
        public override void save(System.Collections.Hashtable data)
        {
            //DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            //DatabaseLib.Tools tools = new DatabaseLib.Tools();

            ////获取数据访问操作端
            //DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            //string strSql = "UPDATE sy_users SET Name = '@Name@', Password = '@Password@', PassStr = '@PassStr@', HeadPic = '@HeadPic@', Email = '@Email@', Phone = '@Phone@', DeptID = @DeptID@, UserTypeID = @UserTypeID@, ModifyAt = '@ModifyAt@' WHERE UserID = @UserID@";

            //foreach (System.Collections.DictionaryEntry item in data)
            //{
            //    string strKey = "@" + item.Key + "@";
            //    string strValue = item.Value.ToString();

            //    //用户密码md5 加密
            //    if (item.Key.ToString() == "Password")
            //    {
            //        strSql = strSql.Replace("@PassStr@", strValue);

            //        strValue = CommonLib.Common.Function.toMD5String(strValue);
            //    }

            //    strSql = strSql.Replace(strKey, strValue);
            //}

            //strSql = tools.fixSqlText(strSql);

            //dataClient.SqlText = strSql;

            //dataClient.Execute(CommandType.Text);

            //this.Message = dataClient.Message;
            //this.Result = dataClient.Result;

            string strValue = data["Password"].ToString();

            data.Add("PassStr", strValue);

            data["Password"] = CommonLib.Common.Function.toMD5String(strValue);

            this.SqlText = "UPDATE sy_users SET Name = '@Name@', Password = '@Password@', PassStr = '@PassStr@', HeadPic = '@HeadPic@', Email = '@Email@', Phone = '@Phone@', DeptID = @DeptID@, UserTypeID = @UserTypeID@, ModifyAt = '@ModifyAt@' WHERE UserID = @UserID@";

            base.save(data);
        }

        public override void delete(string id)
        {
            //DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            //DatabaseLib.Tools tools = new DatabaseLib.Tools();

            ////获取数据访问操作端
            //DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            //string strSql = "DELETE FROM sy_users WHERE UserID = " + id;

            //strSql = tools.fixSqlText(strSql);

            //dataClient.SqlText = strSql;

            //dataClient.Execute(CommandType.Text);

            //this.Message = dataClient.Message;
            //this.Result = dataClient.Result;
            this.SqlText = "DELETE FROM sy_users WHERE UserID = ";

            base.delete(id);
        }
    }
}
