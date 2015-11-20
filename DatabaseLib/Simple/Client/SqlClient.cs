using System;
using System.Data;
using System.Data.SqlClient;

namespace ToolsLib.Data.Client
{
    public class SqlClient : IDatabase
    {
        public string ConnectionString
        {
            get
            {
                if (this._con == null)
                    return "";

                return this._con.ConnectionString;
            }
            set
            {
                if (this._con == null)
                    this._con = this.GetConnection(value);
                else
                    this._con.ConnectionString = value;
            }
        }

        private SqlConnection _con = null;

        private SqlConnection Connection
        {
            get
            {
                if (this._con == null)
                    this._con = GetConnection();

                if (this._con.ConnectionString == "")
                    this._con.ConnectionString = this.GetConnectionString();

                return this._con;
            }
        }

        public void Open()
        {
            if (this.Connection.State == ConnectionState.Closed)
                this.Connection.Open();
        }

        public void Close()
        {
            this.ConnectionClose();

            this.DataReaderClose();
        }

        private void ConnectionClose()
        {
            if (this._con.State == ConnectionState.Open)
                this._con.Close();

            if (this._con != null)
                this._con.Dispose();
        }

        private void DataReaderClose()
        {
            if (this._DataReader != null && this._DataReader.IsClosed == false)
                this._DataReader.Close();
        }

        private string GetConnectionString()
        {
            return System.Configuration.ConfigurationManager.AppSettings["ConnectionString"].ToString();
        }

        private string GetConnectionString(string strConnectionString)
        {
            return strConnectionString;
        }

        private SqlConnection GetConnection()
        {
            return new SqlConnection(this.GetConnectionString());
        }

        public SqlConnection GetConnection(string strConnectionString)
        {
            return new SqlConnection(strConnectionString);
        }

        #region IDatabase 成员

        private SqlDataReader _DataReader;

        public IDataReader DataReader
        {
            get
            {
                return this._DataReader;
            }
        }

        private string _sMessage = "Operating Succeed!";
        /// <summary>
        /// 操作信息
        /// </summary>
        public string Message
        {
            get { return this._sMessage; }
        }

        private bool _bResult = true;
        /// <summary>
        /// 操作结果
        /// </summary>
        public bool Result
        {
            get { return this._bResult; }
        }

        public DataSet Query(string strSql)
        {
            SqlDataAdapter da = new SqlDataAdapter();
            SqlCommand cmd = new SqlCommand(strSql, this.Connection);

            da.SelectCommand = cmd;
            cmd.CommandTimeout = 999;

            DataSet ds = new DataSet();

            try
            {
                da.Fill(ds);
            }
            catch (Exception ex)
            {
                this._sMessage = ex.Message;
                this._bResult = false;
            }
            finally
            {
                this.Close();
            }

            return ds;
        }

        public void Read(string strSql)
        {
            SqlCommand cmd = new SqlCommand(strSql, this.Connection);

            cmd.CommandType = CommandType.Text;
            cmd.CommandTimeout = 999;

            try
            {
                this.Open();

                this._DataReader = cmd.ExecuteReader(CommandBehavior.CloseConnection);
            }
            catch (Exception ex)
            {
                this._sMessage = ex.Message;
                this._bResult = false;
            }
        }

        public int Execute(string strSql)
        {
            SqlCommand cmd = new SqlCommand(strSql, this.Connection);

            cmd.CommandType = CommandType.Text;
            cmd.CommandTimeout = 999;

            int nCount = 0;

            try
            {
                this.Open();

                nCount = cmd.ExecuteNonQuery();
            }
            catch (Exception ex)
            {
                this._sMessage = ex.Message;
                this._bResult = false;
            }
            finally
            {
                this.Close();
            }

            return nCount;
        }

        public int Execute(string strSql, IDataParameter[] oParams)
        {
            SqlCommand cmd = new SqlCommand(strSql, this.Connection);

            cmd.CommandType = CommandType.Text;
            cmd.CommandTimeout = 999;

            int nCount = 0;

            foreach (IDataParameter para in oParams)
            {
                cmd.Parameters.Add(para);
            }

            try
            {
                this.Open();

                nCount = cmd.ExecuteNonQuery();
            }
            catch (Exception ex)
            {
                this._sMessage = ex.Message;
                this._bResult = false;
            }
            finally
            {
                this.Close();
            }

            return nCount;
        }

        public object ExecuteScalarSQL(string strSql)
        {
            object oReturn = "";

            SqlCommand cmd = new SqlCommand(strSql, this.Connection);

            cmd.CommandType = CommandType.Text;

            cmd.CommandTimeout = 999;

            try
            {
                this.Open();

                oReturn = cmd.ExecuteScalar();
            }
            catch (Exception ex)
            {
                this._sMessage = ex.Message;
                this._bResult = false;
            }
            finally
            {
                this.Close();
            }

            return oReturn;
        }

        #endregion

        #region 创建存储过程参数

        /// <summary>
        /// 创建存储过程参数
        /// </summary>
        /// <param name="ParamName">参数名称</param>
        /// <param name="DbType">参数类型</param>
        /// <param name="Size">参数大小</param>
        /// <param name="Direction">参数方向</param>
        /// <param name="Value">参数值</param>
        /// <returns>返回一个新的参数</returns>
        public IDataParameter MakeParam(string ParamName, object Value)
        {
            SqlParameter param;

            param = new SqlParameter(ParamName, Value);

            return param;
        }

        #endregion
    }
}
