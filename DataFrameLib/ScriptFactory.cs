using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data;

namespace DataFrameLib
{
    public class ScriptFactory : BaseLib.BaseClass
    {
        private string _sNameSpace = "DataFrameLib";
        private string _sClientPath = "Client";
        private string _sClassPath = "DataFrameLib.Client.";
        private string _sClassName = "";

        public iScript CreateClient(KernelLib.SystemEntity.ProjectEntity project)
        {
            iScript script = null;

            try
            {
                string data_type = project.DbEntity.Data_Type;
                string dll_path = _sClassPath + ((DatabaseLib.DataType)(Int32.Parse(data_type))).ToString();
                //获取对应的数据库类型
                Type ClientType = Type.GetType(dll_path, true);
                //创建对应的数据库操作实例并返回给接口使外层代码通过接口能找到数据库操作实现代码
                script = (iScript)Activator.CreateInstance(ClientType);
            }
            catch (Exception ex)
            {
                //获取操作异常的错误信息
                this.Message = ex.Message;
                this.Result = false;
            }

            //返回操作实例代码
            return script;
        }
    }
}
