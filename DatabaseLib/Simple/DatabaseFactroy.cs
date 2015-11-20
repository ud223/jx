using System;
using System.Collections.Generic;
using System.Text;

namespace ToolsLib.Data
{
    public class DatabaseFactroy
    {
        private static string _sMessage = "Operating Successed!";

        public static string Message
        { get { return _sMessage; } }

        private static bool _bResult = true;

        public static bool Result
        { get { return _bResult; } }

        public static IDatabase MakeDatabaseClient(DataType dataType)
        {
            IDatabase dataClient = null;

            try
            {
                Type type = Type.GetType("ToolsLib.Data.Client." + dataType.ToString());

                dataClient = (IDatabase)Activator.CreateInstance(type, false);
            }
            catch (Exception ex)
            {
                _sMessage = ex.Message;
                _bResult = false;
            }

            return dataClient;
        }
    }
}
