using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace DatabaseDesignLib.MySqlClient
{
    public class Column : IColumn
    {
        public string GetCreateColumn(KernelLib.Elements.ColumnEntity col)
        {
            string createPhysicsColumnSql = "ALTER TABLE "+ col.ParentTB.TableName +" ADD "+ col.ColumnName +" "+ ((DatabaseOptLib.DataClassType)(Convert.ToInt32(col.DataTypeID))).ToString() + "("+ col.Length +")";

            string createEntityColumnSql = "INSERT INTO S_COLUMNS(ColumnName, ColumnHeaderName, DataTypeID, Length, TableID) VALUES('@ColumnName@', '@ColumnHeaderName@', '@DataTypeID@', '@Length@', '@TableID@')";

            createEntityColumnSql = createEntityColumnSql.Replace("@ColumnName@", col.ColumnName);
            createEntityColumnSql = createEntityColumnSql.Replace("@ColumnHeaderName@", col.ColumnHeaderName);
            createEntityColumnSql = createEntityColumnSql.Replace("@DataTypeID@", col.DataTypeID);
            createEntityColumnSql = createEntityColumnSql.Replace("@Length@", col.Length);
            createEntityColumnSql = createEntityColumnSql.Replace("@TableID@", col.ParentTB.ID);

            return createPhysicsColumnSql + "&" + createEntityColumnSql;
        }

        public string GetUpdateColumn(KernelLib.Elements.ColumnEntity col, KernelLib.Elements.ColumnEntity newCol)
        {
            string createPhysicsColumnSql;

            createPhysicsColumnSql = "ALTER TABLE " + newCol.ParentTB.TableName + " CHANGE " + col.ColumnName + " " + " " + newCol.ColumnName + " " + ((DatabaseOptLib.DataClassType)(Convert.ToInt32(newCol.DataTypeID))).ToString() + "(" + newCol.Length + ")"; ;

            string createEntityColumnSql = "UPDATE S_COLUMNS SET ColumnName = '@ColumnName@', ColumnHeaderName = '@ColumnHeaderName@', DataTypeID = '@DataTypeID@', Length = '@Length@' WHERE ID = @ID@";

            createEntityColumnSql = createEntityColumnSql.Replace("@ColumnName@", newCol.ColumnName);
            createEntityColumnSql = createEntityColumnSql.Replace("@ColumnHeaderName@", newCol.ColumnHeaderName);
            createEntityColumnSql = createEntityColumnSql.Replace("@DataTypeID@", newCol.DataTypeID);
            createEntityColumnSql = createEntityColumnSql.Replace("@Length@", newCol.Length);
            createEntityColumnSql = createEntityColumnSql.Replace("@ID@", col.ID);

            return createPhysicsColumnSql + "&" + createEntityColumnSql;
        }

        public string GetDeleteColumn(KernelLib.Elements.ColumnEntity col)
        {
            string createPhysicsColumnSql = "ALTER TABLE " + col.ParentTB.TableName + " DROP " + col.ColumnName;

            string createEntityColumnSql = "DELETE FROM S_COLUMNS WHERE ID = " + col.ID;

            return createPhysicsColumnSql + "&" + createEntityColumnSql;
        }

        public string GetQueryColumn(string tbID)
        {
            string QueryColSql = "SELECT * FROM S_COLUMNS WHERE TableID = @TableID@ ORDER BY ID";

            QueryColSql = QueryColSql.Replace("@TableID@", tbID);

            return QueryColSql;
        }

        public string GetQueryColumnByID(string ID)
        {
            string QueryColSql = "SELECT * FROM S_COLUMNS WHERE ID = " + ID;

            return QueryColSql;
        }

        public string GetQueryDataType()
        {
            string QueryDataTypeSql = "SELECT * FROM S_DATATYPE ORDER BY ID";

            return QueryDataTypeSql;
        }
    }
}
