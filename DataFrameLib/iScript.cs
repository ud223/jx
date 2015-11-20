using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace DataFrameLib
{
    public interface iScript
    {
        string GetQueryScript(KernelLib.SystemEntity.Entity entity, KernelLib.SystemEntity.ProjectEntity project);

        string GetInsertScript(KernelLib.SystemEntity.Entity entity, KernelLib.SystemEntity.ProjectEntity project);

        string GetUpdateScript(KernelLib.SystemEntity.Entity entity, KernelLib.SystemEntity.ProjectEntity project);

        string GetDeleteScript(KernelLib.SystemEntity.Entity entity, KernelLib.SystemEntity.ProjectEntity project);

        string GetLoadScript(KernelLib.SystemEntity.Entity entity, KernelLib.SystemEntity.ProjectEntity project);
    }
}
