using System;
using System.Data;

namespace DatabaseLib
{
    /// <summary>
    /// 抽象数据访问类
    /// </summary>
    public abstract class IDatabase : CommonLib.BaseClass
    {
        #region 提交数据

        private string _sSqlText = null;

        /// <summary>
        /// 要执行的SQL语句
        /// </summary>
        public string SqlText
        { get { return this._sSqlText; } set { this._sSqlText = value; } }

        private string _sServer;

        /// <summary>
        /// 服务路径
        /// </summary>
        public string Server
        { 
            get 
            {
                if (this._sServer == null)
                    this._sServer = CommonLib.Common.ConfigReader.Read("Server", this.SystemType);

                return this._sServer; 
            } 
            set 
            { 
                this._sServer = value; 
            } 
        }

        private string _sDatabaseName;

        /// <summary>
        /// 数据库名
        /// </summary>
        public string DatabaseName
        { 
            get 
            {
                if (this._sDatabaseName == null)
                    this._sDatabaseName = CommonLib.Common.ConfigReader.Read("Database", this.SystemType);

                return this._sDatabaseName; 
            } 
            set 
            { 
                this._sDatabaseName = value; 
            } 
        }

        private string _sUid;

        /// <summary>
        /// 数据库访问用户ID
        /// </summary>
        public string Uid
        { 
            get 
            {
                if (this._sUid == null)
                    this._sUid = CommonLib.Common.ConfigReader.Read("UID", this.SystemType);

                return this._sUid; 
            } 
            set 
            { 
                this._sUid = value; 
            } 
        }

        private string _sPassword;

        /// <summary>
        /// 数据库访问密码
        /// </summary>
        public string Password
        { 
            get 
            {
                if (this._sPassword == null)
                    this._sPassword = CommonLib.Common.ConfigReader.Read("PWD", this.SystemType);

                return this._sPassword; 
            } 
            set 
            { 
                this._sPassword = value; 
            } 
        }

        private DataType _DatabaseType;

        /// <summary>
        /// 数据库类型
        /// </summary>
        public DataType DatabaseType
        { 
            get 
            {
                if (this._DatabaseType == DatabaseLib.DataType.None)
                    this._DatabaseType = (DatabaseLib.DataType)Int32.Parse(CommonLib.Common.ConfigReader.Read("DataType", this.SystemType));

                return this._DatabaseType; 
            } 
            set 
            { 
                this._DatabaseType = value; 
            } 
        }

        private string _sPort;

        /// <summary>
        /// 数据库端口号
        /// </summary>
        public string Port
        {
            get
            {
                if (this._sPort == null || this._sPort == "")
                    this._sPort = CommonLib.Common.ConfigReader.Read("Port", this.SystemType);

                return this._sPort;
            }
            set
            {
                this._sPort = value;
            }
        }

        #endregion

        #region 操作成员

        private IDbConnection _con;

        /// <summary>
        /// 数据连接
        /// </summary>
        protected IDbConnection Connection
        {
            get
            {
                if (this._con == null || this._con.ConnectionString.IndexOf(this.DatabaseName) < 0)
                    this._con = CreateConnection();

                return this._con;
            }
            set
            {
                this._con = value;
            }
        }

        //private IDbCommand _cmd;

        //protected IDbCommand Command
        //{
        //    get
        //    {
        //        if (this._con == null)
        //            this._cmd = CreateCommand();

        //        return this._cmd;
        //    }
        //    set
        //    {
        //        this._cmd = value;
        //    }
        //}

        private IDataAdapter _adapter;

        /// <summary>
        /// 数据读取器
        /// </summary>
        protected IDataAdapter DataAdapter
        {
            get
            {
                this._adapter = CreateDataAdapter();

                return _adapter;
            }
            set
            {
                this._adapter = value;
            }
        }

        private IDataReader _dataReader;

        /// <summary>
        /// 数据读取器
        /// </summary>
        public IDataReader DataReader
        { get { return this._dataReader; } set { this._dataReader = value; } }

        private string _sConnectionString;


        public virtual string ConnectionString
        { get { return this._sConnectionString; } set { this._sConnectionString = value; } }

