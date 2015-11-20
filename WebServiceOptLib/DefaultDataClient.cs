using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Data;

namespace WebServiceOptLib
{
    public class DefaultDataClient : CommonLib.BaseClass
    {
        private DatabaseLib.IDatabase _dataClient = null;

        public DatabaseLib.IDatabase dataClient
        { 
            get 
            {
                if (_dataClient == null)
                {
                    DatabaseLib.DatabaseFactory databaseFactory = new DatabaseLib.DatabaseFactory();
                    
                    _dataClient= databaseFactory.CreateClient(BaseLib.SystemType.Web);
                }
                
                return _dataClient; 
            }
        }

        public DataSet Query(string strSql)
        {
            dataClient.SqlText = strSql;

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;

            return ds;
        }

        public void Execute(string strSql)
        {
            dataClient.SqlText = strSql;

            dataClient.Execute(CommandType.Text);

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;
        }

        public void GetSystemDataClient()
        {
            _dataClient = null;

            DatabaseLib.DatabaseFactory databaseFactory = new DatabaseLib.DatabaseFactory();

            _dataClient = databaseFactory.CreateClient(BaseLib.SystemType.Web);
        }

        public object Clone()
        {
            DefaultDataClient newOptDB = new DefaultDataClient();

            newOptDB.GetSystemDataClient();

            newOptDB.dataClient.DatabaseName = this.dataClient.DatabaseName;
            newOptDB.dataClient.Server = this.dataClient.Server;
            newOptDB.dataClient.Uid = this.dataClient.Uid;
            newOptDB.dataClient.Password = this.dataClient.Password;
            newOptDB.dataClient.Port = this.dataClient.Port;

            return newOptDB;
        }
    }
}
