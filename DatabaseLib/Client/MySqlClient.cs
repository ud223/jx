using System;
using System.Collections.Generic;
using System.Text;
using System.Data;
using MySql.Data.MySqlClient;

namespace DatabaseLib.Client
{
    /// <summary>
    /// MySql数据操作类
    /// </summary>
    public class MySqlClient : IDatabase
    {
        private StringBuilder _sConnectionString = new StringBuilder();

        /// <summary>
        /// 获取数据库连接字符串
        /// </summary>
        public override string ConnectionString
        {
            get
            {
                if (_sConnectionString == null || _sConnectionString.Length == 0 || this._sConnectionString.ToString().IndexOf(this.DatabaseName) < 0)
                    GetConnectionString();

                return _sConnectionString.ToString();
            }
            set
            {
                if (this._sConnectionString.Length > 0)
                    this._sConnectionString.Remove(0, this._sConnectionString.Length);

                this._sConnectionString.Append(value);
            }
        }

        private void GetConnectionString()
        {
            if (this._sConnectionString == null)
            {
                //实例化连接对象
                _sConnectionString = new StringBuilder();
            }
            else
            {
                this._sConnectionString.Remove(0, this._sConnectionString.Length);
            }

            //拼接数据库连接对象
            _sConnectionString.Append("Server=");
            _sConnectionString.Append(this.Server);
            _sConnectionString.Append(";Database=");
            _sConnectionString.Append(this.DatabaseName);
            _sConnectionString.Append(";User ID=");
            _sConnectionString.Append(this.Uid);
            _sConnectionString.Append(";password=");
            _sConnectionString.Append(this.Password);
            _sConnectionString.Append(";Port=");
            _sConnectionString.Append(this.Port);
            _sConnectionString.Append(";pooling=false;charset=utf8");
        }

        protected override IDbConnection CreateConnection()
        {
            return new MySqlConnection(this.ConnectionString); ////
        }

        protected override IDataAdapter CreateDataAdapter()
        {
            return new MySqlDataAdapter(this.SqlText, (MySqlConnection)this.Connection);
        }

        protected override IDataReader CreateDataReader()
        {
            throw new Exception("The method or operation is not implemented.");
        }

        protected override IDbCommand CreateCommand()
        {
            return new MySqlCommand(this.SqlText, (MySqlConnection)this.Connection);
        }

        /// <summary>
        /// 创建事物
        /// </summary>
        /// <returns></returns>
        protected override IDbTransaction CreateTransaction()
        {
            throw new Exception("The method or operation is not implemented.");
        }

        //public override DataSet Query()
        //{
        //    //MySqlCommand cmd = new MySqlCommand("set names gb2312", this.Connection);
        //    //实例一个数据载体
        //    DataSet dDataSet = new DataSet();

        //    //将表中约束及主键传入查询数据集
        //    this.DataAdapter.MissingSchemaAction = MissingSchemaAction.AddWithKey;

        //    try
        //    {
        //        this.Open();

        //        cmd.ExecuteNonQuery();

        //        //填充数据
        //        this.Count = this.DataAdapter.Fill(dDataSet);

        //        this.Result = true;
        //    }
        //    catch (Exception ex)
        //    {
        //        //异常后清空掉数据载体
        //        dDataSet = null;

        //        //获取异常信息
        //        this.Message = ex.Message;
        //    }
        //    finally
        //    {
        //        //关闭数据库连接
        //        this.Close();
        //    }

        //    return dDataSet;
        //}

        /// <summary>
        /// 查询
        /// </summary>
        /// <param name="pParams"></param>
        /// <param name="commandType"></param>
        /// <returns></returns>
        public override DataSet Query(IDataParameter[] pParams, CommandType commandType)
        {
            MySqlCommand dCommand = (MySqlCommand)this.CreateCommand(pParams, commandType);

            MySqlDataAdapter dDataAdapter = new MySqlDataAdapter(dCommand);

            //将表中约束及主键传入查询数据集
            dDataAdapter.MissingSchemaAction = MissingSchemaAction.AddWithKey;

            DataSet ds = new DataSet();

            try
            {
                this.Count = dDataAdapter.Fill(ds);
            }
            catch (Exception ex)
            {
                this.Message = ex.Message;

                this.Result = false;
            }
            finally
            {
                this.Close();
            }

            return ds;
        }

        /// <summary>
        /// 创建参数
        /// </summary>
        /// <param name="ParaName"></param>
        /// <param name="Value"></param>
        /// <returns></returns>
        public override IDataParameter MakeParam(string ParaName, object Value)
        {
            return new MySqlParameter(ParaName, Value);
        }

        /// <summary>
        /// 创建参数
        /// </summary>
        /// <param name="ParamName"></param>
        /// <param name="DbType"></param>
        /// <param name="Size"></param>
        /// <param name="Direction"></param>
        /// <param name="Value"></param>
        /// <returns></returns>
        public override IDataParameter MakeParam(string ParamName, DbType DbType, int Size, ParameterDirection Direction, object Value)
        {
            MySqlParameter pParam;

            //判断参数是否有大小设置，-1为没有
            if (Size != -1)
                pParam = new MySqlParameter(ParamName, this.ConvertToSqlDbType(DbType));
            else
                pParam = new MySqlParameter(ParamName, this.ConvertToSqlDbType(DbType), Size);

            pParam.Direction = Direction;

            //判断参数方向是输入还是输出参数
            if (!(Direction == ParameterDirection.Output && Value == null))
                pParam.Value = Value;

            return pParam;
        }

        /// <summary>
        /// 创建SQL命令对象
        /// </summary>
        /// <param name="pParams"></param>
        /// <param name="commondType"></param>
        /// <returns></returns>
        public override IDbCommand CreateCommand(IDataParameter[] pParams, CommandType commondType)
        {
            return new MySqlCommand(this.SqlText, (MySqlConnection)this.Connection);
        }

        public override void IsCanConnection()
        {
            throw new Exception("没有实现该方法!");
        }

        public override DataSet Query(int nStart, int nEnd)
        {
            throw new Exception("The method or operation is not implemented.");
        }

        //protected override void Execute(IDbCommand cmd)
        //{
        //    try
        //    {
        //        //打开数据库
        //        this.Open();

        //        MySQLCommand cmdChar = new MySQLCommand("set charset gb2312", (MySQLConnection)cmd.Connection);

        //        cmdChar.ExecuteNonQuery();

        //        //执行SQL语句
        //        this.Count = cmd.ExecuteNonQuery();

        //        this.Result = true;
        //    }
        //    catch (Exception ex)
        //    {
        //        //抛出异常
        //        this.Message = ex.Message;
        //    }
        //    finally
        //    {
        //        //关闭数据库
        //        this.Close();
        //    }
        //}

        /// <summary>
        /// 将DbType类型转换为SqlDbType类型
        /// </summary>
        /// <param name="pSourceType">DbType类型</param>
        /// <returns>转换后的SqlDbType类型</returns>
        private MySqlDbType ConvertToSqlDbType(DbType pSourceType)
        {
            MySqlParameter paraConver = new MySqlParameter();

            paraConver.DbType = pSourceType;

            return paraConver.MySqlDbType;
        }
    }
}
