using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data;

namespace CodeLib
{
    public class CodeHandle : BaseLib.BaseClass
    {
        private DataTable _Codes = null;

        private DataTable Codes
        {  
            get
            {
                if (this._Codes == null)
                    this.Init();

                return this._Codes;
            }
        }

        public void Init()
        {
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            string strSql = "SELECT * FROM s_codes";

            dataClient.SqlText = strSql;

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;

            if (this.Result)
            {
                this._Codes = ds.Tables[0];
            }
        }

        public string GetValueById(string id)
        {
            DataRow[] row = this.Codes.Select("ID = '"+ id +"'");

            if (row.Length < 1)
                return "";

            return row[0]["Value"].ToString();
        }

        public DataRow[] GetGroupByCode(string code)
        {
            DataRow[] rows = this.Codes.Select("Code = '" + code + "'");

            return rows;
        }

        public bool Exist(CodeEntity codeEntity)
        {
            DataRow[] rows = this.Codes.Select("ID = '"+ codeEntity.ID +"' AND Code = '"+ codeEntity.Code +"'");

            if (rows.Length > 0)
                return true;
            else
                return false;
        }

        public void Add(CodeEntity codeEntity)
        {
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            string strSql = "INSERT INTO s_codes(ID, Text, Value, Code, CodeText) VALUES('@ID@', '@Text@', '@Value@', '@Code@', '@CodeText@')";

            strSql = strSql.Replace("@ID@", codeEntity.ID);
            strSql = strSql.Replace("@Text@", codeEntity.Text);
            strSql = strSql.Replace("@Value@", codeEntity.Value);
            strSql = strSql.Replace("@Code@", codeEntity.Code);
            strSql = strSql.Replace("@CodeText@", codeEntity.CodeText);

            dataClient.SqlText = strSql;

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;
        }

        public void Modify(CodeEntity codeEntity)
        {
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            string strSql = "UPDATE s_codes SET Text = '@Text@',  Value = '@Value@' WHERE ID = '@ID@'; UPDATE s_codes SET CodeText = '@CodeText@ WHERE Code = '@Code@'";

            strSql = strSql.Replace("@ID@", codeEntity.ID);
            strSql = strSql.Replace("@Text@", codeEntity.Text);
            strSql = strSql.Replace("@Value@", codeEntity.Value);
            strSql = strSql.Replace("@Code@", codeEntity.Code);
            strSql = strSql.Replace("@CodeText@", codeEntity.CodeText);

            dataClient.SqlText = strSql;

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;
        }

        public void Delete(CodeEntity codeEntity)
        {
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            string strSql = "DELETE FROM s_codes WHERE ID = '@ID@'";

            strSql = strSql.Replace("@ID@", codeEntity.ID);

            dataClient.SqlText = strSql;

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;
        }

        public void Save()
        {
            CacheLib.Cache cache = new CacheLib.Cache();
            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string key = cache.Add<DataTable>("codes", this.Codes);

            cookie.AddCookie("codes", key);
        }

        public void Load()
        {
            CacheLib.Cache cache = new CacheLib.Cache();
            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string key = cookie.GetCookie("codes");

            this._Codes = cache.Get<DataTable>(key);
        }

        public void Refresh()
        {
            this.Init();
        }
    }
}   
