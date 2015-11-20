using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace DatabaseDesignLib
{
    public interface IDatabase
    {
        string GetCreateDB(KernelLib.Elements.DatabaseEntity db);

        string GetUpdateDB(KernelLib.Elements.DatabaseEntity db);

        string GetDeleteDB(KernelLib.Elements.DatabaseEntity db);

        string GetQueryDB();

        string GetQueryDBByID(string ID);

        string GetQueryDBByAccountID(string accountID);

        string GetQueryDBType();
    }
}