        private int _nCount = -1;

        /// <summary>
        /// 操作记录的行数
        /// </summary>
        public int Count
        { get { return this._nCount; } set { this._nCount = value; } }

        /// <summary>
        /// 操作是否成功
        /// </summary>
        public new bool Result
        { get { return base.Result; } set { base.Result = value; } }

        /// <summary>
        /// 内部操作消息
        /// </summary>
        public new string Message
        { get { return base.Message; } set { base.Message = value; } }

        #endregion

        #region 抽象方法

        /// <summary>
        /// 创建数据库连接对象
        /// </summary>
        /// <returns></returns>
        protected abstract IDbConnection CreateConnection();

        /// <summary>
        /// 创建填充器对象
        /// </summary>
        /// <param name="con">数据库连接对象</param>
        /// <returns></returns>
        protected abstract IDataAdapter CreateDataAdapter();

        /// <summary>
        /// 创建DataReader对象
        /// </summary>
        /// <returns></returns>
        protected abstract IDataReader CreateDataReader();

        /// <summary>
        /// 创建命令对象
        /// </summary>
        /// <returns></returns>
        protected abstract IDbCommand CreateCommand();

        /// <summary>
        /// 创建事物对象
        /// </summary>
        /// <returns></returns>
        protected abstract IDbTransaction CreateTransaction();

        /// <summary>
        /// 用存储过程查询数据返回一个数据集 
        /// </summary>
        /// <returns>返回一个视图数据载体</returns>
        public abstract DataSet Query(IDataParameter[] pParams, CommandType commandType);

        /// <summary>
        /// 查询特定范围的数据集(高效)
        /// </summary>
        /// <param name="nStart">数据头</param>
        /// <param name="nEnd">数据尾</param>
        /// <returns>返回一个视图数据载体</returns>
        public abstract DataSet Query(int nStart, int nEnd);

        #endregion

        #region 工具方法

        /// <summary>
        /// 创建存储过程简易参数
        /// </summary>
        /// <param name="ParaName">参数名</param>
        /// <param name="Value">参数值</param>
        /// <returns></returns>
        public abstract IDataParameter MakeParam(string ParaName, object Value);

        /// <summary>
        /// 创建存储过程参数
        /// </summary>
        /// <param name="ParamName">参数名称</param>
        /// <param name="DbType">参数类型</param>
        /// <param name="Size">参数大小</param>
        /// <param name="Direction">参数方向</param>
        /// <param name="Value">参数值</param>
        /// <returns>返回一个新的参数</returns>
        public abstract IDataParameter MakeParam(string ParamName, DbType DbType, Int32 Size, ParameterDirection Direction, object Value);

        /// <summary>
        /// 关闭数据连接对象
        /// </summary>
        public virtual void Close()
        {
            if (this._dataReader != null && !this._dataReader.IsClosed)
                this._dataReader.Close();

            if (this._con.State == ConnectionState.Open)
                this._con.Close();

            if (this._con != null)
                this._con.Close();
        }

        /// <summary>
        /// 创建并打开数据库连接对象
        /// </summary>
        public virtual void Open()
        {
            if (this.Connection.State == ConnectionState.Closed)
                this.Connection.Open();
        }

        /// <summary>
        /// 释放数据库资源
        /// </summary>
        public virtual void Dispose()
        {
            GC.SuppressFinalize(this);
        }

        /// <summary>
        /// 创建存储过程命令使用对象
        /// </summary>
        /// <param name="procName">过程名</param>
        /// <param name="prams">过程参数集合</param>
        /// <returns>命令对象</returns>
        public virtual IDbCommand CreateCommand(IDataParameter[] pParams, CommandType commondType)
        {
            IDbCommand dCommand = this.CreateCommand();

            //创建一个数据库操作命令对象
            //给数据库命令操作对象类型赋存储过程属性
            dCommand.CommandType = commondType;

            //循环添加过程参数
            if (pParams != null)
            {
                foreach (IDataParameter parameter in pParams)
                    dCommand.Parameters.Add(parameter);
            }

            // 添加创建的过程参数集合
            dCommand.Parameters.Add(this.MakeReturnParam("ReturnValue", DbType.Int32, 4));
            //返回过程对象
            return dCommand;
        }

