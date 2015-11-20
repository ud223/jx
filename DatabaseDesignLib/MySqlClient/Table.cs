using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace DatabaseDesignLib.MySqlClient
{
    public class Table : ITable
    {
        public string GetCreateTable(KernelLib.Elements.TableEntity tb)
        {
            string createPhysicsTableSql = "Create Table " + tb.TableName + " ( ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY )";

            string createEntityTableSql = "INSERT INTO S_TABLES(TableName, TableHeaderName, DatabaseID, IsCreate) VALUES('@TableName@', '@TableHeaderName@', '@DatabaseID@', '@IsCreate@'); SELECT MAX(ID) FROM S_TABLES";

            createEntityTableSql = createEntityTableSql.Replace("@TableName@", tb.TableName);
            createEntityTableSql = createEntityTableSql.Replace("@TableHeaderName@", tb.TableHeaderName);
            createEntityTableSql = createEntityTableSql.Replace("@DatabaseID@", tb.ParentDB.ID);
            createEntityTableSql = createEntityTableSql.Replace("@IsCreate@", tb.IsCreate);

            return createPhysicsTableSql + "&" + createEntityTableSql;
        }

        public string GetUpdateTable(KernelLib.Elements.TableEntity tb, KernelLib.Elements.TableEntity newTab)
        {
            string createPhysicsTBSql;
            
            if (tb.IsCreate == "1")
                createPhysicsTBSql = "ALTER TABLE " + tb.TableName + " RENAME TO " + newTab.TableName;
            else
                createPhysicsTBSql = "Create Table " + newTab.TableName + " ( ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY )";

            string createEntityTBSql = "UPDATE S_TABLES SET TableName = '@TableName@', TableHeaderName = '@TableHeaderName@', IsCreate = '@IsCreate@' WHERE ID = @ID@";

            createEntityTBSql = createEntityTBSql.Replace("@TableName@", newTab.TableName);
            createEntityTBSql = createEntityTBSql.Replace("@TableHeaderName@", newTab.TableHeaderName);
            createEntityTBSql = createEntityTBSql.Replace("@IsCreate@", newTab.IsCreate);
            createEntityTBSql = createEntityTBSql.Replace("@ID@", tb.ID);

            return createPhysicsTBSql + "&" + createEntityTBSql;
        }

        public string GetDeleteTable(KernelLib.Elements.TableEntity tb)
        {
            string DeletePhysicsTBSql = "Drop TABLE " + tb.TableName;

            string createEntityTBSql = "DELETE FROM S_TABLES WHERE ID = " + tb.ID;

            return DeletePhysicsTBSql + "&" + createEntityTBSql;
        }

        public string GetQueryTable(string dbID)
        {
            string QueryTBSql = "SELECT * FROM S_TABLES WHERE DATABASEID = @DATABASEID@ ORDER BY ID";

            QueryTBSql = QueryTBSql.Replace("@DATABASEID@", dbID);

            return QueryTBSql;
        }

        public string GetQueryTableByID(string ID)
        {
            string QueryTBSql = "SELECT * FROM S_TABLES WHERE ID = " + ID;

            return QueryTBSql;
        }
    }
}
