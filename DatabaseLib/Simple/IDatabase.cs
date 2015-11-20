using System;
using System.Data;

namespace ToolsLib.Data
{
    public interface IDatabase
    {
        /// <summary>
        /// 已读取的DataReader对象
        /// </summary>
        System.Data.IDataReader DataReader
        { get; }

        string ConnectionString { get; set; }

        /// <summary>
        /// 返回操作结果信息
        /// </summary>
        string Message { get; }

        /// <summary>
        /// 返回操作结果
        /// </summary>
        bool Result { get; }

        /// <summary>
        /// 打开数据库连接
        /// </summary>
        void Open();

        /// <summary>
        /// 关闭所有操作资源
        /// </summary>
        void Close();

        /// <summary>
        /// 用DataReader的方式读取数据库
        /// </summary>
        /// <param name="strSql"></param>
        void Read(string strSql);

        /// <summary>
        /// 查询数据集合
        /// </summary>
        /// <param name="strSql"></param>
        /// <returns></returns>
        DataSet Query(string strSql);

        /// <summary>
        /// 执行数据库操作
        /// </summary>
        /// <param name="strSql"></param>
        /// <returns></returns>
        int Execute(string strSql);

        /// <summary>
        /// 执行数据库操作
        /// </summary>
        /// <param name="strSql"></param>
        /// <param name="oParams"></param>
        /// <returns></returns>
        int Execute(string strSql, IDataParameter[] oParams);

        /// <summary>
        /// 返回单个数据字符串
        /// </summary>
        /// <param name="strSql"></param>
        /// <returns></returns>
        object ExecuteScalarSQL(string strSql);

        /// <summary>
        /// 创建存储过程参数
        /// </summary>
        /// <param name="ParamName">参数名称</param>
        /// <param name="DbType">参数类型</param>
        /// <param name="Size">参数大小</param>
        /// <param name="Direction">参数方向</param>
        /// <param name="Value">参数值</param>
        /// <returns>返回一个新的参数</returns>
        IDataParameter MakeParam(string ParamName, object Value);
    }
}
