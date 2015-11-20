using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data;

namespace SystemConfigureLib
{
    public class MenuController : iController
    {
        /// <summary>
        /// 获取所有菜单
        /// </summary>
        /// <returns></returns>
        public override List<System.Collections.Hashtable> getAll()
        {
            this.SqlText = "SELECT * FROM sy_menus ORDER BY ParentIndex, `Index`";

            return base.getAll();
        }

        /// <summary>
        /// 获取顶级菜单
        /// </summary>
        /// <returns></returns>
        public List<System.Collections.Hashtable> getTopMenu()
        {
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            //获取数据访问操作端
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            dataClient.SqlText = "SELECT * FROM sy_menus WHERE Level = 1";

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;

            List<System.Collections.Hashtable> list = null;

            if (this.Result)
            {
                list = new List<System.Collections.Hashtable>();

                foreach (DataRow row in ds.Tables[0].Rows)
                {
                    System.Collections.Hashtable ht = new System.Collections.Hashtable();

                    foreach (DataColumn dc in ds.Tables[0].Columns)
                    {
                        string key = dc.ColumnName;
                        string value = CommonLib.Common.Validate.IsNullString(row[dc.ColumnName]);

                        ht.Add(key, value);
                    }

                    list.Add(ht);

                }

                return list;
            }
            else
                return null;
        }

        public override System.Collections.Hashtable load(string id)
        {
            this.SqlText = "SELECT * FROM sy_menus WHERE MenuID = ";

            return base.load(id);
        }

        /// <summary>
        /// 新增菜单
        /// </summary>
        /// <param name="menu"></param>
        /// <returns>返回新增菜单ID</returns>
        public override string add(System.Collections.Hashtable data)
        {
            this.SqlText = "INSERT INTO sy_menus(MenuText, Url, Level, ParentID, `Index`, CreateAt, ModifyAt) VALUES('@MenuText@', '@Url@', @Level@, @ParentID@, @Index@, '@CreateAt@', '@ModifyAt@'); SELECT MenuID FROM sy_menus ORDER BY MenuID DESC LIMIT 0, 1";

            return base.add(data);
        }

        /// <summary>
        /// 保存菜单信息
        /// </summary>
        /// <param name="id"></param>
        /// <param name="menu_data"></param>
        public override void save(System.Collections.Hashtable data)
        {
            this.SqlText = "UPDATE sy_menus SET MenuText = '@MenuText@', Url = '@Url@', Level = @Level@, ParentID = @ParentID@, `Index` = @Index@, ModifyAt = '@ModifyAt@' WHERE MenuID = @MenuID@";

            base.save(data);
        }

        public override void delete(string id)
        {
            this.SqlText = "DELETE FROM sy_menus WHERE MenuID = ";

            base.delete(id);
        }
    }
}
