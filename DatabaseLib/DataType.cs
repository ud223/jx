using System;

namespace DatabaseLib
{
    /// <summary>
    /// 数据库类型枚举
    /// </summary>
    public enum DataType
    {
        /// <summary>
        /// 没有指定数据库
        /// </summary>
        None = 0,
        /// <summary>
        /// Sql2000数据库
        /// </summary>
        SqlClient = 1,
        /// <summary>
        /// Oracle数据库
        /// </summary>
        OracleClient = 2,
        /// <summary>
        /// MySql数据库
        /// </summary>
        MySqlClient = 3
    }
}
