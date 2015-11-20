using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace ExceptionLib.System
{
    public class DataFrameException
    {
        public void DontFindTable()
        {
            throw new Exception("DataFrameException: 没有发现对应的数据结构数据表名!");
        }
    }
}
