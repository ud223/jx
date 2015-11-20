//using System;
//using System.Collections.Generic;
//using System.Linq;
//using System.Text;
//using System.Data;


//namespace ProjectLib
//{
//    public class ModuleHandle : BaseLib.BaseClass
//    {
//        /// <summary>
//        /// 初始化模块实体的数据模型
//        /// </summary>
//        /// <param name="module"></param>
//        /// <param name="project"></param>
//        public void Init(KernelLib.SystemEntity.ModuleEntity module)
//        {
//            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
//            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

//            string strSql = "SELECT * FROM s_dataentitys WHERE ID = '@ID@'; SELECT s_dataentitydtls.*, s_columns.Name, s_columns.Text FROM s_dataentitydtls left join s_columns on s_dataentitydtls.CO_ID = s_columns.ID WHERE Entity_ID = '@ID@'; SELECT * FROM s_opt_type_relations WHERE Entity_ID = '@ID@'";

//            strSql = strSql.Replace("@ID@", module.Entity.ID);

//            dataClient.SqlText = strSql;

//            DataSet ds = dataClient.Query();

//            StringBuilder tables_id = new StringBuilder();

//            if (this.Result)
//            {
//                module.Entity.Text = ds.Tables[0].Rows[0]["Text"].ToString();
//                module.Entity.Name = ds.Tables[0].Rows[0]["Name"].ToString();
//                module.Entity.EntityType = ds.Tables[0].Rows[0]["EntityType"].ToString();
                
//                foreach (DataRow row in ds.Tables[1].Rows)
//                {
//                    KernelLib.SystemEntity.Item item = new KernelLib.SystemEntity.Item();

//                    item.ID = row["ID"].ToString();
//                    item.Text = row["Text"].ToString();
//                    item.Name = row["Name"].ToString();
//                    item.Tb_ID = row["Tb_ID"].ToString();
//                    item.Co_ID = row["Co_ID"].ToString();

//                    module.Entity.Items.Add(item);

//                    //组织数据表id条件
//                    if (tables_id.ToString().IndexOf(item.Tb_ID) < 0)
//                    {
//                        if (tables_id.Length > 0)
//                            tables_id.Append(",");

//                        tables_id.Append("'");
//                        tables_id.Append(item.Tb_ID);
//                        tables_id.Append("'");
//                    }
//                }

//                foreach (DataRow row in ds.Tables[2].Rows)
//                {
//                    KernelLib.OptTypeEntity.OptEntity opt = new KernelLib.OptTypeEntity.OptEntity();

//                    opt.ID = row["ID"].ToString();
//                    opt.Opt_Type = row["Opt_Type"].ToString();
//                    opt.Entity_ID = row["Entity_ID"].ToString();
//                    opt.IsPage = row["IsPage"].ToString();

//                    module.Entity.OptEntitys.Add(opt);
//                }
//            }

//            this.Message = dataClient.Message;
//            this.Result = dataClient.Result;

//            if (this.Result)
//            {
//                this.LoadTableStructure(tables_id.ToString(), module);
//            }
//        }

//        /// <summary>
//        /// 加载模块所属数据表信息
//        /// </summary>
//        /// <param name="table_id"></param>
//        /// <param name="module"></param>
//        private void LoadTableStructure(string table_id, KernelLib.SystemEntity.ModuleEntity module)
//        {
//            if (table_id.Length == 0)
//                return;

//            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
//            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

//            string strSql = "SELECT * FROM s_tables WHERE ID in (@ID@); SELECT * FROM s_columns WHERE TB_ID in (@ID@)";

//            strSql = strSql.Replace("@ID@", table_id);

//            dataClient.SqlText = strSql;

//            DataSet ds = dataClient.Query();

//            if (dataClient.Result)
//            {
//                foreach (DataRow tb_row in ds.Tables[0].Rows)
//                {
//                    KernelLib.DatabaseEntity.TableEntity table = new KernelLib.DatabaseEntity.TableEntity();

//                    table.ID = tb_row["ID"].ToString();
//                    table.Name = tb_row["Name"].ToString();
//                    table.Text = tb_row["Text"].ToString();
//                    table.Table_Type = tb_row["Table_Type"].ToString();
//                    table.DB_ID = tb_row["DB_ID"].ToString();

//                    DataRow[] column_rows = ds.Tables[1].Select("TB_ID = '" + tb_row["ID"].ToString() + "'");

//                    foreach (DataRow column_row in column_rows)
//                    {
//                        KernelLib.DatabaseEntity.ColumnEntity column = new KernelLib.DatabaseEntity.ColumnEntity();

//                        column.ID = column_row["ID"].ToString();
//                        column.IsNull = column_row["ID"].ToString();
//                        column.IsPri = column_row["ID"].ToString();
//                        column.Length = column_row["Length"].ToString();
//                        column.Name = column_row["Name"].ToString();
//                        column.Scale = column_row["Scale"].ToString();
//                        column.Table_ID = column_row["TB_ID"].ToString();
//                        column.Text = column_row["Text"].ToString();
//                        column.Column_Type = column_row["Column_Type"].ToString();
//                        column.DefaultValue = column_row["DefaultValue"].ToString();

//                        table.Columns.Add(column);
//                    }

//                    module.Entity.Tables.Add(table);
//                }
//            }

//            this.Result = dataClient.Result;
//            this.Message = dataClient.Message;
//        }
//    }
//}
