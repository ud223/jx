using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data;

namespace OptLib
{
    public class Load : BaseLib.BaseClass
    {
        public DataTable LoadData(KernelLib.SystemEntity.ModuleEntity module, KernelLib.SystemEntity.ProjectEntity project)
        {
            DataFrameLib.ScriptFactory scriptFactory = new DataFrameLib.ScriptFactory();
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            //获取数据访问操作端
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);
            //获取脚本生成对象
            DataFrameLib.iScript script = scriptFactory.CreateClient(project);
            //生成执行脚本
            string strScript = script.GetLoadScript(module.Entity, project);

            dataClient.SqlText = strScript;

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;

            if (this.Result)
                return ds.Tables[0];
            else
                return null;
        }
    }
}
