using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace DatabaseDesignLib.MySqlClient
{
    public class Database : IDatabase
    {
        public string  GetCreateDB(KernelLib.Elements.DatabaseEntity db)
        {
            string createPhysicsDBSql = "Create Database "+ db.DatabaseName;

            string createEntityDBSql = "INSERT INTO S_DATABASES(DatabaseName, DatabaseHeaderName, Server, UID, Password, IsCreate, DatabaseSystemTypeID) VALUES('@DatabaseName@', '@DatabaseHeaderName@', '@Server@', '@UID@', '@Password@', '@IsCreate@', '@DatabaseSystemTypeID@');SELECT MAX(ID) FROM S_DATABASES";

            createEntityDBSql = createEntityDBSql.Replace("@DatabaseName@", db.DatabaseName);
            createEntityDBSql = createEntityDBSql.Replace("@DatabaseHeaderName@", db.DatabaseHeaderName);
            createEntityDBSql = createEntityDBSql.Replace("@Server@", db.Server);
            createEntityDBSql = createEntityDBSql.Replace("@UID@", db.UID);
            createEntityDBSql = createEntityDBSql.Replace("@Password@", db.Password);
            createEntityDBSql = createEntityDBSql.Replace("@IsCreate@", db.IsCreate);
            createEntityDBSql = createEntityDBSql.Replace("@DatabaseSystemTypeID@", db.DatabaseSystemTypeID);

            return createPhysicsDBSql + "&" + createEntityDBSql;
        }

        public string GetUpdateDB(KernelLib.Elements.DatabaseEntity db)
        {
            string createPhysicsDBSql = "";

            string createEntityDBSql = "UPDATE S_DATABASES SET DatabaseHeaderName = '@DatabaseHeaderName@', Server = '@Server@', UID = '@UID@', Password = '@Password@', DatabaseSystemTypeID = '@DatabaseSystemTypeID@' WHERE ID = @ID@";

            createEntityDBSql = createEntityDBSql.Replace("@DatabaseHeaderName@", db.DatabaseHeaderName);
            createEntityDBSql = createEntityDBSql.Replace("@Server@", db.Server);
            createEntityDBSql = createEntityDBSql.Replace("@UID@", db.UID);
            createEntityDBSql = createEntityDBSql.Replace("@Password@", db.Password);
            createEntityDBSql = createEntityDBSql.Replace("@DatabaseSystemTypeID@", db.DatabaseSystemTypeID);
            createEntityDBSql = createEntityDBSql.Replace("@ID@", db.ID);

            return createPhysicsDBSql + "&" + createEntityDBSql;
        }

        public string GetDeleteDB(KernelLib.Elements.DatabaseEntity db)
        {
            string DeletePhysicsDBSql = "Drop Database " + db.DatabaseName;

            string createEntityDBSql = "DELETE FROM S_DATABASES WHERE ID = " + db.ID;

            return DeletePhysicsDBSql + "&" + createEntityDBSql;
        }

        public string  GetQueryDB()
        {
            string QueryDBSql = "SELECT * FROM S_DATABASES ORDER BY ID";

            return QueryDBSql;
        }

        public string GetQueryDBByID(string ID)
        {
            string QueryDBSql = "SELECT * FROM S_DATABASES WHERE ID = "+ ID;

            return QueryDBSql;
        }

        public string GetQueryDBType()
        {
            string QueryDBSql = "SELECT * FROM S_DATABASESYSTEMTYPE WHERE ID > 0 ORDER BY ID";

            return QueryDBSql;
        }


        public string GetQueryDBByAccountID(string accountID)
        {
            string QueryDBSql = "SELECT S_DATABASES.* FROM S_DATABASES LEFT JOIN s_accounts ON S_DATABASES.ID = s_accounts.MasterDBID WHERE s_accounts.ID = '" + accountID +"'";

            return QueryDBSql;
        }
    }
}
