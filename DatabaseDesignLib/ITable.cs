using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace DatabaseDesignLib
{
    public interface ITable
    {
        string GetCreateTable(KernelLib.Elements.TableEntity tb);

        string GetUpdateTable(KernelLib.Elements.TableEntity tb, KernelLib.Elements.TableEntity newTab);

        string GetDeleteTable(KernelLib.Elements.TableEntity tb);

        string GetQueryTable(string dbID);

        string GetQueryTableByID(string ID);
    }
}
