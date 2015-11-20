using System;
using System.Data;
using System.Data.SqlClient;
using System.Text;

namespace DatabaseLib.Client
{
    /// <summary>
    /// Sql2000数据库操作类
    /// </summary>
    public class SqlClient : IDatabase
    {
        #region 数据成员

        private StringBuilder _sConnectionString = null;

        /// <summary>
        /// 获取数据库连接字符串
        /// </summary>
        public override string ConnectionString
        {
            get
            {
                if (_sConnectionString == null || _sConnectionString.Length == 0)
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

        #endregion

        #region 工具方法

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
            _sConnectionString.Append("server=");
            _sConnectionString.Append(this.Server);
            _sConnectionString.Append(";uid=");
            _sConnectionString.Append(this.Uid);
            _sConnectionString.Append(";pwd=");
            _sConnectionString.Append(this.Password);
            _sConnectionString.Append(";database=");
            _sConnectionString.Append(this.DatabaseName);
        }

        /// <summary>
        /// 创建简易存储过程参数
        /// </summary>
        /// <param name="ParaName">参数名</param>
        /// <param name="Value">参数的值</param>
        /// <returns></returns>
        public override IDataParameter MakeParam(string ParaName, object Value)
        {
            return new SqlParameter(ParaName, Value);
        }

        /// <summary>
        /// 将DbType类型转换为SqlDbType类型
        /// </summary>
        /// <param name="pSourceType">DbType类型</param>
        /// <returns>转换后的SqlDbType类型</returns>
        private SqlDbType ConvertToSqlDbType(DbType pSourceType)
        {
            SqlParameter paraConver = new SqlParameter();

            paraConver.DbType = pSourceType;

            return paraConver.SqlDbType;
        }

        #endregion

        #region 实现的继承的抽象方法

        /// <summary>
        /// 创建一个连接对象
        /// </summary>
        /// <returns></returns>
        protected override IDbConnection CreateConnection()
        {
            return new SqlConnection(this.ConnectionString);
        }

        /// <summary>
        /// 创建一个数据填充器
        /// </summary>
        /// <returns></returns>
        protected override IDataAdapter CreateDataAdapter()
        {
            return new SqlDataAdapter(this.SqlText, (SqlConnection)this.Connection);
        }

        /// <summary>
        /// 创建一个数据读取器
        /// </summary>
        /// <returns></returns>
        protected override IDataReader CreateDataReader()
        {
            throw new NotImplementedException();
        }

        /// <summary>
        /// 创建一个命令执行对象
        /// </summary>
        /// <returns></returns>
        protected override IDbCommand CreateCommand()
        {
            return new SqlCommand(this.SqlText, (SqlConnection)this.Connection);
        }

        /// <summary>
        /// 创建数据库事物对象
        /// </summary>
        /// <returns></returns>
        protected override IDbTransaction CreateTransaction()
        {
            throw new NotImplementedException();
        }

        /// <summary>
        /// Command方式查询数据
        /// </summary>
        /// <param name="pParams"></param>
        /// <returns></returns>
        public override DataSet Query(IDataParameter[] pParams,CommandType commandType)
        {
            SqlCommand dCommand = (SqlCommand)this.CreateCommand(pParams, commandType);

            SqlDataAdapter dDataAdapter = new SqlDataAdapter(dCommand);

            //将表中约束及主键传入查询数据集
            dDataAdapter.MissingSchemaAction = MissingSchemaAction.AddWithKey;

            DataSet ds = new DataSet();

            try
            {
                this.Count = dDataAdapter.Fill(ds);

                this.Message = "操作成功!";

                this.Result = true;
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
        /// 查询部分数据
        /// </summary>
        /// <param name="nStart">查询记录的其实位置</param>
        /// <param name="nEnd">查询记录的结束位置</param>
        /// <returns></returns>
        public override DataSet Query(int nStart, int nEnd)
        {
            SqlDataAdapter dDataAdapter = new SqlDataAdapter(this.SqlText, (SqlConnection)this.Connection);

            //将表中约束及主键传入查询数据集
            dDataAdapter.MissingSchemaAction = MissingSchemaAction.AddWithKey;

            DataSet ds = new DataSet();

            try
            {
                this.Count = dDataAdapter.Fill(ds, nStart, nEnd, "temp");

                this.Message = "操作成功!";

                this.Result = true;
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
        /// 创建存储过程参数
        /// </summary>
        /// <param name="ParamName">参数名</param>
        /// <param name="DbType">数据类型</param>
        /// <param name="Size">数据大小</param>
        /// <param name="Direction">数据方向</param>
        /// <param name="Value">数据值</param>
        /// <returns></returns>
        public override IDataParameter MakeParam(string ParamName, DbType DbType, int Size, ParameterDirection Direction, object Value)
        {
            SqlParameter pParam;

            //判断参数是否有大小设置，-1为没有
            if (Size != -1)
                pParam = new SqlParameter(ParamName, ConvertToSqlDbType(DbType), Size);
            else
                pParam = new SqlParameter(ParamName, ConvertToSqlDbType(DbType));

            pParam.Direction = Direction;

            //判断参数方向是输入还是输出参数
            if (!(Direction == ParameterDirection.Output && Value == null))
                pParam.Value = Value;

            return pParam;
        }

        /// <summary>
        /// 创建新的数据库
        /// </summary>
        /// <param name="commandType"></param>
        /// <returns></returns>
        public override string CreateDatabase(CommandType commandType)
        {
            string strConnectonString = "DATA SOURCE=@Server;USER ID=@Uid;PASSWORD=@Pwd;PERSIST SECURITY INFO=FALSE;PACKET SIZE=4096";
            string strSql = "CREATE DATABASE @DatabaseName";

            strConnectonString = strConnectonString.Replace("@Server", this.Server);
            strConnectonString = strConnectonString.Replace("@Uid", this.Uid);
            strConnectonString = strConnectonString.Replace("@Pwd", this.Password);

            strSql = strSql.Replace("@DatabaseName", this.DatabaseName);

            this.Connection.ConnectionString = strConnectonString;

            //创建数据库命令对象
            IDbCommand dCommand = this.CreateCommand(null, commandType);

            dCommand.Connection = this.Connection;
            dCommand.CommandText = strSql;

            try
            {
                dCommand.Connection.Open();

                dCommand.ExecuteNonQuery();

                this.Message = "操作成功!";

                this.Result = true;
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

            return strConnectonString;
        }

        /// <summary>
        /// 是否能连接指定数据库
        /// </summary>
        /// <returns></returns>
        public override void IsCanConnection()
        {
            string strConnectonString = "DATA SOURCE=@Server;USER ID=@Uid;PASSWORD=@Pwd;@DatabaseName;PERSIST SECURITY INFO=FALSE;PACKET SIZE=4096";

            strConnectonString = strConnectonString.Replace("@Server", this.Server);
            strConnectonString = strConnectonString.Replace("@Uid", this.Uid);
            strConnectonString = strConnectonString.Replace("@Pwd", this.Password);

            if (this.DatabaseName != null && this.DatabaseName != "")
                strConnectonString = strConnectonString.Replace("@DatabaseName", "INITIAL CATALOG=" + this.DatabaseName);
            else
                strConnectonString = strConnectonString.Replace("@DatabaseName;", "");

            this.Connection.ConnectionString = strConnectonString;

            try
            {
                this.Open();

                this.Message = "服务器连接成功!";

                this.Result = true;
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
        }

        #endregion
    }
}
