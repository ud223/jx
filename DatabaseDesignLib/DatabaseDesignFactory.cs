using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace DatabaseDesignLib
{
    public class DatabaseDesignFactory
    {
        public static IDatabase MakeDBDesign(DatabaseOptLib.DataType dataType)
        {
            IDatabase dbDesign = null;

            try
            {
                Type type = Type.GetType("DatabaseDesignLib." + dataType.ToString() + ".Database");
                
                dbDesign = (IDatabase)Activator.CreateInstance(type);
            }
            catch (Exception ex)
            { 
            
            }

            return dbDesign;
        }

        

        public static ITable MakeTableDesign(DatabaseOptLib.DataType dataType)
        {
            ITable tbDesign = null;

            try
            {
                Type type = Type.GetType("DatabaseDesignLib." + dataType.ToString() + ".Table");

                tbDesign = (ITable)Activator.CreateInstance(type);
            }
            catch (Exception ex)
            {

            }

            return tbDesign;
        }

        public static IColumn MakeColumnDesign(DatabaseOptLib.DataType dataType)
        {
            IColumn colDesign = null;

            try
            {
                Type type = Type.GetType("DatabaseDesignLib." + dataType.ToString() + ".Column");

                colDesign = (IColumn)Activator.CreateInstance(type);
            }
            catch (Exception ex)
            {

            }

            return colDesign;
        }
    }
}
