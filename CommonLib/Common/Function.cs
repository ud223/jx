using System;
using System.Data;
using System.Security.Cryptography;

namespace CommonLib.Common
{
    /// <summary>
    /// 通用小方法
    /// </summary>
    public class Function
    {
        /// <summary>
        /// 判断对象是否为空,同时返回对象字符串
        /// </summary>
        /// <param name="obj"></param>
        /// <returns></returns>
        public static string IsNull(object obj)
        {
            if (obj == null || obj == System.DBNull.Value)
                return "";

            return obj.ToString();
        }

        /// <summary>
        /// 判断表列集合中是否包含该字段列,不存在就返回null,存在就返回应对对象
        /// </summary>
        /// <param name="dt"></param>
        /// <param name="ColumnName"></param>
        /// <returns></returns>
        public static object Contains(DataRow dr, string ColumnName)
        {
            if (!dr.Table.Columns.Contains(ColumnName) || dr[ColumnName] == System.DBNull.Value)
                return null;

            return dr[ColumnName];
        }

        /// <summary>
        /// 判断HashTable中是否包含该key的value,存在就返回对应值,不存在就返回null
        /// </summary>
        /// <param name="ht"></param>
        /// <param name="key"></param>
        /// <returns></returns>
        public static object Contains(System.Collections.Hashtable ht, string key)
        {
            if (ht.Contains(key))
                return ht[key];

            return null;
        }

        /// <summary>
        /// 布尔类型转整形字符串
        /// </summary>
        /// <param name="b"></param>
        /// <returns></returns>
        public static string BooleanToIntString(bool b)
        {
            if (b)
                return "1";
            else
                return "0";
        }

        /// <summary>
        /// 字符串md5 32位加密
        /// </summary>
        /// <param name="str"></param>
        /// <returns></returns>
        public static string toMD5String(string str)
        {
            string cl = str;
            string pwd = "";
            MD5 md5 = MD5.Create();//实例化一个md5对像
            // 加密后是一个字节类型的数组，这里要注意编码UTF8/Unicode等的选择　
            byte[] s = md5.ComputeHash(System.Text.Encoding.UTF8.GetBytes(cl));
            // 通过使用循环，将字节类型的数组转换为字符串，此字符串是常规字符格式化所得
            for (int i = 0; i < s.Length; i++)
            {
                // 将得到的字符串使用十六进制类型格式。格式后的字符是小写的字母，如果使用大写（X）则格式后的字符是大写字符 
                pwd = pwd + s[i].ToString("X");

            }

            return pwd;
        }
    }
}