        /// <summary>
        /// 创建存储过程命令使用对象
        /// </summary>
        /// <param name="procName">过程名</param>
        /// <param name="prams">过程参数集合</param>
        /// <returns>命令对象</returns>
        public virtual IDbCommand HashCreateCommand(System.Collections.Hashtable pParams, CommandType commondType)
        {
            IDbCommand dCommand = this.CreateCommand();

            //创建一个数据库操作命令对象
            //给数据库命令操作对象类型赋存储过程属性
            dCommand.CommandType = commondType;

            //循环添加过程参数
            if (pParams != null)
            {
                foreach (System.Collections.DictionaryEntry parameter in pParams)
                    dCommand.Parameters.Add(MakeParam("@" + parameter.Key,parameter.Value));
            }

            // 添加创建的过程参数集合
            dCommand.Parameters.Add(this.MakeReturnParam("ReturnValue", DbType.Int32, 4));
            //返回过程对象
            return dCommand;
        }

        /// <summary>
        /// 创建一个存储过程输入参数
        /// </summary>
        /// <param name="ParamName">参数名称</param>
        /// <param name="DbType">参数类型</param>
        /// <param name="Size">参数大小</param>
        /// <param name="Value">参数值</param>
        /// <returns>返回一个参数</returns>
        public virtual IDataParameter MakeInParam(string ParamName, DbType DbType, int Size, object Value)
        {
            return MakeParam(ParamName, DbType, Size, ParameterDirection.Input, Value); ;
        }

        /// <summary>
        /// 创建一个存储过程返回参数
        /// </summary>
        /// <param name="ParamName">参数名称</param>
        /// <param name="DbType">参数类型</param>
        /// <param name="Size">参数大小</param>
        /// <returns>返回一个参数</returns>
        public virtual IDataParameter MakeOutParam(string ParamName, DbType DbType, int Size)
        {
            return MakeParam(ParamName, DbType, Size, ParameterDirection.Output, null);
        }

        /// <summary>
        /// 创建过程返回值
        /// </summary>
        /// <param name="ParaName">参数名称</param>
        /// <param name="DbType">参数类别</param>
        /// <param name="Size">参数大小</param>
        /// <returns>返回值</returns>
        public virtual IDataParameter MakeReturnParam(string ParaName, DbType DbType, int Size)
        {
            return MakeParam(ParaName, DbType, Size, ParameterDirection.ReturnValue, null);
        }

        #endregion

        #region 查询方法

        /// <summary>
        /// 传SQL语句查询数据返回一个视图 
        /// </summary>
        /// <returns>返回一个视图数据载体</returns>
        public virtual DataSet Query()
        {
            //实例一个数据载体
            DataSet dDataSet = new DataSet();

            //将表中约束及主键传入查询数据集
            this.DataAdapter.MissingSchemaAction = MissingSchemaAction.AddWithKey;

            try
            {
                //填充数据
                this._nCount = this.DataAdapter.Fill(dDataSet);

                this.Message = "操作成功!";

                this.Result = true;
            }
            catch (Exception ex)
            {
                //异常后清空掉数据载体
                dDataSet = null;

                //获取异常信息
                this.Message = ex.Message;

                this.Result = false;
            }
            finally
            {
                //关闭数据库连接
                this.Close();
            }

            return dDataSet;
        }

        /// <summary>
        /// 用存储过程查询数据集
        /// </summary>
        /// <param name="procName">存储过程名字</param>
        /// <param name="dDataReader">DataReader数据载体</param>
        public virtual void Read(CommandType commandType)
        {
            //存储过程查询数据
            ReadData(null,commandType);
        }

        /// <summary>
        /// 用带参数的存储过程查询数据集
        /// </summary>
        /// <param name="prams">存储过程参数</param>
        public virtual void Read(IDataParameter[] pParams, CommandType commandType)
        {
            //存储过程查询数据
            ReadData(pParams, commandType);
        }

