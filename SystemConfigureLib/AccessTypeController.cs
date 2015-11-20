using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace SystemConfigureLib
{
    public class AccessTypeControllerr : iController
    {
        /// <summary>
        /// 获取所有访问类型
        /// </summary>
        /// <returns></returns>
        public override List<System.Collections.Hashtable> getAll()
        {
            this.SqlText = "SELECT * FROM sy_accesstype";

            return base.getAll();
        }


        public override System.Collections.Hashtable load(string id)
        {
            this.SqlText = "SELECT * FROM sy_accesstype WHERE AccessTypeID = ";

            return base.load(id);
        }

        /// <summary>
        /// 新增访问类型
        /// </summary>
        /// <param name="data"></param>
        /// <returns>返回新增用户ID</returns>
        public override string add(System.Collections.Hashtable data)
        {
            this.SqlText = "INSERT INTO sy_accesstype(AccessTypeText) VALUES('@AccessTypeText@'); SELECT AccessTypeID FROM sy_accesstype ORDER BY AccessTypeID DESC LIMIT 0, 1";

            return base.add(data);
        }

        /// <summary>
        /// 保存访问类型信息
        /// </summary>
        /// <param name="id"></param>
        /// <param name="data"></param>
        public override void save(System.Collections.Hashtable data)
        {
            this.SqlText = "UPDATE sy_accesstype SET AccessTypeText = '@AccessTypeText@' WHERE AccessTypeID = @AccessTypeID@";

            base.save(data);
        }

        public override void delete(string id)
        {
            this.SqlText = "DELETE FROM sy_accesstype WHERE AccessTypeID = ";

            base.delete(id);
        }
    }
}
