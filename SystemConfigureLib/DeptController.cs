using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data;

namespace SystemConfigureLib
{
    public class DeptController : iController
    {
        /// <summary>
        /// 获取所有部门
        /// </summary>
        /// <returns></returns>
        public override List<System.Collections.Hashtable> getAll()
        {
            this.SqlText = "SELECT * FROM sy_depts";

            return base.getAll();
        }


        public override System.Collections.Hashtable load(string id)
        {
            this.SqlText = "SELECT * FROM sy_depts WHERE DeptID = ";

            return base.load(id);
        }

        /// <summary>
        /// 新增部门
        /// </summary>
        /// <param name="data"></param>
        /// <returns>返回新增部门ID</returns>
        public override string add(System.Collections.Hashtable data)
        {
            this.SqlText = "INSERT INTO sy_depts(DeptText) VALUES('@DeptText@'); SELECT DeptID FROM sy_depts ORDER BY DeptID DESC LIMIT 0, 1";

            return base.add(data);
        }

        /// <summary>
        /// 保存部门信息
        /// </summary>
        /// <param name="id"></param>
        /// <param name="data"></param>
        public override void save(System.Collections.Hashtable data)
        {
            this.SqlText = "UPDATE sy_depts SET DeptText = '@DeptText@' WHERE DeptID = @DeptID@";

            base.save(data);
        }

        public override void delete(string id)
        {
            this.SqlText = "DELETE FROM sy_depts WHERE DeptID = ";

            base.delete(id);
        }
    }
}