        /// <summary>
        /// 用带参数的存储过程查询数据集
        /// </summary>
        /// <param name="prams">存储过程参数</param>
        public virtual void Read(System.Collections.Hashtable pParams, CommandType commandType)
        {
            //存储过程查询数据
            HashReadData(pParams, commandType);
        }

        #endregion

        #region 操作数据

        /// <summary>
        /// 执行命令
        /// </summary>
        /// <param name="cmd">要执行的Command对象</param>
        protected virtual void Execute(IDbCommand cmd)
        {
            try
            {
                //打开数据库
                this.Open();

                //执行SQL语句
                this._nCount = cmd.ExecuteNonQuery();

                this.Message = "操作成功!";

                this.Result = true;
            }
            catch (Exception ex)
            {
                //抛出异常
                this.Message = ex.Message;

                this.Result = false;
            }
            finally
            {
                //关闭数据库
                this.Close();
            }
        }

        public virtual string ExecuteScalar(IDbCommand cmd)
        {
            string strValue = "";

            try
            {
                //打开数据库
                this.Open();

                //执行SQL语句
                strValue = cmd.ExecuteScalar().ToString();

                this.Message = "操作成功!";

                this.Result = true;
            }
            catch (Exception ex)
            {
                //抛出异常
                this.Message = ex.Message;

                this.Result = false;
            }
            finally
            {
                //关闭数据库
                this.Close();
            }

            return strValue;
        }

        /// <summary>
        /// 传SQL语句操作数据
        /// </summary>
        public virtual void Execute(CommandType commandType)
        {
            //创建数据库命令对象
            IDbCommand dCommand = this.CreateCommand(null,commandType);

            //传有参数的Command对象调用执行代码
            Execute(dCommand);
        }

        /// <summary>
        /// 带返回值的执行语句
        /// </summary>
        /// <param name="commandType"></param>
        /// <returns></returns>
        public virtual string ExecuteScalar(CommandType commandType)
        {
            //创建数据库命令对象
            IDbCommand dCommand = this.CreateCommand(null, commandType);

            //传有参数的Command对象调用执行代码
            return ExecuteScalar(dCommand);
        }

        /// <summary>
        /// 用存储过程操作数据
        /// </summary>
        /// <param name="prams">存储过程参数</param>
        /// <returns>返回影响的条数</returns>
        public virtual void  Execute(IDataParameter[] pParams, CommandType commandType)
        {
            //创建数据库命令对象
            IDbCommand dCommand = this.CreateCommand(pParams, commandType);

            //传有参数的Command对象调用执行代码
            Execute(dCommand);
        }

        /// <summary>
        /// 带参数的存储过程操作数据
        /// </summary>
        /// <param name="prams">存储过程参数</param>
        /// <returns>返回影响的条数</returns>
        public virtual string ExecuteScalar(IDataParameter[] pParams, CommandType commandType)
        {
            //创建数据库命令对象
            IDbCommand dCommand = this.CreateCommand(pParams, commandType);

            //传有参数的Command对象调用执行代码
            return ExecuteScalar(dCommand);
        }

        /// <summary>
        /// 用存储过程操作数据
        /// </summary>
        /// <param name="prams">存储过程参数</param>
        /// <returns>返回影响的条数</returns>
        public virtual void Execute(System.Collections.Hashtable pParams, CommandType commandType)
        {
            //创建数据库命令对象
            IDbCommand dCommand = this.HashCreateCommand(pParams, commandType);

            //传有参数的Command对象调用执行代码
            Execute(dCommand);
        }

        /// <summary>
        /// 带参数存储过程操作数据
        /// </summary>
        /// <param name="prams">存储过程参数</param>
        /// <returns>返回影响的条数</returns>
        public virtual string ExecuteScalar(System.Collections.Hashtable pParams, CommandType commandType)
        {
            //创建数据库命令对象
            IDbCommand dCommand = this.HashCreateCommand(pParams, commandType);

            //传有参数的Command对象调用执行代码
            return ExecuteScalar(dCommand);
        }

