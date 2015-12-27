using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Collections;

namespace DatabaseLib
{
    public class Tools :BaseLib.BaseClass
    {
        /// <summary>
        /// 将参数字符串转为hashtable的形式,方便循环保存
        /// </summary>
        /// <param name="param"></param>
        /// <returns></returns>
        public Hashtable paramToData(string param)
        {
            Hashtable data = new Hashtable();

            string[] arr_param = param.Split('&');

            foreach (string str in arr_param)
            {
                string[] tmp_param = str.Split('=');

                string key = tmp_param[0];
                string value = CommonLib.Common.Validate.IsNullString(System.Web.HttpUtility.UrlDecode(tmp_param[1], Encoding.GetEncoding("UTF-8")), "NULL");

                data.Add(key, value);
            }
            //给与提交数据实体当前修改时间
            data.Add("EndeAt", DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss"));
            data.Add("CreateAt", DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss"));
            data.Add("ModifyAt", DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss"));

            return data;
        }

        /// <summary>
        /// 将参数字符串转为hashtable的形式,方便循环保存
        /// </summary>
        /// <param name="param"></param>
        /// <returns></returns>
        public Hashtable paramToData(System.Collections.Specialized.NameValueCollection param)
        {
            Hashtable data = new Hashtable();

            foreach (string key in param.Keys)
            {
                string value = param[key];

                data.Add(key, value);
            }

            //给与提交数据实体当前修改时间
            data.Add("CreateAt", DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss"));
            data.Add("ModifyAt", DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss"));

            return data;
        }

        /// <summary>
        /// 处理完整sql语句中没有匹配上参数的字符
        /// </summary>
        /// <param name="strSql"></param>
        /// <returns></returns>
        public string fixSqlText(string strSql)
        {
            strSql = strSql.Replace("'NULL'", "null");
            strSql = System.Text.RegularExpressions.Regex.Replace(strSql, @"[@]\w+[@]", "null");

            return strSql;
        }

        /// <summary>
        /// 判断数据集是否为空
        /// </summary>
        /// <param name="ds"></param>
        /// <returns>不为空返回true, 为空返回false</returns>
        public static bool tableIsNull(System.Data.DataSet ds)
        {
            if (ds == null || ds.Tables.Count == 0 || ds.Tables[0].Rows.Count == 0)
            {
                return false;
            }

            return true;
        }

        public string createUpdateSqlText(Hashtable data, string key)
        {
            StringBuilder sql = new StringBuilder();

            foreach (System.Collections.DictionaryEntry item in data)
            {
                if (key.IndexOf(item.Key.ToString()) > -1)
                    continue;

                if (sql.Length > 0)
                    sql.Append(", ");

                sql.Append(item.Key);
                sql.Append("='");
                sql.Append(item.Value);
                sql.Append("' ");
            }

            return sql.ToString();
        }
    }
}
