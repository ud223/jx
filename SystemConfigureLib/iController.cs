using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data;

namespace SystemConfigureLib
{
    /// <summary>
    /// 抽象控制类
    /// </summary>
    public abstract class iController :BaseLib.BaseClass
    {
        private string _sSqlText;

        public string SqlText
        { get { return this._sSqlText; } set { this._sSqlText = value; } }

        public virtual List<System.Collections.Hashtable> getAll()
        {
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            //获取数据访问操作端
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            dataClient.SqlText = this.SqlText;

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;

            List<System.Collections.Hashtable> list = null;

            if (this.Result)
            {
                list = new List<System.Collections.Hashtable>();

                foreach (DataRow row in ds.Tables[0].Rows)
                {
                    System.Collections.Hashtable ht = new System.Collections.Hashtable();

                    foreach (DataColumn dc in ds.Tables[0].Columns)
                    {
                        string key = dc.ColumnName;
                        string value = row[dc.ColumnName].ToString();

                        ht.Add(key, value);
                    }

                    list.Add(ht);

                }

                return list;
            }
            else
                return null;
        }


        public virtual System.Collections.Hashtable load(string id)
        {
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            string strSql = this.SqlText + id;

            dataClient.SqlText = strSql;

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;

            if (this.Result)
            {
                if (!DatabaseLib.Tools.tableIsNull(ds))
                {
                    return null;
                }

                System.Collections.Hashtable item = new System.Collections.Hashtable();

                foreach (DataColumn dc in ds.Tables[0].Columns)
                {
                    string key = dc.ColumnName;
                    string value = CommonLib.Common.Validate.IsNullString(ds.Tables[0].Rows[0][dc.ColumnName]);

                    item.Add(key, value);
                }

                return item;

            }
            else
            {
                return null;
            }
        }

        public virtual System.Collections.Hashtable loadStructure()
        {
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            string strSql = this.SqlText;

            dataClient.SqlText = strSql;

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;

            if (this.Result)
            {
                if (ds == null)
                    return null;

                System.Collections.Hashtable item = new System.Collections.Hashtable();

                foreach (DataColumn dc in ds.Tables[0].Columns)
                {
                    string key = dc.ColumnName;
                    string value = "";

                    item.Add(key, value);
                }

                return item;

            }
            else
            {
                return null;
            }
        }

        /// <summary>
        /// 新增
        /// </summary>
        /// <param name="data"></param>
        /// <returns>返回新增ID</returns>
        public virtual string add(System.Collections.Hashtable data)
        {
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();

            //获取数据访问操作端
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            string strSql = this.extendSql(this.SqlText, data);

            dataClient.SqlText = strSql;

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;

            if (this.Result)
            {
                return ds.Tables[0].Rows[0][0].ToString();
            }
            else
            {
                return strSql;
            }
        }

        /// <summary>
        /// 保存信息
        /// </summary>
        /// <param name="id"></param>
        /// <param name="data"></param>
        public virtual void save(System.Collections.Hashtable data)
        {
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();

            //获取数据访问操作端
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            string strSql = this.extendSql(this.SqlText, data);

            dataClient.SqlText = strSql;

            dataClient.Execute(CommandType.Text);

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;
        }

        public virtual void delete(string id)
        {
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            //获取数据访问操作端
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            string strSql = this.SqlText + id;

            strSql = tools.fixSqlText(strSql);

            dataClient.SqlText = strSql;

            dataClient.Execute(CommandType.Text);

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;
        }

        public virtual List<System.Collections.Hashtable> Query(string strSql)
        {
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            //获取数据访问操作端
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            dataClient.SqlText = strSql;

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;

            List<System.Collections.Hashtable> list = new List<System.Collections.Hashtable>();

            if (this.Result)
            {
                foreach (DataRow row in ds.Tables[0].Rows)
                {
                    System.Collections.Hashtable ht = new System.Collections.Hashtable();

                    foreach (DataColumn dc in ds.Tables[0].Columns)
                    {
                        string key = dc.ColumnName;
                        string value = CommonLib.Common.Validate.IsNullString(row[dc.ColumnName]);

                        ht.Add(key, value);
                    }

                    list.Add(ht);

                }

                return list;
            }
            else
                return null;
        }

        /// <summary>
        /// 合并模板sql语句和数据为执行sql语句
        /// </summary>
        /// <param name="strSql">模板sql语句</param>
        /// <param name="data">hashtable类型的数据集合</param>
        /// <returns>执行的sql语句字符串</returns>
        public string extendSql(string strSql, System.Collections.Hashtable data)
        {
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            foreach (System.Collections.DictionaryEntry item in data)
            {
                string strKey = "@" + item.Key + "@";
                string strValue = item.Value.ToString();

                strSql = strSql.Replace(strKey, strValue);
            }

            strSql = tools.fixSqlText(strSql);

            return strSql;
        }
    }
}