        /// <summary>
        /// 带参数执行存储过程并返回一个DataReader的数据集合
        /// </summary>
        /// <param name="prams">参数集合</param>
        public void ReadData(IDataParameter[] pParams, CommandType commandType)
        {
            IDbCommand dCommand = this.CreateCommand(pParams, commandType);

            try
            {
                //打开数据库
                this.Open();

                //执行并返回数据读取器
                this._dataReader = dCommand.ExecuteReader(System.Data.CommandBehavior.CloseConnection);

                this.Message = "操作成功!";

                this.Result = true;
            }
            catch (Exception ex)
            {
                this.Message = ex.Message;

                this.Result = false;
            }
        }

        /// <summary>
        /// 带参数执行存储过程并返回一个DataReader的数据集合
        /// </summary>
        /// <param name="prams">参数集合</param>
        public void HashReadData(System.Collections.Hashtable pParams, CommandType commandType)
        {
            IDbCommand dCommand = this.HashCreateCommand(pParams, commandType);

            try
            {
                //打开数据库
                this.Open();

                //执行并返回数据读取器
                this._dataReader = dCommand.ExecuteReader(System.Data.CommandBehavior.CloseConnection);

                this.Message = "操作成功!";

                this.Result = true;
            }
            catch (Exception ex)
            {
                this.Message = ex.Message;

                this.Result = false;
            }
        }

        #endregion

        #region 判断数据是否存在

        /// <summary>
        /// 用SQL语句查询是否有相同的记录
        /// </summary>
        /// <returns>返回一个true or false</returns>
        public virtual bool IsExist(CommandType commandType)
        {
            //定义变量为默认不相同
            bool bResult = false;

            try
            {
                //调用执行读取器方法
                this.Read(commandType);

                //判断是否相同
                if (this._dataReader.Read())
                    bResult = true;

                this.Message = "操作成功!";

                this.Result = true;
            }
            catch (Exception ex)
            {
                //抛出异常
                this.Message = ex.Message;

                this.Result = false;
            }
            finally
            {
                //关闭所有连接
                this.Close();
            }

            //返回结果
            return bResult;
        }

        /// <summary>
        /// 用存储过程查询是否有相同的记录
        /// </summary>
        /// <param name="prams">参数集合</param>
        /// <returns>返回一个true or false</returns>
        public virtual bool IsExist(IDataParameter[] pParams,CommandType commandType)
        {
            //定义变量为默认不相同
            bool bResult = false;

            try
            {
                //调用执行读取器方法
                this.Read(pParams,commandType);

                //判断是否相同
                if (this._dataReader.Read())
                    bResult = true;

                this.Message = "操作成功!";

                this.Result = true;
            }
            catch (Exception ex)
            {
                //抛出异常
                base.Message = ex.Message;

                this.Result = false;
            }
            finally
            {
                //关闭所有连接
                this.Close();
            }

            //返回结果
            return bResult;
        }

        /// <summary>
        /// 用存储过程查询是否有相同的记录
        /// </summary>
        /// <param name="prams">参数集合</param>
        /// <returns>返回一个true or false</returns>
        public virtual bool IsExist(System.Collections.Hashtable pParams, CommandType commandType)
        {
            //定义变量为默认不相同
            bool bResult = false;

            try
            {
                //调用执行读取器方法
                this.Read(pParams, commandType);

                //判断是否相同
                if (this._dataReader.Read())
                    bResult = true;

                this.Message = "操作成功!";

                this.Result = true;
            }
            catch (Exception ex)
            {
                //抛出异常
                base.Message = ex.Message;

                this.Result = false;
            }
            finally
            {
                //关闭所有连接
                this.Close();
            }

            //返回结果
            return bResult;
        }

        #endregion

        #region 数据库内部操作

        /// <summary>
        /// 创建新的数据库
        /// </summary>
        /// <param name="commandType"></param>
        /// <returns></returns>
        public virtual string CreateDatabase(CommandType commandType)
        {
            throw new Exception("Null Function");
        }

        /// <summary>
        /// 是否能连接数据库
        /// </summary>
        /// <returns></returns>
        public virtual void IsCanConnection()
        {
            throw new Exception("Null Function");
        }

        #endregion
    }
}
