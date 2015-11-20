using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data;

namespace SystemConfigureLib
{
    public class UserTypeController : iController
    {
        /// <summary>
        /// 获取所有角色
        /// </summary>
        /// <returns></returns>
        public override List<System.Collections.Hashtable> getAll()
        {
            this.SqlText = "SELECT * FROM sy_usertype";

            return base.getAll();
        }


        public override System.Collections.Hashtable load(string id)
        {
            this.SqlText = "SELECT * FROM sy_usertype WHERE UserTypeID = ";

            return base.load(id);
        }

        /// <summary>
        /// 新增角色
        /// </summary>
        /// <param name="data"></param>
        /// <returns>返回新增用户ID</returns>
        public override string add(System.Collections.Hashtable data)
        {
            this.SqlText = "INSERT INTO sy_usertype(UserTypeText, isSys) VALUES('@UserTypeText@', @isSys@); SELECT UserTypeID FROM sy_usertype ORDER BY UserTypeID DESC LIMIT 0, 1";

            return base.add(data);
        }

        /// <summary>
        /// 保存角色信息
        /// </summary>
        /// <param name="id"></param>
        /// <param name="data"></param>
        public override void save(System.Collections.Hashtable data)
        {
            this.SqlText = "UPDATE sy_usertype SET UserTypeText = '@UserTypeText@', isSys = @isSys@ WHERE UserTypeID = @UserTypeID@";

            base.save(data);
        }

        public override void delete(string id)
        {
            this.SqlText = "DELETE FROM sy_usertype WHERE UserTypeID = ";

            base.delete(id);
        }
    }
}
