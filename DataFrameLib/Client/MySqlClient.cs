using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace DataFrameLib.Client
{
    public class MySqlClient : iScript
    {
        public string GetQueryScript(KernelLib.SystemEntity.Entity entity, KernelLib.SystemEntity.ProjectEntity project)
        {
            throw new NotImplementedException();
        }

        public string GetInsertScript(KernelLib.SystemEntity.Entity entity, KernelLib.SystemEntity.ProjectEntity project)
        {
            throw new NotImplementedException();
        }

        public string GetUpdateScript(KernelLib.SystemEntity.Entity entity, KernelLib.SystemEntity.ProjectEntity project)
        {
            throw new NotImplementedException();
        }

        public string GetDeleteScript(KernelLib.SystemEntity.Entity entity, KernelLib.SystemEntity.ProjectEntity project)
        {
            throw new NotImplementedException();
        }

        public string GetLoadScript(KernelLib.SystemEntity.Entity entity, KernelLib.SystemEntity.ProjectEntity project)
        {
            StringBuilder script = new StringBuilder();
            //查询脚本头
            script.Append("SELECT ");
            //查询字段脚本
            script.Append(this.GetQueryColumnsScript(entity));
            //查询表-------------暂时单表，马上升级为多表的查询
            script.Append(" FROM ");
            script.Append(this.GetTableName(entity.Items[0].Tb_ID, entity));

            return script.ToString();
        }

        #region 内部处理方法

        #region 查询类内部处理方法
        private string GetQueryColumnsScript(KernelLib.SystemEntity.Entity entity)
        {
            StringBuilder script = new StringBuilder();
            //组织查询字段脚本
            foreach (KernelLib.SystemEntity.Item item in entity.Items)
            {
                if (script.Length > 0)
                    script.Append(", ");

                script.Append(this.GetTableName(item.Tb_ID, entity));
                script.Append(".");
                script.Append(item.Name);
            }

            return script.ToString();
        }

        #endregion

        #region 通用类
        private string GetTableName(string table_id, KernelLib.SystemEntity.Entity entity)
        {
            foreach (KernelLib.DatabaseEntity.TableEntity table in entity.Tables)
            {
                if (table.ID == table_id)
                {
                    return table.Name;
                }
            }
            //提交异常，没有返回对应的数据表格
            ExceptionLib.System.DataFrameException exception = new ExceptionLib.System.DataFrameException();
            //返回异常
            exception.DontFindTable();
            //附带代码，不然编译不通过
            return null;
        }

        #endregion

        #endregion
    }
}
