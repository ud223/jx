using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace DatabaseDesignLib
{
    public interface IColumn
    {
        string GetCreateColumn(KernelLib.Elements.ColumnEntity col);

        string GetUpdateColumn(KernelLib.Elements.ColumnEntity col, KernelLib.Elements.ColumnEntity newCol);

        string GetDeleteColumn(KernelLib.Elements.ColumnEntity col);

        string GetQueryColumn(string tbID);

        string GetQueryColumnByID(string ID);

        string GetQueryDataType();
    }
}
